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

    // 1. Drop old tables
    Schema::dropIfExists('transactional');
    Schema::dropIfExists('allowances');
    
    // Drop migration records so they don't cause issues later
    DB::table('migrations')->where('migration', 'like', '%create_allowances_table%')->delete();
    DB::table('migrations')->where('migration', 'like', '%create_transactional_table%')->delete();

    // 2. Create NEW transactional table
    Schema::create('transactional', function (Blueprint $table) {
        $table->string('transactional_id')->primary();
        $table->string('employee_id');
        $table->string('transactional_month'); // e.g. '2026-05'
        $table->decimal('transactional_thr', 15, 2)->default(0);
        $table->decimal('transactional_bonus', 15, 2)->default(0);
        $table->decimal('transactional_overtime', 15, 2)->default(0);
        $table->decimal('transactional_bpjs', 15, 2)->default(0);
        $table->decimal('transactional_total', 15, 2)->default(0);
        $table->timestamps();

        $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
    });

    // 3. Refactor payrolls table
    // We will drop it and recreate it because it has too many columns being renamed/dropped.
    // It is a cleaner approach to just drop and recreate `payrolls`.
    Schema::dropIfExists('payrolls');
    DB::table('migrations')->where('migration', 'like', '%create_payrolls_table%')->delete();
    DB::table('migrations')->where('migration', 'like', '%add_bonus_columns_to_payrolls_table%')->delete();
    DB::table('migrations')->where('migration', 'like', '%add_dynamic_fields_to_payrolls_table%')->delete();

    Schema::create('payrolls', function (Blueprint $table) {
        $table->string('payroll_id')->primary(); // e.g., PY01
        $table->string('employee_id');
        $table->string('transactional_id')->nullable();
        $table->string('employee_name')->nullable();
        
        $table->string('payroll_periode_month');
        $table->integer('payroll_periode_year');
        
        $table->decimal('payroll_reimburse_total', 15, 2)->default(0);
        $table->integer('payroll_total_attendance')->default(0);
        $table->decimal('payroll_total_late', 15, 2)->default(0); // 50k * late count
        $table->decimal('payroll_tax', 15, 2)->default(0);
        $table->decimal('payroll_net_salary', 15, 2)->default(0);
        
        $table->enum('payroll_status', ['pending', 'approved', 'rejected'])->default('pending');
        
        $table->timestamp('payroll_create_at')->nullable();
        $table->timestamp('payroll_update_at')->nullable();

        $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        $table->foreign('transactional_id')->references('transactional_id')->on('transactional')->onDelete('set null');
    });

    Schema::enableForeignKeyConstraints();
    echo "Payrolls and Transactional schemas refactored successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
