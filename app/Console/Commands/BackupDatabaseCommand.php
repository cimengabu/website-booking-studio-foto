<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:backup {--retention-days=30 : Jumlah hari retensi file backup}';

    /**
     * The console command description.
     */
    protected $description = 'Melakukan backup berkala database dan arsip data penting SnapSpace Studio';

    /**
     * Execute the command.
     */
    public function handle(): int
    {
        $this->info('Memulai proses backup data SnapSpace Studio...');

        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $dbConnection = config('database.default');
        $backupFile = null;

        if ($dbConnection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if (File::exists($dbPath)) {
                $backupFile = "{$backupDir}/backup_{$timestamp}.sqlite";
                File::copy($dbPath, $backupFile);
                $this->info("Database SQLite berhasil disalin ke: {$backupFile}");
            }
        } else {
            // MySQL / MariaDB Dump with pure PHP fallback
            $backupFile = "{$backupDir}/backup_{$timestamp}.sql";
            $this->dumpDatabase($backupFile);
            $this->info("Database berhasil diekspor ke: {$backupFile} (".round(File::size($backupFile) / 1024, 2).' KB)');
        }

        // Cleanup old backups beyond retention days
        $retentionDays = (int) $this->option('retention-days');
        $files = File::files($backupDir);
        $deletedCount = 0;

        foreach ($files as $file) {
            $fileTimestamp = Carbon::createFromTimestamp(File::lastModified($file->getPathname()));
            if ($fileTimestamp->isBefore(Carbon::now()->subDays($retentionDays))) {
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        $statusMsg = 'Backup database selesai: '.basename($backupFile ?? 'none').". ({$deletedCount} backup usang dibersihkan).";
        $this->info($statusMsg);

        ActivityLog::record(null, 'system_backup', $statusMsg);

        return Command::SUCCESS;
    }

    /**
     * Export MySQL / MariaDB tables & data to SQL file using PHP DB connection
     */
    protected function dumpDatabase(string $backupFile): void
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = "Tables_in_{$dbName}";

        $handle = fopen($backupFile, 'w');
        fwrite($handle, "-- SnapSpace Studio Database Backup\n-- Date: ".now()->toIso8601String()."\n\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $t) {
            $tableName = $t->$keyName ?? array_values((array) $t)[0];
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $createTable[0]->{'Create Table'} ?? '';
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n{$createSql};\n\n");

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = implode('`, `', array_keys($rowArray));
                $values = array_map(function ($val) {
                    if (is_null($val)) {
                        return 'NULL';
                    }

                    return "'".addslashes((string) $val)."'";
                }, array_values($rowArray));
                $valuesStr = implode(', ', $values);
                fwrite($handle, "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n");
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }
}
