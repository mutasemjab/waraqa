<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProvidersReportExport implements WithMultipleSheets
{
    private $data;
    private $exportOptions;

    public function __construct($data)
    {
        $this->data = $data;
        $this->exportOptions = $data['export_options'] ?? [];
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Provider Info
        if ($this->shouldInclude('provider_info')) {
            $sheets[] = new ProviderInfoSheet($this->data);
        }

        // Sheet 2: Statistics
        if ($this->shouldInclude('statistics')) {
            $sheets[] = new StatisticsSheet($this->data);
        }

        // Sheet 3: Products
        if ($this->shouldInclude('products')) {
            $sheets[] = new ProductsSheet($this->data);
        }

        // Sheet 4: Purchases
        if ($this->shouldInclude('purchases') && !empty($this->data['purchases'])) {
            $sheets[] = new PurchasesSheet($this->data);
        }

        // Sheet 5: Distribution
        if ($this->shouldInclude('distribution') && !empty($this->data['distributions'])) {
            $sheets[] = new DistributionSheet($this->data);
        }

        // Sheet 6: Sales
        if ($this->shouldInclude('sales') && !empty($this->data['sales'])) {
            $sheets[] = new SalesSheet($this->data);
        }

        // Sheet 7: Refunds
        if ($this->shouldInclude('refunds') && !empty($this->data['refunds'])) {
            $sheets[] = new RefundsSheet($this->data);
        }

        // Sheet 8: Sellers Payments
        if ($this->shouldInclude('sellers_payments') && !empty($this->data['payments'])) {
            $sheets[] = new SellersPaymentsSheet($this->data);
        }

        // Sheet 9: Stock Balance
        if ($this->shouldInclude('stock_balance') && !empty($this->data['stock'])) {
            $sheets[] = new StockBalanceSheet($this->data);
        }

        // Sheet 10: Book Requests
        if ($this->shouldInclude('book_requests') && !empty($this->data['requests'])) {
            $sheets[] = new BookRequestsSheet($this->data);
        }

        return $sheets;
    }

    private function shouldInclude($key)
    {
        return $this->exportOptions[$key] ?? true;
    }
}

// ============================================
// Individual Sheet Classes
// ============================================

class ProviderInfoSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'معلومات المورد';
    }

    public function collection()
    {
        $rows = new Collection();
        $provider = $this->data['provider'];

        $rows->push([__('messages.name'), $provider['name'] ?? '-']);
        $rows->push([__('messages.email'), $provider['email'] ?? '-']);
        $rows->push([__('messages.phone'), $provider['phone'] ?? '-']);
        $rows->push([__('messages.country'), $provider['country'] ?? '-']);
        $rows->push([__('messages.address'), $provider['address'] ?? '-']);
        $rows->push([__('messages.created_date'), $provider['created_at'] ?? '-']);
        $rows->push([__('messages.status'), $provider['activate'] == 1 ? 'مفعل' : 'غير مفعل']);

        return $rows;
    }

    public function headings(): array
    {
        return [__('messages.field'), __('messages.value')];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class StatisticsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'الإحصائيات';
    }

    public function collection()
    {
        $rows = new Collection();
        $stats = $this->data['statistics'];

        $rows->push([__('messages.total_products'), $stats['total_products'] ?? '-']);
        $rows->push([__('messages.total_quantity'), $stats['total_quantity'] ?? '-']);
        $rows->push([__('messages.total_revenue'), ($stats['total_revenue'] ?? '-') . ' ' . __('messages.SAR')]);
        $rows->push([__('messages.total_requests'), $stats['total_requests'] ?? '-']);
        $rows->push([__('messages.approved'), $stats['approved'] ?? '-']);
        $rows->push([__('messages.rejected'), $stats['rejected'] ?? '-']);
        $rows->push([__('messages.pending'), $stats['pending'] ?? '-']);
        $rows->push([__('messages.approval_rate'), ($stats['approval_rate'] ?? '-') . '%']);
        $rows->push([__('messages.total_import_value'), ($stats['total_import_value'] ?? '-') . ' ' . __('messages.SAR')]);
        $rows->push([__('messages.total_import_tax'), ($stats['total_import_tax'] ?? '-') . ' ' . __('messages.SAR')]);

        return $rows;
    }

    public function headings(): array
    {
        return [__('messages.statistic'), __('messages.value')];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class ProductsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'المنتجات';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['products'] as $index => $product) {
            $rows->push([
                $index + 1,
                $product['name'] ?? '-',
                $product['sku'] ?? '-',
                $product['unit_price'] ?? '-',
                $product['quantity'] ?? '-',
                $product['revenue'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            __('messages.product_name'),
            __('messages.sku'),
            __('messages.unit_price'),
            __('messages.total_quantity'),
            __('messages.total_revenue'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class PurchasesSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'المشتريات';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['purchases'] as $index => $purchase) {
            $totalWithTax = floatval($purchase['total_amount'] ?? 0) + floatval($purchase['total_tax'] ?? 0);
            $rows->push([
                $index + 1,
                $purchase['purchase_number'] ?? '-',
                $purchase['created_at'] ?? '-',
                number_format($purchase['total_amount'] ?? 0, 2),
                number_format($purchase['total_tax'] ?? 0, 2),
                number_format($totalWithTax, 2),
                $this->getPurchaseStatusText($purchase['status'] ?? '-'),
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            __('messages.purchase_number'),
            __('messages.date'),
            __('messages.total_amount'),
            __('messages.tax'),
            __('messages.total_with_tax'),
            __('messages.status'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9D00']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }

    private function getPurchaseStatusText($status)
    {
        $statuses = [
            'pending' => __('messages.pending'),
            'confirmed' => __('messages.confirmed'),
            'received' => __('messages.received'),
            'paid' => __('messages.paid'),
        ];

        return $statuses[$status] ?? $status;
    }
}

class DistributionSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'التوزيع على المكتبات';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['distributions'] as $index => $dist) {
            $rows->push([
                $index + 1,
                $dist['warehouse_name'] ?? '-',
                $dist['product_name'] ?? '-',
                $dist['quantity'] ?? '-',
                $dist['date'] ?? '-',
                $dist['note_voucher_number'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم المكتبة',
            'المنتج',
            'الكمية',
            'التاريخ',
            'رقم السند',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class SalesSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'المبيعات';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['sales'] as $index => $sale) {
            $rows->push([
                $index + 1,
                $sale['warehouse_name'] ?? '-',
                $sale['product_name'] ?? '-',
                $sale['quantity_sold'] ?? '-',
                number_format($sale['revenue'] ?? 0, 2),
                $sale['date'] ?? '-',
                $sale['order_number'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'المكتبة',
            'المنتج',
            'الكمية',
            'الإيرادات',
            'التاريخ',
            'رقم الطلب',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class RefundsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'المردود';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['refunds'] as $index => $refund) {
            $rows->push([
                $index + 1,
                $refund['warehouse_name'] ?? '-',
                $refund['product_name'] ?? '-',
                $refund['quantity_returned'] ?? '-',
                $refund['amount'] ?? '-',
                $refund['date'] ?? '-',
                $refund['order_number'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'المكتبة',
            'المنتج',
            'الكمية',
            'القيمة',
            'التاريخ',
            'رقم الطلب',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C5504B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class SellersPaymentsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'دفعات المكتبات';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['payments'] as $index => $payment) {
            $rows->push([
                $index + 1,
                $payment['seller_name'] ?? '-',
                $payment['total_orders_amount'] ?? '-',
                $payment['paid_amount'] ?? '-',
                $payment['remaining_amount'] ?? '-',
                $payment['payment_status'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'المكتبة',
            'إجمالي المبيعات',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
            'الحالة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9D00']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class StockBalanceSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'الرصيد المتبقي';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['stock'] as $index => $item) {
            $rows->push([
                $index + 1,
                $item['warehouse_name'] ?? '-',
                $item['product_name'] ?? '-',
                $item['quantity_distributed'] ?? '-',
                $item['quantity_sold'] ?? '-',
                $item['quantity_returned'] ?? '-',
                $item['quantity_remaining'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'المكتبة',
            'المنتج',
            'الموزع',
            'المباع',
            'المرتجع',
            'المتبقي',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B0F0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }
}

class BookRequestsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'طلبات الكتب';
    }

    public function collection()
    {
        $rows = new Collection();

        foreach ($this->data['requests'] as $index => $request) {
            $rows->push([
                $index + 1,
                $request['product_name'] ?? '-',
                $request['requested_quantity'] ?? '-',
                $request['available_quantity'] ?? '-',
                $request['price'] ?? '-',
                $request['tax_percentage'] ?? '-',
                $request['total_with_tax'] ?? '-',
                $this->getStatusText($request['status'] ?? '-'),
                $request['created_at'] ?? '-',
                $request['note'] ?? '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            __('messages.product_name'),
            __('messages.requested_quantity'),
            __('messages.available_quantity'),
            __('messages.price'),
            __('messages.tax'),
            __('messages.total_with_tax'),
            __('messages.status'),
            __('messages.date'),
            __('messages.notes'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7030A0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];
    }

    private function getStatusText($status)
    {
        $statuses = [
            'approved' => __('messages.approved'),
            'rejected' => __('messages.rejected'),
            'pending' => __('messages.pending'),
        ];

        return $statuses[$status] ?? $status;
    }
}
