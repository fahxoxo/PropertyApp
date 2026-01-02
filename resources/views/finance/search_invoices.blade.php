@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Back Button -->
            <a href="{{ route('finance.create') }}" class="btn btn-secondary mb-3">
                ← กลับไปค้นหา
            </a>

            <!-- Search Results Card -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">💳 ใบแจ้งหนี้ที่ค้างชำระ</h3>
                </div>

                <div class="card-body">
                    @if($keyword)
                        <div class="alert alert-info mb-3">
                            <strong>🔍 ผลการค้นหา:</strong> "{{ $keyword }}"
                            @if($invoices->count() > 0)
                                - พบ <strong>{{ $invoices->count() }}</strong> รายการ
                            @endif
                        </div>
                    @endif

                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 15%">เลขที่ใบแจ้งหนี้</th>
                                        <th style="width: 12%">วันที่ออก</th>
                                        <th class="text-end" style="width: 12%">ยอดตามบิล</th>
                                        <th class="text-end" style="width: 12%">ยอดคงเหลือ</th>
                                        <th style="width: 25%">ชื่อลูกค้า</th>
                                        <th class="text-center" style="width: 12%">สถานะ</th>
                                        <th class="text-center" style="width: 12%">เลือก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr class="@if($invoice->isOverdueNow()) table-danger @endif">
                                            <td>
                                                <strong>{{ $invoice->invoice_number }}</strong>
                                            </td>
                                            <td>
                                                {{ $invoice->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ number_format($invoice->amount, 2) }}</strong> ฿
                                            </td>
                                            <td class="text-end">
                                                <span class="text-danger fw-bold">
                                                    {{ number_format($invoice->outstanding_balance, 2) }} ฿
                                                </span>
                                            </td>
                                            <td>
                                                @if($invoice->billable)
                                                    {{ $invoice->billable->customer->first_name }}
                                                    {{ $invoice->billable->customer->last_name }}
                                                    <br>
                                                    <small class="text-muted">{{ $invoice->billable->customer->code }}</small>
                                                @else
                                                    <span class="text-muted">ไม่พบข้อมูล</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @switch($invoice->status)
                                                    @case('overdue')
                                                        <span class="badge bg-danger">🔴 เลยชำระ</span>
                                                        @break
                                                    @case('issued')
                                                        @if($invoice->paid_amount > 0)
                                                            <span class="badge bg-warning">🟡 บางส่วน</span>
                                                        @else
                                                            <span class="badge bg-warning">🟡 ออกแล้ว</span>
                                                        @endif
                                                        @break
                                                    @case('draft')
                                                        <span class="badge bg-secondary">⚪ ร่าง</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">ℹ️ {{ $invoice->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('finance.selectInvoice', $invoice) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="เลือกใบแจ้งหนี้นี้เพื่อรับชำระ">
                                                    ☑️
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Summary Row -->
                                    <tr class="table-active fw-bold">
                                        <td colspan="2">รวมยอดค้าง</td>
                                        <td class="text-end">
                                            {{ number_format($invoices->sum('amount'), 2) }} ฿
                                        </td>
                                        <td class="text-end text-danger">
                                            <strong>{{ number_format($invoices->sum('outstanding_balance'), 2) }} ฿</strong>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Cards Below Table -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">จำนวนรายการ</h6>
                                        <p class="display-6 mb-0">{{ $invoices->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">ยอดรวมตามบิล</h6>
                                        <p class="display-6 mb-0 text-danger">
                                            {{ number_format($invoices->sum('amount'), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">ยอดรวมค้างชำระ</h6>
                                        <p class="display-6 mb-0 text-warning">
                                            {{ number_format($invoices->sum('outstanding_balance'), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted">รายการเลยกำหนด</h6>
                                        <p class="display-6 mb-0 text-danger">
                                            {{ $invoices->filter(fn($inv) => $inv->isOverdueNow())->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading">ℹ️ ไม่พบข้อมูล</h6>
                            @if($keyword)
                                ไม่พบใบแจ้งหนี้ที่ค้างชำระสำหรับคำค้นหา: <strong>"{{ $keyword }}"</strong>
                            @else
                                ยังไม่มีใบแจ้งหนี้ที่ค้างชำระ ลูกค้าทั้งหมดได้ชำระเต็มจำนวนแล้ว
                            @endif
                        </div>

                        @if(!$keyword)
                            <div class="text-center">
                                <p class="text-muted">ลองค้นหาเพื่อดูใบแจ้งหนี้ที่ค้างชำระ</p>
                                <form action="{{ route('finance.searchInvoices') }}" method="GET" class="mt-3">
                                    <div class="input-group">
                                        <input type="text" name="search_keyword" class="form-control" 
                                               placeholder="ค้นหาจากชื่อลูกค้า, รหัสสัญญา (RENT-0001), เบอร์โทร, หรือเลขที่บิล">
                                        <button type="submit" class="btn btn-success">🔍 ค้นหา</button>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        💡 สามารถค้นหาจาก: ชื่อลูกค้า | รหัสลูกค้า | เบอร์โทร | รหัสสัญญาเช่า (RENT-xxx) | รหัสสัญญาขายฝาก (LOAN-xxx) | เลขที่ใบแจ้งหนี้ (INV-xxx)
                                    </small>
                                </form>
                            </div>
                        @endif
                    @endif

                    <!-- Search Form -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="mb-3">🔍 ค้นหาใบแจ้งหนี้อื่น</h6>
                        <form action="{{ route('finance.searchInvoices') }}" method="GET">
                            <div class="input-group mb-2">
                                <input type="text" name="search_keyword" class="form-control" 
                                       placeholder="ค้นหาจากชื่อลูกค้า, รหัสสัญญา (RENT-0001), เบอร์โทร, หรือเลขที่บิล"
                                       value="{{ $keyword }}">
                                <button type="submit" class="btn btn-success">🔍 ค้นหา</button>
                                <a href="{{ route('finance.searchInvoices') }}" class="btn btn-secondary">
                                    🔄 แสดงทั้งหมด
                                </a>
                            </div>
                            <small class="text-muted d-block">
                                💡 ตัวอย่าง: RENT-0002 | LOAN-0001 | INV-26001 | 08912345678 | นาย
                            </small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endsection
