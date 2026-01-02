<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest' => 'decimal:2',
        'principal_paid' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'principal_reduced' => 'decimal:2',
        'outstanding_balance_before' => 'decimal:2',
        'outstanding_balance_after' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ความสัมพันธ์แบบ Polymorphic (MorphTo)
    // จะคืนค่าเป็น Model RentalContract หรือ LoanContract อัตโนมัติ แล้วแต่ว่าใบเสร็จนี้จ่ายให้สัญญาประเภทไหน
    public function payable()
    {
        return $this->morphTo();
    }

    // ความสัมพันธ์: ใบแจ้งหนี้
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // ความสัมพันธ์: บันทึกการจัดสรรเงิน
    public function paymentJournals()
    {
        return $this->hasMany(PaymentJournal::class);
    }
}