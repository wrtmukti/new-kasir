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
use App\Models\Admin\Order;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionItem;
use App\Models\Admin\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GeprekGambosSeeder extends Seeder
{
    /**
     * Run database seeds khusus client PT Geprek Gambos Indonesia.
     */
    public function run(string $clientId): void
    {
        // ==========================================
        // 1. OUTLETS (Semua format: Geprek Gambos - Kota)
        // ==========================================
        $outletsData = [
            [
                'outlet_name' => 'Geprek Gambos - Jakarta',
                'outlet_code' => 'GGB-JKT',
                'outlet_branch' => 'Jakarta Rawamangun',
                'outlet_slug' => 'geprek-gambos-jakarta',
                'outlet_email' => 'jakarta@geprekgambos.com',
                'outlet_phone' => '021-47861234',
                'outlet_address' => 'Jl. Balai Pustaka Timur No. 18, Rawamangun, Jakarta Timur',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Geprek Gambos - Bogor',
                'outlet_code' => 'GGB-BGR',
                'outlet_branch' => 'Bogor Pajajaran',
                'outlet_slug' => 'geprek-gambos-bogor',
                'outlet_email' => 'bogor@geprekgambos.com',
                'outlet_phone' => '0251-8321098',
                'outlet_address' => 'Jl. Pajajaran No. 88, Bogor Timur, Kota Bogor',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Geprek Gambos - Yogyakarta',
                'outlet_code' => 'GGB-YOG',
                'outlet_branch' => 'Yogyakarta Gejayan',
                'outlet_slug' => 'geprek-gambos-yogyakarta',
                'outlet_email' => 'jogja@geprekgambos.com',
                'outlet_phone' => '0274-554321',
                'outlet_address' => 'Jl. Gejayan No. 22, Caturtunggal, Depok, Sleman',
                'outlet_status' => 1,
            ],
            [
                'outlet_name' => 'Geprek Gambos - Surabaya',
                'outlet_code' => 'GGB-SBY',
                'outlet_branch' => 'Surabaya Gubeng',
                'outlet_slug' => 'geprek-gambos-surabaya',
                'outlet_email' => 'surabaya@geprekgambos.com',
                'outlet_phone' => '031-5034567',
                'outlet_address' => 'Jl. Raya Gubeng No. 64, Surabaya Pusat',
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
                'theme' => 'spicy_bites',
                'created_by' => 'GeprekGambosSeeder',
            ]);

            // Pajak & Service Charge
            Tax::create([
                'outlet_id' => $outlet->outlet_id,
                'tax_name' => 'PB1 Restoran (10%)',
                'rate_percent' => 10.00,
                'type' => 'exclusive',
                'is_active' => 1,
                'created_by' => 'GeprekGambosSeeder',
            ]);

            ServiceCharge::create([
                'outlet_id' => $outlet->outlet_id,
                'service_name' => 'Service Charge Resto (5%)',
                'rate_percent' => 5.00,
                'is_taxable' => 1,
                'is_active' => 1,
                'created_by' => 'GeprekGambosSeeder',
            ]);

            // Shift Setting
            ShiftSetting::create([
                'outlet_id' => $outlet->outlet_id,
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);
        }

        $jkt = $outlets['geprek-gambos-jakarta'];
        $bgr = $outlets['geprek-gambos-bogor'];
        $yog = $outlets['geprek-gambos-yogyakarta'];
        $sby = $outlets['geprek-gambos-surabaya'];

        // ==========================================
        // 2. USERS (Lengkap client_id & outlet_id)
        // ==========================================
        $users = [
            [
                'client_id' => $clientId,
                'outlet_id' => $jkt->outlet_id,
                'name' => 'Budi Santoso (Owner)',
                'email' => 'owner@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $jkt->outlet_id,
                'name' => 'Ahmad Fauzi (Kasir Jakarta)',
                'email' => 'kasir@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $bgr->outlet_id,
                'name' => 'Hendra Wijaya (Kasir Bogor)',
                'email' => 'kasir.bogor@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $yog->outlet_id,
                'name' => 'Agus Setiawan (Kasir Jogja)',
                'email' => 'kasir.jogja@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $sby->outlet_id,
                'name' => 'Bayu Saputra (Kasir Surabaya)',
                'email' => 'kasir.surabaya@geprekgambos.com',
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
        // Jakarta: 12 Meja
        for ($i = 1; $i <= 12; $i++) {
            Table::create([
                'outlet_id' => $jkt->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 3 == 0) ? 6 : (($i % 2 == 0) ? 4 : 2),
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Rawamangun Jakarta",
            ]);
        }
        // Bogor: 10 Meja
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'outlet_id' => $bgr->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 2 == 0) ? 4 : 2,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Pajajaran Bogor",
            ]);
        }
        // Yogyakarta: 8 Meja
        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'outlet_id' => $yog->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Gejayan Jogja",
            ]);
        }
        // Surabaya: 8 Meja
        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'outlet_id' => $sby->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Gubeng Surabaya",
            ]);
        }

        // ==========================================
        // 4. KATEGORI & PRODUK GEPREK
        // ==========================================
        $catPaket = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Paket Geprek Komplit', 'category_slug' => 'paket-geprek-komplit', 'category_status' => 1]);
        $catAyam = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Ayam & Bebek Goreng', 'category_slug' => 'ayam-bebek-goreng', 'category_status' => 1]);
        $catSambal = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Aneka Sambal Spesial', 'category_slug' => 'aneka-sambal-spesial', 'category_status' => 1]);
        $catMinum = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Minuman Segar', 'category_slug' => 'minuman-segar', 'category_status' => 1]);
        $catEkstra = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Ekstra & Camilan', 'category_slug' => 'ekstra-camilan', 'category_status' => 1]);

        $productsData = [
            // Paket
            ['category_id' => $catPaket->category_id, 'name' => 'Paket Geprek Gambos Original', 'code' => 'GGB-ORI', 'price' => 22000, 'cost' => 10000],
            ['category_id' => $catPaket->category_id, 'name' => 'Paket Geprek Mozarella Leleh', 'code' => 'GGB-MOZ', 'price' => 28000, 'cost' => 13500],
            ['category_id' => $catPaket->category_id, 'name' => 'Paket Geprek Sambal Matah Bali', 'code' => 'GGB-MTH', 'price' => 25000, 'cost' => 11000],
            ['category_id' => $catPaket->category_id, 'name' => 'Paket Ayam Bakar Madu Pedas Manis', 'code' => 'GGB-BKR', 'price' => 26000, 'cost' => 12000],
            // Ayam & Bebek
            ['category_id' => $catAyam->category_id, 'name' => 'Ayam Goreng Crispy Gambos (A la Carte)', 'code' => 'GGB-AYM', 'price' => 16000, 'cost' => 7500],
            ['category_id' => $catAyam->category_id, 'name' => 'Bebek Goreng Sambal Korek', 'code' => 'GGB-BBK', 'price' => 32000, 'cost' => 16000],
            // Ekstra
            ['category_id' => $catEkstra->category_id, 'name' => 'Kulit Ayam Crispy Gurih', 'code' => 'GGB-KLT', 'price' => 14000, 'cost' => 5000],
            ['category_id' => $catEkstra->category_id, 'name' => 'Tahu Tempe Goreng Bumbu Kuning', 'code' => 'GGB-TTG', 'price' => 8000, 'cost' => 2500],
            ['category_id' => $catEkstra->category_id, 'name' => 'Nasi Putih Pandan Wangi', 'code' => 'GGB-NAS', 'price' => 6000, 'cost' => 2000],
            ['category_id' => $catEkstra->category_id, 'name' => 'Terong Crispy Sambal Bawang', 'code' => 'GGB-TRG', 'price' => 10000, 'cost' => 3500],
            // Minuman
            ['category_id' => $catMinum->category_id, 'name' => 'Es Teh Manis Jumbo', 'code' => 'GGB-TEH', 'price' => 5000, 'cost' => 1000],
            ['category_id' => $catMinum->category_id, 'name' => 'Es Jeruk Peras Murni', 'code' => 'GGB-JRK', 'price' => 9000, 'cost' => 3000],
            ['category_id' => $catMinum->category_id, 'name' => 'Es Timun Serut Selasih', 'code' => 'GGB-TMN', 'price' => 12000, 'cost' => 4000],
            ['category_id' => $catMinum->category_id, 'name' => 'Air Mineral Botol 600ml', 'code' => 'GGB-MIN', 'price' => 5000, 'cost' => 2200],
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
            ['name' => 'Ayam Broiler Segar (Potong 8)', 'unit' => 'kg', 'price' => 36000, 'qty' => 250, 'min' => 30],
            ['name' => 'Beras Pandan Wangi Premium', 'unit' => 'kg', 'price' => 14500, 'qty' => 500, 'min' => 50],
            ['name' => 'Cabai Rawit Merah Domba', 'unit' => 'kg', 'price' => 65000, 'qty' => 60, 'min' => 10],
            ['name' => 'Minyak Goreng Sawit Kemasan', 'unit' => 'liter', 'price' => 16500, 'qty' => 200, 'min' => 25],
            ['name' => 'Bawang Putih Kating', 'unit' => 'kg', 'price' => 38000, 'qty' => 40, 'min' => 5],
            ['name' => 'Tepung Bumbu Krispi Spesial', 'unit' => 'kg', 'price' => 18000, 'qty' => 150, 'min' => 20],
        ];

        foreach ([$jkt, $bgr, $yog, $sby] as $ot) {
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
            Customer::create(['customer_name' => 'Raffi Ahmad', 'customer_phone' => '081122334455', 'customer_email' => 'raffi@gmail.com']),
            Customer::create(['customer_name' => 'Nagita Slavina', 'customer_phone' => '081122334466', 'customer_email' => 'gigi@gmail.com']),
            Customer::create(['customer_name' => 'Atta Halilintar', 'customer_phone' => '081122334477', 'customer_email' => 'atta@gmail.com']),
            Customer::create(['customer_name' => 'Aurel Hermansyah', 'customer_phone' => '081122334488', 'customer_email' => 'aurel@gmail.com']),
        ];

        Voucher::create([
            'outlet_id' => $jkt->outlet_id,
            'voucher_name' => 'Voucher Geprek Mantap 20%',
            'voucher_code' => 'GEPREK20',
            'voucher_type' => 'percentage',
            'voucher_value' => 20,
            'voucher_max_discount' => 30000,
            'voucher_min_purchase' => 60000,
            'voucher_status' => 1,
            'voucher_start_date' => now()->subDays(10),
            'voucher_end_date' => now()->addMonths(3),
        ]);

        // ==========================================
        // 7. ORDER & TRANSAKSI RESTO (DISTINKSI TINGGI)
        // ==========================================
        // Buat 30 Jakarta, 20 Bogor, 12 Yogya, 15 Surabaya
        $branchOrders = [
            ['outlet' => $jkt, 'count' => 30, 'prefix' => 'GGB-JKT'],
            ['outlet' => $bgr, 'count' => 20, 'prefix' => 'GGB-BGR'],
            ['outlet' => $yog, 'count' => 12, 'prefix' => 'GGB-YOG'],
            ['outlet' => $sby, 'count' => 15, 'prefix' => 'GGB-SBY'],
        ];

        foreach ($branchOrders as $b) {
            $currentOutlet = $b['outlet'];
            $tableList = Table::where('outlet_id', $currentOutlet->outlet_id)->get();

            for ($i = 1; $i <= $b['count']; $i++) {
                $orderId = 'ORD-' . $b['prefix'] . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $invoiceNo = 'INV-' . $b['prefix'] . '-' . date('ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $randCust = $customers[array_rand($customers)];
                $randTable = $tableList->isNotEmpty() ? $tableList->random() : null;

                // Pick 2-4 distinct products
                $pickedProds = collect($products)->random(rand(2, 4));
                $itemsTotal = 0;
                $lineItems = [];

                foreach ($pickedProds as $prod) {
                    $qty = rand(1, 4);
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
                    'order_grand_total' => $grandTotal,
                    'tax_percent' => 10.00,
                    'tax_amount' => $taxAmount,
                    'service_charge_percent' => 5.00,
                    'service_charge_amount' => $serviceAmount,
                    'order_remark' => 'Dine-In Geprek Gambos Meja ' . ($randTable?->table_number ?? 1),
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
                    'transaction_remark' => 'Penjualan Geprek Gambos ' . $currentOutlet->outlet_branch,
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
