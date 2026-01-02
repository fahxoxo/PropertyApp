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
            // เพิ่มฟิลด์สำหรับบันทึกยอดค้างชำระก่อนและหลังการจ่ายเงิน
            $table->decimal('outstanding_balance_before', 12, 2)->default(0)->after('payment_type')->comment('ยอดค้างชำระก่อนจ่ายเงิน');
            $table->decimal('outstanding_balance_after', 12, 2)->default(0)->after('outstanding_balance_before')->comment('ยอดค้างชำระหลังจ่ายเงิน');
            
            // เพิ่มจำนวนดอกเบี้ยที่จ่ายในครั้งนี้
            $table->decimal('interest_paid', 10, 2)->default(0)->after('outstanding_balance_after')->comment('ดอกเบี้ยที่จ่ายในครั้งนี้');
            
            // เพิ่มจำนวนเงินต้นที่จ่ายในครั้งนี้
            $table->decimal('principal_reduced', 12, 2)->default(0)->after('interest_paid')->comment('เงินต้นที่ลดลง');
            
            // เพิ่มหมายเหตุ
            $table->text('notes')->nullable()->after('principal_reduced')->comment('หมายเหตุการจ่ายเงิน');
            
            // เพิ่มการอ้างอิงไปยัง invoice
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null')->after('payable_type')->comment('เสร็จเรียบร้อยของใบแจ้งหนี้');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'outstanding_balance_before',
                'outstanding_balance_after',
                'interest_paid',
                'principal_reduced',
                'notes',
                'invoice_id'
            ]);
        });
    }
};
