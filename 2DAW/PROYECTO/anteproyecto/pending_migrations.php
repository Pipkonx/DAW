<?php
// Get pending migrations
$ran = DB::table('migrations')->pluck('migration')->toArray();

$migrationPath = database_path('migrations');
$files = glob($migrationPath . '/*.php');

echo "=== PENDING MIGRATIONS ===\n";
foreach ($files as $file) {
    $name = pathinfo($file, PATHINFO_FILENAME);
    if (!in_array($name, $ran)) {
        echo "PENDING: $name\n";
    }
}

echo "\n=== ALREADY RAN ===\n";
foreach ($ran as $m) {
    echo "RAN: $m\n";
}
