@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary mb-3 w-100 w-md-auto">
                ← กลับไปหน้ารายละเอียด
            </a>

            <!-- Payment Card -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0 fs-5 fs-md-4">💳 บันทึกการจ่ายเงิน</h3>
                </div>

                <div class="card-body p-2 p-md-3">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>⚠️ เกิดข้อผิดพลาด:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Invoice Summary -->
                    <div class="row g-2 g-md-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body p-2 p-md-3">
                                    <h6 class="card-title text-muted fs-7 fs-md-6">ข้อมูลใบแจ้งหนี้</h6>
                                    <p class="mb-1 fs-8 fs-md-7">
                                        <strong>เลขที่:</strong> {{ $invoice->invoice_number }}
                                    </p>
                                    <p class="mb-1 fs-8 fs-md-7">
                                        <strong>เดือน/ปี:</strong> {{ $invoice->month }}/{{ $invoice->year }}
                                    </p>
                                    <p class="mb-0 fs-8 fs-md-7">
                                        <strong>ประเภท:</strong>
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
                                            @default
                                                📊 เงินกู้
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body p-2 p-md-3">
                                    <h6 class="card-title text-muted fs-7 fs-md-6">ข้อมูลการชำระ</h6>
                                    <p class="mb-1 fs-8 fs-md-7">
                                        <strong>ยอดรวม:</strong> 
                                        <span class="text-danger">{{ number_format($invoice->amount, 2) }}</span> บาท
                                    </p>
                                    <p class="mb-1 fs-8 fs-md-7">
                                        <strong>จ่ายแล้ว:</strong> 
                                        <span class="text-success">{{ number_format($invoice->paid_amount, 2) }}</span> บาท
                                    </p>
                                    <p class="mb-0 fs-8 fs-md-7">
                                        <strong>ค้างชำระ:</strong> 
                                        <span class="text-warning fw-bold">{{ number_format($invoice->outstanding_balance, 2) }}</span> บาท
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details (For Loan Contracts) -->
                    @if($invoice->billable instanceof App\Models\LoanContract)
                        <div class="card border-info mb-4">
                            <div class="card-body p-2 p-md-3">
                                <h6 class="card-title fs-7 fs-md-6">📊 รายละเอียดเงินกู้</h6>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <p class="mb-0 fs-8 fs-md-7">
                                            <strong>ดอกเบี้ยต่อเดือน:</strong>
                                            {{ number_format($invoice->interest_amount, 2) }} บาท
                                            ({{ $invoice->billable->interest_rate }}%)
                                        </p>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <p class="mb-0 fs-8 fs-md-7">
                                            <strong>เงินต้นคงเหลือ:</strong>
                                            {{ number_format($invoice->principal_at_billing, 2) }} บาท
                                        </p>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <p class="mb-0 fs-8 fs-md-7">
                                            <strong>ยอดค้างจากเดือนก่อน:</strong>
                                            {{ number_format($invoice->amount - $invoice->interest_amount, 2) }} บาท
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('invoice.recordPayment', $invoice) }}" class="mt-4">
                        @csrf

                        <div class="row g-2 g-md-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="amount" class="form-label">
                                    <strong>จำนวนเงินที่จ่าย *</strong>
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="number" 
                                        class="form-control @error('amount') is-invalid @enderror" 
                                        id="amount" 
                                        name="amount" 
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0.01"
                                        max="{{ $invoice->outstanding_balance }}"
                                        value="{{ old('amount') }}"
                                        required>
                                    <span class="input-group-text">บาท</span>
                                </div>
                                <small class="form-text text-muted d-block mt-1 fs-8 fs-md-7">
                                    ยอดสูงสุด: {{ number_format($invoice->outstanding_balance, 2) }} บาท
                                </small>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="payment_method" class="form-label">
                                    <strong>วิธีการชำระ *</strong>
                                </label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                    <option value="">-- เลือกวิธีการชำระ --</option>
                                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>💵 เงินสด</option>
                                    <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>🏦 โอนเงิน</option>
                                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>📝 เช็ค</option>
                                    <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>📋 อื่น ๆ</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="receipt_number" class="form-label">
                                <strong>หมายเลขใบเสร็จ</strong> (ตัวเลือก)
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('receipt_number') is-invalid @enderror" 
                                id="receipt_number" 
                                name="receipt_number" 
                                placeholder="หมายเลขใบเสร็จ"
                                value="{{ old('receipt_number') }}">
                            <small class="form-text text-muted d-block">
                                ถ้าไม่ระบุ ระบบจะสร้างอัตโนมัติ
                            </small>
                            @error('receipt_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <strong>หมายเหตุ</strong> (ตัวเลือก)
                            </label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" 
                                name="notes" 
                                rows="3"
                                placeholder="บันทึกหมายเหตุเพิ่มเติม">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-sm-flex mb-4">
                            <button type="submit" class="btn btn-success btn-sm btn-md-lg flex-sm-grow-1">
                                ✅ บันทึกการจ่ายเงิน
                            </button>
                            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary btn-sm btn-md-lg flex-sm-grow-1">
                                ❌ ยกเลิก
                            </a>
                        </div>
                    </form>

                    <!-- Quick Payment Buttons -->
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted mb-2 fs-8 fs-md-7"><strong>⚡ จ่ายอย่างรวดเร็ว:</strong></p>
                        <form id="quickPaymentForm" method="POST" action="{{ route('invoice.recordPayment', $invoice) }}" class="d-none">
                            @csrf
                            <input type="hidden" id="quickAmount" name="amount">
                            <input type="hidden" name="payment_method" value="cash">
                            <input type="hidden" id="quickReceiptNumber" name="receipt_number">
                        </form>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm btn-md" onclick="quickPayment({{ (float)$invoice->outstanding_balance }})">
                                จ่ายเต็มจำนวน ({{ number_format($invoice->outstanding_balance, 2) }}) บาท
                            </button>
                            @if($invoice->interest_amount > 0)
                                <button type="button" class="btn btn-outline-info btn-sm btn-md" onclick="quickPayment({{ (float)$invoice->interest_amount }})">
                                    จ่ายเฉพาะดอกเบี้ย ({{ number_format($invoice->interest_amount, 2) }}) บาท
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link to Payment History -->
            <div class="mt-3">
                <a href="{{ route('invoice.paymentHistory', $invoice) }}" class="btn btn-info w-100 w-md-auto">
                    📊 ดูประวัติการจ่ายเงิน
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function quickPayment(amount) {
    // Generate a unique receipt number based on timestamp
    const receiptNumber = 'QUICK-' + Date.now();
    document.getElementById('quickAmount').value = amount;
    document.getElementById('quickReceiptNumber').value = receiptNumber;
    document.getElementById('quickPaymentForm').submit();
}
</script>
@endsection
