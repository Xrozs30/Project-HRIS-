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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('position_id')->references('position_id')->on('positions')->onDelete('set null');
            $table->foreign('allowance_id')->references('allowance_id')->on('allowances')->onDelete('set null');
            $table->foreign('tax_id')->references('tax_id')->on('taxes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['allowance_id']);
            $table->dropForeign(['tax_id']);
        });
    }
};
