@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">✏️ แก้ไขข้อมูลบ้านเช่า ({{ $property->code }})</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- รหัสบ้าน (แสดงเท่านั้น) -->
                            <div class="col-md-12 mb-3">
                                <label for="code" class="form-label">รหัสบ้าน</label>
                                <input type="text" class="form-control" id="code" value="{{ $property->code }}" disabled>
                                <small class="text-muted">รหัสนี้ถูกสร้างอัตโนมัติและไม่สามารถเปลี่ยนแปลงได้</small>
                            </div>

                            <!-- ชื่อบ้าน -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">ชื่อบ้าน <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $property->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- เบอร์ที่ -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">บ้านเลขที่ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $property->address) }}" required>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- หมู่ -->
                            <div class="col-md-3 mb-3">
                                <label for="moo" class="form-label">หมู่</label>
                                <input type="text" class="form-control @error('moo') is-invalid @enderror" 
                                       id="moo" name="moo" value="{{ old('moo', $property->moo) }}">
                                @error('moo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ตำบล -->
                            <div class="col-md-3 mb-3">
                                <label for="subdistrict" class="form-label">ตำบล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subdistrict') is-invalid @enderror" 
                                       id="subdistrict" name="subdistrict" value="{{ old('subdistrict', $property->subdistrict) }}" required>
                                @error('subdistrict')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- อำเภอ -->
                            <div class="col-md-3 mb-3">
                                <label for="district" class="form-label">อำเภอ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                       id="district" name="district" value="{{ old('district', $property->district) }}" required>
                                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- จังหวัด -->
                            <div class="col-md-3 mb-3">
                                <label for="province" class="form-label">จังหวัด <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                       id="province" name="province" value="{{ old('province', $property->province) }}" required>
                                @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ราคาเช่า -->
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">ราคาเช่า (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $property->price) }}" step="0.01" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- มิเตอร์น้ำ -->
                            <div class="col-md-4 mb-3">
                                <label for="water_meter" class="form-label">หมายเลขมิเตอร์น้ำ</label>
                                <input type="text" class="form-control @error('water_meter') is-invalid @enderror" 
                                       id="water_meter" name="water_meter" value="{{ old('water_meter', $property->water_meter) }}">
                                @error('water_meter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- มิเตอร์ไฟ -->
                            <div class="col-md-4 mb-3">
                                <label for="electric_meter" class="form-label">หมายเลขมิเตอร์ไฟฟ้า</label>
                                <input type="text" class="form-control @error('electric_meter') is-invalid @enderror" 
                                       id="electric_meter" name="electric_meter" value="{{ old('electric_meter', $property->electric_meter) }}">
                                @error('electric_meter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ประเภท -->
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">ประเภทเอกสาร <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="โฉนด" {{ old('type', $property->type) === 'โฉนด' ? 'selected' : '' }}>โฉนด</option>
                                    <option value="ธนารักษ์" {{ old('type', $property->type) === 'ธนารักษ์' ? 'selected' : '' }}>ธนารักษ์</option>
                                    <option value="หนังสือสำคัญที่ดิน" {{ old('type', $property->type) === 'หนังสือสำคัญที่ดิน' ? 'selected' : '' }}>หนังสือสำคัญที่ดิน</option>
                                    <option value="อื่นๆ" {{ old('type', $property->type) === 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- GPS -->
                            <div class="col-md-4 mb-3">
                                <label for="gps" class="form-label">พิกัด GPS</label>
                                <input type="text" class="form-control @error('gps') is-invalid @enderror" 
                                       id="gps" name="gps" value="{{ old('gps', $property->gps) }}" placeholder="เช่น 13.7563,100.5018">
                                @error('gps')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- สถานะ -->
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="vacant" {{ old('status', $property->status) === 'vacant' ? 'selected' : '' }}>🔓 ว่าง</option>
                                    <option value="rented" {{ old('status', $property->status) === 'rented' ? 'selected' : '' }}>🔒 เช่าแล้ว</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- เอกสารที่ดิน -->
                            <div class="col-md-12 mb-3">
                                <label for="doc_image" class="form-label">รูปภาพเอกสารที่ดิน</label>
                                
                                @if($property->doc_image)
                                    <div class="mb-3">
                                        <label class="form-label">รูปภาพปัจจุบัน</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $property->doc_image) }}" 
                                                 alt="Property Doc" style="max-width: 400px; max-height: 400px; border: 1px solid #ddd; padding: 5px;">
                                        </div>
                                    </div>
                                @endif

                                <input type="file" class="form-control @error('doc_image') is-invalid @enderror" 
                                       id="doc_image" name="doc_image" accept="image/*">
                                <small class="text-muted">เลือกไฟล์ใหม่เพื่ออัพโหลดรูปภาพใหม่</small>
                                @error('doc_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Preview รูปใหม่ -->
                            <div class="col-md-12 mb-3" id="imagePreview" style="display:none;">
                                <label class="form-label">ตัวอย่างรูปภาพใหม่</label>
                                <div>
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 400px; max-height: 400px;">
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('properties.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-warning btn-lg">💾 บันทึกการเปลี่ยนแปลง</button>
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
