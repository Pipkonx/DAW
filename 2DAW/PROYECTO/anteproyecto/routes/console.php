<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\UpdatePricesJob;

use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily price updates
Schedule::job(new UpdatePricesJob)->dailyAt('06:00')->timezone('Europe/Madrid');

// Schedule daily DB Backup (SQLite)
Schedule::call(function () {
    $databasePath = database_path('database.sqlite');
    if (File::exists($databasePath)) {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $filename = 'backup-auto-' . now()->format('Y-m-d-His') . '.sqlite';
        File::copy($databasePath, $backupDir . '/' . $filename);
        
        // Mantener solo los últimos 5 backups automáticos
        $files = File::glob($backupDir . '/backup-auto-*.sqlite');
        if (count($files) > 5) {
            array_multisort(array_map('filemtime', $files), SORT_ASC, $files);
            File::delete(array_slice($files, 0, count($files) - 5));
        }
    }
})->dailyAt('03:00')->timezone('Europe/Madrid');
