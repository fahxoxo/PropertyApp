@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">➕ เพิ่มบ้านเช่าใหม่</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- ชื่อบ้าน -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">ชื่อบ้าน <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- เบอร์ที่ --></div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">บ้านเลขที่ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address') }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- หมู่ -->
                            <div class="col-md-3 mb-3">
                                <label for="moo" class="form-label">หมู่</label>
                                <input type="text" class="form-control @error('moo') is-invalid @enderror" 
                                       id="moo" name="moo" value="{{ old('moo') }}">
                                @error('moo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ตำบล -->
                            <div class="col-md-3 mb-3">
                                <label for="subdistrict" class="form-label">ตำบล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subdistrict') is-invalid @enderror" 
                                       id="subdistrict" name="subdistrict" value="{{ old('subdistrict') }}" required>
                                @error('subdistrict')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- อำเภอ -->
                            <div class="col-md-3 mb-3">
                                <label for="district" class="form-label">อำเภอ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                       id="district" name="district" value="{{ old('district') }}" required>
                                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- จังหวัด -->
                            <div class="col-md-3 mb-3">
                                <label for="province" class="form-label">จังหวัด <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                       id="province" name="province" value="{{ old('province') }}" required>
                                @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ราคาเช่า -->
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">ราคาเช่า (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" step="0.01" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- มิเตอร์น้ำ -->
                            <div class="col-md-4 mb-3">
                                <label for="water_meter" class="form-label">หมายเลขมิเตอร์น้ำ</label>
                                <input type="text" class="form-control @error('water_meter') is-invalid @enderror" 
                                       id="water_meter" name="water_meter" value="{{ old('water_meter') }}">
                                @error('water_meter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- มิเตอร์ไฟ -->
                            <div class="col-md-4 mb-3">
                                <label for="electric_meter" class="form-label">หมายเลขมิเตอร์ไฟฟ้า</label>
                                <input type="text" class="form-control @error('electric_meter') is-invalid @enderror" 
                                       id="electric_meter" name="electric_meter" value="{{ old('electric_meter') }}">
                                @error('electric_meter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ประเภท -->
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">ประเภทเอกสาร <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="โฉนด" {{ old('type') === 'โฉนด' ? 'selected' : '' }}>โฉนด</option>
                                    <option value="ธนารักษ์" {{ old('type') === 'ธนารักษ์' ? 'selected' : '' }}>ธนารักษ์</option>
                                    <option value="หนังสือสำคัญที่ดิน" {{ old('type') === 'หนังสือสำคัญที่ดิน' ? 'selected' : '' }}>หนังสือสำคัญที่ดิน</option>
                                    <option value="อื่นๆ" {{ old('type') === 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- GPS -->
                            <div class="col-md-4 mb-3">
                                <label for="gps" class="form-label">พิกัด GPS</label>
                                <input type="text" class="form-control @error('gps') is-invalid @enderror" 
                                       id="gps" name="gps" value="{{ old('gps') }}" placeholder="เช่น 13.7563,100.5018">
                                @error('gps')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- สถานะ -->
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="vacant" {{ old('status') === 'vacant' ? 'selected' : '' }}>🔓 ว่าง</option>
                                    <option value="rented" {{ old('status') === 'rented' ? 'selected' : '' }}>🔒 เช่าแล้ว</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- เอกสารที่ดิน -->
                            <div class="col-md-12 mb-3">
                                <label for="doc_image" class="form-label">รูปภาพเอกสารที่ดิน</label>
                                <input type="file" class="form-control @error('doc_image') is-invalid @enderror" 
                                       id="doc_image" name="doc_image" accept="image/*">
                                <small class="text-muted">รูปแบบ: JPG, PNG (ขนาดสูงสุด 5MB)</small>
                                @error('doc_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Preview รูป -->
                            <div class="col-md-12 mb-3" id="imagePreview" style="display:none;">
                                <label class="form-label">ตัวอย่างรูปภาพ</label>
                                <div>
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 400px; max-height: 400px;">
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('properties.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-success btn-lg">💾 บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('doc_image').addEventListener('change', function(e) {
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
