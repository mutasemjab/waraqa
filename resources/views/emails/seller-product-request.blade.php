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
            border-bottom: 3px solid #ff6b6b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ff6b6b;
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
            color: #ff6b6b;
            margin-top: 0;
            border-bottom: 2px solid #ff6b6b;
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
            background-color: #ff6b6b;
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
        }
        .status-pending {
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
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }
        .note-box strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 طلب منتجات جديد من البائع</h1>
        </div>

        <div class="greeting">
            <p>السلام عليكم ورحمة الله وبركاته،</p>
            <p>تم استقبال طلب منتجات جديد من أحد البائعين. يرجى مراجعة التفاصيل أدناه:</p>
        </div>

        <div class="content">
            <div class="info-section">
                <h3>👤 بيانات البائع</h3>
                <div class="info-item">
                    <span class="label">الاسم:</span>
                    {{ $sellerProductRequest->user->name }}
                </div>
                <div class="info-item">
                    <span class="label">البريد الإلكتروني:</span>
                    {{ $sellerProductRequest->user->email }}
                </div>
                <div class="info-item">
                    <span class="label">رقم الطلب:</span>
                    #{{ $sellerProductRequest->id }}
                </div>
                <div class="info-item">
                    <span class="label">التاريخ:</span>
                    {{ $sellerProductRequest->created_at->format('Y-m-d H:i') }}
                </div>
                <div class="info-item">
                    <span class="label">الحالة:</span>
                    <span class="status-badge status-pending">{{ \App\Enums\SellerProductRequestStatus::PENDING->value }}</span>
                </div>
                @if ($sellerProductRequest->note)
                    <div class="info-item">
                        <span class="label">ملاحظات:</span>
                        {{ $sellerProductRequest->note }}
                    </div>
                @endif
            </div>

            <div class="info-section">
                <h3>📋 المنتجات المطلوبة</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>اسم المنتج</th>
                            <th>الفئة</th>
                            <th>الكمية المطلوبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sellerProductRequest->items as $item)
                            <tr>
                                <td>
                                    <strong>
                                        {{ app()->getLocale() === 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                    </strong>
                                </td>
                                <td>
                                    {{ $item->product->category->name ?? 'غير محدد' }}
                                </td>
                                <td>{{ $item->requested_quantity }} وحدة</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #999;">لا توجد عناصر</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="note-box">
                <strong>⏰ تنويه مهم:</strong> يرجى معالجة هذا الطلب والتواصل مع البائع بأسرع وقت ممكن.
            </div>
        </div>

        <div class="footer">
            <p>هذا إيميل تلقائي من نظام دار ورقة. يرجى عدم الرد على هذا البريد.</p>
            <p>للمزيد من المعلومات، يرجى تسجيل الدخول إلى لوحة التحكم الخاصة بك.</p>
        </div>
    </div>
</body>
</html>
