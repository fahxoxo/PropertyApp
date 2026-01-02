<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // อนุญาตให้บันทึกข้อมูลได้ทุกฟิลด์
    protected $guarded = [];

    // ความสัมพันธ์: ลูกค้า 1 คน มีสัญญาเช่าได้หลายฉบับ
    public function rentals()
    {
        return $this->hasMany(RentalContract::class);
    }

    public function rentalContracts()
    {
        return $this->hasMany(RentalContract::class);
    }

    // ความสัมพันธ์: ลูกค้า 1 คน มีสัญญาขายฝาก/จำนองได้หลายฉบับ
    public function loans()
    {
        return $this->hasMany(LoanContract::class);
    }

    public function loanContracts()
    {
        return $this->hasMany(LoanContract::class);
    }

    // Helper: ดึงชื่อนามสกุลเต็ม (เรียกใช้ $customer->full_name)
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}