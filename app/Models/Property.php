<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $guarded = [];

    // ความสัมพันธ์: บ้าน 1 หลัง มีประวัติสัญญาเช่าได้หลายฉบับ
    public function rentalContracts()
    {
        return $this->hasMany(RentalContract::class);
    }

    // Helper: ดึงสัญญาเช่าปัจจุบัน (ที่ยัง Active อยู่)
    public function activeContract()
    {
        return $this->hasOne(RentalContract::class)->where('status', 'active')->latest();
    }
}