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
        Schema::disableForeignKeyConstraints();

        $relatedTables = ['absences', 'leave_requests', 'overtimes', 'payrolls', 'presensis', 'reimbursements', 'sessions'];

        // Add employee_id to related tables
        foreach ($relatedTables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->string('employee_id')->nullable();
            });
        }

        Schema::rename('users', 'employees');

        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_id')->first()->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->string('allowance_type')->nullable();
            
            $table->renameColumn('position', 'position_type');
            $table->renameColumn('nik', 'employee_nik');
            $table->renameColumn('name', 'employee_name');
            $table->renameColumn('email', 'employee_email');
            $table->renameColumn('gender', 'employee_gender');
            $table->renameColumn('phone', 'employee_phone');
            $table->renameColumn('address', 'employee_addres');
            $table->renameColumn('pph21_status', 'tax_status');
            $table->renameColumn('basic_salary', 'employee_basic_salary');
            $table->renameColumn('password', 'employee_password');
            $table->renameColumn('face_descriptor', 'employee_face_descriptor');
            $table->renameColumn('account_number', 'employee_bank_number');
            $table->renameColumn('bank_name', 'employee_bank_name');
            $table->renameColumn('bpjs_number', 'employee_bpjs_number');
            $table->renameColumn('birth_date', 'employee_birth_date');
            $table->renameColumn('created_at', 'employee_create_at');
            $table->renameColumn('updated_at', 'employee_update_at');
        });

        // Map Data
        $employees = \Illuminate\Support\Facades\DB::table('employees')->get();
        foreach ($employees as $index => $employee) {
            $newId = 'ST' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            \Illuminate\Support\Facades\DB::table('employees')->where('id', $employee->id)->update([
                'employee_id' => $newId
            ]);
            
            foreach ($relatedTables as $table) {
                \Illuminate\Support\Facades\DB::table($table)->where('user_id', $employee->id)->update([
                    'employee_id' => $newId
                ]);
            }
        }

        // Alter primary keys and foreign keys
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE employees MODIFY id bigint(20) unsigned NOT NULL');
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
            $table->primary('employee_id');
            $table->string('employee_id')->nullable(false)->change();
        });

        foreach ($relatedTables as $table) {
            // Drop foreign key if it exists. Using DB queries to safely drop if we don't know the name.
            $foreignKeys = \Illuminate\Support\Facades\DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = 'user_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            foreach ($foreignKeys as $fk) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE $table DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
            
            Schema::table($table, function (Blueprint $tableBlueprint) {
                $tableBlueprint->dropColumn('user_id');
                $tableBlueprint->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For brevity, down method is not implemented for this massive refactor.
    }
};
