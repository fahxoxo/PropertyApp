@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 ข้อมูลลูกค้า</h1>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-lg">
            ➕ เพิ่มลูกค้า
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
            <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="🔍 ค้นหาจากรหัสลูกค้า/เบอร์โทร/เลขบัตรประชาชน" 
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
        @forelse($customers as $customer)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <!-- Code -->
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6">{{ $customer->code }}</span>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <h6 class="mb-1">ชื่อ - นามสกุล</h6>
                        <p class="mb-0"><strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong></p>
                    </div>

                    <!-- Nickname -->
                    @if($customer->nickname)
                        <div class="mb-3">
                            <h6 class="mb-1">ชื่อเล่น</h6>
                            <p class="mb-0"><strong>{{ $customer->nickname }}</strong></p>
                        </div>
                    @endif

                    <!-- Phone -->
                    <div class="mb-3">
                        <h6 class="mb-1">เบอร์โทรศัพท์</h6>
                        <p class="mb-0"><strong>{{ $customer->phone }}</strong></p>
                    </div>

                    <!-- ID Card -->
                    <div class="mb-3">
                        <h6 class="mb-1">เลขบัตรประชาชน</h6>
                        <p class="mb-0"><strong>{{ $customer->id_card }}</strong></p>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                            ✏️ แก้ไข
                        </a>
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" 
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
                <h5>ไม่มีข้อมูลลูกค้า</h5>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th style="width: 10%;">รหัสลูกค้า</th>
                    <th style="width: 20%;">ชื่อ - นามสกุล</th>
                    <th style="width: 15%;">ชื่อเล่น</th>
                    <th style="width: 15%;">เบอร์โทรศัพท์</th>
                    <th style="width: 20%;">เลขบัตรประชาชน</th>
                    <th style="width: 20%;">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td data-label="รหัสลูกค้า">
                            <span class="badge bg-primary fs-6">{{ $customer->code }}</span>
                        </td>
                        <td data-label="ชื่อ - นามสกุล">
                            <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                        </td>
                        <td data-label="ชื่อเล่น">{{ $customer->nickname ?? '-' }}</td>
                        <td data-label="เบอร์โทรศัพท์">{{ $customer->phone }}</td>
                        <td data-label="เลขบัตรประชาชน">{{ $customer->id_card }}</td>
                        <td data-label="การดำเนินการ">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                                ✏️ แก้ไข
                            </a>
                            
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" 
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
                        <td colspan="6" class="text-center text-muted py-5">
                            <h5>ไม่มีข้อมูลลูกค้า</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $customers->links('pagination::bootstrap-5') }}
        </ul>
    </nav>
</div>
@endsection