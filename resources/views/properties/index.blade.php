@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🏠 ข้อมูลบ้านเช่า</h1>
        <a href="{{ route('properties.create') }}" class="btn btn-primary btn-lg">
            ➕ เพิ่มบ้านเช่า
        </a>
    </div>

    <!-- ข้อความสำเร็จ -->
    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ปุ่มค้นหา -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('properties.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="🔍 ค้นหาจากรหัสบ้าน/ชื่อ/ที่อยู่" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info btn-lg w-100">ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Card View (< 768px) -->
    <div class="d-md-none">
        @forelse($properties as $property)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <!-- Code and Status -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary fs-6">{{ $property->code }}</span>
                        @if($property->status === 'vacant')
                            <span class="badge bg-success">🔓 ว่าง</span>
                        @else
                            <span class="badge bg-danger">🔒 เช่าแล้ว</span>
                        @endif
                    </div>

                    <!-- Property Name -->
                    <div class="mb-3">
                        <h6 class="mb-1">ชื่อบ้านเช่า</h6>
                        <p class="mb-0"><strong>{{ $property->name }}</strong></p>
                    </div>

                    <!-- Property Type -->
                    <div class="mb-3">
                        <h6 class="mb-1">ประเภท</h6>
                        <p class="mb-0"><strong>{{ $property->type }}</strong></p>
                    </div>

                    <!-- Address Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">ที่อยู่</h6>
                        <p class="mb-1"><strong>{{ $property->address }}</strong></p>
                        @if($property->moo)
                            <small class="text-muted">หมู่ {{ $property->moo }}</small>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <h6 class="mb-1">ราคาเช่า</h6>
                        <p class="mb-0"><strong class="text-primary">{{ number_format($property->price, 2) }} ฿</strong></p>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-warning">
                            ✏️ แก้ไข
                        </a>
                        <form method="POST" action="{{ route('properties.destroy', $property) }}" 
                              style="display:block;" 
                              onsubmit="return confirm('ยืนยันการลบข้อมูล?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100">
                                🗑️ ลบ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center py-5">
                <h5>ไม่มีข้อมูลบ้านเช่า</h5>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th style="width: 10%;">รหัสบ้าน</th>
                    <th style="width: 15%;">ชื่อบ้าน</th>
                    <th style="width: 20%;">ที่อยู่</th>
                    <th style="width: 10%;">ราคาเช่า</th>
                    <th style="width: 10%;">ประเภท</th>
                    <th style="width: 10%;">สถานะ</th>
                    <th style="width: 15%;">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                    <tr>
                        <td data-label="รหัสบ้าน">
                            <span class="badge bg-primary fs-6">{{ $property->code }}</span>
                        </td>
                        <td data-label="ชื่อบ้าน">
                            <strong>{{ $property->name }}</strong>
                        </td>
                        <td data-label="ที่อยู่">
                            {{ $property->address }}
                            @if($property->moo)
                                <br><small class="text-muted">หมู่ {{ $property->moo }}</small>
                            @endif
                        </td>
                        <td data-label="ราคาเช่า">
                            {{ number_format($property->price, 2) }} ฿
                        </td>
                        <td data-label="ประเภท">{{ $property->type }}</td>
                        <td data-label="สถานะ">
                            @if($property->status === 'vacant')
                                <span class="badge bg-success">🔓 ว่าง</span>
                            @else
                                <span class="badge bg-danger">🔒 เช่าแล้ว</span>
                            @endif
                        </td>
                        <td data-label="การดำเนินการ">
                            <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-warning">
                                ✏️ แก้ไข
                            </a>
                            
                            <form method="POST" action="{{ route('properties.destroy', $property) }}" 
                                  style="display:inline;" 
                                  onsubmit="return confirm('ยืนยันการลบข้อมูล?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    🗑️ ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <h5>ไม่มีข้อมูลบ้านเช่า</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $properties->links('pagination::bootstrap-5') }}
        </ul>
    </nav>
</div>
@endsection
