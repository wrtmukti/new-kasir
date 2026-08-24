<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CentralMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'central:migrate 
                            {--fresh : Drop all tables sebelum migrasi ulang}
                            {--seed : Jalankan seeder CentralDatabaseSeeder setelah migrasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan skema migrasi tabel Central/SysAdmin ke Central Database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Menjalankan migrasi tabel Central SysAdmin...");

        $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';

        $params = [
            '--database' => 'central',
            '--path' => 'database/migrations/central',
            '--force' => true,
        ];

        Artisan::call($command, $params);
        $this->info(Artisan::output());

        if ($this->option('seed')) {
            $this->info("🌱 Menjalankan CentralDatabaseSeeder...");
            Artisan::call('db:seed', [
                '--class' => 'CentralDatabaseSeeder',
                '--force' => true,
            ]);
            $this->info(Artisan::output());
        }

        $this->info("✅ Migrasi Central Database selesai.");
        return 0;
    }
}
