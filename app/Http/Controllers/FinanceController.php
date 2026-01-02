<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\RentalContract;
use App\Models\LoanContract;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use PDF;

class FinanceController extends Controller
{
    public function index(Request $request) {
        $query = Transaction::with('payable');
        
        // ค้นหา
        if($search = $request->search){
            $query->where('receipt_number', 'LIKE', "%$search%")
                  ->orWhereHasMorph('payable', [RentalContract::class, LoanContract::class], function($q) use ($search) {
                      $q->where('code', 'LIKE', "%$search%")
                        ->orWhereHas('customer', function($c) use ($search) {
                            $c->where('code', 'LIKE', "%$search%")
                              ->orWhere('first_name', 'LIKE', "%$search%")
                              ->orWhere('last_name', 'LIKE', "%$search%")
                              ->orWhere('phone', 'LIKE', "%$search%");
                        });
                  });
        }

        $transactions = $query->latest()->paginate(25);
        return view('finance.index', compact('transactions'));
    }

    public function create() {
        return view('finance.create');
    }

    public function search(Request $request) {
        $type = $request->search_type;
        $keyword = $request->search_keyword;
        $data = null;
        $searchType = null;

        if(!$type || !$keyword) {
            return back()->with('error', 'กรุณาเลือกประเภทและใส่รหัส')->withInput();
        }

        // ตรวจสอบ Property ก่อน (ไม่สามารถรับชำระจากบ้านเช่าได้)
        if($type === 'property') {
            return back()->with('error', '❌ ไม่สามารถรับชำระจากรหัสบ้านเช่าได้ โปรดค้นหาจากสัญญาเช่าหรือสัญญาขายฝาก')->withInput();
        }

        // ค้นหาตามประเภท
        if($type === 'rental') {
            $data = RentalContract::with('customer', 'property')->where('code', 'LIKE', "%$keyword%")->first();
            $searchType = 'rental';
        } else if($type === 'loan') {
            $data = LoanContract::with('customer')->where('code', 'LIKE', "%$keyword%")->first();
            $searchType = 'loan';
        } else if($type === 'customer') {
            $data = Customer::where('code', 'LIKE', "%$keyword%")->first();
            $searchType = 'customer';
        } else if($type === 'phone') {
            $data = Customer::where('phone', 'LIKE', "%$keyword%")->first();
            $searchType = 'customer';
        }

        if(!$data) {
            return back()->with('error', '❌ ไม่พบข้อมูลตามคำค้นหา: "' . $keyword . '"')->withInput();
        }

        // ถ้าเป็น customer ต้องหา rental หรือ loan ของลูกค้านั้น
        if($searchType === 'customer') {
            // ให้เลือกว่าต้องการรับชำระจากสัญญาเช่าหรือขายฝาก
            return view('finance.select_contract', compact('data'));
        }

        return view('finance.receipt_form', compact('data', 'searchType'));
    }


    /**
     * ค้นหาใบแจ้งหนี้ที่ค้างชำระเพื่อรับชำระ (เส้นทางใหม่)
     */
    public function searchInvoices(Request $request)
    {
        $keyword = $request->search_keyword;
        $invoices = Invoice::pending();

        // ค้นหาจากชื่อลูกค้า หรือเลขที่ใบแจ้งหนี้
        if ($keyword) {
            $invoices = $invoices->searchByCustomerOrInvoice($keyword);
        }

        // โหลด relationship และเรียงลำดับจากวันสิ้นสุดชำระ
        $invoices = $invoices->with(['billable' => function($q) {
            $q->with('customer');
        }])->orderBy('due_date', 'asc')->get();

        return view('finance.search_invoices', compact('invoices', 'keyword'));
    }

    /**
     * เลือกใบแจ้งหนี้เพื่อรับชำระ
     */
    public function selectInvoice(Invoice $invoice)
    {
        $invoice->load('billable');
        return redirect()->route('invoice.payment', $invoice)
                       ->with('info', 'รับชำระจากใบแจ้งหนี้เลขที่: ' . $invoice->invoice_number);
    }


