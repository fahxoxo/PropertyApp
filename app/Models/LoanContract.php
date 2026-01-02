<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanContract extends Model
{
    use HasFactory;

    protected $guarded = [];

    // ความสัมพันธ์: สัญญานี้เป็นของลูกค้าคนไหน
    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
        $startDate = \Carbon\Carbon::parse($this->contract_date);
        $endDate = \Carbon\Carbon::now()->addYears(100);
        
        if ($this->duration) {
            // แปลง duration เช่น "6 เดือน" หรือ "1 ปี" เป็นจำนวนเดือน
            if (strpos($this->duration, 'ปี') !== false) {
                $years = (int)$this->duration;
                $endDate = $startDate->copy()->addYears($years);
            } elseif (strpos($this->duration, 'เดือน') !== false) {
                $months = (int)$this->duration;
                $endDate = $startDate->copy()->addMonths($months);
            }
        }
        
        $monthDate = \Carbon\Carbon::create($year, $month, 1);
        
        return $monthDate->between($startDate, $endDate);
    }
    
    // Helper: เช็คว่าสัญญาใกล้หมดอายุหรือไม่ (ภายใน 60 วัน)
    public function isExpiringSoon()
    {
        // คำนวณวันหมดอายุตาม duration
        $expireDate = null;
        if ($this->duration == '6 เดือน') {
            $expireDate = \Carbon\Carbon::parse($this->contract_date)->addMonths(6);
        } elseif ($this->duration == '1 ปี') {
            $expireDate = \Carbon\Carbon::parse($this->contract_date)->addYear();
        } elseif ($this->duration == '2 ปี') {
            $expireDate = \Carbon\Carbon::parse($this->contract_date)->addYears(2);
        }

        if ($expireDate) {
            return \Carbon\Carbon::now()->diffInDays($expireDate, false) <= 60 && \Carbon\Carbon::now()->diffInDays($expireDate, false) >= 0;
        }
        return false;
    }

    /**
     * คำนวณดอกเบี้ยรายเดือนปัจจุบัน
     * สูตร: ดอกเบี้ย = เงินต้นคงเหลือ × (ดอกเบี้ย ร้อยละต่อเดือน / 100)
     */
    public function calculateMonthlyInterest()
    {
        return ($this->principal_remaining * $this->interest_rate) / 100;
    }

    /**
     * ลดเงินต้น
     * 
     * @param float $amount จำนวนเงินต้นที่ต้องการลด
     * @return float เงินต้นคงเหลือหลังจากลด
     */
    public function reducePrincipal(float $amount)
    {
        $this->principal_remaining = max(0, $this->principal_remaining - $amount);
        $this->save();
        return $this->principal_remaining;
    }

    /**
     * ดึงข้อมูลสรุปเงินกู้ปัจจุบัน
     */
    public function getSummary()
    {
        $monthlyInterest = $this->calculateMonthlyInterest();
        $totalPaid = $this->transactions()->where('payment_type', '!=', 'interest')->sum('principal_reduced');
        
        return [
            'principal' => $this->principal,
            'principal_remaining' => $this->principal_remaining,
            'principal_paid' => $totalPaid,
            'interest_rate' => $this->interest_rate,
            'monthly_interest' => $monthlyInterest,
            'interest_rate_per_annum' => $this->interest_rate * 12,
        ];
    }

    /**
     * เช็คว่าเงินกู้จ่ายหมดแล้วหรือไม่
     */
    public function isPaidOff()
    {
        return $this->principal_remaining <= 0;
    }
}