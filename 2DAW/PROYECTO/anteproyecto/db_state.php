<?php
// Full migration tracking info
$ran = DB::table('migrations')->orderBy('batch')->get();

echo "=== MIGRATIONS IN DB ===\n";
foreach($ran as $m) {
    echo "batch={$m->batch} | {$m->migration}\n";
}

echo "\n=== TABLES ===\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $v = array_values((array)$t);
    echo $v[0] . "\n";
}
