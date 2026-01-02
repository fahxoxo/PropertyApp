@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">➕ เพิ่มสัญญาขายฝาก/จำนองใหม่</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- เลือกลูกค้า -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
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

                        <!-- ดอกเบี้ยร้อยละ -->
                        <div class="mb-3">
                            <label for="interest_rate" class="form-label">จำนวนดอกเบี้ย (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                                   id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" 
                                   step="0.01" min="0" required>
                            @error('interest_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เงินต้น -->
                        <div class="mb-3">
                            <label for="principal" class="form-label">จำนวนเงินที่เอาไป (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('principal') is-invalid @enderror" 
                                   id="principal" name="principal" value="{{ old('principal') }}" 
                                   step="0.01" min="0" required>
                            @error('principal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ระยะเวลา -->
                        <div class="mb-3">
                            <label for="duration" class="form-label">ระยะเวลาที่ทำสัญญา <span class="text-danger">*</span></label>
                            <select class="form-control @error('duration') is-invalid @enderror" 
                                    id="duration" name="duration" required>
                                <option value="">-- เลือกระยะเวลา --</option>
                                <option value="6 เดือน" {{ old('duration') === '6 เดือน' ? 'selected' : '' }}>6 เดือน</option>
                                <option value="1 ปี" {{ old('duration') === '1 ปี' ? 'selected' : '' }}>1 ปี</option>
                                <option value="2 ปี" {{ old('duration') === '2 ปี' ? 'selected' : '' }}>2 ปี</option>
                            </select>
                            @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- วันที่ทำสัญญา -->
                        <div class="mb-3">
                            <label for="contract_date" class="form-label">วันที่ทำสัญญา <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('contract_date') is-invalid @enderror" 
                                   id="contract_date" name="contract_date" value="{{ old('contract_date', date('Y-m-d')) }}" required>
                            @error('contract_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- สถานะสัญญา -->
                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะสัญญา <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- เลือกสถานะ --</option>
                                <option value="อยู่ในสัญญา" {{ old('status') === 'อยู่ในสัญญา' ? 'selected' : '' }}>✅ อยู่ในสัญญา</option>
                                <option value="ฟ้องร้อง" {{ old('status') === 'ฟ้องร้อง' ? 'selected' : '' }}>⚠️ ฟ้องร้อง</option>
                                <option value="ต่อสัญญา" {{ old('status') === 'ต่อสัญญา' ? 'selected' : '' }}>🔄 ต่อสัญญา</option>
                                <option value="ไถ่ถอน" {{ old('status') === 'ไถ่ถอน' ? 'selected' : '' }}>✓ ไถ่ถอน</option>
                                <option value="ทรัพย์หลุด" {{ old('status') === 'ทรัพย์หลุด' ? 'selected' : '' }}>❌ ทรัพย์หลุด</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ประเภทสัญญา -->
                        <div class="mb-3">
                            <label for="type" class="form-label">ประเภทสัญญา <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">-- เลือกประเภท --</option>
                                <option value="ขายฝาก" {{ old('type') === 'ขายฝาก' ? 'selected' : '' }}>🏪 ขายฝาก</option>
                                <option value="จำนอง" {{ old('type') === 'จำนอง' ? 'selected' : '' }}>🏦 จำนอง</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- อัพโหลดรูปสัญญา -->
                        <div class="mb-3">
                            <label for="contract_image" class="form-label">รูปไฟล์สัญญา</label>
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

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('loans.index') }}" class="btn btn-secondary">ยกเลิก</a>
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
</script>
@endsection
