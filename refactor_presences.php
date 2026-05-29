<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    Schema::disableForeignKeyConstraints();

    // 1. Rename table
    Schema::rename('presensis', 'presence');

    // 2. Add new columns
    Schema::table('presence', function (Blueprint $table) {
        $table->string('employee_name')->nullable();
    });

    // 3. Rename columns using raw SQL for enums
    DB::statement("ALTER TABLE presence CHANGE id presence_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT");
    DB::statement("ALTER TABLE presence CHANGE date presence_date DATE NOT NULL");
    DB::statement("ALTER TABLE presence CHANGE time_in presence_time_in TIME NOT NULL");
    DB::statement("ALTER TABLE presence CHANGE time_out presence_time_out TIME NULL DEFAULT NULL");
    DB::statement("ALTER TABLE presence CHANGE photo_in presence_photo_in VARCHAR(255) NOT NULL");
    DB::statement("ALTER TABLE presence CHANGE photo_out presence_photo_out VARCHAR(255) NULL DEFAULT NULL");
    DB::statement("ALTER TABLE presence CHANGE lat presence_lat DOUBLE NULL DEFAULT NULL");
    DB::statement("ALTER TABLE presence CHANGE `long` presence_long DOUBLE NULL DEFAULT NULL");
    DB::statement("ALTER TABLE presence CHANGE status presence_status ENUM('late', 'on_time') DEFAULT 'on_time'");
    DB::statement("ALTER TABLE presence CHANGE created_at presence_create_at TIMESTAMP NULL DEFAULT NULL");
    DB::statement("ALTER TABLE presence CHANGE updated_at presence_update_at TIMESTAMP NULL DEFAULT NULL");
    
    // 4. Data Mapping
    $presences = DB::table('presence')->get();
    foreach ($presences as $p) {
        $employee = DB::table('employees')->where('employee_id', $p->employee_id)->first();
        $employeeName = $employee ? $employee->employee_name : null;
        
        DB::table('presence')->where('presence_id', $p->presence_id)->update([
            'employee_name' => $employeeName,
        ]);
    }

    // 5. Re-add Foreign Key
    try {
        Schema::table('presence', function (Blueprint $table) {
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    } catch (\Exception $e) {
        // Just in case it already exists
    }

    Schema::enableForeignKeyConstraints();
    echo "Refactor completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
