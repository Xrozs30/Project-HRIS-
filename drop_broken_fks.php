<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Disable FK constraints
Schema::disableForeignKeyConstraints();

$brokenKeys = DB::select("
    SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND REFERENCED_TABLE_NAME = 'employees'
");

foreach ($brokenKeys as $key) {
    echo "Dropping FK {$key->CONSTRAINT_NAME} from {$key->TABLE_NAME}\n";
    try {
        DB::statement("ALTER TABLE {$key->TABLE_NAME} DROP FOREIGN KEY {$key->CONSTRAINT_NAME}");
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

Schema::enableForeignKeyConstraints();
echo "Done.\n";
