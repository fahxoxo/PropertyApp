<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่า - {{ $rental->code }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 30px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            background: #f0f0f0;
            padding: 10px;
            font-weight: bold;
            border-left: 5px solid #000;
            margin-bottom: 10px;
        }
        .row {
            display: flex;
            margin-bottom: 10px;
        }
        .col {
            flex: 1;
        }
        .col-full {
            flex: 2;
        }
        label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
            font-size: 13px;
        }
        .value {
            font-size: 13px;
            border-bottom: 1px dotted #999;
            padding: 3px 0;
            min-width: 150px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }
        .signature {
            width: 250px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 30px;
            font-size: 13px;
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()" class="btn btn-primary">🖨️ พิมพ์</button>
        <button onclick="window.close()" class="btn btn-secondary">❌ ปิด</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>สัญญาเช่าบ้าน</h1>
            <p>รหัสสัญญา: <strong>{{ $rental->code }}</strong></p>
            <p>วันที่สร้าง: <strong>{{ \Carbon\Carbon::parse($rental->created_at)->format('d/m/Y') }}</strong></p>
        </div>

        <div class="section">
            <div class="section-title">1. ข้อมูลผู้ให้เช่า</div>
            <div class="row">
                <div class="col-full">
                    <label>ชื่อ:</label>
                    <span class="value">____________________________________</span>
                </div>
            </div>
            <div class="row">
                <div class="col-full">
                    <label>ที่อยู่:</label>
                    <span class="value">____________________________________</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>เบอร์โทร:</label>
                    <span class="value">____________________</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">2. ข้อมูลผู้เช่า (ผู้เสียค่าเช่า)</div>
            <div class="row">
                <div class="col-full">
                    <label>ชื่อ:</label>
                    <span class="value">{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-full">
                    <label>เลขบัตรประชาชน:</label>
                    <span class="value">{{ $rental->customer->id_card }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-full">
                    <label>เบอร์โทรศัพท์:</label>
                    <span class="value">{{ $rental->customer->phone }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">3. ข้อมูลสิ่งปลูกสร้าง (ทรัพย์สินที่ให้เช่า)</div>
            <div class="row">
                <div class="col-full">
                    <label>ชื่อบ้าน:</label>
                    <span class="value">{{ $rental->property->name }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-full">
                    <label>ที่อยู่:</label>
                    <span class="value">{{ $rental->property->address }} {{ $rental->property->moo ? 'หมู่ ' . $rental->property->moo : '' }} {{ $rental->property->subdistrict }} {{ $rental->property->district }} {{ $rental->property->province }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>ประเภท:</label>
                    <span class="value">{{ $rental->property->type }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">4. เงื่อนไขการเช่า</div>
            <div class="row">
                <div class="col">
                    <label>ราคาเช่าต่อเดือน:</label>
                    <span class="value">{{ number_format($rental->property->price, 2) }} ฿</span>
                </div>
                <div class="col">
                    <label>ค่ามัดจำ:</label>
                    <span class="value">{{ number_format($rental->deposit, 2) }} ฿</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>ค่าเช่าล่วงหน้า:</label>
                    <span class="value">{{ number_format($rental->advance_rent, 2) }} ฿</span>
                </div>
                <div class="col">
                    <label>วันเริ่มสัญญา:</label>
                    <span class="value">{{ \Carbon\Carbon::parse($rental->start_date)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">5. เงื่อนไขทั่วไป</div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-full">
                    <p style="font-size: 12px; line-height: 1.8; margin: 0;">
                        1. ผู้เช่าจะต้องชำระค่าเช่าให้ผู้ให้เช่าตรงตามวันกำหนด<br>
                        2. ผู้เช่าต้องดูแลรักษาทรัพย์สินให้อยู่ในสภาพที่ดี<br>
                        3. ผู้เช่าห้ามเช่าต่อให้ผู้อื่น<br>
                        4. ค่ามัดจำจะคืนให้เมื่อสิ้นสุดสัญญาหากไม่มีการเสียหาย<br>
                        5. ระยะเวลาการเช่า: ตามที่ตกลงกัน
                    </p>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature">
                <p>ผู้ให้เช่า</p>
                <p>_____________________</p>
                <p style="font-size: 12px;">( _____________________ )</p>
            </div>
            <div class="signature">
                <p>พยาน</p>
                <p>_____________________</p>
                <p style="font-size: 12px;">( _____________________ )</p>
            </div>
            <div class="signature">
                <p>ผู้เช่า</p>
                <p>_____________________</p>
                <p style="font-size: 12px;">( {{ $rental->customer->first_name }} {{ $rental->customer->last_name }} )</p>
            </div>
        </div>
    </div>

    <style>
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
    </style>
</body>
</html>
