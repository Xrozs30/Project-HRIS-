<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

try {
    Schema::disableForeignKeyConstraints();

    // Remove foreign keys if any (they were dropped earlier but just in case)
    try {
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'overtimes' AND COLUMN_NAME = 'assigned_by'");
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE overtimes DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }
    } catch (\Exception $e) {}

    // 1. Add new columns
    Schema::table('overtimes', function (Blueprint $table) {
        $table->string('overtime_id')->first()->nullable();
        $table->string('employee_name')->nullable();
        $table->decimal('overtime_duration', 8, 2)->nullable();
        $table->string('overtime_approve_by')->nullable();
    });

    // 2. Rename columns using raw SQL for enums
    DB::statement("ALTER TABLE overtimes CHANGE date overtime_date DATE NOT NULL");
    DB::statement("ALTER TABLE overtimes CHANGE start_time overtime_start TIME NOT NULL");
    DB::statement("ALTER TABLE overtimes CHANGE end_time overtime_finish TIME NOT NULL");
    DB::statement("ALTER TABLE overtimes CHANGE status overtime_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    DB::statement("ALTER TABLE overtimes CHANGE description overtime_description TEXT DEFAULT NULL");
    DB::statement("ALTER TABLE overtimes CHANGE created_at overtime_create_at TIMESTAMP NULL DEFAULT NULL");
    DB::statement("ALTER TABLE overtimes CHANGE updated_at overtime_update_at TIMESTAMP NULL DEFAULT NULL");
    
    // 3. Drop assigned_by
    Schema::table('overtimes', function (Blueprint $table) {
        if (Schema::hasColumn('overtimes', 'assigned_by')) {
            $table->dropColumn('assigned_by');
        }
    });

    // 4. Data Mapping
    $overtimes = DB::table('overtimes')->get();
    foreach ($overtimes as $index => $ot) {
        $newId = 'OT' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        
        $employee = DB::table('employees')->where('employee_id', $ot->employee_id)->first();
        $employeeName = $employee ? $employee->employee_name : null;
        
        // Calculate duration (Decimal hours)
        $start = Carbon::parse($ot->overtime_start);
        $finish = Carbon::parse($ot->overtime_finish);
        $durationMinutes = $start->diffInMinutes($finish);
        $durationHours = $durationMinutes / 60;

        DB::table('overtimes')->where('id', $ot->id)->update([
            'overtime_id' => $newId,
            'employee_name' => $employeeName,
            'overtime_duration' => $durationHours
        ]);
    }

    // 5. Alter Primary Key
    DB::statement('ALTER TABLE overtimes MODIFY id bigint(20) unsigned NOT NULL');
    
    Schema::table('overtimes', function (Blueprint $table) {
        $table->dropPrimary();
        $table->dropColumn('id');
        $table->primary('overtime_id');
        $table->string('overtime_id')->nullable(false)->change();
    });

    Schema::enableForeignKeyConstraints();
    echo "Refactor completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
