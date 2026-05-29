<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

try {
    // Cari HR user berdasarkan role atau email
    $user = User::where('email', 'hr@example.com')->orWhere('role', 'hr')->first();
    
    if ($user) {
        $user->email = 'hr@arci.com';
        $user->password = Hash::make('123456');
        $user->save();
        echo "SUCCESS: HR account updated to hr@arci.com\n";
    } else {
        echo "ERROR: HR account not found in database.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
