<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    // ============ MAIN HISTORY DASHBOARD ============

    public function index()
    {
        $totalStock = DB::table('stock_histories')->where('delete_status', 0)->count();
        $totalProduct = DB::table('product_histories')->where('delete_status', 0)->count();
        $totalDiscount = DB::table('discount_histories')->where('delete_status', 0)->count();
        $totalVoucher = DB::table('voucher_histories')->where('delete_status', 0)->count();
        $totalBundle = DB::table('bundle_histories')->where('delete_status', 0)->count();
        $totalAll = $totalStock + $totalProduct + $totalDiscount + $totalVoucher + $totalBundle;

        return view('admin.kasir.history.index', compact(
            'totalStock',
            'totalProduct',
            'totalDiscount',
            'totalVoucher',
            'totalBundle',
            'totalAll'
        ));
    }

    // ============ STOCK HISTORY ============

    public function stockShow($id)
    {
        $history = DB::table('stock_histories')->where('stock_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.stock.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        // Cari record sebelumnya (buat perbandingan)
        $previous = DB::table('stock_histories')
            ->where('stock_id', $history->stock_id)
            ->where('stock_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.kasir.history.stock.show', compact('history', 'previous'));
    }

    public function stockIndex()
    {
        $histories = DB::table('stock_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.history.stock.index', compact('histories'));
    }

    public function stockData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('stock_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action_type', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.history.stock._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.kasir.history.stock.index', compact('histories'));
    }

    // ============ PRODUCT HISTORY ============

    public function productShow($id)
    {
        $history = DB::table('product_histories')->where('product_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.product.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        // Cari record sebelumnya (buat perbandingan)
        $previous = DB::table('product_histories')
            ->where('product_id', $history->product_id)
            ->where('product_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.kasir.history.product.show', compact('history', 'previous'));
    }

    public function productIndex()
    {
        $histories = DB::table('product_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.history.product.index', compact('histories'));
    }

    public function productData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('product_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action_type', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.history.product._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.kasir.history.product.index', compact('histories'));
    }

    // ============ VOUCHER HISTORY ============

    public function voucherShow($id)
    {
        $history = DB::table('voucher_histories')->where('history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.voucher.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        $previous = DB::table('voucher_histories')
            ->where('voucher_id', $history->voucher_id)
            ->where('history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.kasir.history.voucher.show', compact('history', 'previous'));
    }

    public function voucherIndex()
    {
        $histories = DB::table('voucher_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.history.voucher.index', compact('histories'));
    }

    public function voucherData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('voucher_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.history.voucher._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.kasir.history.voucher.index', compact('histories'));
    }

    // ============ DISCOUNT HISTORY ============

    public function discountShow($id)
    {
        $history = DB::table('discount_histories')->where('discount_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.discount.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        $previous = DB::table('discount_histories')
            ->where('discount_id', $history->discount_id)
            ->where('discount_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.kasir.history.discount.show', compact('history', 'previous'));
    }

    public function discountIndex()
    {
        $histories = DB::table('discount_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.history.discount.index', compact('histories'));
    }

    public function discountData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('discount_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('reason', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.history.discount._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.kasir.history.discount.index', compact('histories'));
    }

    // ============ BUNDLE HISTORY ============

    public function bundleShow($id)
    {
        $history = DB::table('bundle_histories')->where('bundle_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.bundle.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        $previous = DB::table('bundle_histories')
            ->where('bundle_id', $history->bundle_id)
            ->where('bundle_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.kasir.history.bundle.show', compact('history', 'previous'));
    }

    public function bundleIndex()
    {
        $histories = DB::table('bundle_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.history.bundle.index', compact('histories'));
    }

    public function bundleData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('bundle_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action_type', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.history.bundle._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.kasir.history.bundle.index', compact('histories'));
    }
}
