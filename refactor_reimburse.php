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
    if (Schema::hasTable('reimbursements')) {
        Schema::rename('reimbursements', 'reimburse');
    }

    // Drop FK if it exists on the new table
    try {
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reimburse' AND COLUMN_NAME = 'employee_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE reimburse DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }
    } catch (\Exception $e) {}

    // 2. Add new columns
    Schema::table('reimburse', function (Blueprint $table) {
        if (!Schema::hasColumn('reimburse', 'employee_name')) {
            $table->string('employee_name')->nullable();
        }
    });

    // 3. Rename columns using raw SQL for safety
    $columns = DB::select("SHOW COLUMNS FROM reimburse");
    $colNames = array_column($columns, 'Field');

    if (in_array('id', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE id reimburse_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    if (in_array('date', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE date reimburse_date DATE NOT NULL");
    }
    if (in_array('file_path', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE file_path reimburse_proof VARCHAR(255) NULL DEFAULT NULL");
    }
    if (in_array('description', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE description reimburse_description TEXT NOT NULL");
    }
    if (in_array('amount', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE amount reimburse_total DECIMAL(15,2) NOT NULL DEFAULT 0.00");
    }
    if (in_array('hr_notes', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE hr_notes reimburse_notes TEXT NULL DEFAULT NULL");
    }
    if (in_array('status', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE status reimburse_status ENUM('pending', 'hr_approved', 'approved', 'rejected') DEFAULT 'pending'");
    }
    if (in_array('created_at', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE created_at reimburse_create_at TIMESTAMP NULL DEFAULT NULL");
    }
    if (in_array('updated_at', $colNames)) {
        DB::statement("ALTER TABLE reimburse CHANGE updated_at reimburse_update_at TIMESTAMP NULL DEFAULT NULL");
    }

    // 4. Data Mapping
    $reimbursements = DB::table('reimburse')->get();
    foreach ($reimbursements as $r) {
        $employee = DB::table('employees')->where('employee_id', $r->employee_id)->first();
        $employeeName = $employee ? $employee->employee_name : null;
        
        DB::table('reimburse')->where('reimburse_id', $r->reimburse_id)->update([
            'employee_name' => $employeeName,
        ]);
    }

    // 5. Re-add Foreign Key
    try {
        Schema::table('reimburse', function (Blueprint $table) {
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    } catch (\Exception $e) {
        // Just in case it already exists
        echo "FK Exception: " . $e->getMessage() . "\n";
    }

    Schema::enableForeignKeyConstraints();
    echo "Refactor completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
