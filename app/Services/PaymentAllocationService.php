<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\PaymentJournal;
use App\Models\LoanContract;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    /**
     * จัดสรรการจ่ายเงินและบันทึก transaction
     * 
     * ลำดับการจัดสรร:
     * 1. ยอดค้างชำระจากเดือนก่อน (Outstanding Balance)
     * 2. ดอกเบี้ยของเดือนปัจจุบัน (Interest)
     * 3. เงินต้น (Principal) - เฉพาะเงินกู้
     * 
     * @param Invoice $invoice ใบแจ้งหนี้
     * @param float $amountPaid จำนวนเงินที่จ่าย
     * @param string $paymentMethod วิธีการจ่าย (เงินสด, โอน, etc)
     * @param string|null $receiptNumber หมายเลขใบเสร็จ
     * @return Transaction|null transaction ที่สร้าง หรือ null หากมีข้อผิดพลาด
     */
    public function processPayment(Invoice $invoice, float $amountPaid, string $paymentMethod, ?string $receiptNumber = null)
    {
        return DB::transaction(function () use ($invoice, $amountPaid, $paymentMethod, $receiptNumber) {
            // ตรวจสอบว่าเงินจำนวนนี้ไม่เกินกว่ายอดค้างชำระ
            if ($amountPaid > $invoice->outstanding_balance) {
                throw new \Exception("จำนวนเงินที่จ่ายเกินกว่ายอดค้างชำระ");
            }

            // เริ่มต้นค่าต่างๆ
            $remainingAmount = $amountPaid;
            $outstandingBefore = $invoice->outstanding_balance;
            $interestPaid = 0;
            $principalPaid = 0;
            $paymentJournals = []; // เก็บ payment journals ไว้สร้างหลังจาก transaction

            // เช็คว่าบิลนี้เป็นบิลเงินกู้หรือบ้านเช่า
            $isLoanInvoice = $invoice->billable instanceof LoanContract;
            
            // หากเป็นบ้านเช่า หรือเป็นบิลเงินกู้แต่ไม่มียอดค้างเดือนก่อน
            // ให้ถือว่ายอดค้างในบิลนี้คือยอดเรียกเก็บทั้งหมด
            $totalOutstanding = $outstandingBefore > 0 ? $outstandingBefore : $invoice->amount;
            
            // ขั้นตอนที่ 1: จ่ายยอดค้างชำระ (รวมทั้งค่าเช่าหรือดอกเบี้ย)
            if ($totalOutstanding > 0) {
                $outstandingPayment = min($remainingAmount, $totalOutstanding);
                $remainingAmount -= $outstandingPayment;
                
                // หากยอดค้างมาจากเดือนก่อน ให้แยกเป็น outstanding
                // หากยอดค้างคือบิลใหม่ ให้แยกเป็นดอกเบี้ย (สำหรับเงินกู้) หรือเช่าไป
                if ($outstandingBefore > 0) {
                    // เป็นยอดค้างจากเดือนก่อน
                    $paymentJournals[] = [
                        'invoice_id' => $invoice->id,
                        'payable_id' => $invoice->billable_id,
                        'payable_type' => $invoice->billable_type,
                        'amount' => $outstandingPayment,
                        'allocation_type' => 'outstanding',
                        'principal_before' => $isLoanInvoice ? $invoice->billable->principal_remaining : null,
                        'principal_after' => $isLoanInvoice ? $invoice->billable->principal_remaining : null,
                        'outstanding_before' => $outstandingBefore,
                        'outstanding_after' => max(0, $outstandingBefore - $outstandingPayment),
                        'description' => $this->getAllocationDescription('outstanding'),
                    ];
                } else if ($isLoanInvoice && $invoice->interest_amount > 0) {
                    // เป็นบิลเงินกู้ใหม่ - จ่ายดอกเบี้ย
                    $interestToPay = min($outstandingPayment, $invoice->interest_amount);
                    $interestPaid = $interestToPay;
                    
                    $paymentJournals[] = [
                        'invoice_id' => $invoice->id,
                        'payable_id' => $invoice->billable_id,
                        'payable_type' => $invoice->billable_type,
                        'amount' => $interestToPay,
                        'allocation_type' => 'interest',
                        'principal_before' => $invoice->billable->principal_remaining,
                        'principal_after' => $invoice->billable->principal_remaining,
                        'outstanding_before' => $invoice->amount,
                        'outstanding_after' => $invoice->amount - $interestToPay,
                        'description' => $this->getAllocationDescription('interest'),
                    ];
                    
                    // เงินที่เหลือจะไปจ่ายเงินต้น
                    $remainingAmount = $outstandingPayment - $interestToPay;
                } else {
                    // เป็นบิลบ้านเช่า - จ่ายค่าเช่า
                    $paymentJournals[] = [
                        'invoice_id' => $invoice->id,
                        'payable_id' => $invoice->billable_id,
                        'payable_type' => $invoice->billable_type,
                        'amount' => $outstandingPayment,
                        'allocation_type' => 'outstanding',
                        'principal_before' => null,
                        'principal_after' => null,
                        'outstanding_before' => $invoice->amount,
                        'outstanding_after' => $invoice->amount - $outstandingPayment,
                        'description' => $this->getAllocationDescription('outstanding'),
                    ];
                }
            }

            // ขั้นตอนที่ 2: ถ้ายังมีเงินเหลือ และเป็นบิลเงินกู้ ให้จ่ายดอกเบี้ย
            if ($remainingAmount > 0 && $isLoanInvoice && $invoice->interest_amount > 0 && $interestPaid == 0) {
                $interestPayment = min($remainingAmount, $invoice->interest_amount - $interestPaid);
                $interestPaid += $interestPayment;
                $remainingAmount -= $interestPayment;

                $paymentJournals[] = [
                    'invoice_id' => $invoice->id,
                    'payable_id' => $invoice->billable_id,
                    'payable_type' => $invoice->billable_type,
                    'amount' => $interestPayment,
                    'allocation_type' => 'interest',
                    'principal_before' => $invoice->billable->principal_remaining,
                    'principal_after' => $invoice->billable->principal_remaining,
                    'outstanding_before' => max(0, $outstandingBefore - min($amountPaid, $totalOutstanding)),
                    'outstanding_after' => max(0, $outstandingBefore - $amountPaid),
                    'description' => $this->getAllocationDescription('interest'),
                ];
            }

            // ขั้นตอนที่ 3: จ่ายเงินต้น (สำหรับสัญญาเงินกู้เท่านั้น)
            $principalBefore = null;
            $principalAfter = null;
            
            if ($remainingAmount > 0 && $invoice->billable instanceof LoanContract) {
                $loanContract = $invoice->billable;
                $principalBefore = $loanContract->principal_remaining;
                $principalPayment = $remainingAmount;
                
                // อัปเดตเงินต้นคงเหลือ
                $principalAfter = $principalBefore - $principalPayment;
                $loanContract->principal_remaining = $principalAfter;
                $loanContract->save();
                
                $principalPaid = $principalPayment;
                $remainingAmount = 0;

                $paymentJournals[] = [
                    'invoice_id' => $invoice->id,
                    'payable_id' => $invoice->billable_id,
                    'payable_type' => $invoice->billable_type,
                    'amount' => $principalPayment,
                    'allocation_type' => 'principal',
                    'principal_before' => $principalBefore,
                    'principal_after' => $principalAfter,
                    'outstanding_before' => 0,
                    'outstanding_after' => 0,
                    'description' => $this->getAllocationDescription('principal'),
                ];
            }

            // สร้าง transaction record FIRST
            $transaction = Transaction::create([
                'receipt_number' => $receiptNumber ?? $this->generateReceiptNumber(),
                'invoice_id' => $invoice->id,
                'payable_id' => $invoice->billable_id,
                'payable_type' => $invoice->billable_type,
                'payment_date' => now()->toDateString(),
                'amount' => $amountPaid,
                'payment_method' => $paymentMethod,
                'interest' => $invoice->interest_amount ?? 0,
                'principal_paid' => $principalPaid,
                'interest_paid' => $interestPaid,
                'principal_reduced' => $principalPaid,
                'payment_type' => $this->determinePaymentType($interestPaid, $principalPaid),
                'outstanding_balance_before' => $outstandingBefore,
                'outstanding_balance_after' => max(0, $outstandingBefore - $amountPaid),
                'status' => 'paid',
            ]);
            
            \Log::info('Transaction created', [
                'transaction_id' => $transaction->id,
                'invoice_id' => $invoice->id,
                'amount' => $amountPaid
            ]);

            // สร้าง payment journals หลังจาก transaction
            foreach ($paymentJournals as $journalData) {
                $journalData['transaction_id'] = $transaction->id;
                PaymentJournal::create($journalData);
            }

            // อัปเดต invoice: paid_amount และ outstanding_balance
            $invoice->paid_amount += $amountPaid;
            $invoice->outstanding_balance = max(0, $invoice->outstanding_balance - $amountPaid);
            $invoice->save();

            return $transaction;
        });
    }

    /**
     * สร้าง payment journal record
     */
    private function createPaymentJournal(
        Invoice $invoice,
        string $allocationType,
        float $amount,
        float $outstandingBefore,
        float $outstandingAfter,
        ?float $principalBefore = null,
        ?float $principalAfter = null
    ) {
        PaymentJournal::create([
            'invoice_id' => $invoice->id,
            'payable_id' => $invoice->billable_id,
            'payable_type' => $invoice->billable_type,
            'amount' => $amount,
            'allocation_type' => $allocationType,
            'principal_before' => $principalBefore,
            'principal_after' => $principalAfter,
            'outstanding_before' => $outstandingBefore,
            'outstanding_after' => $outstandingAfter,
            'description' => $this->getAllocationDescription($allocationType),
        ]);
    }

    /**
     * สร้างหมายเลขใบเสร็จอัตโนมัติ
     */
    private function generateReceiptNumber(): string
    {
        $date = now();
        $prefix = 'RCP-' . $date->format('YmdHis');
        $latestReceipt = Transaction::where('receipt_number', 'like', $prefix . '%')
                                   ->latest('receipt_number')
                                   ->first();
        
        $sequence = $latestReceipt ? (int)substr($latestReceipt->receipt_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * กำหนดประเภทการจ่ายเงิน
     */
    private function determinePaymentType(float $interestPaid, float $principalPaid): string
    {
        if ($interestPaid > 0 && $principalPaid > 0) {
            return 'both';
        } elseif ($principalPaid > 0) {
            return 'principal';
        } else {
            return 'interest';
        }
    }

    /**
     * สร้างคำอธิบายการจัดสรร
     */
    private function getAllocationDescription(string $allocationType): string
    {
        return match($allocationType) {
            'outstanding' => 'จ่ายยอดค้างชำระจากเดือนก่อน',
            'interest' => 'จ่ายดอกเบี้ยของเดือนปัจจุบัน',
            'principal' => 'จ่ายลดเงินต้น',
            default => 'จัดสรรเงิน',
        };
    }

    /**
     * ดึงสรุปการจัดสรรเงิน
     */
    public function getPaymentSummary(Invoice $invoice): array
    {
        $journals = PaymentJournal::where('invoice_id', $invoice->id)->get();
        
        return [
            'outstanding_paid' => $journals->where('allocation_type', 'outstanding')->sum('amount'),
            'interest_paid' => $journals->where('allocation_type', 'interest')->sum('amount'),
            'principal_paid' => $journals->where('allocation_type', 'principal')->sum('amount'),
            'total_paid' => $journals->sum('amount'),
            'journals' => $journals,
        ];
    }
}
