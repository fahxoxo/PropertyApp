<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'principal_at_billing' => 'decimal:2',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Polymorphic relationship ไปยัง RentalContract หรือ LoanContract
    public function billable()
    {
        return $this->morphTo();
    }

    // ความสัมพันธ์: รายการจ่ายเงิน
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ความสัมพันธ์: บันทึกการจัดสรรเงิน
    public function paymentJournals()
    {
        return $this->hasMany(PaymentJournal::class);
    }

    /**
     * เช็คว่าใบแจ้งหนี้นี้จ่ายครบแล้วหรือไม่
     */
    public function isFulPaid()
    {
        return $this->outstanding_balance == 0;
    }

    /**
     * คำนวณยอดค้างชำระปัจจุบัน
     * = ยอดเรียกเก็บ - ยอดจ่ายแล้ว
     */
    public function calculateOutstanding()
    {
        return $this->amount - $this->paid_amount;
    }

    /**
     * อัปเดตยอดจ่ายแล้ว และ ยอดค้างชำระ
     */
    public function recordPayment($amountPaid)
    {
        $this->paid_amount += $amountPaid;
        $this->outstanding_balance = $this->amount - $this->paid_amount;
        $this->save();
    }

    /**
     * Scope: ดึงเฉพาะใบแจ้งหนี้ที่ยังค้างชำระ
     * สถานะ: issued, draft, overdue และมียอดค้าง
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['issued', 'draft', 'overdue'])
                    ->where('outstanding_balance', '>', 0);
    }

    /**
     * Scope: ดึงใบแจ้งหนี้ที่จ่ายแล้วเสร็จ
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid')
                    ->where('outstanding_balance', '=', 0);
    }

    /**
     * Scope: ดึงใบแจ้งหนี้ที่เลยกำหนดชำระ
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->where('outstanding_balance', '>', 0);
    }

    /**
     * Scope: ค้นหาจากชื่อลูกค้า รหัสลูกค้า เลขที่บิล หรือรหัสสัญญา
     */
    public function scopeSearchByCustomerOrInvoice($query, $keyword)
    {
        return $query->where('invoice_number', 'LIKE', "%$keyword%")
                    ->orWhereHasMorph('billable', 
                        [RentalContract::class, LoanContract::class],
                        function($q) use ($keyword) {
                            // ค้นหาจากรหัสสัญญา (RENT-0001, LOAN-0001)
                            $q->where('code', 'LIKE', "%$keyword%")
                              ->orWhereHas('customer', function($c) use ($keyword) {
                                // ค้นหาจากชื่อลูกค้า รหัสลูกค้า เบอร์โทร
                                $c->where('first_name', 'LIKE', "%$keyword%")
                                  ->orWhere('last_name', 'LIKE', "%$keyword%")
                                  ->orWhere('code', 'LIKE', "%$keyword%")
                                  ->orWhere('phone', 'LIKE', "%$keyword%");
                            });
                        }
                    );
    }

    /**
     * เช็คว่าใบแจ้งหนี้เลยกำหนดชำระหรือไม่
     */
    public function isOverdueNow()
    {
        return now()->isAfter($this->due_date) && $this->outstanding_balance > 0;
    }

    /**
     * อัปเดตสถานะจาก outstanding_balance
     */
    public function updateStatusByOutstanding()
    {
        if ($this->outstanding_balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->isOverdueNow()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'issued';
        }
        $this->save();
    }
}
