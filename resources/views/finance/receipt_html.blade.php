<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Sarabun', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
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
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .header p {
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
            color: #28a745;
            font-size: 18px;
            margin: 0;
            font-weight: 700;
        }

        /* Sections */
        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background: #ecf0f1;
            padding: 10px 15px;
            font-weight: 700;
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
            font-weight: 700;
            width: 40%;
            color: #2c3e50;
        }

        .info-value {
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
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        .amount-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: 700;
            font-size: 20px;
            color: #28a745;
            border-bottom: 2px solid #28a745;
        }

        .amount-in-words {
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
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }

        .thank-you {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #28a745;
            margin: 20px 0;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f5f5f5;
            font-weight: 700;
            border-bottom: 2px solid #333;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .print-button {
            text-align: center;
            margin: 20px 0;
        }

        .print-button button {
            padding: 10px 30px;
            font-size: 16px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Sarabun', sans-serif;
        }

        .print-button button:hover {
            background: #2980b9;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>ใบเสร็จรับเงิน</h1>
            <p>Receipt</p>
        </div>

        <!-- Receipt Number -->
        <div class="receipt-number">
            <h3>ใบเสร็จเลขที่: {{ $transaction->receipt_number }}</h3>
        </div>

        <!-- Receipt Number -->
        <div class="receipt-number">
            <h3>ใบเสร็จเลขที่: {{ $transaction->receipt_number }}</h3>
        </div>

        <!-- Transaction Info -->
        <div class="section">
            <div class="section-title">ข้อมูลการชำระเงิน</div>
            <div class="info-row">
                <div class="info-label">วันที่รับชำระ:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">วิธีชำระเงิน:</div>
                <div class="info-value">{{ $transaction->payment_method === 'cash' ? 'เงินสด' : 'โอนเงิน' }}</div>
            </div>
        </div>

        <!-- Customer Info -->
        @if($transaction->payable)
            @php
                $payable = $transaction->payable;
                $customer = $payable->customer;
            @endphp

            <div class="section">
                <div class="section-title">ข้อมูลลูกค้า</div>
                <div class="info-row">
                    <div class="info-label">ชื่อลูกค้า:</div>
                    <div class="info-value">{{ $customer->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">เลขประจำตัว:</div>
                    <div class="info-value">{{ $customer->id_card ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">เบอร์โทรศัพท์:</div>
                    <div class="info-value">{{ $customer->phone ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Contract Info -->
            @if(get_class($payable) === 'App\Models\RentalContract')
                <div class="section">
                    <div class="section-title">ข้อมูลสัญญาเช่า</div>
                    <div class="info-row">
                        <div class="info-label">เลขสัญญา:</div>
                        <div class="info-value">{{ $payable->code ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ที่อยู่:</div>
                        <div class="info-value">{{ $payable->property->address ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">วันเริ่มสัญญา:</div>
                        <div class="info-value">{{ $payable->start_date ? \Carbon\Carbon::parse($payable->start_date)->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                </div>
            @elseif(get_class($payable) === 'App\Models\LoanContract')
                <div class="section">
                    <div class="section-title">ข้อมูลสัญญากู้ยืมเงิน</div>
                    <div class="info-row">
                        <div class="info-label">เลขสัญญา:</div>
                        <div class="info-value">{{ $payable->code ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ยอดเงินกู้:</div>
                        <div class="info-value">{{ number_format($payable->principal ?? 0, 2) }} บาท</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">อัตราดอกเบี้ย:</div>
                        <div class="info-value">{{ $payable->interest_rate ?? 0 }}% ต่อปี</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ยอดคงเหลือ:</div>
                        <div class="info-value">{{ number_format($payable->principal_remaining ?? 0, 2) }} บาท</div>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-danger">ไม่พบข้อมูลสัญญา</div>
        @endif

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="section-title">รายละเอียดการชำระเงิน</div>
            
            @if($transaction->payment_type === 'interest')
                <div class="amount-row">
                    <span>ดอกเบี้ย:</span>
                    <span>{{ number_format($transaction->interest, 2) }} บาท</span>
                </div>
                <div class="amount-total">
                    <span>รวมทั้งสิ้น:</span>
                    <span>{{ number_format($transaction->interest, 2) }} บาท</span>
                </div>
                <div class="amount-in-words">
                    <span>จำนวนเงิน(ตัวอักษร):</span>
                    <span>{{ thaiNumberToWord($transaction->interest) }} บาทถ้วน</span>
                </div>
            @elseif($transaction->payment_type === 'principal')
                <div class="amount-row">
                    <span>เงินต้น:</span>
                    <span>{{ number_format($transaction->principal_paid, 2) }} บาท</span>
                </div>
                <div class="amount-total">
                    <span>รวมทั้งสิ้น:</span>
                    <span>{{ number_format($transaction->principal_paid, 2) }} บาท</span>
                </div>
                <div class="amount-in-words">
                    <span>จำนวนเงิน(ตัวอักษร):</span>
                    <span>{{ thaiNumberToWord($transaction->principal_paid) }} บาทถ้วน</span>
                </div>
            @elseif($transaction->payment_type === 'both')
                <div class="amount-row">
                    <span>ดอกเบี้ย:</span>
                    <span>{{ number_format($transaction->interest, 2) }} บาท</span>
                </div>
                <div class="amount-row">
                    <span>เงินต้น:</span>
                    <span>{{ number_format($transaction->principal_paid, 2) }} บาท</span>
                </div>
                <div class="amount-total">
                    <span>รวมทั้งสิ้น:</span>
                    <span>{{ number_format($transaction->amount, 2) }} บาท</span>
                </div>
                <div class="amount-in-words">
                    <span>จำนวนเงิน(ตัวอักษร):</span>
                    <span>{{ thaiNumberToWord($transaction->amount) }} บาทถ้วน</span>
                </div>
            @else
                <div class="amount-row">
                    <span>จำนวนเงิน:</span>
                    <span>{{ number_format($transaction->amount, 2) }} บาท</span>
                </div>
                <div class="amount-total">
                    <span>รวมทั้งสิ้น:</span>
                    <span>{{ number_format($transaction->amount, 2) }} บาท</span>
                </div>
                <div class="amount-in-words">
                    <span>จำนวนเงิน(ตัวอักษร):</span>
                    <span>{{ thaiNumberToWord($transaction->amount) }} บาทถ้วน</span>
                </div>
            @endif
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">ผู้รับชำระเงิน</div>
                <div style="margin-top: 20px; font-size: 12px;">(_____________________)</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">ลูกค้า / ผู้ชำระเงิน</div>
                <div style="margin-top: 20px; font-size: 12px;">(_____________________)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>ขอบคุณที่ใช้บริการของเรา</p>
            <p>Thank you for your payment</p>
        </div>
    </div>

    <div class="print-button">
        <button onclick="window.print()">🖨️ พิมพ์ใบเสร็จ</button>
        <button onclick="window.history.back()" style="background: #95a5a6; margin-left: 10px;">← กลับไปหน้าการเงิน</button>
    </div>
</body>
</html>
