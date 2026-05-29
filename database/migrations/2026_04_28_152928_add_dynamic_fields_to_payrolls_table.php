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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('hke')->default(0)->after('year');
            $table->decimal('daily_rate', 15, 2)->default(0)->after('hke');
            $table->integer('hadir_count')->default(0)->after('net_salary');
            $table->integer('cuti_count')->default(0)->after('hadir_count');
            $table->integer('sakit_count')->default(0)->after('cuti_count');
            $table->integer('alpha_count')->default(0)->after('sakit_count');
            $table->integer('terlambat_count')->default(0)->after('alpha_count');
            $table->decimal('potongan_alpha', 15, 2)->default(0)->after('terlambat_count');
            $table->decimal('denda_terlambat', 15, 2)->default(0)->after('potongan_alpha');
            $table->decimal('reimbursement', 15, 2)->default(0)->after('overtime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'hke', 'daily_rate', 'hadir_count', 'cuti_count', 'sakit_count', 
                'alpha_count', 'terlambat_count', 'potongan_alpha', 'denda_terlambat', 'reimbursement'
            ]);
        });
    }
};
