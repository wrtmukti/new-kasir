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
use App\Models\Admin\DailyClosing;
use App\Models\Admin\CashDrawerLog;
use App\Models\Admin\Supplier;
use App\Models\Admin\PurchaseOrder;
use App\Models\Admin\PurchaseOrderItem;
use App\Models\Admin\PurchaseReceiving;
use App\Models\Admin\PurchaseReceivingItem;
use App\Models\Admin\Keuangan\RawStockMaterial;
use App\Models\Admin\Keuangan\RawStockMaterialHistory;
use App\Models\Admin\Keuangan\CogsRecipe;
use App\Models\Admin\Keuangan\CogsRecipeItem;
use App\Models\Admin\Keuangan\CogsRecipeHistory;
use App\Models\Admin\Keuangan\HppFinancialReport;
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
        $cashierUser = DB::connection('client')->table('users')->where('role', 'kasir')->first();

        // ==========================================
        // 3. MEJA PER CABANG
        // ==========================================
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'outlet_id' => $jkt->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 3 == 0) ? 6 : (($i % 2 == 0) ? 4 : 2),
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Indoor Jakarta",
            ]);
        }
        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'outlet_id' => $bdg->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 2 == 0) ? 4 : 2,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Area Outdoor Dago",
            ]);
        }
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
        $catNonCoffee = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Non-Coffee & Mocktails', 'category_slug' => 'non-coffee-mocktails', 'category_status' => 1]);
        $catFood = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Pastry & Toast', 'category_slug' => 'pastry-toast', 'category_status' => 1]);

        $productsData = [
            ['name' => 'Kopi Susu Senja (Aren)', 'cat' => $catCoffee, 'price' => 22000, 'cost' => 8500, 'sku' => 'KS-001', 'desc' => 'Espresso house blend dengan gula aren asli dan creamy fresh milk'],
            ['name' => 'Espresso Single Origin Gayo', 'cat' => $catCoffee, 'price' => 18000, 'cost' => 5000, 'sku' => 'KS-002', 'desc' => 'Ekstraksi murni 30ml biji kopi Arabica Gayo Aceh medium roast'],
            ['name' => 'Americano Iced', 'cat' => $catCoffee, 'price' => 20000, 'cost' => 5500, 'sku' => 'KS-003', 'desc' => 'Double shot espresso dengan air dingin dan es kristal menyegarkan'],
            ['name' => 'Caramel Macchiato', 'cat' => $catCoffee, 'price' => 28000, 'cost' => 11000, 'sku' => 'KS-004', 'desc' => 'Steamed milk dengan vanilla syrup, espresso shot, dan saus karamel legit'],
            ['name' => 'Caffe Latte Art', 'cat' => $catCoffee, 'price' => 26000, 'cost' => 9500, 'sku' => 'KS-005', 'desc' => 'Espresso lembut berpadu dengan microfoam susu segar dan latte art'],
            ['name' => 'Matcha Latte Uji', 'cat' => $catNonCoffee, 'price' => 27000, 'cost' => 10000, 'sku' => 'KS-006', 'desc' => 'Bubuk matcha murni asal Uji Jepang berpadu dengan susu creamy'],
            ['name' => 'Earl Grey Milk Tea', 'cat' => $catNonCoffee, 'price' => 24000, 'cost' => 8000, 'sku' => 'KS-007', 'desc' => 'Teh hitam aromatik bergamot dengan paduan susu dan brown sugar'],
            ['name' => 'Butter Croissant Premium', 'cat' => $catFood, 'price' => 25000, 'cost' => 12000, 'sku' => 'KS-008', 'desc' => 'Pastry Prancis renyah berlapis dengan French butter berkualitas'],
            ['name' => 'Almond Pain Au Chocolat', 'cat' => $catFood, 'price' => 29000, 'cost' => 13500, 'sku' => 'KS-009', 'desc' => 'Pastry cokelat lumer dengan topping irisan kacang almond panggang'],
            ['name' => 'Kaya Toast Butter Senja', 'cat' => $catFood, 'price' => 20000, 'cost' => 7500, 'sku' => 'KS-010', 'desc' => 'Roti panggang garing dengan selai srikaya pandan dan potongan butter dingin'],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $prod = Product::create([
                'outlet_id' => $jkt->outlet_id,
                'category_id' => $p['cat']->category_id,
                'product_name' => $p['name'],
                'product_slug' => Str::slug($p['name']),
                'product_code' => $p['sku'],
                'product_price' => $p['price'],
                'product_description' => $p['desc'],
                'product_status' => 1,
            ]);
            $products[] = $prod;

            foreach ([$jkt, $bdg, $yog] as $ot) {
                Stock::create([
                    'outlet_id' => $ot->outlet_id,
                    'stock_code' => 'STK-' . $p['sku'],
                    'stock_name' => $prod->product_name,
                    'stock_slug' => Str::slug($prod->product_name . '-' . $ot->outlet_branch),
                    'stock_unit' => 'pcs',
                    'stock_amount' => rand(50, 200),
                    'stock_price' => $prod->product_price,
                    'stock_status' => 1,
                ]);
            }
        }

        // ==========================================
        // 5. MASTER SUPPLIER
        // ==========================================
        $supGayo = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-COFFEE-01',
            'supplier_name' => 'CV Gayo Highland Coffee',
            'supplier_contact' => 'Bpk. Faisal Gayo',
            'supplier_phone' => '081299887766',
            'supplier_address' => 'Takengon, Aceh Tengah',
        ]);
        $supDairy = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-DAIRY-01',
            'supplier_name' => 'PT Diamond Fresh Milk & Dairy',
            'supplier_contact' => 'Ibu Linda',
            'supplier_phone' => '021-88997711',
            'supplier_address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
        ]);
        $supPack = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-PACK-01',
            'supplier_name' => 'Mitra Packaging Solusindo',
            'supplier_contact' => 'Hendra Setiawan',
            'supplier_phone' => '081377889900',
            'supplier_address' => 'Jl. Daan Mogot KM 12, Jakarta Barat',
        ]);

        // ==========================================
        // 6. RAW STOCK MATERIALS & HISTORIES (Plan B)
        // ==========================================
        $rawMaterialsData = [
            [
                'raw_material_code' => 'RAW-COFFEE-GAYO',
                'name' => 'Biji Kopi Arabica Gayo (Roasted Beans)',
                'unit' => 'gram',
                'amount' => 15000.0, // 15 kg
                'min_amount' => 2000.0,
                'price_per_unit' => 280, // Rp 280 / gram (Rp 280.000 / kg)
                'loss_percent' => 5.0,
            ],
            [
                'raw_material_code' => 'RAW-FRESH-MILK',
                'name' => 'Fresh Milk UHT Full Cream',
                'unit' => 'ml',
                'amount' => 50000.0, // 50 Liter
                'min_amount' => 10000.0,
                'price_per_unit' => 22, // Rp 22 / ml (Rp 22.000 / Liter)
                'loss_percent' => 2.0,
            ],
            [
                'raw_material_code' => 'RAW-GULA-AREN',
                'name' => 'Gula Aren Organik Cair',
                'unit' => 'ml',
                'amount' => 20000.0, // 20 Liter
                'min_amount' => 3000.0,
                'price_per_unit' => 35, // Rp 35 / ml (Rp 35.000 / Liter)
                'loss_percent' => 1.0,
            ],
            [
                'raw_material_code' => 'RAW-CARAMEL-SYRUP',
                'name' => 'Sirup Karamel Premium Monin',
                'unit' => 'ml',
                'amount' => 10000.0, // 10 Liter
                'min_amount' => 1500.0,
                'price_per_unit' => 150, // Rp 150 / ml
                'loss_percent' => 1.0,
            ],
            [
                'raw_material_code' => 'RAW-MATCHA-POWDER',
                'name' => 'Matcha Powder Grade Uji',
                'unit' => 'gram',
                'amount' => 5000.0, // 5 kg
                'min_amount' => 500.0,
                'price_per_unit' => 450, // Rp 450 / gram
                'loss_percent' => 2.0,
            ],
            [
                'raw_material_code' => 'RAW-CUP-PAPER',
                'name' => 'Cup Gelas Kertas Senja 12oz + Tutup',
                'unit' => 'pcs',
                'amount' => 1200.0,
                'min_amount' => 200.0,
                'price_per_unit' => 850,
                'loss_percent' => 0.0,
            ],
        ];

        $rawStockMap = [];
        foreach ($rawMaterialsData as $rm) {
            $raw = RawStockMaterial::create([
                'outlet_id' => $jkt->outlet_id,
                'raw_material_code' => $rm['raw_material_code'],
                'name' => $rm['name'],
                'slug' => Str::slug($rm['name']),
                'unit' => $rm['unit'],
                'amount' => $rm['amount'],
                'min_amount' => $rm['min_amount'],
                'price_per_unit' => $rm['price_per_unit'],
                'loss_percent' => $rm['loss_percent'],
                'yield_percent' => 100 - $rm['loss_percent'],
                'effective_price' => $rm['price_per_unit'] / ((100 - $rm['loss_percent']) / 100),
                'created_by' => 'KopiSenjaSeeder',
            ]);
            $rawStockMap[$rm['raw_material_code']] = $raw;

            RawStockMaterialHistory::create([
                'raw_stock_material_id' => $raw->raw_stock_material_id,
                'outlet_id' => $jkt->outlet_id,
                'name' => $raw->name,
                'unit' => $raw->unit,
                'amount' => $raw->amount,
                'price_per_unit' => $raw->price_per_unit,
                'loss_percent' => $raw->loss_percent,
                'yield_percent' => $raw->yield_percent,
                'effective_price' => $raw->effective_price,
                'action_type' => 'initial_seed',
                'changed_by' => 'KopiSenjaSeeder',
                'effective_date' => now()->subDays(30),
                'history_remark' => 'Stok awal bahan mentah pembukaan cafe',
                'created_by' => 'KopiSenjaSeeder',
            ]);
        }

        // ==========================================
        // 7. COGS RECIPES & RECIPE ITEMS
        // ==========================================
        // Resep Kopi Susu Senja
        $pSenja = $products[0];
        $recipe1 = CogsRecipe::create([
            'outlet_id' => $jkt->outlet_id,
            'product_id' => $pSenja->product_id,
            'recipe_name' => 'Resep Standard Kopi Susu Senja 12oz',
            'target_food_cost' => 37.05,
            'estimated_cogs' => 8150,
            'suggested_price' => $pSenja->product_price,
            'notes' => 'Resep andalan Kopi Susu Senja',
            'created_by' => 'KopiSenjaSeeder',
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipe1->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-COFFEE-GAYO']->raw_stock_material_id,
            'ingredient_qty' => 18, // 18g espresso
            'ingredient_cost' => 18 * $rawStockMap['RAW-COFFEE-GAYO']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipe1->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-FRESH-MILK']->raw_stock_material_id,
            'ingredient_qty' => 100, // 100ml susu
            'ingredient_cost' => 100 * $rawStockMap['RAW-FRESH-MILK']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipe1->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-GULA-AREN']->raw_stock_material_id,
            'ingredient_qty' => 20, // 20ml aren
            'ingredient_cost' => 20 * $rawStockMap['RAW-GULA-AREN']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipe1->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-CUP-PAPER']->raw_stock_material_id,
            'ingredient_qty' => 1,
            'ingredient_cost' => 850,
        ]);

        // ==========================================
        // 8. PURCHASE ORDERS & CASH FLOW DISBURSEMENTS
        // ==========================================
        // PO 1: Lunas Transfer (Biji Kopi Gayo)
        $po1Date = now()->subDays(12);
        $po1Total = 5600000; // 20 kg x 280.000
        $po1 = PurchaseOrder::create([
            'outlet_id' => $jkt->outlet_id,
            'po_code' => 'PO-KS-2026-001',
            'po_date' => $po1Date,
            'supplier_id' => $supGayo->supplier_id,
            'po_status' => 'completed',
            'payment_status' => 'paid',
            'payment_date' => $po1Date->copy()->addHours(2),
            'payment_method' => 'transfer_bank',
            'due_date' => $po1Date->copy()->addDays(14)->toDateString(),
            'po_total_amount' => $po1Total,
            'po_notes' => 'Restock biji kopi Arabica Gayo batch bulanan',
            'created_by' => 'KopiSenjaSeeder',
        ]);
        $poItem1 = PurchaseOrderItem::create([
            'po_id' => $po1->po_id,
            'raw_stock_material_id' => $rawStockMap['RAW-COFFEE-GAYO']->raw_stock_material_id,
            'qty' => 20000, // 20.000 gram
            'price' => 280,
            'subtotal' => $po1Total,
            'received_qty' => 20000,
            'created_by' => 'KopiSenjaSeeder',
        ]);
        $rcv1 = PurchaseReceiving::create([
            'outlet_id' => $jkt->outlet_id,
            'po_id' => $po1->po_id,
            'receiving_code' => 'RCV-KS-2026-001',
            'receiving_date' => $po1Date->copy()->addHours(4),
            'po_code' => $po1->po_code,
            'receiving_status' => 'completed',
            'receiving_notes' => 'Biji kopi diterima dengan kualitas roasting sangat baik',
            'received_by' => 'Siti Rahma',
        ]);
        PurchaseReceivingItem::create([
            'receiving_id' => $rcv1->receiving_id,
            'po_item_id' => $poItem1->po_item_id,
            'raw_stock_material_id' => $rawStockMap['RAW-COFFEE-GAYO']->raw_stock_material_id,
            'received_qty' => 20000,
            'received_price' => 280,
            'subtotal' => $po1Total,
        ]);

        // PO 2: Belum Lunas (Tempo 14 Hari) - Fresh Milk
        $po2Date = now()->subDays(3);
        $po2Total = 1320000; // 60 Liter x 22.000
        $po2 = PurchaseOrder::create([
            'outlet_id' => $jkt->outlet_id,
            'po_code' => 'PO-KS-2026-002',
            'po_date' => $po2Date,
            'supplier_id' => $supDairy->supplier_id,
            'po_status' => 'completed',
            'payment_status' => 'unpaid',
            'payment_date' => null,
            'payment_method' => null,
            'due_date' => $po2Date->copy()->addDays(14)->toDateString(),
            'po_total_amount' => $po2Total,
            'po_notes' => 'Pengiriman susu UHT karton Diamond - Tempo jatuh tempo 14 hari',
            'created_by' => 'KopiSenjaSeeder',
        ]);
        $poItem2 = PurchaseOrderItem::create([
            'po_id' => $po2->po_id,
            'raw_stock_material_id' => $rawStockMap['RAW-FRESH-MILK']->raw_stock_material_id,
            'qty' => 60000,
            'price' => 22,
            'subtotal' => $po2Total,
            'received_qty' => 60000,
            'created_by' => 'KopiSenjaSeeder',
        ]);
        $rcv2 = PurchaseReceiving::create([
            'outlet_id' => $jkt->outlet_id,
            'po_id' => $po2->po_id,
            'receiving_code' => 'RCV-KS-2026-002',
            'receiving_date' => $po2Date->copy()->addHours(3),
            'po_code' => $po2->po_code,
            'receiving_status' => 'completed',
            'receiving_notes' => 'Susu dingin 60L diterima di chiller',
            'received_by' => 'Siti Rahma',
        ]);
        PurchaseReceivingItem::create([
            'receiving_id' => $rcv2->receiving_id,
            'po_item_id' => $poItem2->po_item_id,
            'raw_stock_material_id' => $rawStockMap['RAW-FRESH-MILK']->raw_stock_material_id,
            'received_qty' => 60000,
            'received_price' => 22,
            'subtotal' => $po2Total,
        ]);

        // ==========================================
        // 9. SHIFT OPERASIONAL & CASH DRAWER LOGS (Plan B)
        // ==========================================
        // Shift Kemarin (Closed)
        $shiftYesterday = DailyClosing::create([
            'outlet_id' => $jkt->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Pagi - Sore',
            'business_date' => now()->subDay()->toDateString(),
            'opened_at' => now()->subDay()->setTime(7, 30),
            'closed_at' => now()->subDay()->setTime(16, 0),
            'starting_cash' => 300000,
            'system_cash_sales' => 1250000,
            'system_non_cash_sales' => 2100000,
            'cash_in_amount' => 200000,  // Owner Top-up
            'cash_out_amount' => 35000,  // Beli Es Batu & Snack Kasir
            'system_expected_cash' => 1715000, // 300k + 1250k + 200k - 35k
            'actual_cash_counted' => 1715000,
            'retained_cash_float' => 300000,  // Modal shift berikutnya
            'cash_deposit_to_safe' => 1415000, // Setor ke brankas/owner
            'cash_difference' => 0,
            'status' => 'closed',
            'notes' => 'Shift lancar, kas fisik klop pas dengan sistem POS.',
            'cashier_note' => 'Kasir Siti Rahma - Uang setoran Rp 1.415.000 sudah dimasukkan ke brankas.',
        ]);

        // Cash In log (Owner Top-up)
        CashDrawerLog::create([
            'outlet_id' => $jkt->outlet_id,
            'daily_closing_id' => $shiftYesterday->id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'type' => 'in',
            'category' => 'owner_topup',
            'amount' => 200000,
            'reason' => 'Top up uang kembalian pecahan 2rb dan 5rb dari Owner',
            'created_by' => 'Siti Rahma',
            'created_at' => now()->subDay()->setTime(8, 0),
        ]);

        // Cash Out log (Petty Cash Es Batu)
        CashDrawerLog::create([
            'outlet_id' => $jkt->outlet_id,
            'daily_closing_id' => $shiftYesterday->id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'type' => 'out',
            'category' => 'petty_cash',
            'amount' => 35000,
            'reason' => 'Beli 2 karung es batu kristal darurat warung tetangga',
            'created_by' => 'Siti Rahma',
            'created_at' => now()->subDay()->setTime(11, 30),
        ]);

        // Shift Hari Ini Jakarta (Open / Running)
        $shiftToday = DailyClosing::create([
            'outlet_id' => $jkt->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Pagi - Sore',
            'business_date' => now()->toDateString(),
            'opened_at' => now()->setTime(8, 0),
            'closed_at' => null,
            'starting_cash' => 300000,
            'system_cash_sales' => 0,
            'system_non_cash_sales' => 0,
            'cash_in_amount' => 0,
            'cash_out_amount' => 0,
            'system_expected_cash' => 300000,
            'actual_cash_counted' => 0,
            'retained_cash_float' => 0,
            'cash_deposit_to_safe' => 0,
            'cash_difference' => 0,
            'status' => 'open',
            'notes' => 'Shift pagi Jakarta sedang berlangsung',
            'cashier_note' => null,
        ]);

        // Shift Kemarin Bandung (Closed)
        $shiftBdg = DailyClosing::create([
            'outlet_id' => $bdg->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Full Day',
            'business_date' => now()->subDay()->toDateString(),
            'opened_at' => now()->subDay()->setTime(8, 0),
            'closed_at' => now()->subDay()->setTime(21, 30),
            'starting_cash' => 200000,
            'system_cash_sales' => 850000,
            'system_non_cash_sales' => 1400000,
            'cash_in_amount' => 100000,
            'cash_out_amount' => 25000,
            'system_expected_cash' => 1125000,
            'actual_cash_counted' => 1125000,
            'retained_cash_float' => 200000,
            'cash_deposit_to_safe' => 925000,
            'cash_difference' => 0,
            'status' => 'closed',
            'notes' => 'Shift Bandung berjalan lancar tanpa selisih',
            'cashier_note' => 'Setoran Bandung Rp 925.000 diserahkan ke brankas',
        ]);

        // Shift Kemarin Yogyakarta (Closed - ada selisih minus 5rb)
        $shiftYog = DailyClosing::create([
            'outlet_id' => $yog->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Full Day',
            'business_date' => now()->subDay()->toDateString(),
            'opened_at' => now()->subDay()->setTime(8, 30),
            'closed_at' => now()->subDay()->setTime(22, 0),
            'starting_cash' => 200000,
            'system_cash_sales' => 700000,
            'system_non_cash_sales' => 1100000,
            'cash_in_amount' => 50000,
            'cash_out_amount' => 20000,
            'system_expected_cash' => 930000,
            'actual_cash_counted' => 925000,
            'retained_cash_float' => 200000,
            'cash_deposit_to_safe' => 725000,
            'cash_difference' => -5000,
            'status' => 'closed',
            'notes' => 'Terdapat selisih kas kecil minus Rp 5.000 uang receh koin',
            'cashier_note' => 'Setoran Jogja Rp 725.000 masuk brankas',
        ]);

        // Waste Logs Multi-Cabang
        \App\Models\Admin\Keuangan\CogsWasteLog::create([
            'outlet_id' => $jkt->outlet_id,
            'raw_stock_material_id' => $rawStockMap['RAW-FRESH-MILK']->raw_stock_material_id,
            'loss_date' => now()->subDays(2)->toDateString(),
            'qty_lost' => 3000, // 3 Liter
            'waste_cost' => 66000,
            'reason' => 'Basi - Chiller mati mendadak saat perbaikan listrik PLN',
            'notes' => 'Susu Fresh Milk Basi',
            'created_by' => 'Head Barista',
        ]);

        \App\Models\Admin\Keuangan\CogsWasteLog::create([
            'outlet_id' => $bdg->outlet_id,
            'raw_stock_material_id' => $rawStockMap['RAW-COFFEE-GAYO']->raw_stock_material_id,
            'loss_date' => now()->subDay()->toDateString(),
            'qty_lost' => 500, // 500 Gram
            'waste_cost' => 140000,
            'reason' => 'Tumpah - Toples biji kopi jatuh saat kalibrasi grinder',
            'notes' => 'Biji Kopi Tercecer / Tumpah',
            'created_by' => 'Barista Bandung',
        ]);

        // ==========================================
        // 10. CUSTOMERS & VOUCHERS
        // ==========================================
        $customersData = [
            ['customer_name' => 'Budi Santoso', 'customer_phone' => '081234567891', 'customer_email' => 'budi@gmail.com'],
            ['customer_name' => 'Anisa Tri Hapsari', 'customer_phone' => '081234567892', 'customer_email' => 'anisa@gmail.com'],
            ['customer_name' => 'Reza Gunawan', 'customer_phone' => '081234567893', 'customer_email' => 'reza@gmail.com'],
            ['customer_name' => 'Dewi Lestari', 'customer_phone' => '081234567894', 'customer_email' => 'dewi@gmail.com'],
            ['customer_name' => 'Kevin Sanjaya', 'customer_phone' => '081234567895', 'customer_email' => 'kevin@gmail.com'],
        ];
        $customers = [];
        foreach ($customersData as $c) {
            $cust = Customer::create(array_merge($c, ['outlet_id' => $jkt->outlet_id]));
            $customers[] = $cust;
        }

        Voucher::create([
            'outlet_id' => $jkt->outlet_id,
            'voucher_code' => 'SENJABARU20',
            'voucher_name' => 'Diskon Pelanggan Baru 20%',
            'voucher_type' => 'percentage',
            'voucher_value' => 20,
            'voucher_min_purchase' => 50000,
            'voucher_max_discount' => 15000,
            'voucher_status' => 1,
            'created_by' => 'KopiSenjaSeeder',
        ]);

        Discount::create([
            'outlet_id' => $jkt->outlet_id,
            'discount_name' => 'Happy Hour Senja 15%',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'discount_status' => 1,
            'created_by' => 'KopiSenjaSeeder',
        ]);

        // ==========================================
        // 11. TRANSACTIONS & ORDERS (Lengkap Shift ID)
        // ==========================================
        $branchConfigs = [
            ['outlet' => $jkt, 'count' => 20, 'prefix' => 'JKT'],
            ['outlet' => $bdg, 'count' => 12, 'prefix' => 'BDG'],
            ['outlet' => $yog, 'count' => 10, 'prefix' => 'YOG'],
        ];

        $orderGlobalIdx = 0;
        foreach ($branchConfigs as $b) {
            $currentOutlet = $b['outlet'];
            $tableList = Table::where('outlet_id', $currentOutlet->outlet_id)->get();

            for ($i = 1; $i <= $b['count']; $i++) {
                $orderGlobalIdx++;
                $orderId = 'ORD-' . $b['prefix'] . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $invoiceNo = 'INV-' . $b['prefix'] . '-' . date('ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $randCust = $customers[array_rand($customers)];
                $randTable = $tableList->isNotEmpty() ? $tableList->random() : null;

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
                $isYesterday = ($i <= 10 && $currentOutlet->outlet_id === $jkt->outlet_id);
                $closingId = $isYesterday ? $shiftYesterday->id : ($currentOutlet->outlet_id === $jkt->outlet_id ? $shiftToday->id : null);
                $txDate = $isYesterday ? now()->subDay() : now()->subDays(rand(0, 7))->subHours(rand(1, 8));

                // Create Order
                $order = Order::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'daily_closing_id' => $closingId,
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
                    'created_at' => $txDate,
                ]);

                foreach ($lineItems as $item) {
                    $order->products()->attach($item['product']->product_id, [
                        'quantity' => $item['qty'],
                        'note' => '',
                    ]);
                }

                // Create Transaction
                $tx = Transaction::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'daily_closing_id' => $closingId,
                    'transaction_code' => $invoiceNo,
                    'transaction_date' => $txDate->toDateString(),
                    'transaction_subtotal' => $itemsTotal,
                    'transaction_tax' => $taxAmount,
                    'transaction_service_charge' => $serviceAmount,
                    'transaction_grand_total' => $grandTotal,
                    'transaction_status' => 'success',
                    'transaction_table_id' => $randTable?->table_id,
                    'transaction_customer_id' => $randCust->customer_id,
                    'transaction_remark' => 'Penjualan Kopi Senja ' . $currentOutlet->outlet_branch,
                    'created_at' => $txDate,
                ]);

                $order->update(['order_transaction_id' => $tx->transaction_id]);

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

                $payMethods = ['qris', 'cash', 'transfer'];
                $payMethod = $payMethods[array_rand($payMethods)];
                Payment::create([
                    'outlet_id' => $currentOutlet->outlet_id,
                    'transaction_id' => $tx->transaction_id,
                    'payment_metode' => $payMethod,
                    'payment_amount' => $grandTotal,
                    'payment_grand_total' => $grandTotal,
                    'payment_status' => 'paid',
                    'payment_date' => $txDate->toDateString(),
                    'created_at' => $txDate,
                ]);
            }
        }

        // ==========================================
        // 12. HPP & LABA RUGI FINANCIAL REPORTS
        // ==========================================
        HppFinancialReport::create([
            'outlet_id' => $jkt->outlet_id,
            'year' => (int) date('Y'),
            'month' => (int) date('m'),
            'total_revenue' => 48500000,
            'total_cogs_estimated' => 17460000,
            'total_waste_cost' => 450000,
            'total_labor_cost' => 8500000,
            'total_overhead_cost' => 4200000,
            'gross_profit' => 30590000,
            'gross_margin_percent' => 63.07,
            'net_profit_estimated' => 17890000,
            'net_margin_percent' => 36.89,
            'notes' => 'Laporan Laba Rugi Eksekutif Kopi Senja Jakarta (Plan B)',
            'created_by' => 'KopiSenjaSeeder',
        ]);
    }
}
