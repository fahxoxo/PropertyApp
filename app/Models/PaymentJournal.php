<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentJournal extends Model
{
    protected $table = 'payment_journals';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_before' => 'decimal:2',
        'principal_after' => 'decimal:2',
        'outstanding_before' => 'decimal:2',
        'outstanding_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ความสัมพันธ์: ใบแจ้งหนี้
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // ความสัมพันธ์: รายการจ่ายเงิน
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Polymorphic relationship ไปยัง RentalContract หรือ LoanContract
    public function payable()
    {
        return $this->morphTo();
    }
}
