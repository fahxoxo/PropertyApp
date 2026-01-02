@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">💳 ระบบรับชำระเงิน</h3>
                </div>
                <div class="card-body">
                    <!-- ข้อความเตือน -->
                    @if($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Two Options -->
                    <div class="row">
                        <!-- Option 1: Search by Invoice (Recommended) -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-success border-2 h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">✅ วิธีที่ 1: ค้นหาจากใบแจ้งหนี้ (แนะนำ)</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        ค้นหาจากชื่อลูกค้า เบอร์โทร หรือเลขที่ใบแจ้งหนี้ และดูยอดค้างชำระทั้งหมด
                                    </p>
                                    <ul class="small mb-3">
                                        <li>✓ ป้องกันการรับชำระซ้ำซ้อน</li>
                                        <li>✓ ดูยอดค้างชำระได้ชัดเจน</li>
                                        <li>✓ รองรับการจ่ายบางส่วน</li>
                                        <li>✓ ติดตามประวัติการจ่าย</li>
                                    </ul>
                                    <form action="{{ route('finance.searchInvoices') }}" method="GET" class="mt-auto">
                                        <div class="input-group mb-3">
                                            <input type="text" name="search_keyword" class="form-control" 
                                                   placeholder="ค้นหาชื่อลูกค้า, เบอร์โทร, หรือเลขที่บิล">
                                            <button type="submit" class="btn btn-success">
                                                🔍 ค้นหา
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: Search by Contract (Legacy) -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-warning border-2 h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">📋 วิธีที่ 2: ค้นหาจากสัญญา</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        ค้นหาจากสัญญาเช่าหรือสัญญาขายฝาก (วิธีเดิม)
                                    </p>
                                    <ul class="small mb-3">
                                        <li>• รหัสสัญญาเช่า (RENT-xxx)</li>
                                        <li>• รหัสสัญญาขายฝาก (LOAN-xxx)</li>
                                        <li>• บันทึกใบเสร็จแบบเดิม</li>
                                    </ul>
                                    
                                    <form action="{{ route('finance.search') }}" method="POST" class="mt-auto">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="search_type" class="form-label small">
                                                <strong>เลือกประเภท</strong>
                                            </label>
                                            <select class="form-select form-select-sm" id="search_type" name="search_type" required>
                                                <option value="">-- เลือก --</option>
                                                <option value="rental">📝 สัญญาเช่าบ้าน</option>
                                                <option value="loan">💰 สัญญาขายฝาก/จำนอง</option>
                                                <option value="customer">👥 รหัสลูกค้า</option>
                                                <option value="phone">☎️ เบอร์โทรศัพท์</option>
                                            </select>
                                        </div>

                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="search_keyword" placeholder="ใส่รหัส/เบอร์โทร" required>
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                🔍 ค้นหา
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info mt-4">
                        <h6 class="alert-heading">💡 ข้อมูลเพิ่มเติม</h6>
                        <ul class="mb-0 small">
                            <li><strong>วิธีที่ 1 (แนะนำ):</strong> ใช้สำหรับส่วนใหญ่ - ค้นหาจากชื่อลูกค้าหรือเลขที่บิล จะแสดงใบแจ้งหนี้ทั้งหมดที่ค้างชำระ</li>
                            <li><strong>วิธีที่ 2:</strong> ใช้สำหรับบันทึกใบเสร็จแบบเดิม - ค้นหาจากรหัสสัญญาและบันทึกการจ่ายเงินตรง</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
