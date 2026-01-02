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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // เลขที่บิล เช่น INV-202601-0001
            $table->integer('month'); // เดือน 1-12
            $table->integer('year'); // ปี
            $table->enum('type', ['rental', 'loan_sale', 'loan_mortgage', 'loan']); // ประเภทบิล
            $table->morphs('billable'); // Polymorphic relation ไปยัง RentalContract หรือ LoanContract
            $table->decimal('amount', 10, 2); // จำนวนเงิน
            $table->date('due_date')->nullable(); // วันครบกำหนด
            $table->enum('status', ['draft', 'issued', 'paid', 'overdue'])->default('draft');
            $table->text('notes')->nullable(); // หมายเหตุ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
