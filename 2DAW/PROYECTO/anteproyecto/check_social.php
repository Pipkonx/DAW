<?php
$socialTables = ['posts', 'comments', 'likes', 'reposts', 'bookmarks', 'reports'];
foreach ($socialTables as $table) {
    $exists = Schema::hasTable($table) ? 'EXISTS' : 'MISSING';
    echo "$table: $exists\n";
}
