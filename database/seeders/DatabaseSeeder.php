<?php

namespace Database\Seeders;

use App\Models\SysAdmin\User;
use Illuminate\Database\Seeder;
use Database\Seeders\Keuangan\CogsRawMaterialSeeder;
use Database\Seeders\Keuangan\CogsRecipeSeeder;
use Database\Seeders\Keuangan\CogsWasteLogSeeder;
use Database\Seeders\Keuangan\HppFinancialReportSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            TaxSeeder::class,
            ServiceChargeSeeder::class,
            ShiftSeeder::class,
            SupplierSeeder::class,

            TableSeeder::class,

            StockSeeder::class,
            CogsRawMaterialSeeder::class,
            PurchaseOrderSeeder::class,
            ProductSeeder::class,
            BundleSeeder::class,
            DiscountSeeder::class,
            VoucherSeeder::class,
            DailyClosingSeeder::class,
            OrderSeeder::class,

            TransactionSeeder::class,
            HistoryStockSeeder::class,
            HistoryDiscountSeeder::class,
            HistoryBundleSeeder::class,
            HistoryProductSeeder::class,
            HistoryVoucherSeeder::class,
            OrderBundleSeeder::class,
            CogsRecipeSeeder::class,
            CogsWasteLogSeeder::class,
            HppFinancialReportSeeder::class,
        ]);
    }
}
