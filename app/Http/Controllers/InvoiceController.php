<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RentalContract;
use App\Models\LoanContract;
use App\Services\PaymentAllocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $invoices = Invoice::where('month', $month)
                           ->where('year', $year)
                           ->with([
                               'billable' => function($query) {
                                   // Load customer สำหรับทั้ง RentalContract และ LoanContract
                                   $query->with('customer');
                                   // เพิ่ม property สำหรับ RentalContract ผ่าน conditional
                               }
                           ])
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        return view('invoice.index', compact('invoices', 'month', 'year'));
    }

    public function create()
    {
        return view('invoice.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:rental,loan_sale,loan_mortgage,loan',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        $count = 0;

        if ($validated['type'] === 'rental') {
            // สร้างใบแจ้งหนี้สำหรับบ้านเช่าทั้งหมดที่ใช้งานอยู่
            $rentals = RentalContract::where('status', 'active')
                                    ->with('property', 'customer')
                                    ->get();

            foreach ($rentals as $rental) {
                // ตรวจสอบว่าในเดือนนี้สัญญายังใช้งานอยู่
                if ($rental->isActiveInMonth($validated['month'], $validated['year'])) {
                    $invoiceNumber = $this->generateInvoiceNumber($validated['type'], $validated['month'], $validated['year']);
                    
                    // ดึงยอดค้างชำระจากเดือนก่อนมา
                    $previousOutstanding = $this->getPreviousOutstandingBalance($rental, $validated['month'], $validated['year']);
                    
                    // ยอดรวมที่ต้องจ่าย = ค่าเช่าเดือนนี้ + ยอดค้างชำระเดือนก่อน
                    $rentalAmount = $rental->property->price;
                    $totalDue = $rentalAmount + $previousOutstanding;
                    
                    Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'month' => $validated['month'],
                        'year' => $validated['year'],
                        'type' => 'rental',
                        'billable_id' => $rental->id,
                        'billable_type' => RentalContract::class,
                        'amount' => $totalDue,
                        'outstanding_balance' => $totalDue,
                        'paid_amount' => 0,
                        'due_date' => Carbon::create($validated['year'], $validated['month'], 5),
                        'status' => 'issued',
                    ]);
                    $count++;
                }
            }
        } else {
            // สร้างใบแจ้งหนี้สำหรับสัญญากู้ยืม
            $loanType = match($validated['type']) {
                'loan_sale' => 'ขายฝาก',
                'loan_mortgage' => 'จำนอง',
                'loan' => 'เงินกู้',
            };

            $loans = LoanContract::where('type', $loanType)
                                ->with('customer')
                                ->get();

            foreach ($loans as $loan) {
                if ($loan->isActiveInMonth($validated['month'], $validated['year'])) {
                    $invoiceNumber = $this->generateInvoiceNumber($validated['type'], $validated['month'], $validated['year']);
                    
                    // คำนวณดอกเบี้ยรายเดือน
                    // interest_rate เก็บเป็นร้อยละ ต่อ 1 เดือน เช่น 1.0 = 1% ต่อเดือน
                    // สูตร: ดอกเบี้ย = เงินต้น × (ดอกเบี้ย ร้อยละต่อเดือน / 100)
                    $monthlyInterest = ($loan->principal_remaining * $loan->interest_rate) / 100;
                    
                    // ดึงยอดค้างชำระจากเดือนก่อนมา
                    $previousOutstanding = $this->getPreviousOutstandingBalance($loan, $validated['month'], $validated['year']);
                    
                    // ยอดรวมที่ต้องจ่าย = ดอกเบี้ยเดือนนี้ + ยอดค้างชำระเดือนก่อน
                    $totalDue = $monthlyInterest + $previousOutstanding;

                    Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'month' => $validated['month'],
                        'year' => $validated['year'],
                        'type' => $validated['type'],
                        'billable_id' => $loan->id,
                        'billable_type' => LoanContract::class,
                        'amount' => $totalDue,
                        'outstanding_balance' => $totalDue,
                        'paid_amount' => 0,
                        'interest_amount' => $monthlyInterest,
                        'principal_at_billing' => $loan->principal_remaining,
                        'due_date' => Carbon::create($validated['year'], $validated['month'], 5),
                        'status' => 'issued',
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('invoice.index', [
            'month' => $validated['month'],
            'year' => $validated['year']
        ])->with('success', "✅ สร้างใบแจ้งหนี้สำเร็จ ($count ใบ)");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('billable');
        return view('invoice.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('invoice.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,issued,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoice.show', $invoice)
                       ->with('success', '✅ แก้ไขใบแจ้งหนี้สำเร็จ');
    }

    public function destroy(Invoice $invoice)
    {
        $month = $invoice->month;
        $year = $invoice->year;
        
        $invoice->delete();

        return redirect()->route('invoice.index', [
            'month' => $month,
            'year' => $year
        ])->with('success', '✅ ลบใบแจ้งหนี้สำเร็จ');
    }

    /**
     * ฟอร์มสำหรับบันทึกการจ่ายเงิน
     */
    public function payment(Invoice $invoice)
    {
        $invoice->load('billable');
        return view('invoice.payment', compact('invoice'));
    }

    /**
     * บันทึกการจ่ายเงิน
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        // ตรวจสอบว่า invoice มีข้อมูล outstanding_balance
        if ($invoice->outstanding_balance === null || $invoice->outstanding_balance < 0) {
            return redirect()->back()
                           ->with('error', '❌ ข้อมูลใบแจ้งหนี้ผิดปกติ กรุณาตรวจสอบ')
                           ->withInput();
        }

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $invoice->outstanding_balance,
            ],
            'payment_method' => 'required|in:cash,transfer,cheque,other',
            'receipt_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $paymentService = new PaymentAllocationService();
            $transaction = $paymentService->processPayment(
                invoice: $invoice,
                amountPaid: (float)$validated['amount'],
                paymentMethod: $validated['payment_method'],
                receiptNumber: $validated['receipt_number']
            );

            if ($transaction) {
                // อัปเดต notes ใน transaction
                if ($validated['notes']) {
                    $transaction->update(['notes' => $validated['notes']]);
                }

                // ดึงสรุปการจัดสรร
                $summary = $paymentService->getPaymentSummary($invoice);
                
                // รีเฟรช invoice เพื่อดึงข้อมูลล่าสุด
                $invoice->refresh();

                return redirect()->route('invoice.paymentHistory', $invoice)
                               ->with('success', "✅ บันทึกการจ่ายเงินสำเร็จ\n" .
                                   "ยอดค้างชำระ: " . number_format($summary['outstanding_paid'], 2) . " บาท\n" .
                                   "ดอกเบี้ย: " . number_format($summary['interest_paid'], 2) . " บาท\n" .
                                   "เงินต้น: " . number_format($summary['principal_paid'], 2) . " บาท");
            } else {
                \Log::error('Payment recording failed', [
                    'invoice_id' => $invoice->id,
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method']
                ]);
                return redirect()->back()
                               ->with('error', '❌ ไม่สามารถบันทึกการจ่ายเงินได้')
                               ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Payment recording exception', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                           ->with('error', '❌ เกิดข้อผิดพลาด: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * ดูประวัติการจ่ายเงิน
     */
    public function paymentHistory(Invoice $invoice)
    {
        $paymentService = new PaymentAllocationService();
        $summary = $paymentService->getPaymentSummary($invoice);
        $transactions = $invoice->transactions()->latest()->get();

        return view('invoice.payment-history', compact('invoice', 'summary', 'transactions'));
    }

    private function generateInvoiceNumber($type, $month, $year)
    {
        $typePrefix = match($type) {
            'rental' => 'RNT',
            'loan_sale' => 'SL',
            'loan_mortgage' => 'MG',
            'loan' => 'LN',
        };

        $lastInvoice = Invoice::where('type', $type)
                             ->where('month', $month)
                             ->where('year', $year)
                             ->latest('id')
                             ->first();

        $nextNumber = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return "{$typePrefix}-{$year}{$month}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * ดึงยอดค้างชำระจากเดือนก่อนมา
     * 
     * @param Model $contract RentalContract หรือ LoanContract
     * @param int $month เดือนปัจจุบัน
     * @param int $year ปีปัจจุบัน
     * @return float ยอดค้างชำระจากเดือนก่อน
     */
    private function getPreviousOutstandingBalance($contract, $month, $year)
    {
        // คำนวณเดือน/ปีก่อนหน้า
        $previousMonth = $month === 1 ? 12 : $month - 1;
        $previousYear = $month === 1 ? $year - 1 : $year;

        // ค้นหาใบแจ้งหนี้ล่าสุดของเดือนก่อน
        $previousInvoice = Invoice::where('billable_id', $contract->id)
                                  ->where('billable_type', get_class($contract))
                                  ->where('month', $previousMonth)
                                  ->where('year', $previousYear)
                                  ->latest('created_at')
                                  ->first();

        // ถ้าไม่มีใบแจ้งหนี้เดือนก่อน หรือ จ่ายครบแล้ว ให้คืนค่า 0
        if (!$previousInvoice) {
            return 0;
        }

        // คืนค่าส่วนที่ยังค้างชำระ
        return $previousInvoice->outstanding_balance ?? 0;
    }
}


