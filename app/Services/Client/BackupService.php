<?php

namespace App\Services\Client;

use App\Models\SysAdmin\AuditLog;
use App\Models\SysAdmin\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class BackupService
{
    /**
     * Buat snapshot backup SQL dari database client.
     *
     * @param string $clientId
     * @return array
     */
    public static function createSnapshot(string $clientId): array
    {
        $client = Client::where('client_id', $clientId)->first();
        if (!$client) {
            return ['success' => false, 'message' => 'Client tidak ditemukan.'];
        }

        $databaseName = $client->database_name;
        $backupDir = storage_path("app/backups/{$clientId}");

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $fileName = "backup_{$clientId}_{$databaseName}_{$timestamp}.sql";
        $filePath = "{$backupDir}/{$fileName}";

        try {
            $connected = ClientDatabaseManager::connectToClient($databaseName);
            if (!$connected) {
                throw new Exception("Gagal terhubung ke database {$databaseName}.");
            }

            $pdo = DB::connection('client')->getPdo();
            $tables = [];
            $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $handle = fopen($filePath, 'w+');
            fwrite($handle, "-- Nexora POS Multi-Tenant Database Backup\n");
            fwrite($handle, "-- Client ID: {$clientId} ({$client->client_name})\n");
            fwrite($handle, "-- Database: {$databaseName}\n");
            fwrite($handle, "-- Timestamp: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Total Tables: " . count($tables) . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                // Table Structure
                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createStmt->fetch(\PDO::FETCH_NUM);
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createRow[1] . ";\n\n");

                // Table Rows Data
                $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
                $columnCount = $rowsStmt->columnCount();

                while ($row = $rowsStmt->fetch(\PDO::FETCH_NUM)) {
                    $escapedValues = array_map(function ($val) use ($pdo) {
                        if ($val === null) return 'NULL';
                        return $pdo->quote($val);
                    }, $row);

                    fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(", ", $escapedValues) . ");\n");
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            ClientDatabaseManager::connectToCentral();

            $fileSizeBytes = File::size($filePath);
            $fileSizeMb = round($fileSizeBytes / 1048576, 2);

            AuditLog::record(
                action: 'backup_created',
                clientId: $clientId,
                targetType: 'DatabaseBackup',
                targetId: $fileName,
                result: 'success',
                metadata: [
                    'database' => $databaseName,
                    'size_mb' => $fileSizeMb,
                    'tables' => count($tables),
                ]
            );

            return [
                'success' => true,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size_mb' => $fileSizeMb,
                'tables_count' => count($tables),
                'created_at' => now(),
                'message' => "Snapshot backup database {$databaseName} berhasil dibuat ({$fileSizeMb} MB).",
            ];
        } catch (Exception $e) {
            ClientDatabaseManager::connectToCentral();
            Log::error("[BackupService] Backup error for {$clientId}: " . $e->getMessage());

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            AuditLog::record(
                action: 'backup_failed',
                clientId: $clientId,
                targetType: 'DatabaseBackup',
                targetId: $fileName,
                result: 'failure',
                metadata: ['error' => $e->getMessage()]
            );

            return [
                'success' => false,
                'message' => "Gagal membuat snapshot backup: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Dapatkan daftar seluruh file backup snapshot yang ada.
     */
    public static function getAllBackups(): array
    {
        $backupBasePath = storage_path('app/backups');
        if (!File::exists($backupBasePath)) {
            return [];
        }

        $files = [];
        $clientDirs = File::directories($backupBasePath);

        foreach ($clientDirs as $clientDir) {
            $clientId = basename($clientDir);
            $clientFiles = File::files($clientDir);

            foreach ($clientFiles as $file) {
                if ($file->getExtension() === 'sql') {
                    $files[] = [
                        'client_id' => $clientId,
                        'file_name' => $file->getFilename(),
                        'file_path' => $file->getPathname(),
                        'file_size_mb' => round($file->getSize() / 1048576, 2),
                        'created_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime()),
                    ];
                }
            }
        }

        // Urutkan dari yang terbaru
        usort($files, function ($a, $b) {
            return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
        });

        return $files;
    }
}
