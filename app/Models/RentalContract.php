<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalContract extends Model
{
    use HasFactory;

    protected $guarded = [];

    // ความสัมพันธ์: สัญญานี้เป็นของลูกค้าคนไหน
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // ความสัมพันธ์: สัญญานี้เป็นของบ้านหลังไหน
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // ความสัมพันธ์: ประวัติการจ่ายเงิน (Polymorphic)
    // เชื่อมกับตาราง transactions โดยใช้ชื่อฟังก์ชัน 'payable'
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'payable');
    }

    // ความสัมพันธ์: ใบแจ้งหนี้ (Polymorphic)
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'billable');
    }

    // Helper: เช็คว่าสัญญาใช้งานอยู่ในเดือนนั้นหรือไม่
    public function isActiveInMonth($month, $year)
    {
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = $this->end_date ? \Carbon\Carbon::parse($this->end_date) : \Carbon\Carbon::now()->addYears(100);
        $monthDate = \Carbon\Carbon::create($year, $month, 1);
        
        return $monthDate->between($startDate, $endDate);
    }
}