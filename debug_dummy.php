<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('app:generate-dummy-data');
}
catch (\Exception $e) {
    file_put_contents('dummy_error_full.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "Error caught and logged to dummy_error_full.txt";
}
