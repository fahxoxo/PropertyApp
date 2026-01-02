@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Back Button -->
            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary mb-3">
                ← กลับไปหน้ารายละเอียด
            </a>

            <!-- Payment History Card -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">📊 ประวัติการจ่ายเงิน - {{ $invoice->invoice_number }}</h3>
                </div>

                <div class="card-body">
                    <!-- Invoice Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">ยอดรวม</h6>
                                    <p class="display-6 mb-0 text-danger">
                                        {{ number_format($invoice->amount, 2) }} ฿
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">จ่ายแล้ว</h6>
                                    <p class="display-6 mb-0 text-success">
                                        {{ number_format($invoice->paid_amount, 2) }} ฿
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">ค้างชำระ</h6>
                                    <p class="display-6 mb-0 text-warning">
                                        {{ number_format($invoice->outstanding_balance, 2) }} ฿
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">ร้อยละที่จ่าย</h6>
                                    <p class="display-6 mb-0">
                                        @if($invoice->amount > 0)
                                            {{ round(($invoice->paid_amount / $invoice->amount) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Allocation Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">📈 สรุปการจัดสรรเงิน</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ประเภท</th>
                                            <th class="text-end">จำนวนเงิน</th>
                                            <th class="text-end">ร้อยละ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="badge bg-warning">ยอดค้างชำระ</span>
                                                ยอดค้างจากเดือนก่อน
                                            </td>
                                            <td class="text-end text-warning">
                                                {{ number_format($summary['outstanding_paid'], 2) }} บาท
                                            </td>
                                            <td class="text-end">
                                                @if($invoice->amount > 0)
                                                    {{ round(($summary['outstanding_paid'] / $invoice->amount) * 100, 1) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">ดอกเบี้ย</span>
                                                ดอกเบี้ยของเดือนปัจจุบัน
                                            </td>
                                            <td class="text-end text-info">
                                                {{ number_format($summary['interest_paid'], 2) }} บาท
                                            </td>
                                            <td class="text-end">
                                                @if($invoice->amount > 0)
                                                    {{ round(($summary['interest_paid'] / $invoice->amount) * 100, 1) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="badge bg-success">เงินต้น</span>
                                                เงินต้นที่ลดลง
                                            </td>
                                            <td class="text-end text-success">
                                                {{ number_format($summary['principal_paid'], 2) }} บาท
                                            </td>
                                            <td class="text-end">
                                                @if($invoice->amount > 0)
                                                    {{ round(($summary['principal_paid'] / $invoice->amount) * 100, 1) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-active fw-bold">
                                            <td>รวมทั้งสิ้น</td>
                                            <td class="text-end">
                                                {{ number_format($summary['total_paid'], 2) }} บาท
                                            </td>
                                            <td class="text-end">
                                                @if($invoice->amount > 0)
                                                    {{ round(($summary['total_paid'] / $invoice->amount) * 100, 1) }}%
                                                @else
                                                    0%
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction List -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">💳 รายการจ่ายเงิน</h5>
                            
                            @if($transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ลำดับที่</th>
                                                <th>วันที่จ่าย</th>
                                                <th>หมายเลขใบเสร็จ</th>
                                                <th>วิธีการจ่าย</th>
                                                <th class="text-end">จำนวนเงิน</th>
                                                <th class="text-end">ยอดค้างก่อน</th>
                                                <th class="text-end">ยอดค้างหลัง</th>
                                                <th class="text-center">สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $index => $transaction)
                                                <tr>
                                                    <td>{{ $transactions->count() - $index }}</td>
                                                    <td>{{ $transaction->payment_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        <code>{{ $transaction->receipt_number }}</code>
                                                    </td>
                                                    <td>
                                                        @switch($transaction->payment_method)
                                                            @case('cash')
                                                                💵 เงินสด
                                                                @break
                                                            @case('transfer')
                                                                🏦 โอนเงิน
                                                                @break
                                                            @case('cheque')
                                                                📝 เช็ค
                                                                @break
                                                            @default
                                                                📋 {{ $transaction->payment_method }}
                                                        @endswitch
                                                    </td>
                                                    <td class="text-end">
                                                        <strong class="text-primary">
                                                            {{ number_format($transaction->amount, 2) }} ฿
                                                        </strong>
                                                    </td>
                                                    <td class="text-end">
                                                        {{ number_format($transaction->outstanding_balance_before, 2) }} ฿
                                                    </td>
                                                    <td class="text-end">
                                                        {{ number_format($transaction->outstanding_balance_after, 2) }} ฿
                                                    </td>
                                                    <td class="text-center">
                                                        @if($transaction->status === 'paid')
                                                            <span class="badge bg-success">✅ สำเร็จ</span>
                                                        @else
                                                            <span class="badge bg-warning">⏳ {{ $transaction->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Payment Details Row -->
                                                @if($transaction->paymentJournals->count() > 0)
                                                    <tr class="table-light">
                                                        <td colspan="8">
                                                            <small class="d-block mb-2"><strong>รายละเอียดการจัดสรร:</strong></small>
                                                            <table class="table table-sm mb-0">
                                                                <tr>
                                                                    <td style="width: 30%">
                                                                        @foreach($transaction->paymentJournals as $journal)
                                                                            @if($journal->allocation_type === 'outstanding')
                                                                                <span class="badge bg-warning">ยอดค้างชำระ</span>
                                                                                {{ number_format($journal->amount, 2) }} บาท
                                                                                <br>
                                                                            @endif
                                                                        @endforeach
                                                                    </td>
                                                                    <td style="width: 30%">
                                                                        @foreach($transaction->paymentJournals as $journal)
                                                                            @if($journal->allocation_type === 'interest')
                                                                                <span class="badge bg-info">ดอกเบี้ย</span>
                                                                                {{ number_format($journal->amount, 2) }} บาท
                                                                                <br>
                                                                            @endif
                                                                        @endforeach
                                                                    </td>
                                                                    <td style="width: 40%">
                                                                        @foreach($transaction->paymentJournals as $journal)
                                                                            @if($journal->allocation_type === 'principal')
                                                                                <span class="badge bg-success">เงินต้น</span>
                                                                                {{ number_format($journal->amount, 2) }} บาท
                                                                                <br>
                                                                                @if($journal->principal_before && $journal->principal_after)
                                                                                    <small class="text-muted">
                                                                                        เงินต้นก่อน: {{ number_format($journal->principal_before, 2) }} บาท
                                                                                        → หลัง: {{ number_format($journal->principal_after, 2) }} บาท
                                                                                    </small>
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @endif

                                                <!-- Notes Row -->
                                                @if($transaction->notes)
                                                    <tr class="table-light">
                                                        <td colspan="8">
                                                            <small class="text-muted">
                                                                <strong>หมายเหตุ:</strong> {{ $transaction->notes }}
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <strong>ℹ️ ยังไม่มีการจ่ายเงิน</strong><br>
                                    ใบแจ้งหนี้นี้ยังไม่มีรายการจ่ายเงินแต่อย่างใด
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            @if($invoice->outstanding_balance > 0)
                                <a href="{{ route('invoice.payment', $invoice) }}" class="btn btn-success btn-lg">
                                    💳 บันทึกการจ่ายเงิน
                                </a>
                            @else
                                <div class="alert alert-success" role="alert">
                                    <h6 class="alert-heading">✅ จ่ายเต็มจำนวนแล้ว!</h6>
                                    ใบแจ้งหนี้นี้ได้รับการชำระเต็มจำนวนเรียบร้อยแล้ว
                                </div>
                            @endif
                            
                            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary btn-lg ms-2">
                                กลับไปหน้ารายละเอียด
                            </a>
                        </div>
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
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
    }
</style>
@endsection
