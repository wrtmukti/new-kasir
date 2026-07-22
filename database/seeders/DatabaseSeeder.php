<?php

namespace Database\Seeders;

use App\Models\SysAdmin\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            SupplierSeeder::class,
            TableSeeder::class,
            StockSeeder::class,
            PurchaseOrderSeeder::class,
            ProductSeeder::class,
            BundleSeeder::class,
            OrderSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
