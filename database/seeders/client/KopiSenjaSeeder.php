<?php

namespace Database\Seeders\Client;

use Illuminate\Database\Seeder;
use App\Models\Admin\Outlet;
use App\Models\Admin\SettingOutlet;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\Stock;
use App\Models\Admin\Table;
use App\Models\Admin\Customer;
use App\Models\Admin\Voucher;
use App\Models\Admin\Discount;
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KopiSenjaSeeder extends Seeder
{
    /**
     * Run database seeds khusus client PT Kopi Senja Indonesia.
     */
    public function run(string $clientId): void
    {
        // ==========================================
        // 1. OUTLETS (Semua format: Kopi Senja - Kota)
        // ==========================================
        $outletsData = [
            [
                'outlet_name' => 'Kopi Senja - Jakarta',
                'outlet_code' => 'KS-JKT',
                'outlet_branch' => 'Jakarta Selatan',
                'outlet_slug' => 'kopi-senja-jakarta',
                'outlet_email' => 'jakarta@kopisenja.com',
                'outlet_phone' => '021-78901234',
                'outlet_address' => 'Jl. Tebet Raya No. 45, Jakarta Selatan',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Kopi Senja - Bandung',
                'outlet_code' => 'KS-BDG',
                'outlet_branch' => 'Bandung Dago',
                'outlet_slug' => 'kopi-senja-bandung',
                'outlet_email' => 'bandung@kopisenja.com',
                'outlet_phone' => '022-2501234',
                'outlet_address' => 'Jl. Ir. H. Juanda (Dago) No. 102, Bandung',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Kopi Senja - Yogyakarta',
                'outlet_code' => 'KS-YOG',
                'outlet_branch' => 'Yogyakarta Malioboro',
                'outlet_slug' => 'kopi-senja-yogyakarta',
                'outlet_email' => 'jogja@kopisenja.com',
                'outlet_phone' => '0274-561234',
                'outlet_address' => 'Jl. Malioboro No. 55, Sosromenduran, Yogyakarta',
                'outlet_status' => 1,
            ],
        ];

        $outlets = [];
        foreach ($outletsData as $data) {
            $outlet = Outlet::create($data);
            $outlets[$data['outlet_slug']] = $outlet;

            // Setting Outlet & Struk
            SettingOutlet::create([
                'outlet_id' => $outlet->outlet_id,
                'outlet_name' => $outlet->outlet_name,
                'payment_timing' => 'post_payment',
                'theme' => 'metropolis_brew',
                'created_by' => 'KopiSenjaSeeder',
            ]);

            // Pajak & Service Charge
            Tax::create([
                'outlet_id' => $outlet->outlet_id,
                'tax_name' => 'PB1 Restoran (10%)',
                'rate_percent' => 10.00,
                'type' => 'exclusive',
                'is_active' => 1,
                'created_by' => 'KopiSenjaSeeder',
            ]);

            ServiceCharge::create([
                'outlet_id' => $outlet->outlet_id,
                'service_name' => 'Service Charge Cafe (5%)',
                'rate_percent' => 5.00,
                'is_taxable' => 1,
                'is_active' => 1,
                'created_by' => 'KopiSenjaSeeder',
            ]);

            // Shift Setting
            ShiftSetting::create([
                'outlet_id' => $outlet->outlet_id,
                'daily_cutoff_time' => '02:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);
        }

        $jkt = $outlets['kopi-senja-jakarta'];
        $bdg = $outlets['kopi-senja-bandung'];
        $yog = $outlets['kopi-senja-yogyakarta'];

        // ==========================================
        // 2. USERS (Lengkap client_id & outlet_id)
        // ==========================================
        $users = [
            [
                'client_id' => $clientId,
                'outlet_id' => $jkt->outlet_id,
                'name' => 'Aditya Pratama (Owner)',
                'email' => 'owner@kopisenja.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $jkt->outlet_id,
                'name' => 'Siti Rahma (Kasir Jakarta)',
                'email' => 'kasir@kopisenja.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $bdg->outlet_id,
                'name' => 'Rian Ardiansyah (Kasir Bandung)',
                'email' => 'kasir.bandung@kopisenja.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $yog->outlet_id,
                'name' => 'Dimas Pratama (Kasir Jogja)',
                'email' => 'kasir.jogja@kopisenja.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('client')->table('users')->insert($users);

        // ==========================================
        // 3. MEJA PER CABANG
        // ==========================================
        // Jakarta: 10 Meja
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'outlet_id' => $jkt->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 3 == 0) ? 6 : (($i % 2 == 0) ? 4 : 2),
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Indoor Jakarta",
            ]);
        }
        // Bandung: 8 Meja
        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'outlet_id' => $bdg->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 2 == 0) ? 4 : 2,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Outdoor Dago",
            ]);
        }
        // Yogya: 6 Meja
        for ($i = 1; $i <= 6; $i++) {
            Table::create([
                'outlet_id' => $yog->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Malioboro",
            ]);
        }

        // ==========================================
        // 4. KATEGORI & PRODUK CAFE
        // ==========================================
        $catCoffee = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Coffee & Espresso', 'category_slug' => 'coffee-espresso', 'category_status' => 1]);
        $catNonCoffee = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Non-Coffee & Tea', 'category_slug' => 'non-coffee-tea', 'category_status' => 1]);
        $catPastry = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Pastry & Bakery', 'category_slug' => 'pastry-bakery', 'category_status' => 1]);
        $catMeals = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Main Course Senja', 'category_slug' => 'main-course-senja', 'category_status' => 1]);

        $productsData = [
            // Coffee
            ['category_id' => $catCoffee->category_id, 'name' => 'Kopi Senja Signature', 'code' => 'KS-SIG', 'price' => 24000, 'cost' => 8500],
            ['category_id' => $catCoffee->category_id, 'name' => 'Caramel Macchiato', 'code' => 'KS-CRM', 'price' => 28000, 'cost' => 10000],
            ['category_id' => $catCoffee->category_id, 'name' => 'Cafe Latte Single Origin', 'code' => 'KS-LAT', 'price' => 26000, 'cost' => 9000],
            ['category_id' => $catCoffee->category_id, 'name' => 'Americano Iced', 'code' => 'KS-AME', 'price' => 20000, 'cost' => 5000],
            // Non-Coffee
            ['category_id' => $catNonCoffee->category_id, 'name' => 'Matcha Kyoto Latte', 'code' => 'KS-MTC', 'price' => 27000, 'cost' => 11000],
            ['category_id' => $catNonCoffee->category_id, 'name' => 'Earl Grey Milk Tea', 'code' => 'KS-EGY', 'price' => 25000, 'cost' => 8000],
            ['category_id' => $catNonCoffee->category_id, 'name' => 'Lychee Berry Sparkler', 'code' => 'KS-LYC', 'price' => 23000, 'cost' => 7000],
            // Pastry
            ['category_id' => $catPastry->category_id, 'name' => 'Butter Croissant Premium', 'code' => 'KS-CRS', 'price' => 22000, 'cost' => 9500],
            ['category_id' => $catPastry->category_id, 'name' => 'Cinnamon Roll Glaze', 'code' => 'KS-CIN', 'price' => 24000, 'cost' => 10000],
            ['category_id' => $catPastry->category_id, 'name' => 'Almond Pain Au Chocolat', 'code' => 'KS-CHO', 'price' => 26000, 'cost' => 11500],
            // Meals
            ['category_id' => $catMeals->category_id, 'name' => 'Nasi Goreng Senja Spesial', 'code' => 'KS-NAS', 'price' => 35000, 'cost' => 14000],
            ['category_id' => $catMeals->category_id, 'name' => 'Spaghetti Aglio Olio Smoked Beef', 'code' => 'KS-SPG', 'price' => 38000, 'cost' => 15500],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $prod = Product::create([
                'outlet_id' => $jkt->outlet_id,
                'category_id' => $p['category_id'],
                'product_name' => $p['name'],
                'product_code' => $p['code'],
                'product_slug' => Str::slug($p['name']),
                'product_price' => $p['price'],
                'product_status' => 1,
            ]);
            $products[] = $prod;
        }

        // ==========================================
        // 5. STOK BAHAN BAKU PER CABANG
        // ==========================================
        $stocksData = [
            ['name' => 'Biji Kopi Arabica Gayo', 'unit' => 'gram', 'price' => 250, 'qty' => 15000, 'min' => 2000],
            ['name' => 'Susu Fresh Milk Pasteurisasi', 'unit' => 'ml', 'price' => 20, 'qty' => 50000, 'min' => 5000],
            ['name' => 'Sirup Karamel Gourmet', 'unit' => 'ml', 'price' => 80, 'qty' => 10000, 'min' => 1000],
            ['name' => 'Matcha Powder Grade A', 'unit' => 'gram', 'price' => 450, 'qty' => 5000, 'min' => 500],
            ['name' => 'Cup Plastik Takeaway 16oz', 'unit' => 'pcs', 'price' => 800, 'qty' => 1000, 'min' => 100],
            ['name' => 'Croissant Dough Frozen', 'unit' => 'pcs', 'price' => 7500, 'qty' => 200, 'min' => 30],
        ];

        foreach ([$jkt, $bdg, $yog] as $ot) {
            foreach ($stocksData as $idx => $stk) {
                Stock::create([
                    'outlet_id' => $ot->outlet_id,
                    'stock_name' => $stk['name'],
                    'stock_slug' => Str::slug($stk['name']),
                    'stock_code' => 'STK-' . $ot->outlet_code . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'stock_unit' => $stk['unit'],
                    'stock_price' => $stk['price'],
                    'stock_amount' => $stk['qty'],
                    'stock_status' => 1,
                ]);
            }
        }

        // ==========================================
        // 6. CUSTOMERS & VOUCHERS
        // ==========================================
        $customers = [
            Customer::create(['customer_name' => 'Reza Rahadian', 'customer_phone' => '081299887711', 'customer_email' => 'reza@gmail.com']),
            Customer::create(['customer_name' => 'Dian Sastro', 'customer_phone' => '081299887722', 'customer_email' => 'dian@gmail.com']),
            Customer::create(['customer_name' => 'Nicholas Saputra', 'customer_phone' => '081299887733', 'customer_email' => 'nico@gmail.com']),
            Customer::create(['customer_name' => 'Chelsea Islan', 'customer_phone' => '081299887744', 'customer_email' => 'chelsea@gmail.com']),
        ];

        Voucher::create([
            'outlet_id' => $jkt->outlet_id,
            'voucher_name' => 'Voucher Ngopi Senja Hemat 15%',
            'voucher_code' => 'SENJA15',
            'voucher_type' => 'percentage',
            'voucher_value' => 15,
            'voucher_max_discount' => 25000,
            'voucher_min_purchase' => 50000,
            'voucher_status' => 1,
            'voucher_start_date' => now()->subDays(10),
            'voucher_end_date' => now()->addMonths(3),
        ]);

        // ==========================================
        // 7. ORDER & TRANSAKSI CAFE (DISTINKSI TINGGI)
        // ==========================================
        // Buat 25 Transaksi Jakarta, 15 Bandung, 10 Yogyakarta
        $branchOrders = [
            ['outlet' => $jkt, 'count' => 25, 'prefix' => 'KS-JKT'],
            ['outlet' => $bdg, 'count' => 15, 'prefix' => 'KS-BDG'],
            ['outlet' => $yog, 'count' => 10, 'prefix' => 'KS-YOG'],
        ];

        $orderGlobalIdx = 100;
        foreach ($branchOrders as $b) {
            $currentOutlet = $b['outlet'];
            $tableList = Table::where('outlet_id', $currentOutlet->outlet_id)->get();

            for ($i = 1; $i <= $b['count']; $i++) {
                $orderGlobalIdx++;
                $orderId = 'ORD-' . $b['prefix'] . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $invoiceNo = 'INV-' . $b['prefix'] . '-' . date('ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $randCust = $customers[array_rand($customers)];
                $randTable = $tableList->isNotEmpty() ? $tableList->random() : null;

                // Pick 2-4 distinct products
                $pickedProds = collect($products)->random(rand(2, 4));
                $itemsTotal = 0;
                $lineItems = [];

                foreach ($pickedProds as $prod) {
                    $qty = rand(1, 3);
                    $sub = $prod->product_price * $qty;
                    $itemsTotal += $sub;
                    $lineItems[] = [
                        'product' => $prod,
                        'qty' => $qty,
                        'price' => $prod->product_price,
                        'subtotal' => $sub,
                    ];
                }

                $taxAmount = round($itemsTotal * 0.10, 2);
                $serviceAmount = round($itemsTotal * 0.05, 2);
                $grandTotal = $itemsTotal + $taxAmount + $serviceAmount;

                // Create Order
                $order = Order::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'order_table_id' => $randTable?->table_id,
                    'order_customer_id' => $randCust->customer_id,
                    'order_type' => 'dine_in',
                    'order_status' => 'completed',
                    'payment_status' => 'paid',
                    'order_grand_total' => $grandTotal,
                    'tax_percent' => 10.00,
                    'tax_amount' => $taxAmount,
                    'service_charge_percent' => 5.00,
                    'service_charge_amount' => $serviceAmount,
                    'order_remark' => 'Dine-In Cafe Senja Meja ' . ($randTable?->table_number ?? 1),
                    'created_at' => now()->subDays(rand(0, 14))->subHours(rand(1, 10)),
                ]);

                // Attach to order_product pivot
                foreach ($lineItems as $item) {
                    $order->products()->attach($item['product']->product_id, [
                        'quantity' => $item['qty'],
                        'note' => '',
                    ]);
                }

                // Create Transaction
                $tx = Transaction::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'transaction_code' => $invoiceNo,
                    'transaction_date' => $order->created_at->toDateString(),
                    'transaction_subtotal' => $itemsTotal,
                    'transaction_tax' => $taxAmount,
                    'transaction_service_charge' => $serviceAmount,
                    'transaction_grand_total' => $grandTotal,
                    'transaction_status' => 'success',
                    'transaction_table_id' => $randTable?->table_id,
                    'transaction_customer_id' => $randCust->customer_id,
                    'transaction_remark' => 'Penjualan Kopi Senja ' . $currentOutlet->outlet_branch,
                    'created_at' => $order->created_at,
                ]);

                $order->update(['order_transaction_id' => $tx->transaction_id]);

                // Create Line Items
                foreach ($lineItems as $item) {
                    TransactionItem::create([
                        'outlet_id' => $currentOutlet->outlet_id,
                        'transaction_id' => $tx->transaction_id,
                        'product_id' => $item['product']->product_id,
                        'product_name' => $item['product']->product_name,
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // Create Payment
                $payMethods = ['qris', 'cash', 'transfer'];
                $payMethod = $payMethods[array_rand($payMethods)];
                Payment::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'transaction_id' => $tx->transaction_id,
                    'payment_metode' => $payMethod,
                    'payment_amount' => $grandTotal,
                    'payment_grand_total' => $grandTotal,
                    'payment_status' => 'paid',
                    'payment_date' => $order->created_at->toDateString(),
                    'created_at' => $order->created_at,
                ]);
            }
        }
    }
}
