<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('sessions', function (Blueprint $table) {
    if (Schema::hasColumn('sessions', 'employee_id')) {
        $table->renameColumn('employee_id', 'user_id');
        echo "Renamed employee_id to user_id in sessions table.\n";
    }
});
