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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique(['receipt_number']);
            
            // Change receipt_number to be nullable and add unique constraint only for non-null values
            $table->string('receipt_number')->nullable()->change();
            
            // Add a unique constraint that allows multiple NULL values
            $table->unique('receipt_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert to the original state
            $table->dropUnique(['receipt_number']);
            $table->string('receipt_number')->unique()->change();
        });
    }
};
