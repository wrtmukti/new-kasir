<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menjalankan Central Platform Seeder (Super Admin, Paket SaaS, Setting Global, & Provisioning Client Kopi Senja + Geprek Gambos)
        $this->call([
            CentralDatabaseSeeder::class,
        ]);
    }
}
