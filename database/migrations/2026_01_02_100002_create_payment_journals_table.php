<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ตารางนี้ใช้เพื่อบันทึกรายละเอียดการจ่ายเงินแบบละเอียด
     * ว่าจ่ายไปยังดอกเบี้ยเท่าไร, เงินต้นเท่าไร
     */
    public function up(): void
    {
        Schema::create('payment_journals', function (Blueprint $table) {
            $table->id();
            
            // อ้างอิง
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            
            // Polymorphic relation ไปยัง Rental/Loan Contract
            $table->unsignedBigInteger('payable_id');
            $table->string('payable_type');
            
            // บันทึกรายละเอียดการจ่าย
            $table->decimal('amount', 12, 2)->comment('จำนวนเงินในครั้งนี้');
            $table->enum('allocation_type', ['outstanding', 'interest', 'principal'])
                  ->comment('การจัดสรร: ยอดค้างชำระ | ดอกเบี้ย | เงินต้น');
            
            // สำหรับเงินกู้: ติดตามเงินต้นที่เหลือ
            $table->decimal('principal_before', 12, 2)->nullable()->comment('เงินต้นก่อนการจ่าย');
            $table->decimal('principal_after', 12, 2)->nullable()->comment('เงินต้นหลังการจ่าย');
            
            // ยอดค้างชำระ
            $table->decimal('outstanding_before', 12, 2)->comment('ยอดค้างชำระก่อน');
            $table->decimal('outstanding_after', 12, 2)->comment('ยอดค้างชำระหลัง');
            
            // ข้อมูลเพิ่มเติม
            $table->text('description')->nullable()->comment('คำอธิบายการจัดสรร');
            $table->timestamps();
            
            // Indexes
            $table->index(['transaction_id', 'invoice_id']);
            $table->index(['payable_id', 'payable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_journals');
    }
};
