<?php

namespace App\Http\Controllers;

use App\Models\LoanContract;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanContractController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanContract::with('customer');

        // ค้นหา
        if ($s = $request->search) {
            $query->where('code', 'LIKE', "%$s%")
                  ->orWhereHas('customer', function($q) use ($s) {
                      $q->where('code', 'LIKE', "%$s%")
                        ->orWhere('first_name', 'LIKE', "%$s%")
                        ->orWhere('last_name', 'LIKE', "%$s%")
                        ->orWhere('phone', 'LIKE', "%$s%");
                  });
        }

        $loans = $query->latest()->paginate(25);
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('loans.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'interest_rate' => 'required|numeric|min:0',
            'principal' => 'required|numeric|min:0',
            'duration' => 'required|in:6 เดือน,1 ปี,2 ปี',
            'contract_date' => 'required|date',
            'status' => 'required|in:อยู่ในสัญญา,ฟ้องร้อง,ต่อสัญญา,ไถ่ถอน,ทรัพย์หลุด',
            'type' => 'required|in:ขายฝาก,จำนอง',
            'contract_image' => 'nullable|image|max:5120',
        ]);

        // Upload Image
        if($request->hasFile('contract_image')){
            $validated['contract_image'] = $request->file('contract_image')->store('loans', 'public');
        }

        // Auto Generate Code
        $lastLoan = LoanContract::latest('id')->first();
        $nextNumber = $lastLoan ? $lastLoan->id + 1 : 1;
        $validated['code'] = 'LOAN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $validated['principal_remaining'] = $validated['principal']; // ตั้งค่า principal_remaining เท่ากับ principal เริ่มต้น

        LoanContract::create($validated);
        return redirect()->route('loans.index')->with('success', '✅ เพิ่มสัญญาขายฝาก/จำนองสำเร็จ (รหัส: ' . $validated['code'] . ')');
    }

    public function edit(LoanContract $loan)
    {
        $customers = Customer::all();
        return view('loans.edit', compact('loan', 'customers'));
    }

    public function update(Request $request, LoanContract $loan)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'interest_rate' => 'required|numeric|min:0',
            'principal' => 'required|numeric|min:0',
            'duration' => 'required|in:6 เดือน,1 ปี,2 ปี',
            'contract_date' => 'required|date',
            'status' => 'required|in:อยู่ในสัญญา,ฟ้องร้อง,ต่อสัญญา,ไถ่ถอน,ทรัพย์หลุด',
            'type' => 'required|in:ขายฝาก,จำนอง',
            'contract_image' => 'nullable|image|max:5120',
        ]);

        // Upload Image ถ้ามี
        if($request->hasFile('contract_image')){
            if($loan->contract_image){
                Storage::disk('public')->delete($loan->contract_image);
            }
            $validated['contract_image'] = $request->file('contract_image')->store('loans', 'public');
        }

        $loan->update($validated);
        return redirect()->route('loans.index')->with('success', '✅ แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(LoanContract $loan)
    {
        // ลบรูปภาพ
        if($loan->contract_image){
            Storage::disk('public')->delete($loan->contract_image);
        }
        
        $loan->delete();
        return back()->with('success', '✅ ลบข้อมูลสำเร็จ');
    }
}