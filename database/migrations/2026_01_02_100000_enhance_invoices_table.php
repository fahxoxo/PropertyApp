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
        Schema::table('invoices', function (Blueprint $table) {
            // เพิ่มฟิลด์สำหรับติดตามยอดค้างชำระ
            $table->decimal('outstanding_balance', 12, 2)->default(0)->after('amount')->comment('ยอดค้างชำระ');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('outstanding_balance')->comment('ยอดจ่ายแล้ว');
            
            // สำหรับสัญญาเงินกู้: บันทึกจำนวนดอกเบี้ยที่ออกบิล
            $table->decimal('interest_amount', 12, 2)->nullable()->after('paid_amount')->comment('ยอดดอกเบี้ยที่ออกบิล');
            
            // สำหรับสัญญาเงินกู้: บันทึกเงินต้นคงเหลือ ณ วันออกบิล
            $table->decimal('principal_at_billing', 12, 2)->nullable()->after('interest_amount')->comment('เงินต้นคงเหลือ ณ วันออกบิล');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['outstanding_balance', 'paid_amount', 'interest_amount', 'principal_at_billing']);
        });
    }
};
