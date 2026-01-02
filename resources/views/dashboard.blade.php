@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">📊 Dashboard</h1>
    
    <!-- สถิติพื้นฐาน -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">👥 ลูกค้า</h5>
                    <h2>{{ $customers }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">🏠 บ้านเช่า</h5>
                    <h2>{{ $properties }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">📝 สัญญาเช่า</h5>
                    <h2>{{ $rentals }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">💰 ขายฝาก/จำนอง</h5>
                    <h2>{{ $loans }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- รายรับและบ้านว่าง -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">💵 รายรับเดือนนี้</h5>
                    <h2 class="text-success">{{ number_format($income, 2) }} ฿</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body">
                    <h5 class="card-title">🔓 บ้านว่าง</h5>
                    <h2>{{ $vacantProperties }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title">📋 สัญญาปิด</h5>
                    <h2>{{ $rentalsClosed }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">⏰ ใกล้ครบกำหนด</h5>
                    <h2 class="text-danger">{{ $expiringTotal }}</h2>
                    <small class="text-muted">เช่า: {{ $expiringRentals }} | ฝาก: {{ $expiringLoans }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ค้างชำระ -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">⚠️ สัญญาเช่าค้างชำระ</h5>
                    <h2 class="text-danger">{{ $overdueRentals }}</h2>
                    <small>จำนวนสัญญาเช่าที่มีการจ่ายเงินค้างชำระ</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">⚠️ สัญญาขายฝากค้างชำระ</h5>
                    <h2 class="text-danger">{{ $overdueLoans }}</h2>
                    <small>จำนวนสัญญาขายฝาก/จำนองที่ค้างชำระ</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ปุ่มจัดการ -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>⚙️ จัดการข้อมูล</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('backup') }}" class="btn btn-primary">
                        📥 Download สำรองข้อมูล
                    </a>
                    
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#restoreModal">
                        📤 Restore ข้อมูล
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Restore -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore ข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>⚠️ ระวัง!</strong> การ Restore จะแทนที่ข้อมูลทั้งหมดในระบบ
                    </div>
                    <div class="mb-3">
                        <label for="backup_file" class="form-label">เลือกไฟล์ Backup (.sqlite)</label>
                        <input type="file" class="form-control" id="backup_file" name="backup_file" accept=".sqlite" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">Restore ข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
