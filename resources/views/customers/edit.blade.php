@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">✏️ แก้ไขข้อมูลลูกค้า ({{ $customer->code }})</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- รหัสลูกค้า (แสดงเท่านั้น) -->
                        <div class="mb-3">
                            <label for="code" class="form-label">รหัสลูกค้า</label>
                            <input type="text" class="form-control" id="code" value="{{ $customer->code }}" disabled>
                            <small class="text-muted">รหัสนี้ถูกสร้างอัตโนมัติและไม่สามารถเปลี่ยนแปลงได้</small>
                        </div>

                        <!-- ชื่อ -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- นามสกุล -->
                        <div class="mb-3">
                            <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ชื่อเล่น -->
                        <div class="mb-3">
                            <label for="nickname" class="form-label">ชื่อเล่น</label>
                            <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                                   id="nickname" name="nickname" value="{{ old('nickname', $customer->nickname) }}">
                            @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เลขบัตรประชาชน -->
                        <div class="mb-3">
                            <label for="id_card" class="form-label">เลขบัตรประชาชน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('id_card') is-invalid @enderror" 
                                   id="id_card" name="id_card" value="{{ old('id_card', $customer->id_card) }}" required>
                            @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- รูปภาพบัตรประชาชน -->
                        <div class="mb-3">
                            <label for="id_card_image" class="form-label">รูปภาพบัตรประชาชน</label>
                            
                            @if($customer->id_card_image)
                                <div class="mb-3">
                                    <label class="form-label">รูปภาพปัจจุบัน</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $customer->id_card_image) }}" 
                                             alt="ID Card" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('id_card_image') is-invalid @enderror" 
                                   id="id_card_image" name="id_card_image" accept="image/*">
                            <small class="text-muted">เลือกไฟล์ใหม่เพื่ออัพโหลดรูปภาพใหม่</small>
                            @error('id_card_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Preview รูปใหม่ -->
                        <div class="mb-3" id="imagePreview" style="display:none;">
                            <label class="form-label">ตัวอย่างรูปภาพใหม่</label>
                            <div>
                                <img id="previewImg" src="" alt="Preview" style="max-width: 300px; max-height: 300px;">
                            </div>
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-warning btn-lg">💾 บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview รูปภาพ
document.getElementById('id_card_image').addEventListener('change', function(e) {
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
