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
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            color: #333;
            line-height: 1.6;
        }
        .info-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .info-section h3 {
            color: #007bff;
            margin-top: 0;
        }
        .info-item {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>إخطار جديد: تم الرد على طلب شراء</h1>
        </div>

        <div class="content">
            <p>تم تلقي رد من قبل مورد على طلب شراء. فيما يلي تفاصيل الرد:</p>

            <div class="info-section">
                <h3>بيانات المورد</h3>
                <div class="info-item">
                    <span class="label">اسم المورد:</span>
                    {{ $response->provider->name ?? 'غير محدد' }}
                </div>
                <div class="info-item">
                    <span class="label">البريد الإلكتروني:</span>
                    {{ $response->provider->email ?? 'غير محدد' }}
                </div>
            </div>

            <div class="info-section">
                <h3>بيانات طلب الشراء</h3>
                <div class="info-item">
                    <span class="label">رقم الطلب:</span>
                    #{{ $bookRequest->id }}
                </div>
                <div class="info-item">
                    <span class="label">العميل:</span>
                    {{ $bookRequest->user->name ?? 'غير محدد' }}
                </div>
                <div class="info-item">
                    <span class="label">الملاحظات:</span>
                    {{ $bookRequest->note ?? 'لا توجد ملاحظات' }}
                </div>
            </div>

            <div class="info-section">
                <h3>بيانات الرد</h3>
                <div class="info-item">
                    <span class="label">الكمية المتوفرة:</span>
                    {{ $response->available_quantity }} وحدة
                </div>
                <div class="info-item">
                    <span class="label">السعر:</span>
                    {{ $response->price }} ريال
                </div>
                @if ($response->tax_percentage)
                    <div class="info-item">
                        <span class="label">نسبة الضريبة:</span>
                        {{ $response->tax_percentage }}%
                    </div>
                @endif
                @if ($response->expected_delivery_date)
                    <div class="info-item">
                        <span class="label">تاريخ التسليم المتوقع:</span>
                        {{ $response->expected_delivery_date->format('Y-m-d') }}
                    </div>
                @endif
                @if ($response->note)
                    <div class="info-item">
                        <span class="label">ملاحظات المورد:</span>
                        {{ $response->note }}
                    </div>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>هذا إيميل تلقائي من نظام دار ورقة. يرجى عدم الرد على هذا البريد.</p>
        </div>
    </div>
</body>
</html>
