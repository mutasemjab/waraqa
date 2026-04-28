<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 3px solid #17a2b8;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #17a2b8;
            margin: 0;
        }
        .greeting {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .content {
            color: #333;
            line-height: 1.8;
        }
        .info-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-section h3 {
            color: #17a2b8;
            margin-top: 0;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 10px;
        }
        .info-item {
            margin: 10px 0;
            padding: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .items-table tr:hover {
            background-color: #f0f0f0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            background-color: #ffc107;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        .note-box {
            background-color: #cfe2ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 15px 0;
        }
        .note-box strong {
            color: #0d6efd;
        }
        .totals {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
        }
        .total-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💼 تسجيل مبيعات جديد من البائع</h1>
        </div>

        <div class="greeting">
            <p>السلام عليكم ورحمة الله وبركاته،</p>
            <p>تم تسجيل مبيعات جديد من أحد البائعين. يرجى مراجعة التفاصيل أدناه:</p>
        </div>

        <div class="content">
            <div class="info-section">
                <h3>👤 بيانات البائع</h3>
                <div class="info-item">
                    <span class="label">الاسم:</span>
                    {{ $sellerSale->user->name }}
                </div>
                <div class="info-item">
                    <span class="label">البريد الإلكتروني:</span>
                    {{ $sellerSale->user->email }}
                </div>
                <div class="info-item">
                    <span class="label">رقم المبيعات:</span>
                    {{ $sellerSale->sale_number }}
                </div>
                <div class="info-item">
                    <span class="label">تاريخ المبيعات:</span>
                    {{ $sellerSale->sale_date?->format('Y-m-d') ?? 'غير محدد' }}
                </div>
                <div class="info-item">
                    <span class="label">الحالة:</span>
                    <span class="status-badge">معلق للموافقة</span>
                </div>
                @if ($sellerSale->notes)
                    <div class="info-item">
                        <span class="label">ملاحظات:</span>
                        {{ $sellerSale->notes }}
                    </div>
                @endif
            </div>

            <div class="info-section">
                <h3>📦 المنتجات المباعة</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>اسم المنتج</th>
                            <th>الكمية</th>
                            <th>السعر (شامل الضريبة)</th>
                            <th>الضريبة %</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sellerSale->items as $item)
                            <tr>
                                <td>
                                    <strong>
                                        {{ app()->getLocale() === 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                    </strong>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price_with_tax, 2) }}</td>
                                <td>{{ number_format($item->tax_percentage, 2) }}%</td>
                                <td><strong>{{ number_format($item->total_price, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999;">لا توجد عناصر</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="totals">
                    <div class="total-row">
                        <span class="total-label">الإجمالي (بدون ضريبة):</span>
                        <span>{{ number_format($sellerSale->total_amount - $sellerSale->total_tax, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">إجمالي الضريبة:</span>
                        <span>{{ number_format($sellerSale->total_tax, 2) }}</span>
                    </div>
                    <div class="total-row" style="border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px;">
                        <span class="total-label" style="font-size: 16px;">الإجمالي الكلي:</span>
                        <span style="font-size: 16px; color: #17a2b8;"><strong>{{ number_format($sellerSale->total_amount, 2) }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="note-box">
                <strong>⏰ تنويه مهم:</strong> يرجى مراجعة ومعالجة هذا التسجيل والموافقة عليه أو رفضه.
            </div>
        </div>

        <div class="footer">
            <p>هذا إيميل تلقائي من نظام دار ورقة. يرجى عدم الرد على هذا البريد.</p>
            <p>للمزيد من المعلومات، يرجى تسجيل الدخول إلى لوحة التحكم الخاصة بك.</p>
        </div>
    </div>
</body>
</html>
