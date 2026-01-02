<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ใบเสร็จรับเงิน</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url('file://{{ str_replace("\\", "/", storage_path("fonts/THSarabunNew.ttf")) }}') format('truetype');
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        .header h1 {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 12px;
            color: #666;
        }

        .receipt-number {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .receipt-number h3 {
            font-family: 'THSarabunNew', sans-serif;
            color: #28a745;
            font-size: 18px;
            margin: 0;
        }

        /* Sections */
        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-family: 'THSarabunNew', sans-serif;
            background: #ecf0f1;
            padding: 10px 15px;
            font-weight: bold;
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dotted #ddd;
        }

        .info-label {
            font-family: 'THSarabunNew', sans-serif;
            font-weight: bold;
            width: 40%;
            color: #2c3e50;
        }

        .info-value {
            font-family: 'THSarabunNew', sans-serif;
            width: 60%;
            text-align: right;
        }

        /* Amount Section */
        .amount-section {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .amount-row {
            font-family: 'THSarabunNew', sans-serif;
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        .amount-total {
            font-family: 'THSarabunNew', sans-serif;
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: bold;
            font-size: 20px;
            color: #28a745;
            border-bottom: 2px solid #28a745;
        }

        .amount-in-words {
            font-family: 'THSarabunNew', sans-serif;
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-style: italic;
            color: #666;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }

        .signature-box {
            text-align: center;
            width: 40%;
        }

        .signature-line {
            font-family: 'THSarabunNew', sans-serif;
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            font-family: 'THSarabunNew', sans-serif;
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }

        .thank-you {
            font-family: 'THSarabunNew', sans-serif;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'THSarabunNew', sans-serif;
        }

        th, td {
            font-family: 'THSarabunNew', sans-serif;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            border-bottom: 2px solid #333;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏢 ใบเสร็จรับเงิน</h1>
            <p>RECEIPT</p>
        </div>

        <!-- Receipt Number -->
        <div class="receipt-number">
            <h3>หมายเลขใบเสร็จ: {{ $transaction->receipt_number }}</h3>
        </div>

        <!-- Information Section -->
        <div class="section">
            <div class="section-title">📊 ข้อมูลการชำระเงิน</div>
            <div class="info-row">
                <span class="info-label">วันที่รับเงิน:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">ช่องทางชำระ:</span>
                <span class="info-value">{{ $transaction->payment_method }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">สถานะ:</span>
                <span class="info-value">
                    @if($transaction->status === 'paid')
                        ✓ ชำระแล้ว
                    @else
                        ⏳ {{ $transaction->status }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Payable Information -->
        <div class="section">
            <div class="section-title">📋 ข้อมูลสัญญา</div>
            @php
                $payable = $transaction->payable;
                if($payable) {
                    $isRental = get_class($payable) === 'App\Models\RentalContract';
                    $customer = $payable->customer;
                    $amount_due = $isRental ? $payable->property->price : $payable->principal;
                    $contract_type = $isRental ? 'สัญญาเช่าบ้าน' : 'สัญญาขายฝาก/จำนอง';
                    $contract_code = $payable->code;
                }
            @endphp
            @if($payable)
                <div class="info-row">
                    <span class="info-label">ประเภทสัญญา:</span>
                    <span class="info-value">{{ $contract_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">รหัสสัญญา:</span>
                    <span class="info-value">{{ $contract_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ชื่อลูกค้า:</span>
                    <span class="info-value">{{ $customer->first_name }} {{ $customer->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">รหัสลูกค้า:</span>
                    <span class="info-value">{{ $customer->code }}</span>
                </div>
            @endif

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-row">
                <span>จำนวนเงินที่ได้รับ:</span>
                <span class="text-right"><strong>{{ number_format($transaction->amount, 2) }}</strong> บาท</span>
            </div>
            <div class="amount-total">
                <span>รวมทั้งสิ้น</span>
                <span>{{ number_format($transaction->amount, 2) }} ฿</span>
            </div>
            <div class="amount-in-words">
                <span>อักษรไทย:</span>
                <span class="text-right">{{ thaiNumberToWord($transaction->amount) }}</span>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            ✓ ขอบคุณที่ไว้วางใจในการให้บริการ ✓
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>ลงชื่อผู้รับเงิน</p>
                <div class="signature-line">
                    <p>________________________</p>
                </div>
            </div>
            <div class="signature-box">
                <p>ลงชื่อเจ้าของ/ผู้มีอำนาจ</p>
                <div class="signature-line">
                    <p>________________________</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>เอกสารนี้พิมพ์ออกมาเมื่อ: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>ระบบบริหารจัดการบ้านเช่าและสัญญาขายฝาก</p>
        </div>
    </div>
</body>
</html>
