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
        Schema::create('transactional', function (Blueprint $table) {
            $table->string('transactional_id')->primary();
            $table->string('employee_id');
            $table->unsignedBigInteger('allowance_id');
            $table->string('transactional_month');
            $table->decimal('transactional_total', 15, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreign('allowance_id')->references('allowance_id')->on('allowances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactional');
    }
};
