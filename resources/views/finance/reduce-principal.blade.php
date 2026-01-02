@extends('layouts.app')

@section('content')
<div class="container-fluid p-2 p-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('finance.index') }}" class="btn btn-secondary mb-3 w-100 w-md-auto">
                ← กลับไปหน้าหลัก
            </a>

            <!-- Reduce Principal Card -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0 fs-5 fs-md-4">📉 ลดเงินต้น</h3>
                </div>

                <div class="card-body">
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

                    <!-- Step 1: Select Loan Contract -->
                    <div class="mb-4">
                        <label for="loan_contract_id" class="form-label">
                            <strong>เลือกสัญญาเงินกู้ *</strong>
                        </label>
                        <select class="form-select" id="loan_contract_id" required>
                            <option value="">-- เลือกสัญญา --</option>
                        </select>
                        <small class="form-text text-muted d-block mt-2">
                            จะแสดงเฉพาะสัญญาที่อยู่ในสถานะ "อยู่ในสัญญา" เท่านั้น
                        </small>
                    </div>

                    <!-- Step 2: Display Loan Details -->
                    <div id="loanDetails" style="display: none;">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-2 mb-4">
                            <div class="col">
                                <div class="card bg-light h-100">
                                    <div class="card-body p-2 p-md-3">
                                        <h6 class="card-title text-muted fs-7 fs-md-6">เงินต้นคงเหลือ</h6>
                                        <p class="display-7 display-md-6 mb-0 text-danger" id="principalRemaining">
                                            0.00
                                        </p>
                                        <small>บาท</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card bg-light h-100">
                                    <div class="card-body p-2 p-md-3">
                                        <h6 class="card-title text-muted fs-7 fs-md-6">อัตราดอกเบี้ย</h6>
                                        <p class="display-7 display-md-6 mb-0 text-info" id="interestRate">
                                            0.00
                                        </p>
                                        <small>% ต่อเดือน</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card bg-light h-100">
                                    <div class="card-body p-2 p-md-3">
                                        <h6 class="card-title text-muted fs-7 fs-md-6">ดอกเบี้ยต่อเดือน</h6>
                                        <p class="display-7 display-md-6 mb-0 text-success" id="monthlyInterest">
                                            0.00
                                        </p>
                                        <small>บาท</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card bg-light h-100">
                                    <div class="card-body p-2 p-md-3">
                                        <h6 class="card-title text-muted fs-7 fs-md-6">ประเภท</h6>
                                        <p class="mb-0 fs-7 fs-md-6" id="loanType">
                                            -
                                        </p>
                                        <small id="loanCode" class="text-muted">-</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Input Principal Amount -->
                        <form method="POST" action="{{ route('finance.storePrincipal') }}">
                            @csrf

                            <input type="hidden" id="hiddenLoanId" name="loan_contract_id">

                            <div class="mb-4">
                                <label for="amount" class="form-label">
                                    <strong>จำนวนเงินลดต้น *</strong>
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
                                        id="maxPrincipal"
                                        value="{{ old('amount') }}"
                                        required>
                                    <span class="input-group-text">บาท</span>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    ยอดสูงสุด: <span id="maxAmount">0.00</span> บาท
                                </small>
                                @error('amount')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-sm-flex">
                                <button type="submit" class="btn btn-success btn-sm btn-md-lg flex-sm-grow-1">
                                    ✅ บันทึกลดเงินต้น
                                </button>
                                <a href="{{ route('finance.index') }}" class="btn btn-secondary btn-sm btn-md-lg flex-sm-grow-1">
                                    ❌ ยกเลิก
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('loan_contract_id');
    const loanDetailsDiv = document.getElementById('loanDetails');
    const principalRemaining = document.getElementById('principalRemaining');
    const interestRate = document.getElementById('interestRate');
    const monthlyInterest = document.getElementById('monthlyInterest');
    const loanType = document.getElementById('loanType');
    const loanCode = document.getElementById('loanCode');
    const maxAmount = document.getElementById('maxAmount');
    const amountInput = document.getElementById('amount');
    const hiddenLoanId = document.getElementById('hiddenLoanId');

    // โหลดรายชื่อสัญญา
    fetch('{{ route("finance.getActiveLoans") }}')
        .then(response => response.json())
        .then(data => {
            data.forEach(loan => {
                const option = document.createElement('option');
                option.value = loan.id;
                option.textContent = `${loan.code} - ${loan.customer} (${loan.type})`;
                option.dataset.principal = loan.principal_remaining;
                option.dataset.rate = loan.interest_rate;
                option.dataset.monthly = loan.monthly_interest;
                option.dataset.type = loan.type;
                option.dataset.code = loan.code;
                selectElement.appendChild(option);
            });
        });

    // เมื่อเลือกสัญญา
    selectElement.addEventListener('change', function() {
        if (this.value) {
            const option = this.options[this.selectedIndex];
            const principal = parseFloat(option.dataset.principal);
            const rate = parseFloat(option.dataset.rate);
            const monthly = parseFloat(option.dataset.monthly);

            principalRemaining.textContent = principal.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            interestRate.textContent = rate.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            monthlyInterest.textContent = monthly.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            loanType.textContent = option.dataset.type;
            loanCode.textContent = option.dataset.code;
            maxAmount.textContent = principal.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // ตั้งค่า max attribute
            amountInput.max = principal;
            
            // ตั้งค่า hidden field
            hiddenLoanId.value = this.value;

            loanDetailsDiv.style.display = 'block';
        } else {
            loanDetailsDiv.style.display = 'none';
            amountInput.value = '';
        }
    });

    // Validate input amount
    amountInput.addEventListener('change', function() {
        const principal = parseFloat(document.getElementById('maxPrincipal').max);
        if (parseFloat(this.value) > principal) {
            this.value = principal.toFixed(2);
        }
    });
});
</script>
@endsection
