<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Drop FK if exists
try {
    $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leave_permissions' AND COLUMN_NAME = 'approved_by'");
    foreach ($foreignKeys as $fk) {
        DB::statement("ALTER TABLE leave_permissions DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
    }
} catch (\Exception $e) {}

// Rename remaining columns using raw SQL to bypass Doctrine ENUM issues
DB::statement("ALTER TABLE leave_permissions CHANGE status leave_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
DB::statement("ALTER TABLE leave_permissions CHANGE approved_by leave_approve_by VARCHAR(255) DEFAULT NULL");
DB::statement("ALTER TABLE leave_permissions CHANGE rejection_reason leave_rejection_reason TEXT DEFAULT NULL");
DB::statement("ALTER TABLE leave_permissions CHANGE created_at leave_create_at TIMESTAMP NULL DEFAULT NULL");
DB::statement("ALTER TABLE leave_permissions CHANGE updated_at leave_update_at TIMESTAMP NULL DEFAULT NULL");

// 3. Map Data
$leavePermissions = DB::table('leave_permissions')->get();
foreach ($leavePermissions as $index => $leave) {
    $newId = 'LV' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
    
    $employee = DB::table('employees')->where('employee_id', $leave->employee_id)->first();
    $employeeName = $employee ? $employee->employee_name : null;
    
    $leaveApproveBy = is_numeric($leave->leave_approve_by) ? null : $leave->leave_approve_by;

    DB::table('leave_permissions')->where('id', $leave->id)->update([
        'leave_id' => $newId,
        'employee_name' => $employeeName,
        'leave_approve_by' => $leaveApproveBy
    ]);
}

// 4. Alter Primary Key
DB::statement('ALTER TABLE leave_permissions MODIFY id bigint(20) unsigned NOT NULL');

Schema::table('leave_permissions', function (Blueprint $table) {
    $table->dropPrimary();
    $table->dropColumn('id');
    $table->primary('leave_id');
    $table->string('leave_id')->nullable(false)->change();
});

echo "Migration finished successfully.\n";
