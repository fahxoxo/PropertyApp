<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. ตารางลูกค้า
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // รหัสลูกค้า
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nickname')->nullable();
            $table->string('phone');
            $table->string('id_card');
            $table->string('id_card_image')->nullable();
            $table->timestamps();
        });

        // 2. ตารางบ้านเช่า
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // รหัสบ้าน
            $table->string('name');
            $table->string('address'); // บ้านเลขที่
            $table->string('moo')->nullable();
            $table->string('subdistrict');
            $table->string('district');
            $table->string('province');
            $table->decimal('price', 10, 2); // ราคาเช่ามาตรฐาน
            $table->string('water_meter')->nullable();
            $table->string('electric_meter')->nullable();
            $table->string('type'); // โฉนด, ธนารักษ์, อื่นๆ
            $table->string('gps')->nullable();
            $table->string('doc_image')->nullable();
            $table->enum('status', ['vacant', 'rented'])->default('vacant');
            $table->timestamps();
        });

        // 3. สัญญาเช่า
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('deposit', 10, 2);
            $table->decimal('advance_rent', 10, 2);
            $table->date('start_date');
            $table->string('contract_image')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });

        // 4. สัญญาขายฝาก/จำนอง
        Schema::create('loan_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('interest_rate', 5, 2); // ร้อยละ
            $table->decimal('principal', 12, 2); // เงินต้นเริ่มต้น
            $table->decimal('principal_remaining', 12, 2); // เงินต้นคงเหลือ
            $table->string('duration'); // 6 เดือน, 1 ปี
            $table->date('contract_date');
            $table->string('status'); // อยู่ในสัญญา, ฟ้องร้อง, ฯลฯ
            $table->string('type'); // ขายฝาก, จำนอง
            $table->string('contract_image')->nullable();
            $table->timestamps();
        });

        // 5. การเงิน/ใบเสร็จ
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // เงินสด, โอน
            $table->enum('status', ['paid', 'pending'])->default('paid');
            // Polymorphic relation เพื่อผูกกับสัญญาเช่า หรือ ขายฝาก
            $table->unsignedBigInteger('payable_id');
            $table->string('payable_type'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('loan_contracts');
        Schema::dropIfExists('rental_contracts');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('customers');
    }
};