    public function store(Request $request) {
        // Validation ต่างกันสำหรับ rental และ loan
        if($request->payable_type === 'App\Models\LoanContract') {
            $validated = $request->validate([
                'payable_type' => 'required',
                'payable_id' => 'required',
                'payment_date' => 'required|date',
                'interest' => 'nullable|numeric|min:0',
                'principal_paid' => 'nullable|numeric|min:0',
                'payment_type' => 'required|in:interest,principal,both',
                'payment_method' => 'required|in:เงินสด,โอน',
            ]);
            
            // ตรวจสอบว่าจ่ายเงินอย่างน้อย 1 บาท
            $totalAmount = (float)($validated['interest'] ?? 0) + (float)($validated['principal_paid'] ?? 0);
            if($totalAmount <= 0) {
                return back()->with('error', 'กรุณากรอกจำนวนดอกเบี้ยหรือเงินต้นอย่างน้อย 1 บาท')->withInput();
            }

            $validated['amount'] = $totalAmount;
        } else {
            $validated = $request->validate([
                'payable_type' => 'required',
                'payable_id' => 'required',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:เงินสด,โอน',
            ]);
            $validated['payment_type'] = 'interest'; // Default สำหรับ rental
            $validated['interest'] = $validated['amount'];
            $validated['principal_paid'] = 0;
        }

        // สร้างหมายเลขใบเสร็จ
        $lastTransaction = Transaction::latest('id')->first();
        $nextNumber = $lastTransaction ? $lastTransaction->id + 1 : 1;
        $validated['receipt_number'] = 'RCP-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $validated['status'] = 'paid';

        $transaction = Transaction::create($validated);

        // ถ้าเป็น Loan และจ่ายเงินต้น ต้องอัปเดต principal_remaining
        if($request->payable_type === 'App\Models\LoanContract' && 
           ($validated['payment_type'] === 'principal' || $validated['payment_type'] === 'both')) {
            $loan = LoanContract::find($validated['payable_id']);
            $loan->principal_remaining -= (float)$validated['principal_paid'];
            $loan->save();
        }

        return redirect()->route('finance.index')->with('success', '✅ บันทึกการชำระเงินสำเร็จ (ใบเสร็จ: ' . $validated['receipt_number'] . ')');
    }

    public function receipt(Transaction $transaction) {
        // โหลด payable relation ให้ถูกต้อง
        $transaction = Transaction::with([
            'payable' => function($query) {
                $query->with('customer', 'property');
            }
        ])->find($transaction->id);
        
        // ถ้า payable เป็น null ให้ redirect กลับ
        if (!$transaction || !$transaction->payable) {
            return redirect()->route('finance.index')->with('error', 'ไม่พบข้อมูลสัญญา');
        }
        
        // ส่งกลับ HTML receipt แทน PDF เพื่อให้ Thai แสดงได้อย่างถูกต้อง
        return view('finance.receipt_html', compact('transaction'));
    }

    public function revenue(Request $request) {
        $year = $request->year ?? date('Y');
        
        // รายรับเดือนนี้
        $currentMonthIncome = Transaction::whereMonth('payment_date', now()->month)
                                          ->whereYear('payment_date', now()->year)
                                          ->sum('amount');
        
        // รายรับปีนี้
        $currentYearIncome = Transaction::whereYear('payment_date', now()->year)
                                         ->sum('amount');
        
        // รายรับปีที่เลือก
        $selectedYearIncome = Transaction::whereYear('payment_date', $year)
                                          ->sum('amount');
        
        // รายรับรายเดือน (ปีที่เลือก)
        $monthlyIncome = [];
        for($m = 1; $m <= 12; $m++) {
            $amount = Transaction::whereMonth('payment_date', $m)
                                 ->whereYear('payment_date', $year)
                                 ->sum('amount');
            $count = Transaction::whereMonth('payment_date', $m)
                                ->whereYear('payment_date', $year)
                                ->count();
            $monthlyIncome[$m] = [
                'income' => $amount,
                'count' => $count
            ];
        }

        // รายการทั้งหมด
        $transactions = Transaction::whereYear('payment_date', $year)
                                   ->with('payable')
                                   ->latest()
                                   ->paginate(25);

        return view('finance.revenue', compact('year', 'currentMonthIncome', 'currentYearIncome', 'selectedYearIncome', 'monthlyIncome', 'transactions'));
    }

