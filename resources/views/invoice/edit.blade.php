@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary mb-3">
                ← กลับ
            </a>

            <!-- Edit Card -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">✏️ แก้ไขใบแจ้งหนี้ {{ $invoice->invoice_number }}</h3>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('invoice.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Read-only fields -->
                        <div class="mb-3">
                            <label class="form-label">เลขที่บิล</label>
                            <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ประเภท</label>
                            <input type="text" class="form-control" value="
                                @switch($invoice->type)
                                    @case('rental') บ้านเช่า @break
                                    @case('loan_sale') ขายฝาก @break
                                    @case('loan_mortgage') จำนอง @break
                                    @case('loan') เงินกู้ @break
                                @endswitch
                            " disabled>
                        </div>

                        <!-- Editable fields -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $invoice->amount) }}" 
                                   step="0.01" min="0" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- เลือกสถานะ --</option>
                                <option value="draft" {{ old('status', $invoice->status) === 'draft' ? 'selected' : '' }}>📝 ร่าง</option>
                                <option value="issued" {{ old('status', $invoice->status) === 'issued' ? 'selected' : '' }}>📤 ออกแล้ว</option>
                                <option value="paid" {{ old('status', $invoice->status) === 'paid' ? 'selected' : '' }}>✅ ชำระแล้ว</option>
                                <option value="overdue" {{ old('status', $invoice->status) === 'overdue' ? 'selected' : '' }}>⚠️逾期</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                ✅ บันทึกการเปลี่ยนแปลง
                            </button>
                            <a href="{{ route('invoice.show', $invoice) }}" class="btn btn-secondary">
                                ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
