@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">➕ เพิ่มลูกค้าใหม่</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- ชื่อ -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- นามสกุล -->
                        <div class="mb-3">
                            <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ชื่อเล่น -->
                        <div class="mb-3">
                            <label for="nickname" class="form-label">ชื่อเล่น</label>
                            <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                                   id="nickname" name="nickname" value="{{ old('nickname') }}">
                            @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- เลขบัตรประชาชน -->
                        <div class="mb-3">
                            <label for="id_card" class="form-label">เลขบัตรประชาชน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('id_card') is-invalid @enderror" 
                                   id="id_card" name="id_card" value="{{ old('id_card') }}" required 
                                   placeholder="เช่น 1234567890123">
                            @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- รูปภาพบัตรประชาชน -->
                        <div class="mb-3">
                            <label for="id_card_image" class="form-label">รูปภาพบัตรประชาชน</label>
                            <input type="file" class="form-control @error('id_card_image') is-invalid @enderror" 
                                   id="id_card_image" name="id_card_image" accept="image/*">
                            <small class="text-muted">รูปแบบ: JPG, PNG (ขนาดสูงสุด 5MB)</small>
                            @error('id_card_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">ยกเลิก</a>
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
