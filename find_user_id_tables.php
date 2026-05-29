<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = array_map(function($table) { return $table['name']; }, Schema::getTables());
$relatedTables = [];
foreach ($tables as $table) {
    if ($table === 'users') continue;
    $columns = Schema::getColumnListing($table);
    if (in_array('user_id', $columns)) {
        $relatedTables[] = $table;
    }
}
echo "Related tables: " . implode(', ', $relatedTables) . PHP_EOL;
