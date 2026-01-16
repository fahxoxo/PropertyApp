@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-sm-items-center mb-3 mb-md-4 gap-2">
        <h1 class="h3 h-md-1">💰 การรับชำระเงิน</h1>
        <div class="d-grid gap-2 d-sm-flex w-100 w-sm-auto">
            <a href="{{ route('finance.reducePrincipal') }}" class="btn btn-warning btn-sm btn-md-lg order-2 order-sm-1">
                📉 ลดต้น
            </a>
            <a href="{{ route('finance.revenue') }}" class="btn btn-success btn-sm btn-md-lg order-3 order-sm-2">
                📊 รายรับ
            </a>
            <a href="{{ route('finance.create') }}" class="btn btn-primary btn-sm btn-md-lg order-1 order-sm-3">
                ➕ รับชำระ
            </a>
        </div>
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
            <form method="GET" action="{{ route('finance.index') }}" class="row g-2 g-md-3">
                <div class="col-12 col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="🔍 ค้นหาจากใบเสร็จ/รหัสสัญญา/ชื่อลูกค้า/เบอร์โทร" 
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
        @forelse($transactions as $tx)
            <div class="card mb-3">
                <div class="card-body p-3">
                    <!-- Receipt Number -->
                    <div class="mb-2">
                        <span class="badge bg-primary fs-6">{{ $tx->receipt_number }}</span>
                    </div>

                    <!-- Type and Payment Method -->
                    <div class="d-flex gap-2 mb-3">
                        @if($tx->payable_type === 'App\Models\RentalContract')
                            <span class="badge bg-success">🏠 สัญญาเช่า</span>
                        @elseif($tx->payable_type === 'App\Models\LoanContract')
                            <span class="badge bg-warning text-dark">💰 ขายฝาก/จำนอง</span>
                        @endif
                        
                        @if($tx->payment_method === 'เงินสด')
                            <span class="badge bg-success">💵 เงินสด</span>
                        @else
                            <span class="badge bg-info">🏦 โอน</span>
                        @endif
                    </div>

                    <!-- Customer Info -->
                    <div class="mb-3">
                        <h6 class="mb-1">ลูกค้า</h6>
                        <p class="mb-1"><strong>{{ $tx->payable->customer->first_name }} {{ $tx->payable->customer->last_name }}</strong></p>
                        <small class="text-muted">{{ $tx->payable->customer->phone }}</small>
                    </div>

                    <!-- Amount and Date -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <h6 class="mb-1">จำนวนเงิน</h6>
                            <p class="mb-0 text-danger"><strong>{{ number_format($tx->amount, 2) }} ฿</strong></p>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">วันที่</h6>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($tx->payment_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('finance.receipt', $tx->id) }}" class="btn btn-sm btn-info" target="_blank">
                            📄 ดู PDF
                        </a>
                        <form method="POST" action="{{ route('finance.destroy', $tx) }}" 
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
                <h5>ไม่มีข้อมูลการรับชำระเงิน</h5>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ใบเสร็จ</th>
                    <th class="d-none d-lg-table-cell">รายการ</th>
                    <th>ลูกค้า</th>
                    <th>จำนวนเงิน</th>
                    <th class="d-none d-lg-table-cell">วิธีชำระ</th>
                    <th class="d-none d-xl-table-cell">วันที่</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td data-label="ใบเสร็จ">
                            <span class="badge bg-primary">{{ $tx->receipt_number }}</span>
                        </td>
                        <td class="d-none d-lg-table-cell" data-label="รายการ">
                            @if($tx->payable_type === 'App\Models\RentalContract')
                                <span class="badge bg-success">🏠 สัญญาเช่า</span>
                            @elseif($tx->payable_type === 'App\Models\LoanContract')
                                <span class="badge bg-warning text-dark">💰 ขายฝาก/จำนอง</span>
                            @endif
                        </td>
                        <td data-label="ลูกค้า">
                            <strong class="d-block">{{ $tx->payable->customer->first_name }} {{ $tx->payable->customer->last_name }}</strong>
                            <small class="text-muted">{{ $tx->payable->customer->phone }}</small>
                        </td>
                        <td data-label="จำนวนเงิน" class="text-end">
                            <strong>{{ number_format($tx->amount, 2) }} ฿</strong>
                        </td>
                        <td class="d-none d-lg-table-cell" data-label="วิธีชำระ">
                            @if($tx->payment_method === 'เงินสด')
                                <span class="badge bg-success">💵 เงินสด</span>
                            @else
                                <span class="badge bg-info">🏦 โอน</span>
                            @endif
                        </td>
                        <td class="d-none d-xl-table-cell" data-label="วันที่">{{ \Carbon\Carbon::parse($tx->payment_date)->format('d/m/Y') }}</td>
                        <td data-label="การดำเนินการ">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('finance.receipt', $tx->id) }}" class="btn btn-info" target="_blank" title="PDF">
                                    📄
                                </a>
                                <form method="POST" action="{{ route('finance.destroy', $tx) }}" 
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
                        <td colspan="7" class="text-center text-muted py-5">
                            <h5>ไม่มีข้อมูลการชำระเงิน</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </ul>
    </nav>
</div>
@endsection
