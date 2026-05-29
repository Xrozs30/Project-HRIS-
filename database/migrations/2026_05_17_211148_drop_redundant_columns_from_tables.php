<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Handle presence table (may be named 'presensis' or 'presence')
        $presenceTable = Schema::hasTable('presence') ? 'presence' : (Schema::hasTable('presensis') ? 'presensis' : null);
        if ($presenceTable && Schema::hasColumn($presenceTable, 'employee_name')) {
            Schema::table($presenceTable, function (Blueprint $table) {
                $table->dropColumn('employee_name');
            });
        }

        if (Schema::hasColumn('overtimes', 'employee_name')) {
            Schema::table('overtimes', function (Blueprint $table) {
                $table->dropColumn('employee_name');
            });
        }

        if (Schema::hasColumn('payrolls', 'employee_name')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn('employee_name');
            });
        }

        // Handle reimbursements table (may be named 'reimburse' or 'reimbursements')
        $reimburseTable = Schema::hasTable('reimburse') ? 'reimburse' : (Schema::hasTable('reimbursements') ? 'reimbursements' : null);
        if ($reimburseTable && Schema::hasColumn($reimburseTable, 'employee_name')) {
            Schema::table($reimburseTable, function (Blueprint $table) {
                $table->dropColumn('employee_name');
            });
        }

        // Handle leave table (may be named 'leave_permissions' or 'leave_requests')
        $leaveTable = Schema::hasTable('leave_permissions') ? 'leave_permissions' : (Schema::hasTable('leave_requests') ? 'leave_requests' : null);
        if ($leaveTable && Schema::hasColumn($leaveTable, 'employee_name')) {
            Schema::table($leaveTable, function (Blueprint $table) {
                $table->dropColumn('employee_name');
            });
        }

        if (Schema::hasColumn('employees', 'position_type')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('position_type');
            });
        }
        if (Schema::hasColumn('employees', 'tax_status')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('tax_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presence', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
        });
        Schema::table('overtimes', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
        });
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
        });
        Schema::table('reimburse', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
        });
        Schema::table('leave_permissions', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->string('position_type')->nullable();
            $table->string('tax_status')->nullable();
        });
    }
};
