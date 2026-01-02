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

    <!-- ตารางข้อมูล -->
    <div class="table-responsive">
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
                        <td>
                            <span class="badge bg-primary fs-6">{{ $rental->code }}</span>
                        </td>
                        <td>
                            <strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong>
                            <br><small class="text-muted">{{ $rental->customer->phone }}</small>
                        </td>
                        <td>
                            <strong>{{ $rental->property->name }}</strong>
                            <br><small class="text-muted">{{ $rental->property->address }}</small>
                        </td>
                        <td>{{ number_format($rental->deposit, 2) }} ฿</td>
                        <td>{{ number_format($rental->advance_rent, 2) }} ฿</td>
                        <td>{{ \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($rental->status === 'active')
                                <span class="badge bg-success">✅ ใช้งาน</span>
                            @else
                                <span class="badge bg-secondary">⚫ ปิด</span>
                            @endif
                        </td>
                        <td>
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
