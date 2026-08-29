<?php

namespace App\Services\Client;

use App\Models\SysAdmin\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use PDO;
use Exception;

class ClientDatabaseManager
{
    /**
     * Nama koneksi database client di config/database.php
     */
    public const CLIENT_CONNECTION = 'client';

    /**
     * Alias backward compatibility
     */
    public const TENANT_CONNECTION = 'client';

    /**
     * Nama koneksi database central di config/database.php
     */
    public const CENTRAL_CONNECTION = 'central';

    /**
     * Database client yang sedang aktif saat ini.
     */
    protected static ?string $currentClientDb = null;

    /**
     * Switch koneksi database client secara dinamis ke target client.
     *
     * @param Client|string $client
     * @param string|null $host
     * @param int|null $port
     * @param string|null $username
     * @param string|null $password
     * @return bool
     */
    public static function connectToClient(
        Client|string $client,
        ?string $host = null,
        ?int $port = null,
        ?string $username = null,
        ?string $password = null
    ): bool {
        $dbName = ($client instanceof Client) ? $client->database_name : $client;
        $host = $host ?? (($client instanceof Client) ? ($client->db_host ?? env('DB_HOST', '127.0.0.1')) : env('DB_HOST', '127.0.0.1'));
        $port = $port ?? (($client instanceof Client) ? ($client->db_port ?? env('DB_PORT', 3306)) : env('DB_PORT', 3306));
        $username = $username ?? env('DB_USERNAME', 'root');
        $password = $password ?? env('DB_PASSWORD', '');

        if (empty($dbName)) {
            Log::error("[ClientDatabaseManager] Failed to connect: Database name is empty.");
            return false;
        }

        try {
            // Purge existing connection
            DB::purge(self::CLIENT_CONNECTION);
            DB::purge('tenant');

            // Set dynamic config for 'client' and 'tenant'
            foreach ([self::CLIENT_CONNECTION, 'tenant'] as $conn) {
                Config::set("database.connections.{$conn}.host", $host);
                Config::set("database.connections.{$conn}.port", $port);
                Config::set("database.connections.{$conn}.database", $dbName);
                Config::set("database.connections.{$conn}.username", $username);
                Config::set("database.connections.{$conn}.password", $password);
            }

            // Test connection with ping query
            DB::connection(self::CLIENT_CONNECTION)->getPdo();
            DB::setDefaultConnection(self::CLIENT_CONNECTION);
            self::$currentClientDb = $dbName;

            return true;
        } catch (\Throwable $e) {
            Log::warning("[ClientDatabaseManager] Could not connect to client DB: {$dbName}. Resetting to Central DB. Error: " . $e->getMessage());
            self::$currentClientDb = null;
            self::connectToCentral();
            return false;
        }
    }

    /**
     * Switch default connection kembali ke Central Database.
     */
    public static function connectToCentral(): void
    {
        DB::setDefaultConnection(self::CENTRAL_CONNECTION);
        self::$currentClientDb = null;
    }

    /**
     * Ambil nama database client yang sedang aktif.
     */
    public static function getCurrentClientDatabase(): ?string
    {
        return self::$currentClientDb;
    }

    /**
     * Alias backward compatibility untuk getCurrentClientDatabase.
     */
    public static function getCurrentTenantDatabase(): ?string
    {
        return self::getCurrentClientDatabase();
    }

    /**
     * Cek apakah database tertentu sudah ada di server database.
     */
    public static function databaseExists(string $databaseName): bool
    {
        try {
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
            $result = DB::connection(self::CENTRAL_CONNECTION)->select($query, [$databaseName]);
            return count($result) > 0;
        } catch (Exception $e) {
            Log::error("[ClientDatabaseManager] Error checking DB existence: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buat database baru untuk client secara aman.
     */
    public static function createDatabase(string $databaseName): bool
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $databaseName)) {
            throw new Exception("Nama database tidak valid: hanya alfanumerik dan underscore yang diizinkan.");
        }

        try {
            $statement = "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
            DB::connection(self::CENTRAL_CONNECTION)->statement($statement);
            Log::info("[ClientDatabaseManager] Successfully created database: {$databaseName}");
            return true;
        } catch (Exception $e) {
            Log::error("[ClientDatabaseManager] Failed to create database: {$databaseName}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus database client (Aksi Destruktif Khusus Super Admin).
     */
    public static function dropDatabase(string $databaseName): bool
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $databaseName)) {
            throw new Exception("Nama database tidak valid.");
        }

        try {
            DB::purge(self::CLIENT_CONNECTION);
            DB::purge('tenant');
            $statement = "DROP DATABASE IF EXISTS `{$databaseName}`;";
            DB::connection(self::CENTRAL_CONNECTION)->statement($statement);
            Log::warning("[ClientDatabaseManager] Dropped database: {$databaseName}");
            return true;
        } catch (Exception $e) {
            Log::error("[ClientDatabaseManager] Failed to drop database: {$databaseName}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Jalankan migrasi tabel operasional POS ke database client.
     */
    public static function runClientMigrations(string $databaseName, bool $fresh = false): array
    {
        $connected = self::connectToClient($databaseName);
        if (!$connected) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke database client: {$databaseName}",
            ];
        }

        try {
            $command = $fresh ? 'migrate:fresh' : 'migrate';
            Artisan::call($command, [
                '--database' => self::CLIENT_CONNECTION,
                '--path' => 'database/migrations/client',
                '--force' => true,
            ]);

            $output = Artisan::output();
            return [
                'success' => true,
                'output' => $output,
                'message' => "Migrasi client" . ($fresh ? " (fresh)" : "") . " berhasil dijalankan pada database: {$databaseName}",
            ];
        } catch (Exception $e) {
            Log::error("[ClientDatabaseManager] Error running migrations on {$databaseName}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Alias backward compatibility untuk runClientMigrations.
     */
    public static function runTenantMigrations(string $databaseName): array
    {
        return self::runClientMigrations($databaseName);
    }

    /**
     * Tes koneksi database dan return metadata latency & status.
     */
    public static function testConnection(
        string $databaseName,
        ?string $host = null,
        ?int $port = null,
        ?string $username = null,
        ?string $password = null
    ): array {
        $startTime = microtime(true);
        $success = self::connectToClient($databaseName, $host, $port, $username, $password);
        $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

        if ($success) {
            try {
                $tablesCount = count(DB::connection(self::CLIENT_CONNECTION)->select('SHOW TABLES'));
                return [
                    'status' => 'connected',
                    'success' => true,
                    'latency_ms' => $latencyMs,
                    'tables_count' => $tablesCount,
                    'message' => "Koneksi berhasil ({$latencyMs} ms, {$tablesCount} tabel).",
                ];
            } catch (Exception $e) {
                return [
                    'status' => 'connected_with_warning',
                    'success' => true,
                    'latency_ms' => $latencyMs,
                    'tables_count' => 0,
                    'message' => "Koneksi terbuka tetapi gagal membaca tabel.",
                ];
            }
        }

        return [
            'status' => 'disconnected',
            'success' => false,
            'latency_ms' => $latencyMs,
            'tables_count' => 0,
            'message' => "Gagal membuka koneksi database client.",
        ];
    }
}
