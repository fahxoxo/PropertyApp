<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request) {
        $query = Customer::query();
        
        // ค้นหาจาก code, phone, id_card
        if($search = $request->search){
            $query->where('code', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%")
                  ->orWhere('id_card', 'LIKE', "%$search%");
        }
        
        $customers = $query->latest()->paginate(25);
        return view('customers.index', compact('customers'));
    }

    public function create() {
        return view('customers.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|max:20|unique:customers',
            'id_card_image' => 'nullable|image|max:5120', // 5MB
        ]);

        // Upload Image
        if($request->hasFile('id_card_image')){
            $validated['id_card_image'] = $request->file('id_card_image')->store('customers', 'public');
        }

        // Auto Generate Code: CUS-001, CUS-002, ...
        $lastCustomer = Customer::latest('id')->first();
        $nextNumber = $lastCustomer ? $lastCustomer->id + 1 : 1;
        $validated['code'] = 'CUS-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', '✅ เพิ่มลูกค้าสำเร็จ (รหัส: ' . $validated['code'] . ')');
    }

    public function edit(Customer $customer) {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|max:20|unique:customers,id_card,' . $customer->id,
            'id_card_image' => 'nullable|image|max:5120',
        ]);

        // Upload Image ถ้ามี
        if($request->hasFile('id_card_image')){
            // ลบรูปเก่า
            if($customer->id_card_image){
                \Storage::disk('public')->delete($customer->id_card_image);
            }
            $validated['id_card_image'] = $request->file('id_card_image')->store('customers', 'public');
        }

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', '✅ แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(Customer $customer) {
        // ลบรูปภาพ
        if($customer->id_card_image){
            \Storage::disk('public')->delete($customer->id_card_image);
        }
        
        $customer->delete();
        return back()->with('success', '✅ ลบข้อมูลสำเร็จ');
    }
}