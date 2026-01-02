<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request) {
        $query = Property::query();
        
        // ค้นหาจาก code, name, address
        if($search = $request->search){
            $query->where('code', 'LIKE', "%$search%")
                  ->orWhere('name', 'LIKE', "%$search%")
                  ->orWhere('address', 'LIKE', "%$search%");
        }
        
        $properties = $query->latest()->paginate(25);
        return view('properties.index', compact('properties'));
    }

    public function create() {
        return view('properties.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'moo' => 'nullable|string|max:50',
            'subdistrict' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'water_meter' => 'nullable|string|max:50',
            'electric_meter' => 'nullable|string|max:50',
            'type' => 'required|string|max:50',
            'gps' => 'nullable|string|max:100',
            'doc_image' => 'nullable|image|max:5120',
            'status' => 'required|in:vacant,rented',
        ]);

        // Upload Image
        if($request->hasFile('doc_image')){
            $validated['doc_image'] = $request->file('doc_image')->store('properties', 'public');
        }

        // Auto Generate Code: PROP-001, PROP-002, ...
        $lastProperty = Property::latest('id')->first();
        $nextNumber = $lastProperty ? $lastProperty->id + 1 : 1;
        $validated['code'] = 'PROP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Property::create($validated);
        return redirect()->route('properties.index')->with('success', '✅ เพิ่มบ้านเช่าสำเร็จ (รหัส: ' . $validated['code'] . ')');
    }

    public function edit(Property $property) {
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property) {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'moo' => 'nullable|string|max:50',
            'subdistrict' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'water_meter' => 'nullable|string|max:50',
            'electric_meter' => 'nullable|string|max:50',
            'type' => 'required|string|max:50',
            'gps' => 'nullable|string|max:100',
            'doc_image' => 'nullable|image|max:5120',
            'status' => 'required|in:vacant,rented',
        ]);

        // Upload Image ถ้ามี
        if($request->hasFile('doc_image')){
            if($property->doc_image){
                \Storage::disk('public')->delete($property->doc_image);
            }
            $validated['doc_image'] = $request->file('doc_image')->store('properties', 'public');
        }

        $property->update($validated);
        return redirect()->route('properties.index')->with('success', '✅ แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(Property $property) {
        // ลบรูปภาพ
        if($property->doc_image){
            \Storage::disk('public')->delete($property->doc_image);
        }
        
        $property->delete();
        return back()->with('success', '✅ ลบข้อมูลสำเร็จ');
    }
}
