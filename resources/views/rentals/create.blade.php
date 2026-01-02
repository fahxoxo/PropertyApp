@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">➕ เพิ่มสัญญาเช่าใหม่</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('rentals.store') }}" method="POST" enctype="multipart/form-data" id="rentalForm">
                        @csrf

                        <!-- เลือกลูกค้า -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">ลูกค้า <span class="text-danger">*</span></label>
                            <select class="form-control @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" name="customer_id" required>
                                <option value="">-- เลือกลูกค้า --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->code }} - {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เลือกบ้านเช่า -->
                        <div class="mb-3">
                            <label for="property_id" class="form-label">บ้านเช่า <span class="text-danger">*</span></label>
                            <select class="form-control @error('property_id') is-invalid @enderror" 
                                    id="property_id" name="property_id" required onchange="updatePropertyPrice()">
                                <option value="">-- เลือกบ้านเช่า --</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" 
                                            data-price="{{ $property->price }}"
                                            {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->code }} - {{ $property->name }} (ราคา: {{ number_format($property->price, 2) }} ฿)
                                    </option>
                                @endforeach
                            </select>
                            @error('property_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- แสดงราคา -->
                        <div class="mb-3 card bg-light">
                            <div class="card-body">
                                <label class="form-label">ราคาเช่าต่อเดือน</label>
                                <h3 id="propertyPrice" class="text-success">0.00 ฿</h3>
                            </div>
                        </div>

                        <!-- ค่ามัดจำ -->
                        <div class="mb-3">
                            <label for="deposit" class="form-label">ค่ามัดจำ (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('deposit') is-invalid @enderror" 
                                   id="deposit" name="deposit" value="{{ old('deposit') }}" step="0.01" required>
                            @error('deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ค่าเช่าล่วงหน้า -->
                        <div class="mb-3">
                            <label for="advance_rent" class="form-label">ค่าเช่าล่วงหน้า (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('advance_rent') is-invalid @enderror" 
                                   id="advance_rent" name="advance_rent" value="{{ old('advance_rent') }}" step="0.01" required>
                            @error('advance_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- วันที่เริ่มสัญญา -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label">วันที่เริ่มสัญญา <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- อัพโหลดรูปไฟล์สัญญา -->
                        <div class="mb-3">
                            <label for="contract_image" class="form-label">รูปไฟล์สัญญาเช่า</label>
                            <input type="file" class="form-control @error('contract_image') is-invalid @enderror" 
                                   id="contract_image" name="contract_image" accept="image/*">
                            <small class="text-muted">รูปแบบ: JPG, PNG (ขนาดสูงสุด 5MB)</small>
                            @error('contract_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Preview รูป -->
                        <div class="mb-3" id="imagePreview" style="display:none;">
                            <label class="form-label">ตัวอย่างรูปภาพ</label>
                            <div>
                                <img id="previewImg" src="" alt="Preview" style="max-width: 300px; max-height: 300px;">
                            </div>
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>✅ ใช้งาน</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>⚫ ปิด</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('rentals.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-success btn-lg">💾 บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview รูปภาพ
document.getElementById('contract_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('previewImg').src = event.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// แสดงราคาอัตโนมัติ
function updatePropertyPrice() {
    const select = document.getElementById('property_id');
    const option = select.options[select.selectedIndex];
    const price = option.getAttribute('data-price');
    
    if(price) {
        document.getElementById('propertyPrice').textContent = parseFloat(price).toLocaleString('th-TH', {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ฿';
    } else {
        document.getElementById('propertyPrice').textContent = '0.00 ฿';
    }
}

// โหลดราคาเมื่อแบบฟอร์มเปิด
document.addEventListener('DOMContentLoaded', function() {
    updatePropertyPrice();
});
</script>
@endsection
