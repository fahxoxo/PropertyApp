@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">📋 ระบบจัดการใบแจ้งหนี้</h3>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                            ➕ สร้างใบแจ้งหนี้
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter by Month/Year -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="month" class="form-label">เลือกเดือน</label>
                            <select name="month" id="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F') }} ({{ $m }})
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label">เลือกปี</label>
                            <select name="year" id="year" class="form-select">
                                @for($y = 2020; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y + 543 }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-info w-100">🔍 ค้นหา</button>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">ใบแจ้งหนี้เดือน {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F') }} {{ $year + 543 }}</h5>
                </div>

                <!-- Mobile Card View (< 768px) -->
                <div class="d-md-none p-3">
                    @forelse($invoices as $invoice)
                        <div class="card mb-3">
                            <div class="card-body p-3">
                                <!-- Invoice Number and Type -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary fs-6">{{ $invoice->invoice_number }}</span>
                                    @switch($invoice->type)
                                        @case('rental')
                                            <span class="badge bg-success">🏠 บ้านเช่า</span>
                                            @break
                                        @case('loan_sale')
                                            <span class="badge bg-warning">💰 ขายฝาก</span>
                                            @break
                                        @case('loan_mortgage')
                                            <span class="badge bg-danger">🏦 จำนอง</span>
                                            @break
                                        @case('loan')
                                            <span class="badge bg-info">📊 เงินกู้</span>
                                            @break
                                    @endswitch
                                </div>

                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @switch($invoice->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">📝 ร่าง</span>
                                            @break
                                        @case('issued')
                                            <span class="badge bg-primary">📤 ออกแล้ว</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-success">✅ ชำระแล้ว</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">⚠️逾期</span>
                                            @break
                                    @endswitch
                                </div>

                                <!-- Customer Info -->
                                @if($invoice->billable && $invoice->billable->customer)
                                    <div class="mb-3">
                                        <h6 class="mb-1">ลูกค้า</h6>
                                        <p class="mb-1"><strong>{{ $invoice->billable->customer->first_name }} {{ $invoice->billable->customer->last_name }}</strong></p>
                                        <small class="text-muted">{{ $invoice->billable->customer->code }}</small>
                                    </div>
                                @endif

                                <!-- Contract Info -->
                                <div class="mb-3">
                                    <h6 class="mb-1">ข้อมูลสัญญา</h6>
                                    <p class="mb-0"><strong>{{ $invoice->billable->code ?? 'N/A' }}</strong></p>
                                </div>

                                <!-- Amount -->
                                <div class="mb-3">
                                    <h6 class="mb-1">จำนวนเงิน</h6>
                                    <p class="mb-1"><strong class="text-primary fs-5">{{ number_format($invoice->amount, 2) }} ฿</strong></p>
                                    <small class="text-muted">
                                        @if($invoice->type === 'rental')
                                            ราคาเช่า/เดือน
                                        @else
                                            ดอกเบี้ย/เดือน
                                        @endif
                                    </small>
                                </div>

                                <!-- Due Date -->
                                <div class="mb-3">
                                    <h6 class="mb-1">วันครบกำหนด</h6>
                                    <p class="mb-0"><strong>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</strong></p>
                                </div>

                                <!-- Actions -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-sm btn-info">
                                        👁️ ดู
                                    </a>
                                    @if($invoice->outstanding_balance > 0)
                                        <a href="{{ route('invoice.payment', $invoice) }}" class="btn btn-sm btn-success">
                                            💳 รับชำระ
                                        </a>
                                    @endif
                                    <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                        ✏️ แก้ไข
                                    </a>
                                    <form action="{{ route('invoice.destroy', $invoice) }}" method="POST" style="display:block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('ยืนยันการลบ?')">
                                            🗑️ ลบ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center py-4">
                            ไม่พบใบแจ้งหนี้ในเดือนนี้
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View (>= 768px) -->
                <div class="d-none d-md-block table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>เลขที่บิล</th>
                                <th>ประเภท</th>
                                <th>ข้อมูลสัญญา</th>
                                <th>ลูกค้า</th>
                                <th>จำนวนเงิน</th>
                                <th>วันครบกำหนด</th>
                                <th>สถานะ</th>
                                <th>การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>
                                        @switch($invoice->type)
                                            @case('rental')
                                                <span class="badge bg-success">🏠 บ้านเช่า</span>
                                                @break
                                            @case('loan_sale')
                                                <span class="badge bg-warning">💰 ขายฝาก</span>
                                                @break
                                            @case('loan_mortgage')
                                                <span class="badge bg-danger">🏦 จำนอง</span>
                                                @break
                                            @case('loan')
                                                <span class="badge bg-info">📊 เงินกู้</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $invoice->billable->code ?? 'N/A' }}</td>
                                    <td>
                                        @if($invoice->billable && $invoice->billable->customer)
                                            <strong>{{ $invoice->billable->customer->first_name }} {{ $invoice->billable->customer->last_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $invoice->billable->customer->code }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($invoice->amount, 2) }} ฿</strong><br>
                                        <small class="text-muted">
                                            @if($invoice->type === 'rental')
                                                ราคาเช่า/เดือน
                                            @else
                                                ดอกเบี้ย/เดือน
                                            @endif
                                        </small>
                                    </td>
                                    <td>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @switch($invoice->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">📝 ร่าง</span>
                                                @break
                                            @case('issued')
                                                <span class="badge bg-primary">📤 ออกแล้ว</span>
                                                @break
                                            @case('paid')
                                                <span class="badge bg-success">✅ ชำระแล้ว</span>
                                                @break
                                            @case('overdue')
                                                <span class="badge bg-danger">⚠️逾期</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-info" title="ดู">👁️</a>
                                            @if($invoice->outstanding_balance > 0)
                                                <a href="{{ route('invoice.payment', $invoice) }}" class="btn btn-success" title="รับชำระ">💳</a>
                                            @endif
                                            <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-warning" title="แก้ไข">✏️</a>
                                            <form action="{{ route('invoice.destroy', $invoice) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('ยืนยันการลบ?')" title="ลบ">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        ไม่พบใบแจ้งหนี้ในเดือนนี้
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                    <div class="card-footer">
                        {{ $invoices->render() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal สร้างใบแจ้งหนี้ -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">สร้างใบแจ้งหนี้</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('invoice.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">เลือกประเภทบิล <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- เลือกประเภท --</option>
                            <option value="rental">🏠 บ้านเช่า</option>
                            <option value="loan_sale">💰 ขายฝาก</option>
                            <option value="loan_mortgage">🏦 จำนอง</option>
                            <option value="loan">📊 เงินกู้</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="modal_month" class="form-label">เลือกเดือน <span class="text-danger">*</span></label>
                        <select name="month" id="modal_month" class="form-select @error('month') is-invalid @enderror" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F') }} ({{ $m }})
                                </option>
                            @endfor
                        </select>
                        @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="modal_year" class="form-label">เลือกปี <span class="text-danger">*</span></label>
                        <select name="year" id="modal_year" class="form-select @error('year') is-invalid @enderror" required>
                            @for($y = 2020; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y + 543 }}</option>
                            @endfor
                        </select>
                        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-info">
                        <small>
                            ⓘ ระบบจะสร้างใบแจ้งหนี้อัตโนมัติสำหรับสัญญาทั้งหมดที่อยู่ในระหว่างสัญญาในเดือนที่เลือก
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">✅ สร้างใบแจ้งหนี้</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
