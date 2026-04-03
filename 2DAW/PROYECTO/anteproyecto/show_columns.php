<?php
$cols = DB::select('SHOW COLUMNS FROM market_assets');
foreach($cols as $c) {
    echo $c->Field . "\n";
}
