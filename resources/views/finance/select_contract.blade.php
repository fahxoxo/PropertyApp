@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="mb-0">📋 เลือกสัญญา</h3>
                </div>
                <div class="card-body">
                    <!-- ข้อมูลลูกค้า -->
                    <div class="alert alert-info">
                        <h5>👥 ข้อมูลลูกค้า</h5>
                        <strong>ชื่อ:</strong> {{ $data->first_name }} {{ $data->last_name }}<br>
                        <strong>รหัส:</strong> {{ $data->code }}<br>
                        <strong>เบอร์โทร:</strong> {{ $data->phone }}
                    </div>

                    <!-- ตารางสัญญา -->
                    <h5 class="mb-3">เลือกสัญญาเพื่อรับชำระ</h5>

                    @php
                        $rentals = $data->rentalContracts;
                        $loans = $data->loanContracts;
                        $hasContracts = $rentals->count() > 0 || $loans->count() > 0;
                    @endphp

                    @if(!$hasContracts)
                        <div class="alert alert-warning">
                            ⚠️ ลูกค้ารายนี้ไม่มีสัญญาเช่าหรือขายฝากอยู่
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('finance.create') }}" class="btn btn-secondary">ย้อนกลับ</a>
                        </div>
                    @else
                        <!-- สัญญาเช่า -->
                        @if($rentals->count() > 0)
                        <div class="mb-4">
                            <h6 class="mb-3">🏠 สัญญาเช่าบ้าน</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>รหัสสัญญา</th>
                                            <th>บ้าน</th>
                                            <th>ราคาเช่า</th>
                                            <th>สถานะ</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rentals as $rental)
                                        <tr>
                                            <td><strong>{{ $rental->code }}</strong></td>
                                            <td>{{ $rental->property->name }}</td>
                                            <td>{{ number_format($rental->property->price, 2) }} ฿</td>
                                            <td>
                                                @if($rental->status === 'active')
                                                    <span class="badge bg-success">✓ ใช้งาน</span>
                                                @else
                                                    <span class="badge bg-secondary">ปิดแล้ว</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('finance.search') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="search_type" value="rental">
                                                    <input type="hidden" name="search_keyword" value="{{ $rental->code }}">
                                                    <button type="submit" class="btn btn-sm btn-primary">เลือก</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- สัญญาขายฝาก/จำนอง -->
                        @if($loans->count() > 0)
                        <div class="mb-4">
                            <h6 class="mb-3">💰 สัญญาขายฝาก/จำนอง</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>รหัสสัญญา</th>
                                            <th>ประเภท</th>
                                            <th>เงินต้น</th>
                                            <th>คงเหลือ</th>
                                            <th>สถานะ</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($loans as $loan)
                                        <tr>
                                            <td><strong>{{ $loan->code }}</strong></td>
                                            <td>{{ $loan->type }}</td>
                                            <td>{{ number_format($loan->principal, 2) }} ฿</td>
                                            <td>{{ number_format($loan->principal_remaining, 2) }} ฿</td>
                                            <td>
                                                <span class="badge bg-info">{{ $loan->status }}</span>
                                            </td>
                                            <td>
                                                <form action="{{ route('finance.search') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="search_type" value="loan">
                                                    <input type="hidden" name="search_keyword" value="{{ $loan->code }}">
                                                    <button type="submit" class="btn btn-sm btn-primary">เลือก</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('finance.create') }}" class="btn btn-secondary">ย้อนกลับ</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
