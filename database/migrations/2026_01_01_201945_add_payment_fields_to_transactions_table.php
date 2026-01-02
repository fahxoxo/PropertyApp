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
            $table->decimal('interest', 10, 2)->default(0)->after('payment_method');
            $table->decimal('principal_paid', 10, 2)->default(0)->after('interest');
            $table->enum('payment_type', ['interest', 'principal', 'both'])->default('interest')->after('principal_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['interest', 'principal_paid', 'payment_type']);
        });
    }
};
