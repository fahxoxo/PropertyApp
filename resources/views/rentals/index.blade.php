@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📝 สัญญาเช่าบ้าน</h1>
        <a href="{{ route('rentals.create') }}" class="btn btn-primary btn-lg">
            ➕ เพิ่มสัญญาเช่า
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
            <form method="GET" action="{{ route('rentals.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="🔍 ค้นหาจากรหัสสัญญา/ชื่อลูกค้า/ชื่อบ้าน" 
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
        @forelse($rentals as $rental)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <!-- Code and Status -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary fs-6">{{ $rental->code }}</span>
                        @if($rental->status === 'active')
                            <span class="badge bg-success">✅ ใช้งาน</span>
                        @else
                            <span class="badge bg-secondary">⚫ ปิด</span>
                        @endif
                    </div>

                    <!-- Property Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">บ้านเช่า</h6>
                        <p class="mb-1"><strong>{{ $rental->property->name }}</strong></p>
                        <small class="text-muted">{{ $rental->property->address }}</small>
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">ลูกค้า</h6>
                        <p class="mb-1"><strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong></p>
                        <small class="text-muted">{{ $rental->customer->phone }}</small>
                    </div>

                    <!-- Financial Info -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <h6 class="mb-1">มัดจำ</h6>
                            <p class="mb-0"><strong>{{ number_format($rental->deposit, 2) }} ฿</strong></p>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">เช่าล่วงหน้า</h6>
                            <p class="mb-0"><strong>{{ number_format($rental->advance_rent, 2) }} ฿</strong></p>
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3">
                        <h6 class="mb-1">วันเริ่มสัญญา</h6>
                        <p class="mb-0"><strong>{{ \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') }}</strong></p>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('rentals.edit', $rental) }}" class="btn btn-sm btn-warning">
                            ✏️ แก้ไข
                        </a>
                        <a href="{{ route('rentals.print', $rental) }}" class="btn btn-sm btn-info" target="_blank">
                            🖨️ พิมพ์
                        </a>
                        <form method="POST" action="{{ route('rentals.destroy', $rental) }}" 
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
                <h5>ไม่มีข้อมูลสัญญาเช่า</h5>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th style="width: 10%;">รหัสสัญญา</th>
                    <th style="width: 15%;">ลูกค้า</th>
                    <th style="width: 15%;">บ้านเช่า</th>
                    <th style="width: 10%;">มัดจำ</th>
                    <th style="width: 10%;">เช่าล่วงหน้า</th>
                    <th style="width: 12%;">วันเริ่มสัญญา</th>
                    <th style="width: 8%;">สถานะ</th>
                    <th style="width: 20%;">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td data-label="รหัสสัญญา">
                            <span class="badge bg-primary fs-6">{{ $rental->code }}</span>
                        </td>
                        <td data-label="ลูกค้า">
                            <strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong>
                            <br><small class="text-muted">{{ $rental->customer->phone }}</small>
                        </td>
                        <td data-label="บ้านเช่า">
                            <strong>{{ $rental->property->name }}</strong>
                            <br><small class="text-muted">{{ $rental->property->address }}</small>
                        </td>
                        <td data-label="มัดจำ">{{ number_format($rental->deposit, 2) }} ฿</td>
                        <td data-label="เช่าล่วงหน้า">{{ number_format($rental->advance_rent, 2) }} ฿</td>
                        <td data-label="วันเริ่มสัญญา">{{ \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') }}</td>
                        <td data-label="สถานะ">
                            @if($rental->status === 'active')
                                <span class="badge bg-success">✅ ใช้งาน</span>
                            @else
                                <span class="badge bg-secondary">⚫ ปิด</span>
                            @endif
                        </td>
                        <td data-label="การดำเนินการ">
                            <a href="{{ route('rentals.edit', $rental) }}" class="btn btn-sm btn-warning">
                                ✏️ แก้ไข
                            </a>
                            
                            <a href="{{ route('rentals.print', $rental) }}" class="btn btn-sm btn-info" target="_blank">
                                🖨️ พิมพ์
                            </a>
                            
                            <form method="POST" action="{{ route('rentals.destroy', $rental) }}" 
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <h5>ไม่มีข้อมูลสัญญาเช่า</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $rentals->links('pagination::bootstrap-5') }}
        </ul>
    </nav>
</div>
@endsection
