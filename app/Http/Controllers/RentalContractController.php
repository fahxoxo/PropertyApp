<?php

namespace App\Http\Controllers;

use App\Models\RentalContract;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RentalContractController extends Controller
{
    public function index(Request $request)
    {
        $query = RentalContract::with(['customer', 'property']);
        
        // ค้นหา
        if ($s = $request->search) {
            $query->where('code', 'LIKE', "%$s%")
                  ->orWhereHas('customer', function($q) use ($s) {
                      $q->where('first_name', 'LIKE', "%$s%")
                        ->orWhere('last_name', 'LIKE', "%$s%")
                        ->orWhere('phone', 'LIKE', "%$s%");
                  })
                  ->orWhereHas('property', function($q) use ($s) {
                      $q->where('name', 'LIKE', "%$s%");
                  });
        }

        $rentals = $query->latest()->paginate(25);
        return view('rentals.index', compact('rentals'));
    }

    public function create()
    {
        $customers = Customer::all();
        $properties = Property::where('status', 'vacant')->get();
        return view('rentals.create', compact('customers', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'property_id' => 'required|exists:properties,id',
            'deposit' => 'required|numeric|min:0',
            'advance_rent' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'contract_image' => 'nullable|image|max:5120',
            'status' => 'required|in:active,closed',
        ]);

        // Upload Image
        if($request->hasFile('contract_image')){
            $validated['contract_image'] = $request->file('contract_image')->store('rentals', 'public');
        }

        // Auto Generate Code
        $lastRental = RentalContract::latest('id')->first();
        $nextNumber = $lastRental ? $lastRental->id + 1 : 1;
        $validated['code'] = 'RENT-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $rental = RentalContract::create($validated);

        // อัปเดตสถานะบ้านเป็น rented
        Property::find($request->property_id)->update(['status' => 'rented']);

        return redirect()->route('rentals.index')->with('success', '✅ เพิ่มสัญญาเช่าสำเร็จ (รหัส: ' . $validated['code'] . ')');
    }

    public function edit(RentalContract $rental)
    {
        $customers = Customer::all();
        $properties = Property::all();
        return view('rentals.edit', compact('rental', 'customers', 'properties'));
    }

    public function update(Request $request, RentalContract $rental)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'property_id' => 'required|exists:properties,id',
            'deposit' => 'required|numeric|min:0',
            'advance_rent' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'contract_image' => 'nullable|image|max:5120',
            'status' => 'required|in:active,closed',
        ]);

        // Upload Image ถ้ามี
        if($request->hasFile('contract_image')){
            if($rental->contract_image){
                Storage::disk('public')->delete($rental->contract_image);
            }
            $validated['contract_image'] = $request->file('contract_image')->store('rentals', 'public');
        }

        $rental->update($validated);
        return redirect()->route('rentals.index')->with('success', '✅ แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(RentalContract $rental)
    {
        // ลบรูปภาพ
        if($rental->contract_image){
            Storage::disk('public')->delete($rental->contract_image);
        }
        
        // คืนสถานะบ้านเป็น vacant
        $rental->property->update(['status' => 'vacant']);
        
        $rental->delete();
        return back()->with('success', '✅ ลบข้อมูลสำเร็จ');
    }

    public function print(RentalContract $rental)
    {
        $rental->load('customer', 'property');
        return view('rentals.print', compact('rental'));
    }
}