<?php
$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $cols = array_values((array)$t);
    echo $cols[0] . "\n";
}
