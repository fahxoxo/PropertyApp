@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>📊 รายรับและรายงานการเงิน</h2>
        </div>
        <div class="col-md-4">
            <form action="{{ route('finance.revenue') }}" method="GET" class="d-flex gap-2">
                <select name="year" class="form-control" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            ปี {{ $y + 543 }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Current Month -->
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <strong>รายรับเดือนนี้</strong>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">
                        {{ number_format($currentMonthIncome, 2) }} ฿
                    </h2>
                    <small class="text-muted">{{ now()->format('F Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Current Year -->
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <strong>รายรับปีนี้</strong>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-info mb-0">
                        {{ number_format($currentYearIncome, 2) }} ฿
                    </h2>
                    <small class="text-muted">ปี {{ date('Y') + 543 }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Year Statistics -->
    @if($year != date('Y'))
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-warning">
                <div class="card-header bg-warning">
                    <strong>รายรับปี {{ $year + 543 }}</strong>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning mb-0">
                        {{ number_format($selectedYearIncome, 2) }} ฿
                    </h3>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Monthly Breakdown Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong>📈 รายรับรายเดือน ปี {{ $year + 543 }}</strong>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>เดือน</th>
                                <th class="text-end">รายรับ (บาท)</th>
                                <th class="text-end">จำนวนรายการ</th>
                                <th>ร้อยละ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $months = [
                                    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
                                    4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
                                    10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
                                ];
                                $totalTransactions = 0;
                                foreach($monthlyIncome as $count) {
                                    $totalTransactions += $count['count'];
                                }
                            @endphp

                            @foreach($monthlyIncome as $month => $data)
                            <tr>
                                <td>
                                    <strong>{{ $months[$month] }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success">
                                        {{ number_format($data['income'], 2) }} ฿
                                    </span>
                                </td>
                                <td class="text-end">
                                    {{ $data['count'] }} รายการ
                                </td>
                                <td>
                                    @php
                                        $percentage = $selectedYearIncome > 0 ? ($data['income'] / $selectedYearIncome * 100) : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <strong>📋 รายการชำระเงินปี {{ $year + 543 }}</strong>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>ใบเสร็จ</th>
                                    <th>ลูกค้า</th>
                                    <th>ประเภท</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                    <th>ช่องทาง</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('finance.receipt', $transaction->id) }}" target="_blank" class="text-decoration-none">
                                            {{ $transaction->receipt_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $payable = $transaction->payable;
                                            if(get_class($payable) === 'App\Models\RentalContract') {
                                                $customer = $payable->customer->first_name . ' ' . $payable->customer->last_name;
                                            } else {
                                                $customer = $payable->customer->first_name . ' ' . $payable->customer->last_name;
                                            }
                                        @endphp
                                        {{ $customer }}
                                    </td>
                                    <td>
                                        @php
                                            $payable = $transaction->payable;
                                            if(get_class($payable) === 'App\Models\RentalContract') {
                                                echo '🏠 สัญญาเช่า';
                                            } else {
                                                echo '💰 ขายฝาก/จำนอง';
                                            }
                                        @endphp
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($transaction->amount, 2) }} ฿
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->payment_method === 'เงินสด')
                                            <span class="badge bg-warning">💵 เงินสด</span>
                                        @else
                                            <span class="badge bg-primary">🏦 โอน</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->status === 'paid')
                                            <span class="badge bg-success">✓ ชำระแล้ว</span>
                                        @else
                                            <span class="badge bg-info">{{ $transaction->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <small class="text-muted">
                            แสดง {{ $transactions->count() }} รายการ
                        </small>
                        @if($transactions->hasPages())
                        <nav>
                            {{ $transactions->links() }}
                        </nav>
                        @endif
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        ❌ ไม่มีรายการชำระเงินสำหรับปี {{ $year + 543 }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid #dee2e6;
    }

    .card-header {
        font-weight: 600;
        padding: 1rem 1.25rem;
    }
</style>
@endsection
