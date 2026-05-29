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
            $table->decimal('thr', 15, 2)->default(0)->after('basic_salary');
            $table->decimal('target_bonus', 15, 2)->default(0)->after('thr');
            $table->decimal('overtime', 15, 2)->default(0)->after('target_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['thr', 'target_bonus', 'overtime']);
        });
    }
};
