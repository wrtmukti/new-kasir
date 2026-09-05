<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrderBundle;
use App\Models\Admin\Product;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use Illuminate\Http\Request;

class MenuAnalyticsController extends Controller
{
    public function index(?Request $request = null)
    {
        $request = $request ?? request();

        $activeOutletId = session('active_outlet_id') ?? session('outlet_id');

        // Jika user memilih bulan & tahun secara eksplisit via filter, gunakan input tersebut
        if ($request->filled('month') && $request->filled('year')) {
            $month = sprintf('%02d', $request->input('month'));
            $year = (int) $request->input('year');
        } else {
            // Cek apakah bulan berjalan memiliki transaksi aktif
            $hasCurrentMonth = Transaction::whereYear('transaction_date', date('Y'))
                ->whereMonth('transaction_date', date('m'))
                ->where('transaction_status', 'success')
                ->where('delete_status', 0)
                ->when($activeOutletId, fn($q) => $q->where('outlet_id', $activeOutletId))
                ->exists();

            if ($hasCurrentMonth) {
                $month = sprintf('%02d', date('m'));
                $year = (int) date('Y');
            } else {
                // Fallback otomatis ke bulan terakhir yang memiliki transaksi aktif
                $latestTx = Transaction::where('transaction_status', 'success')
                    ->where('delete_status', 0)
                    ->when($activeOutletId, fn($q) => $q->where('outlet_id', $activeOutletId))
                    ->latest('transaction_date')
                    ->first();

                if ($latestTx && $latestTx->transaction_date) {
                    $month = \Carbon\Carbon::parse($latestTx->transaction_date)->format('m');
                    $year = (int) \Carbon\Carbon::parse($latestTx->transaction_date)->format('Y');
                } else {
                    $month = sprintf('%02d', date('m'));
                    $year = (int) date('Y');
                }
            }
        }

        // Query Paid Transactions in selected month/year
        $transactions = Transaction::whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('transaction_status', 'success')
            ->where('delete_status', 0)
            ->when($activeOutletId, fn($q) => $q->where('outlet_id', $activeOutletId))
            ->get();

        $transactionIds = $transactions->pluck('transaction_id');
        $totalPaidTransactions = $transactions->count();
        $totalRevenue = (float) $transactions->sum('transaction_grand_total');

        // Query Transaction Items (Products)
        $transactionItems = TransactionItem::whereIn('transaction_id', $transactionIds)
            ->where('delete_status', 0)
            ->get();

        // Query Order Bundles (Paket Combo / Bundle)
        $orderBundles = OrderBundle::whereIn('transaction_id', $transactionIds)
            ->where('delete_status', 0)
            ->get();

        // Eager load products with categories for mapping
        $products = Product::with('category')->where('delete_status', 0)->get()->keyBy('product_id');

        $salesMap = [];
        $totalItemsSold = 0;

        // Process Single Product Items
        foreach ($transactionItems as $ti) {
            $prodId = $ti->product_id;
            $name = $ti->product_name ?? 'Produk';
            $qty = (int) $ti->qty;
            $subtotal = (float) $ti->subtotal;
            $price = (float) $ti->price;

            $totalItemsSold += $qty;

            $prodObj = $prodId ? $products->get($prodId) : null;
            $catName = ($prodObj && $prodObj->category) ? $prodObj->category->category_name : 'Makanan & Minuman';

            $key = 'prod_' . ($prodId ?? \Illuminate\Support\Str::slug($name));
            if (!isset($salesMap[$key])) {
                $salesMap[$key] = [
                    'type' => 'product',
                    'name' => $name,
                    'category' => $catName,
                    'unit_price' => $price,
                    'qty_sold' => 0,
                    'total_omzet' => 0,
                ];
            }
            $salesMap[$key]['qty_sold'] += $qty;
            $salesMap[$key]['total_omzet'] += $subtotal;
        }

        // Process Order Bundles
        foreach ($orderBundles as $ob) {
            $name = $ob->bundle_name ?? 'Paket Hemat';
            $qty = (int) ($ob->quantity ?? 1);
            $subtotal = (float) $ob->subtotal;
            $price = (float) $ob->bundle_price;

            $totalItemsSold += $qty;

            $key = 'bundle_' . ($ob->bundle_id ?? \Illuminate\Support\Str::slug($name));
            if (!isset($salesMap[$key])) {
                $salesMap[$key] = [
                    'type' => 'bundle',
                    'name' => '[Paket] ' . $name,
                    'category' => 'Paket Bundle / Combo',
                    'unit_price' => $price,
                    'qty_sold' => 0,
                    'total_omzet' => 0,
                ];
            }
            $salesMap[$key]['qty_sold'] += $qty;
            $salesMap[$key]['total_omzet'] += $subtotal;
        }

        $allSales = collect(array_values($salesMap));
        $allRankings = $allSales->sortByDesc('qty_sold')->values();

        // Calculate omzet contribution % & performance badge for each item
        $rankedItems = $allRankings->map(function ($item, $index) use ($totalRevenue, $totalItemsSold) {
            $item['rank'] = $index + 1;
            $item['omzet_share'] = $totalRevenue > 0 ? ($item['total_omzet'] / $totalRevenue) * 100 : 0;
            $item['qty_share'] = $totalItemsSold > 0 ? ($item['qty_sold'] / $totalItemsSold) * 100 : 0;

            if ($index < 3 || $item['qty_share'] >= 10) {
                $item['badge_label'] = 'Top Seller 🔥';
                $item['badge_style'] = 'background: rgba(248, 113, 113, 0.15); color: var(--danger);';
            } elseif ($item['qty_share'] >= 3) {
                $item['badge_label'] = 'Stable Star ⭐';
                $item['badge_style'] = 'background: rgba(34, 211, 238, 0.15); color: var(--info);';
            } else {
                $item['badge_label'] = 'Slow Moving 🐢';
                $item['badge_style'] = 'background: var(--bg-elevated-2); color: var(--text-muted);';
            }

            return $item;
        });

        // Top 10 items for Bar Chart
        $top10Items = $rankedItems->take(10);
        $chartBarLabels = $top10Items->pluck('name')->toArray();
        $chartBarQtyData = $top10Items->pluck('qty_sold')->toArray();
        $chartBarOmzetData = $top10Items->pluck('total_omzet')->toArray();

        // Category Breakdown for Donut Chart
        $categoryMap = [];
        foreach ($rankedItems as $item) {
            $cat = $item['category'];
            if (!isset($categoryMap[$cat])) {
                $categoryMap[$cat] = 0;
            }
            $categoryMap[$cat] += $item['total_omzet'];
        }
        $chartCategoryLabels = array_keys($categoryMap);
        $chartCategoryData = array_values($categoryMap);

        // Daily Trend for Line Chart (Stop at today if viewing current month)
        $daysInMonth = \Carbon\Carbon::create($year, (int)$month)->daysInMonth;
        $maxDay = ($year == (int)date('Y') && (int)$month == (int)date('m')) ? min((int)date('j'), $daysInMonth) : $daysInMonth;
        $dailyTrendLabels = [];
        $dailyTrendOmzet = [];

        $monthName = \Carbon\Carbon::createFromDate($year, (int)$month, 1)->translatedFormat('M');
        for ($d = 1; $d <= $maxDay; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, (int)$month, $d);
            $dayTxs = $transactions->filter(function ($t) use ($dateStr) {
                return \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') === $dateStr;
            });
            $dailyTrendLabels[] = $d . ' ' . $monthName;
            $dailyTrendOmzet[] = (float) $dayTxs->sum('transaction_grand_total');
        }

        // Top Product #1 and Top Category #1
        $topProduct1 = $rankedItems->first();
        $topCategoryName = !empty($categoryMap) ? array_search(max($categoryMap), $categoryMap) : '-';

        return view('admin.kasir.keuangan.menu-analytics.index', compact(
            'year',
            'month',
            'totalRevenue',
            'totalItemsSold',
            'totalPaidTransactions',
            'topProduct1',
            'topCategoryName',
            'rankedItems',
            'chartBarLabels',
            'chartBarQtyData',
            'chartBarOmzetData',
            'chartCategoryLabels',
            'chartCategoryData',
            'dailyTrendLabels',
            'dailyTrendOmzet'
        ));
    }
}
