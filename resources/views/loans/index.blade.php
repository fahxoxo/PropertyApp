@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-sm-items-center mb-3 mb-md-4 gap-2">
        <h1 class="h3 h-md-1">💰 สัญญาขายฝาก/จำนอง</h1>
        <a href="{{ route('loans.create') }}" class="btn btn-primary w-100 w-sm-auto">
            ➕ เพิ่มสัญญา
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
    <div class="card mb-3 mb-md-4">
        <div class="card-body p-2 p-md-3">
            <form method="GET" action="{{ route('loans.index') }}" class="row g-2 g-md-3">
                <div class="col-12 col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="🔍 ค้นหาจากรหัสสัญญา/ชื่อลูกค้า/เบอร์โทร/รหัสลูกค้า" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-info w-100">ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Card View (< 768px) -->
    <div class="d-md-none">
        @forelse($loans as $loan)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <!-- Code and Type -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary fs-6">{{ $loan->code }}</span>
                        @if($loan->type === 'ขายฝาก')
                            <span class="badge bg-warning text-dark">🏪 ขายฝาก</span>
                        @else
                            <span class="badge bg-danger">🏦 จำนอง</span>
                        @endif
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">ลูกค้า</h6>
                        <p class="mb-1"><strong>{{ $loan->customer->first_name }} {{ $loan->customer->last_name }}</strong></p>
                        <small class="text-muted">{{ $loan->customer->code }}</small>
                    </div>

                    <!-- Principal Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">เงินต้นคงเหลือ</h6>
                        <p class="mb-1 text-danger"><strong>{{ number_format($loan->principal_remaining, 2) }} ฿</strong></p>
                        <small class="text-muted">เบิกเริ่มต้น: {{ number_format($loan->principal, 2) }} ฿</small>
                    </div>

                    <!-- Rate and Duration -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <h6 class="mb-1">ดอกเบี้ย</h6>
                            <p class="mb-0"><strong>{{ $loan->interest_rate }}%</strong></p>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">ระยะเวลา</h6>
                            <p class="mb-0"><strong>{{ $loan->duration }}</strong></p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        @if($loan->status === 'อยู่ในสัญญา')
                            <span class="badge bg-success">✅ อยู่ในสัญญา</span>
                        @elseif($loan->status === 'ฟ้องร้อง')
                            <span class="badge bg-danger">⚠️ ฟ้องร้อง</span>
                        @elseif($loan->status === 'ต่อสัญญา')
                            <span class="badge bg-info">🔄 ต่อสัญญา</span>
                        @elseif($loan->status === 'ไถ่ถอน')
                            <span class="badge bg-secondary">✓ ไถ่ถอน</span>
                        @else
                            <span class="badge bg-dark">❌ ทรัพย์หลุด</span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('loans.edit', $loan) }}" class="btn btn-sm btn-warning">
                            ✏️ แก้ไข
                        </a>
                        <form method="POST" action="{{ route('loans.destroy', $loan) }}" 
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
                <h5>ไม่มีข้อมูลสัญญาขายฝาก/จำนอง</h5>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>รหัสสัญญา</th>
                    <th>ลูกค้า</th>
                    <th>เงินต้น</th>
                    <th class="d-none d-lg-table-cell">ดอกเบี้ย</th>
                    <th class="d-none d-lg-table-cell">ระยะเวลา</th>
                    <th class="d-none d-xl-table-cell">ประเภท</th>
                    <th>สถานะ</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $loan->code }}</span>
                        </td>
                        <td>
                            <strong class="d-block">{{ $loan->customer->first_name }} {{ $loan->customer->last_name }}</strong>
                            <small class="text-muted">{{ $loan->customer->code }}</small>
                        </td>
                        <td>
                            <strong class="text-danger d-block">{{ number_format($loan->principal_remaining, 2) }} ฿</strong>
                            <small class="text-muted">เบิก: {{ number_format($loan->principal, 2) }} ฿</small>
                        </td>
                        <td class="d-none d-lg-table-cell">{{ $loan->interest_rate }}%</td>
                        <td class="d-none d-lg-table-cell">{{ $loan->duration }}</td>
                        <td class="d-none d-xl-table-cell">
                            @if($loan->type === 'ขายฝาก')
                                <span class="badge bg-warning text-dark">🏪</span>
                            @else
                                <span class="badge bg-danger">🏦</span>
                            @endif
                        </td>
                        <td>
                            @if($loan->status === 'อยู่ในสัญญา')
                                <span class="badge bg-success">✅</span>
                            @elseif($loan->status === 'ฟ้องร้อง')
                                <span class="badge bg-danger">⚠️</span>
                            @elseif($loan->status === 'ต่อสัญญา')
                                <span class="badge bg-info">🔄</span>
                            @elseif($loan->status === 'ไถ่ถอน')
                                <span class="badge bg-secondary">✓</span>
                            @else
                                <span class="badge bg-dark">❌</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('loans.edit', $loan) }}" class="btn btn-warning" title="แก้ไข">
                                    ✏️
                                </a>
                                <form method="POST" action="{{ route('loans.destroy', $loan) }}" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('ยืนยันการลบข้อมูล?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="ลบ">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <h5>ไม่มีข้อมูลสัญญาขายฝาก/จำนอง</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-3 mt-md-4">
        <ul class="pagination justify-content-center flex-wrap">
            {{ $loans->links('pagination::bootstrap-5') }}
        </ul>
    </nav>
</div>
@endsection
