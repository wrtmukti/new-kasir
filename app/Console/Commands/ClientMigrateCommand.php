
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SysAdmin\Client;
use App\Models\SysAdmin\DatabaseConnection;
use App\Services\Client\ClientDatabaseManager;

class ClientMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:migrate 
                            {--client= : Client ID spesifik (contoh: CLI001)} 
                            {--fresh : Drop all tables sebelum migrasi ulang}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan skema migrasi ke database client yang aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientId = $this->option('client');

        if ($clientId) {
            $client = Client::where('client_id', $clientId)->first();
            if (!$client) {
                $this->error("❌ Client dengan ID '{$clientId}' tidak ditemukan di Central Database.");
                return 1;
            }

            $this->migrateClientDatabase($client);
            return 0;
        }

        $clients = Client::where('status', 'active')->where('delete_status', 0)->get();

        if ($clients->isEmpty()) {
            $this->warn("ℹ️ Tidak ada client dengan status aktif.");
            return 0;
        }

        $this->info("🚀 Memulai migrasi massal untuk " . $clients->count() . " database client...");
        $bar = $this->output->createProgressBar($clients->count());
        $bar->start();

        foreach ($clients as $client) {
            $this->migrateClientDatabase($client, false);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Seluruh migrasi database client selesai dijalankan!");

        return 0;
    }

    /**
     * Helper migrasi per database client.
     */
    protected function migrateClientDatabase(Client $client, bool $verbose = true)
    {
        $fresh = (bool) $this->option('fresh');
        if ($verbose) {
            $this->info("⏳ Menjalankan migrasi" . ($fresh ? " (FRESH)" : "") . " untuk [{$client->client_id}] {$client->client_name} (DB: {$client->database_name})...");
        }

        $result = ClientDatabaseManager::runClientMigrations($client->database_name, $fresh);

        if ($result['success']) {
            if ($verbose) {
                $this->line($result['output'] ?? '');
                $this->info("✅ Sukses migrasi DB: {$client->database_name}");
            }

            // Update Metadata Database Connection di Central DB
            $health = ClientDatabaseManager::testConnection($client->database_name);
            DatabaseConnection::updateOrCreate(
                ['client_id' => $client->client_id],
                [
                    'database_name' => $client->database_name,
                    'connection_status' => $health['success'] ? 'connected' : 'warning',
                    'latency_ms' => $health['latency_ms'] ?? 0,
                    'tables_count' => $health['tables_count'] ?? 0,
                    'last_health_check_at' => now(),
                    'status_message' => "Migrasi diperbarui pada " . now()->format('Y-m-d H:i:s'),
                ]
            );
        } else {
            $this->error("❌ Gagal migrasi [{$client->client_id}]: " . $result['message']);
        }
    }
}