    public function destroy(Transaction $transaction) {
        $transaction->delete();
        return back()->with('success', '✅ ลบข้อมูลสำเร็จ');
    }

    /**
     * แสดงฟอร์มลดต้น
     */
    public function reducePrincipalForm()
    {
        return view('finance.reduce-principal');
    }

    /**
     * ดึงรายชื่อสัญญาเงินกู้ที่อยู่ในสถานะ "อยู่ในสัญญา"
     */
    public function getActiveLoans(Request $request)
    {
        $loans = LoanContract::where('status', 'อยู่ในสัญญา')
                            ->with('customer')
                            ->get()
                            ->map(function($loan) {
                                return [
                                    'id' => $loan->id,
                                    'code' => $loan->code,
                                    'customer' => $loan->customer->first_name . ' ' . $loan->customer->last_name,
                                    'type' => $loan->type === 'loan_mortgage' ? 'จำนอง' : 'ขายฝาก',
                                    'principal_remaining' => $loan->principal_remaining,
                                    'interest_rate' => $loan->interest_rate,
                                    'monthly_interest' => round(($loan->principal_remaining * $loan->interest_rate) / 100, 2)
                                ];
                            });

        return response()->json($loans);
    }

    /**
     * บันทึกการลดต้น
     */
    public function reducePrincipal(Request $request)
    {
        $validated = $request->validate([
            'loan_contract_id' => 'required|exists:loan_contracts,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $loanContract = LoanContract::find($validated['loan_contract_id']);

            if (!$loanContract || $loanContract->status !== 'อยู่ในสัญญา') {
                return back()->with('error', '❌ ไม่พบสัญญาหรือสัญญานี้ไม่อยู่ในสถานะ "อยู่ในสัญญา"')->withInput();
            }

            if ($validated['amount'] > $loanContract->principal_remaining) {
                return back()->with('error', '❌ จำนวนเงินลดต้นเกินกว่ายอดเงินต้นคงเหลือ')->withInput();
            }

            // ลดเงินต้น
            $principalBefore = $loanContract->principal_remaining;
            $loanContract->principal_remaining -= $validated['amount'];
            $loanContract->save();

            // อัพเดท principal_at_billing ในใบแจ้งหนี้ที่เกี่ยวข้อง
            Invoice::where('billable_id', $loanContract->id)
                   ->where('billable_type', LoanContract::class)
                   ->update(['principal_at_billing' => $loanContract->principal_remaining]);

            // บันทึก Transaction
            Transaction::create([
                'receipt_number' => 'RDP-' . time(),
                'invoice_id' => null,
                'payable_id' => $loanContract->id,
                'payable_type' => LoanContract::class,
                'payment_date' => now()->toDateString(),
                'amount' => $validated['amount'],
                'payment_method' => 'principal_reduction',
                'principal_paid' => $validated['amount'],
                'interest_paid' => 0,
                'principal_reduced' => $validated['amount'],
                'payment_type' => 'principal',
                'outstanding_balance_before' => 0,
                'outstanding_balance_after' => 0,
                'interest' => 0,
                'status' => 'paid',
                'notes' => 'ลดเงินต้นโดยตรง'
            ]);

            return redirect()->route('finance.index')
                          ->with('success', "✅ ลดเงินต้นสำเร็จ\n" .
                              "สัญญา: " . $loanContract->code . "\n" .
                              "เงินต้นก่อน: " . number_format($principalBefore, 2) . " บาท\n" .
                              "ลดเงินต้น: " . number_format($validated['amount'], 2) . " บาท\n" .
                              "เงินต้นหลังจาก: " . number_format($loanContract->principal_remaining, 2) . " บาท");

        } catch (\Exception $e) {
            \Log::error('Reduce principal error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', '❌ เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }
}