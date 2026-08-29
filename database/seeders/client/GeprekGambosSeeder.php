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
use App\Models\Admin\Keuangan\HppFinancialReport;
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
                'service_name' => 'Biaya Pelayanan (0%)',
                'rate_percent' => 0.00,
                'is_taxable' => 0,
                'is_active' => 0,
                'created_by' => 'GeprekGambosSeeder',
            ]);

            // Shift Setting
            ShiftSetting::create([
                'outlet_id' => $outlet->outlet_id,
                'daily_cutoff_time' => '00:00:00',
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
                'name' => 'Hendra Setiawan (Owner Gambos)',
                'email' => 'owner@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $jkt->outlet_id,
                'name' => 'Rina Marlina (Kasir Jakarta)',
                'email' => 'kasir@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $bgr->outlet_id,
                'name' => 'Ahmad Fauzi (Kasir Bogor)',
                'email' => 'kasir.bogor@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $yog->outlet_id,
                'name' => 'Bayu Wicaksono (Kasir Jogja)',
                'email' => 'kasir.jogja@geprekgambos.com',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => $clientId,
                'outlet_id' => $sby->outlet_id,
                'name' => 'Eko Prasetyo (Kasir Surabaya)',
                'email' => 'kasir.surabaya@geprekgambos.com',
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
        for ($i = 1; $i <= 15; $i++) {
            Table::create([
                'outlet_id' => $jkt->outlet_id,
                'table_number' => $i,
                'table_capacity' => ($i % 2 == 0) ? 4 : 2,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Resto Rawamangun",
            ]);
        }
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'outlet_id' => $bgr->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Resto Pajajaran",
            ]);
        }
        for ($i = 1; $i <= 8; $i++) {
            Table::create([
                'outlet_id' => $yog->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Resto Gejayan",
            ]);
        }
        for ($i = 1; $i <= 12; $i++) {
            Table::create([
                'outlet_id' => $sby->outlet_id,
                'table_number' => $i,
                'table_capacity' => 4,
                'table_status' => 'active',
                'table_description' => "Meja {$i} Resto Gubeng",
            ]);
        }

        // ==========================================
        // 4. KATEGORI & PRODUK AYAM GEPREK
        // ==========================================
        $catAyam = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Ayam Geprek Spesial', 'category_slug' => 'ayam-geprek-spesial', 'category_status' => 1]);
        $catPaket = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Paket Hemat Lengkap', 'category_slug' => 'paket-hemat-lengkap', 'category_status' => 1]);
        $catSide = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Side Dishes & Ekstra', 'category_slug' => 'side-dishes-ekstra', 'category_status' => 1]);
        $catDrink = Category::create(['outlet_id' => $jkt->outlet_id, 'category_name' => 'Minuman Segar', 'category_slug' => 'minuman-segar', 'category_status' => 1]);

        $productsData = [
            ['name' => 'Ayam Geprek Original Sambal Korek', 'cat' => $catAyam, 'price' => 17000, 'cost' => 8000, 'sku' => 'GG-001', 'desc' => 'Ayam goreng renyah digeprek dengan sambal korek bawang pedas nendang'],
            ['name' => 'Ayam Geprek Mozzarella Leleh', 'cat' => $catAyam, 'price' => 24000, 'cost' => 12000, 'sku' => 'GG-002', 'desc' => 'Ayam geprek pedas dengan lelehan keju mozzarella import yang gurih'],
            ['name' => 'Ayam Geprek Sambal Matah Bali', 'cat' => $catAyam, 'price' => 19000, 'cost' => 9000, 'sku' => 'GG-003', 'desc' => 'Ayam krispi digeprek bersama irisan serai, bawang merah, dan cabai segar'],
            ['name' => 'Paket Geprek Komplit (Nasi + Es Teh)', 'cat' => $catPaket, 'price' => 23000, 'cost' => 10500, 'sku' => 'GG-004', 'desc' => 'Ayam geprek original + Nasi pulen hangat + Es teh manis jumbo'],
            ['name' => 'Paket Geprek Mozza Mantap', 'cat' => $catPaket, 'price' => 29000, 'cost' => 14000, 'sku' => 'GG-005', 'desc' => 'Ayam geprek mozzarella + Nasi putih + Es teh manis'],
            ['name' => 'Kulit Ayam Crispy Gurih', 'cat' => $catSide, 'price' => 14000, 'cost' => 5000, 'sku' => 'GG-006', 'desc' => 'Kulit ayam goreng garing kriuk dengan bumbu rempah khas Gambos'],
            ['name' => 'Tahu Tempe Goreng Sambal', 'cat' => $catSide, 'price' => 8000, 'cost' => 3000, 'sku' => 'GG-007', 'desc' => '2 potong tahu dan 2 potong tempe bumbu kuning digoreng hangat'],
            ['name' => 'Jamur Crispy Sambal Bawang', 'cat' => $catSide, 'price' => 12000, 'cost' => 4500, 'sku' => 'GG-008', 'desc' => 'Jamur tiram balut tepung renyah dicocol sambal bawang'],
            ['name' => 'Es Teh Manis Jumbo', 'cat' => $catDrink, 'price' => 5000, 'cost' => 1200, 'sku' => 'GG-009', 'desc' => 'Teh melati manis wangi khas Jawa dengan es batu melimpah'],
            ['name' => 'Es Jeruk Peras Murni', 'cat' => $catDrink, 'price' => 8000, 'cost' => 3000, 'sku' => 'GG-010', 'desc' => 'Perasan jeruk segar asli dengan gula pasir cair'],
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

            foreach ([$jkt, $bgr, $yog, $sby] as $ot) {
                Stock::create([
                    'outlet_id' => $ot->outlet_id,
                    'stock_code' => 'STK-' . $p['sku'],
                    'stock_name' => $prod->product_name,
                    'stock_slug' => Str::slug($prod->product_name . '-' . $ot->outlet_branch),
                    'stock_unit' => 'porsi',
                    'stock_amount' => rand(80, 300),
                    'stock_price' => $prod->product_price,
                    'stock_status' => 1,
                ]);
            }
        }

        // ==========================================
        // 5. MASTER SUPPLIER
        // ==========================================
        $supUnggas = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-AYAM-01',
            'supplier_name' => 'PT Unggas Makmur Sejahtera',
            'supplier_contact' => 'H. Suwarno',
            'supplier_phone' => '081288776655',
            'supplier_address' => 'Kawasan Rumah Potong Ayam Cakung, Jakarta Timur',
        ]);
        $supPasar = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-BUMBU-01',
            'supplier_name' => 'UD Berkah Bumbu Pasar Induk Kramat Jati',
            'supplier_contact' => 'Bang Jayadi',
            'supplier_phone' => '085711223344',
            'supplier_address' => 'Pasar Induk Kramat Jati Blok C No. 14, Jakarta Timur',
        ]);
        $supSembako = Supplier::create([
            'outlet_id' => $jkt->outlet_id,
            'supplier_code' => 'SUP-SEMBAKO-01',
            'supplier_name' => 'CV Sumber Rezeki Sembako (Beras & Minyak)',
            'supplier_contact' => 'Koh Awi',
            'supplier_phone' => '021-66554433',
            'supplier_address' => 'Jl. Pangeran Jayakarta No. 99, Jakarta Pusat',
        ]);

        // ==========================================
        // 6. RAW STOCK MATERIALS & HISTORIES (Plan B)
        // ==========================================
        $rawMaterialsData = [
            [
                'raw_material_code' => 'RAW-AYAM-BROILER',
                'name' => 'Daging Ayam Broiler Utuh Karkas 1.2kg',
                'unit' => 'kg',
                'amount' => 120.0, // 120 kg
                'min_amount' => 25.0,
                'price_per_unit' => 38000,
                'loss_percent' => 15.0, // Potong lemak & jeroan -> yield 85%
            ],
            [
                'raw_material_code' => 'RAW-CABAI-RAWIT',
                'name' => 'Cabai Rawit Merah Super Segar',
                'unit' => 'kg',
                'amount' => 45.0,
                'min_amount' => 8.0,
                'price_per_unit' => 48000,
                'loss_percent' => 8.0, // Petik tangkai -> yield 92%
            ],
            [
                'raw_material_code' => 'RAW-BERAS-PULEN',
                'name' => 'Beras Putih Pulen Premium Rojolele',
                'unit' => 'kg',
                'amount' => 250.0,
                'min_amount' => 50.0,
                'price_per_unit' => 15500,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-MINYAK-GORENG',
                'name' => 'Minyak Goreng Sawit Kemasan Jerigen 18L',
                'unit' => 'liter',
                'amount' => 180.0,
                'min_amount' => 36.0,
                'price_per_unit' => 17500,
                'loss_percent' => 0.0,
            ],
            [
                'raw_material_code' => 'RAW-TEPUNG-CRISPY',
                'name' => 'Tepung Bumbu Crispy Rahasia Gambos',
                'unit' => 'kg',
                'amount' => 80.0,
                'min_amount' => 15.0,
                'price_per_unit' => 22000,
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
                'created_by' => 'GeprekGambosSeeder',
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
                'changed_by' => 'GeprekGambosSeeder',
                'effective_date' => now()->subDays(30),
                'history_remark' => 'Stok awal bahan mentah pembukaan resto geprek',
                'created_by' => 'GeprekGambosSeeder',
            ]);
        }

        // ==========================================
        // 7. COGS RECIPES & RECIPE ITEMS
        // ==========================================
        $pGeprek = $products[0];
        $recipeGeprek = CogsRecipe::create([
            'outlet_id' => $jkt->outlet_id,
            'product_id' => $pGeprek->product_id,
            'recipe_name' => 'Resep Ayam Geprek Original Sambal Korek',
            'target_food_cost' => 46.18,
            'estimated_cogs' => 7850,
            'suggested_price' => $pGeprek->product_price,
            'notes' => 'Resep andalan Ayam Geprek Original Sambal Korek',
            'created_by' => 'GeprekGambosSeeder',
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipeGeprek->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-AYAM-BROILER']->raw_stock_material_id,
            'ingredient_qty' => 0.125, // 1/8 ekor ayam (125g)
            'ingredient_cost' => 0.125 * $rawStockMap['RAW-AYAM-BROILER']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipeGeprek->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-CABAI-RAWIT']->raw_stock_material_id,
            'ingredient_qty' => 0.025, // 25g cabai rawit
            'ingredient_cost' => 0.025 * $rawStockMap['RAW-CABAI-RAWIT']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipeGeprek->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-MINYAK-GORENG']->raw_stock_material_id,
            'ingredient_qty' => 0.04, // 40ml serapan minyak
            'ingredient_cost' => 0.04 * $rawStockMap['RAW-MINYAK-GORENG']->effective_price,
        ]);
        CogsRecipeItem::create([
            'cogs_recipe_id' => $recipeGeprek->cogs_recipe_id,
            'raw_stock_material_id' => $rawStockMap['RAW-TEPUNG-CRISPY']->raw_stock_material_id,
            'ingredient_qty' => 0.03, // 30g tepung
            'ingredient_cost' => 0.03 * $rawStockMap['RAW-TEPUNG-CRISPY']->effective_price,
        ]);

        // ==========================================
        // 8. PURCHASE ORDERS & CASH FLOW DISBURSEMENTS
        // ==========================================
        // PO 1: Lunas Cash (Ayam Segar 100kg)
        $po1Date = now()->subDays(8);
        $po1Total = 3800000;
        $po1 = PurchaseOrder::create([
            'outlet_id' => $jkt->outlet_id,
            'po_code' => 'PO-GGB-2026-001',
            'po_date' => $po1Date,
            'supplier_id' => $supUnggas->supplier_id,
            'po_status' => 'completed',
            'payment_status' => 'paid',
            'payment_date' => $po1Date->copy()->addHours(1),
            'payment_method' => 'cash',
            'due_date' => $po1Date->copy()->addDays(7)->toDateString(),
            'po_total_amount' => $po1Total,
            'po_notes' => 'Pasokan ayam segar 100 kg - Lunas bayar tunai di lokasi',
            'created_by' => 'GeprekGambosSeeder',
        ]);
        $poItem1 = PurchaseOrderItem::create([
            'po_id' => $po1->po_id,
            'raw_stock_material_id' => $rawStockMap['RAW-AYAM-BROILER']->raw_stock_material_id,
            'qty' => 100,
            'price' => 38000,
            'subtotal' => $po1Total,
            'received_qty' => 100,
            'created_by' => 'GeprekGambosSeeder',
        ]);
        $rcv1 = PurchaseReceiving::create([
            'outlet_id' => $jkt->outlet_id,
            'po_id' => $po1->po_id,
            'receiving_code' => 'RCV-GGB-2026-001',
            'receiving_date' => $po1Date->copy()->addHours(2),
            'po_code' => $po1->po_code,
            'receiving_status' => 'completed',
            'receiving_notes' => 'Ayam karkas segar 100kg masuk freezer',
            'received_by' => 'Rina Marlina',
        ]);
        PurchaseReceivingItem::create([
            'receiving_id' => $rcv1->receiving_id,
            'po_item_id' => $poItem1->po_item_id,
            'raw_stock_material_id' => $rawStockMap['RAW-AYAM-BROILER']->raw_stock_material_id,
            'received_qty' => 100,
            'received_price' => 38000,
            'subtotal' => $po1Total,
        ]);

        // PO 2: Belum Lunas (Tempo 10 Hari) - Beras & Minyak
        $po2Date = now()->subDays(2);
        $po2Total = 4675000;
        $po2 = PurchaseOrder::create([
            'outlet_id' => $jkt->outlet_id,
            'po_code' => 'PO-GGB-2026-002',
            'po_date' => $po2Date,
            'supplier_id' => $supSembako->supplier_id,
            'po_status' => 'completed',
            'payment_status' => 'unpaid',
            'payment_date' => null,
            'payment_method' => null,
            'due_date' => $po2Date->copy()->addDays(10)->toDateString(),
            'po_total_amount' => $po2Total,
            'po_notes' => 'Beras 200kg + Minyak Jerigen 90L - Tempo 10 hari',
            'created_by' => 'GeprekGambosSeeder',
        ]);
        $poItem2A = PurchaseOrderItem::create([
            'po_id' => $po2->po_id,
            'raw_stock_material_id' => $rawStockMap['RAW-BERAS-PULEN']->raw_stock_material_id,
            'qty' => 200,
            'price' => 15500,
            'subtotal' => 3100000,
            'received_qty' => 200,
            'created_by' => 'GeprekGambosSeeder',
        ]);
        $poItem2B = PurchaseOrderItem::create([
            'po_id' => $po2->po_id,
            'raw_stock_material_id' => $rawStockMap['RAW-MINYAK-GORENG']->raw_stock_material_id,
            'qty' => 90,
            'price' => 17500,
            'subtotal' => 1575000,
            'received_qty' => 90,
            'created_by' => 'GeprekGambosSeeder',
        ]);
        $rcv2 = PurchaseReceiving::create([
            'outlet_id' => $jkt->outlet_id,
            'po_id' => $po2->po_id,
            'receiving_code' => 'RCV-GGB-2026-002',
            'receiving_date' => $po2Date->copy()->addHours(3),
            'po_code' => $po2->po_code,
            'receiving_status' => 'completed',
            'receiving_notes' => 'Sembako beras & minyak ditumpuk di gudang kering',
            'received_by' => 'Rina Marlina',
        ]);
        PurchaseReceivingItem::create([
            'receiving_id' => $rcv2->receiving_id,
            'po_item_id' => $poItem2A->po_item_id,
            'raw_stock_material_id' => $rawStockMap['RAW-BERAS-PULEN']->raw_stock_material_id,
            'received_qty' => 200,
            'received_price' => 15500,
            'subtotal' => 3100000,
        ]);
        PurchaseReceivingItem::create([
            'receiving_id' => $rcv2->receiving_id,
            'po_item_id' => $poItem2B->po_item_id,
            'raw_stock_material_id' => $rawStockMap['RAW-MINYAK-GORENG']->raw_stock_material_id,
            'received_qty' => 90,
            'received_price' => 17500,
            'subtotal' => 1575000,
        ]);

        // ==========================================
        // 9. SHIFT OPERASIONAL & CASH DRAWER LOGS (Plan B)
        // ==========================================
        $shiftYesterday = DailyClosing::create([
            'outlet_id' => $jkt->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Full Day Gambos',
            'business_date' => now()->subDay()->toDateString(),
            'opened_at' => now()->subDay()->setTime(9, 0),
            'closed_at' => now()->subDay()->setTime(21, 30),
            'starting_cash' => 250000,
            'system_cash_sales' => 1850000,
            'system_non_cash_sales' => 2900000,
            'cash_in_amount' => 150000, // Topup kas
            'cash_out_amount' => 45000, // Beli gas LPG & es batu darurat
            'system_expected_cash' => 2205000,
            'actual_cash_counted' => 2205000,
            'retained_cash_float' => 250000,
            'cash_deposit_to_safe' => 1955000,
            'cash_difference' => 0,
            'status' => 'closed',
            'notes' => 'Shift ramai jam makan siang dan makan malam, kas klop.',
            'cashier_note' => 'Uang tunai Rp 1.955.000 diserahkan ke brankas Resto.',
        ]);

        CashDrawerLog::create([
            'outlet_id' => $jkt->outlet_id,
            'daily_closing_id' => $shiftYesterday->id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'type' => 'in',
            'category' => 'owner_topup',
            'amount' => 150000,
            'reason' => 'Pecahan uang kembalian 2.000 & 5.000 dari supervisor',
            'created_by' => 'Rina Marlina',
            'created_at' => now()->subDay()->setTime(9, 30),
        ]);

        CashDrawerLog::create([
            'outlet_id' => $jkt->outlet_id,
            'daily_closing_id' => $shiftYesterday->id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'type' => 'out',
            'category' => 'petty_cash',
            'amount' => 45000,
            'reason' => 'Beli es batu kristal 2 bal warung sebelah',
            'created_by' => 'Rina Marlina',
            'created_at' => now()->subDay()->setTime(12, 15),
        ]);

        $shiftToday = DailyClosing::create([
            'outlet_id' => $jkt->outlet_id,
            'cashier_id' => $cashierUser?->id ?? 1,
            'shift_number' => 1,
            'shift_name' => 'Shift Full Day Gambos',
            'business_date' => now()->toDateString(),
            'opened_at' => now()->setTime(9, 0),
            'closed_at' => null,
            'starting_cash' => 250000,
            'system_cash_sales' => 0,
            'system_non_cash_sales' => 0,
            'cash_in_amount' => 0,
            'cash_out_amount' => 0,
            'system_expected_cash' => 250000,
            'actual_cash_counted' => 0,
            'retained_cash_float' => 0,
            'cash_deposit_to_safe' => 0,
            'cash_difference' => 0,
            'status' => 'open',
            'notes' => 'Shift siang sedang berjalan',
            'cashier_note' => null,
        ]);

        // ==========================================
        // 10. CUSTOMERS & VOUCHERS
        // ==========================================
        $customersData = [
            ['customer_name' => 'Agus Prasetyo', 'customer_phone' => '081298765431', 'customer_email' => 'agus@gmail.com'],
            ['customer_name' => 'Nurul Hidayah', 'customer_phone' => '081298765432', 'customer_email' => 'nurul@gmail.com'],
            ['customer_name' => 'Bambang Sudiro', 'customer_phone' => '081298765433', 'customer_email' => 'bambang@gmail.com'],
            ['customer_name' => 'Fitri Anggraini', 'customer_phone' => '081298765434', 'customer_email' => 'fitri@gmail.com'],
        ];
        $customers = [];
        foreach ($customersData as $c) {
            $cust = Customer::create(array_merge($c, ['outlet_id' => $jkt->outlet_id]));
            $customers[] = $cust;
        }

        Voucher::create([
            'outlet_id' => $jkt->outlet_id,
            'voucher_code' => 'GAMBOSPEDAS',
            'voucher_name' => 'Diskon Sambal Meledak Rp 5.000',
            'voucher_type' => 'nominal',
            'voucher_value' => 5000,
            'voucher_min_purchase' => 30000,
            'voucher_max_discount' => 5000,
            'voucher_status' => 1,
            'created_by' => 'GeprekGambosSeeder',
        ]);

        // ==========================================
        // 11. TRANSACTIONS & ORDERS (Lengkap Shift ID)
        // ==========================================
        $branchConfigs = [
            ['outlet' => $jkt, 'count' => 25, 'prefix' => 'JKT'],
            ['outlet' => $bgr, 'count' => 15, 'prefix' => 'BGR'],
            ['outlet' => $yog, 'count' => 12, 'prefix' => 'YOG'],
            ['outlet' => $sby, 'count' => 18, 'prefix' => 'SBY'],
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
                $serviceAmount = 0;
                $grandTotal = $itemsTotal + $taxAmount + $serviceAmount;
                $isYesterday = ($i <= 12 && $currentOutlet->outlet_id === $jkt->outlet_id);
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
                    'service_charge_percent' => 0.00,
                    'service_charge_amount' => 0.00,
                    'order_remark' => 'Dine-In Geprek Gambos Meja ' . ($randTable?->table_number ?? 1),
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
                    'transaction_service_charge' => 0,
                    'transaction_grand_total' => $grandTotal,
                    'transaction_status' => 'success',
                    'transaction_table_id' => $randTable?->table_id,
                    'transaction_customer_id' => $randCust->customer_id,
                    'transaction_remark' => 'Penjualan Geprek Gambos ' . $currentOutlet->outlet_branch,
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
            'total_revenue' => 62300000,
            'total_cogs_estimated' => 28658000,
            'total_waste_cost' => 620000,
            'total_labor_cost' => 11000000,
            'total_overhead_cost' => 5500000,
            'gross_profit' => 33022000,
            'gross_margin_percent' => 53.00,
            'net_profit_estimated' => 16522000,
            'net_margin_percent' => 26.52,
            'notes' => 'Laporan Laba Rugi Eksekutif Geprek Gambos Rawamangun (Plan B)',
            'created_by' => 'GeprekGambosSeeder',
        ]);
    }
}
