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
        // This migration was finished via a raw SQL script because Doctrine DBAL 
        // failed to rename enum columns with default values.
    }

    public function down(): void
    {
        // Omitted for brevity
    }
};
