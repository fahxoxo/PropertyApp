@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">💳 บันทึกการชำระเงิน</h3>
                </div>
                <div class="card-body">
                    <!-- แสดงข้อมูลที่ค้นหาเจอ -->
                    <div class="alert alert-info">
                        <h5>📋 ข้อมูลที่ค้นหาเจอ</h5>
                        @if($searchType === 'rental')
                            <strong>📝 สัญญาเช่า:</strong> {{ $data->code }}<br>
                            <strong>ลูกค้า:</strong> {{ $data->customer->first_name }} {{ $data->customer->last_name }}<br>
                            <strong>บ้าน:</strong> {{ $data->property->name }}<br>
                            <strong>ราคาเช่า:</strong> {{ number_format($data->property->price, 2) }} ฿/เดือน
                        @elseif($searchType === 'loan')
                            <strong>💰 สัญญาขายฝาก/จำนอง:</strong> {{ $data->code }}<br>
                            <strong>ลูกค้า:</strong> {{ $data->customer->first_name }} {{ $data->customer->last_name }}<br>
                            <strong>เงินต้น:</strong> {{ number_format($data->principal, 2) }} ฿<br>
                            <strong>ประเภท:</strong> {{ $data->type }}
                        @endif
                    </div>

                    <!-- ฟอร์มบันทึก -->
                    <form action="{{ route('finance.store') }}" method="POST">
                        @csrf

                        <!-- Hidden Fields -->
                        <input type="hidden" name="payable_type" value="{{ $searchType === 'rental' ? 'App\Models\RentalContract' : ($searchType === 'loan' ? 'App\Models\LoanContract' : '') }}">
                        <input type="hidden" name="payable_id" value="{{ $data->id }}">

                        <!-- วันที่ -->
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">เลือกวันที่ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- สำหรับ Rental Contract -->
                        @if($searchType === 'rental')
                            <!-- จำนวนดอกเบี้ย (ไม่มี สำหรับสัญญาเช่า) -->
                            <div class="mb-3">
                                <label for="amount" class="form-label">จำนวนเงินที่ได้รับ (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        <!-- สำหรับ Loan Contract -->
                        @if($searchType === 'loan')
                            <input type="hidden" name="payment_type" id="payment_type" value="interest">

                            <!-- จำนวนดอกเบี้ย -->
                            <div class="mb-3">
                                <label for="interest" class="form-label">จำนวนดอกเบี้ย (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('interest') is-invalid @enderror" 
                                       id="interest" name="interest" value="{{ old('interest', 0) }}" step="0.01" min="0">
                                <small class="text-muted">ดอกเบี้ยเดือนนี้ = เงินต้นคงเหลือ × {{ $data->interest_rate }}% ÷ 12</small>
                                @error('interest')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- จำนวนเงินต้น (สำหรับตัดต้น) -->
                            <div class="mb-3">
                                <label for="principal_paid" class="form-label">จำนวนเงินต้นที่ได้รับ (บาท)</label>
                                <input type="number" class="form-control @error('principal_paid') is-invalid @enderror" 
                                       id="principal_paid" name="principal_paid" value="{{ old('principal_paid', 0) }}" step="0.01" min="0">
                                <small class="text-muted">เงินต้นคงเหลือปัจจุบัน: {{ number_format($data->principal_remaining, 2) }} ฿</small>
                                @error('principal_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- ช่องแสดงจำนวนเงินรวม -->
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">จำนวนเงินรวม (บาท)</label>
                                <input type="number" class="form-control" id="total_amount" readonly 
                                       value="{{ old('amount', 0) }}">
                            </div>

                            <script>
                                document.getElementById('interest').addEventListener('input', updateTotal);
                                document.getElementById('principal_paid').addEventListener('input', updateTotal);
                                
                                function updateTotal() {
                                    const interest = parseFloat(document.getElementById('interest').value) || 0;
                                    const principal = parseFloat(document.getElementById('principal_paid').value) || 0;
                                    const total = interest + principal;
                                    
                                    document.getElementById('total_amount').value = total.toFixed(2);
                                    
                                    // Update hidden payment_type field
                                    if (interest > 0 && principal > 0) {
                                        document.getElementById('payment_type').value = 'both';
                                    } else if (principal > 0) {
                                        document.getElementById('payment_type').value = 'principal';
                                    } else {
                                        document.getElementById('payment_type').value = 'interest';
                                    }
                                }
                            </script>
                        @endif

                        <!-- ช่องทางรับเงิน -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">ช่องทางรับเงิน <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="">-- เลือก --</option>
                                <option value="เงินสด" {{ old('payment_method') === 'เงินสด' ? 'selected' : '' }}>💵 เงินสด</option>
                                <option value="โอน" {{ old('payment_method') === 'โอน' ? 'selected' : '' }}>🏦 โอน</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- ปุ่มส่ง -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('finance.create') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-success btn-lg">💾 บันทึกการชำระ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
