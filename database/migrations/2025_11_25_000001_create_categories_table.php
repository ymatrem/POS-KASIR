<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - This is now deprecated, categories table is created in 2024_01_01_000002
     */
    public function up(): void
    {
        // Migration 2024_01_01_000002_create_categories_table handles this
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing since categories table is handled by 2024_01_01_000002
    }
};
