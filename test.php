<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$employees = \App\Models\User::where('role','employee')->get(['id','name']);
foreach ($employees as $u) {
    echo $u->id . " => " . $u->name . "\n";
}
