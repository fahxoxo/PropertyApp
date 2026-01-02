@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Back Button -->
            <a href="{{ route('invoice.index', ['month' => $invoice->month, 'year' => $invoice->year]) }}" class="btn btn-secondary mb-3">
                ← กลับไปหน้ารายการ
            </a>

            <!-- Invoice Card -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">📋 ใบแจ้งหนี้เลขที่ {{ $invoice->invoice_number }}</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">ข้อมูลใบแจ้งหนี้</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>เลขที่บิล:</strong></td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ประเภท:</strong></td>
                                    <td>
                                        @switch($invoice->type)
                                            @case('rental')
                                                🏠 บ้านเช่า
                                                @break
                                            @case('loan_sale')
                                                💰 ขายฝาก
                                                @break
                                            @case('loan_mortgage')
                                                🏦 จำนอง
                                                @break
                                            @case('loan')
                                                📊 เงินกู้
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>เดือน/ปี:</strong></td>
                                    <td>{{ \Carbon\Carbon::create($invoice->year, $invoice->month, 1)->translatedFormat('F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>วันครบกำหนด:</strong></td>
                                    <td>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>สถานะ:</strong></td>
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
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">ข้อมูลสัญญา</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>เลขสัญญา:</strong></td>
                                    <td>{{ $invoice->billable->code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ลูกค้า:</strong></td>
                                    <td>{{ $invoice->billable->customer->name ?? 'N/A' }}</td>
                                </tr>
                                @if($invoice->type === 'rental')
                                    <tr>
                                        <td><strong>บ้าน:</strong></td>
                                        <td>{{ $invoice->billable->property->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ราคาเช่า:</strong></td>
                                        <td>{{ number_format($invoice->billable->property->price ?? 0, 2) }} ฿</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><strong>เงินต้น:</strong></td>
                                        <td>{{ number_format($invoice->billable->principal ?? 0, 2) }} ฿</td>
                                    </tr>
                                    <tr>
                                        <td><strong>คงเหลือ:</strong></td>
                                        <td>{{ number_format($invoice->billable->principal_remaining ?? 0, 2) }} ฿</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ประเภท:</strong></td>
                                        <td>{{ $invoice->billable->type ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Amount Section -->
                    <div class="alert alert-light border-2 border-primary">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6 class="text-muted">จำนวนเงิน</h6>
                                <h2 class="text-primary">{{ number_format($invoice->amount, 2) }} ฿</h2>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">บันทึกเมื่อ</h6>
                                <p>{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">แก้ไขล่าสุด</h6>
                                <p>{{ $invoice->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($invoice->notes)
                        <div class="alert alert-info">
                            <strong>หมายเหตุ:</strong> {{ $invoice->notes }}
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <a href="{{ route('invoice.edit', $invoice) }}" class="btn btn-warning">
                            ✏️ แก้ไข
                        </a>
                        <form action="{{ route('invoice.destroy', $invoice) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('ยืนยันการลบใบแจ้งหนี้?')">
                                🗑️ ลบ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
