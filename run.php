<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \App\Models\User::where('role', 'owner')->delete();
    
    \App\Models\User::create([
        'email' => 'owner@arci.com',
        'name' => 'Owner AJNN',
        'password' => \Illuminate\Support\Facades\Hash::make('123456'),
        'role' => 'owner',
        'nik' => 'OWN-' . rand(100, 999), 
        'pph21_status' => 'TK/0',
        'basic_salary' => 0,
    ]);
    echo "Created owner account: owner@arci.com\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
