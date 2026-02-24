<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProvidersReportExport implements FromCollection, WithStyles, WithEvents, WithColumnWidths
{
    private $data;
    private $exportOptions;
    private $maxCols = 10; // max columns needed (book requests has 10)
    private $titleRows = [];     // section title rows
    private $headingRows = [];   // column heading rows
    private $dataRows = [];      // data rows ranges [startRow, endRow]
    private $noDataRows = [];    // "no data" rows
    private $separatorRows = []; // empty separator rows
    private $currentRow = 1;
    private $mergeRanges = [];   // cells to merge for titles

    // Section colors
    private $sectionColors = [
        'provider_info'    => '2F5496',
        'statistics'       => '548235',
        'products'         => '2E75B6',
        'purchases'        => 'BF8F00',
        'distribution'     => '2E75B6',
        'sales'            => '548235',
        'refunds'          => 'C00000',
        'sellers_payments' => 'BF8F00',
        'stock_balance'    => '0070C0',
        'book_requests'    => '7030A0',
    ];

    public function __construct($data)
    {
        $this->data = $data;
        $this->exportOptions = $data['export_options'] ?? [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 22,
            'D' => 18,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 16,
            'I' => 16,
            'J' => 20,
        ];
    }

    public function collection()
    {
        $rows = new Collection();

        // Section 1: Provider Info
        if ($this->shouldInclude('provider_info')) {
            $this->addSectionTitle($rows, 'معلومات المورد', 'provider_info');
            $this->addHeadingRow($rows, [__('messages.field'), __('messages.value')], 'provider_info');

            $provider = $this->data['provider'];
            $dataStart = $this->currentRow;

            if ($this->shouldIncludeField('provider_name')) {
                $this->addDataRow($rows, [__('messages.name'), $provider['name'] ?? '-']);
            }
            if ($this->shouldIncludeField('provider_email')) {
                $this->addDataRow($rows, [__('messages.email'), $provider['email'] ?? '-']);
            }
            if ($this->shouldIncludeField('provider_phone')) {
                $this->addDataRow($rows, [__('messages.phone'), $provider['phone'] ?? '-']);
            }
            if ($this->shouldIncludeField('provider_country')) {
                $this->addDataRow($rows, [__('messages.country'), $provider['country'] ?? '-']);
            }
            if ($this->shouldIncludeField('provider_address')) {
                $this->addDataRow($rows, [__('messages.address'), $provider['address'] ?? '-']);
            }
            $this->addDataRow($rows, [__('messages.created_date'), $provider['created_at'] ?? '-']);
            $this->addDataRow($rows, [__('messages.status'), $provider['activate'] == 1 ? 'مفعل' : 'غير مفعل']);

            $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'provider_info'];
            $this->addSeparator($rows);
        }

        // Section 2: Statistics
        if ($this->shouldInclude('statistics')) {
            $stats = $this->data['statistics'];

            $this->addSectionTitle($rows, 'الإحصائيات', 'statistics');
            $this->addHeadingRow($rows, [__('messages.statistic'), __('messages.value')], 'statistics');

            $dataStart = $this->currentRow;

            if ($this->shouldIncludeField('total_products')) {
                $this->addDataRow($rows, [__('messages.total_products'), $stats['total_products'] ?? '-']);
            }
            if ($this->shouldIncludeField('total_quantity')) {
                $this->addDataRow($rows, [__('messages.total_quantity'), $stats['total_quantity'] ?? '-']);
            }
            if ($this->shouldIncludeField('total_revenue')) {
                $this->addDataRow($rows, [__('messages.total_revenue'), ($stats['total_revenue'] ?? '-') . ' ' . __('messages.SAR')]);
            }

            // Book request statistics (controlled by book_stats option)
            if ($this->shouldIncludeField('book_stats')) {
                $this->addDataRow($rows, [__('messages.total_requests'), $stats['total_requests'] ?? '-']);
                $this->addDataRow($rows, [__('messages.approved'), $stats['approved'] ?? '-']);
                $this->addDataRow($rows, [__('messages.rejected'), $stats['rejected'] ?? '-']);
                $this->addDataRow($rows, [__('messages.pending'), $stats['pending'] ?? '-']);
                $this->addDataRow($rows, [__('messages.approval_rate'), ($stats['approval_rate'] ?? '-') . '%']);
                $this->addDataRow($rows, [__('messages.total_import_value'), ($stats['total_import_value'] ?? '-') . ' ' . __('messages.SAR')]);
                $this->addDataRow($rows, [__('messages.total_import_tax'), ($stats['total_import_tax'] ?? '-') . ' ' . __('messages.SAR')]);
            }

            $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'statistics'];
            $this->addSeparator($rows);
        }

        // Section 3: Products
        if ($this->shouldInclude('products')) {
            $this->addSectionTitle($rows, 'المنتجات', 'products');
            $this->addHeadingRow($rows, ['#', __('messages.product_name'), __('messages.sku'), __('messages.unit_price'), __('messages.total_quantity'), __('messages.total_revenue')], 'products');

            if (!empty($this->data['products'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['products'] as $index => $product) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $product['name'] ?? '-',
                        $product['sku'] ?? '-',
                        $product['unit_price'] ?? '-',
                        $product['quantity'] ?? '-',
                        $product['revenue'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'products'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 4: Purchases
        if ($this->shouldInclude('purchases')) {
            $this->addSectionTitle($rows, 'المشتريات', 'purchases');
            $this->addHeadingRow($rows, ['#', __('messages.purchase_number'), __('messages.date'), __('messages.total_amount'), __('messages.tax'), __('messages.total_with_tax'), __('messages.status')], 'purchases');

            if (!empty($this->data['purchases'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['purchases'] as $index => $purchase) {
                    $totalWithTax = floatval($purchase['total_amount'] ?? 0) + floatval($purchase['total_tax'] ?? 0);
                    $this->addDataRow($rows, [
                        $index + 1,
                        $purchase['purchase_number'] ?? '-',
                        $purchase['created_at'] ?? '-',
                        number_format($purchase['total_amount'] ?? 0, 2),
                        number_format($purchase['total_tax'] ?? 0, 2),
                        number_format($totalWithTax, 2),
                        $this->getPurchaseStatusText($purchase['status'] ?? '-'),
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'purchases'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 5: Distribution
        if ($this->shouldInclude('distribution')) {
            $this->addSectionTitle($rows, 'التوزيع على المكتبات', 'distribution');
            $this->addHeadingRow($rows, ['#', 'اسم المكتبة', 'المنتج', 'الكمية', 'التاريخ', 'رقم السند'], 'distribution');

            if (!empty($this->data['distributions'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['distributions'] as $index => $dist) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $dist['warehouse_name'] ?? '-',
                        $dist['product_name'] ?? '-',
                        $dist['quantity'] ?? '-',
                        $dist['date'] ?? '-',
                        $dist['note_voucher_number'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'distribution'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 6: Sales
        if ($this->shouldInclude('sales')) {
            $this->addSectionTitle($rows, 'المبيعات', 'sales');
            $this->addHeadingRow($rows, ['#', 'المكتبة', 'المنتج', 'الكمية', 'الإيرادات', 'التاريخ', 'رقم الطلب'], 'sales');

            if (!empty($this->data['sales'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['sales'] as $index => $sale) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $sale['warehouse_name'] ?? '-',
                        $sale['product_name'] ?? '-',
                        $sale['quantity_sold'] ?? '-',
                        number_format($sale['revenue'] ?? 0, 2),
                        $sale['date'] ?? '-',
                        $sale['order_number'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'sales'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 7: Refunds
        if ($this->shouldInclude('refunds')) {
            $this->addSectionTitle($rows, 'المردود', 'refunds');
            $this->addHeadingRow($rows, ['#', 'المكتبة', 'المنتج', 'الكمية', 'القيمة', 'التاريخ', 'رقم الطلب'], 'refunds');

            if (!empty($this->data['refunds'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['refunds'] as $index => $refund) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $refund['warehouse_name'] ?? '-',
                        $refund['product_name'] ?? '-',
                        $refund['quantity_returned'] ?? '-',
                        $refund['amount'] ?? '-',
                        $refund['date'] ?? '-',
                        $refund['order_number'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'refunds'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 8: Sellers Payments
        if ($this->shouldInclude('sellers_payments')) {
            $this->addSectionTitle($rows, 'دفعات المكتبات', 'sellers_payments');
            $this->addHeadingRow($rows, ['#', 'المكتبة', 'إجمالي المبيعات', 'المبلغ المدفوع', 'المبلغ المتبقي', 'الحالة'], 'sellers_payments');

            if (!empty($this->data['payments'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['payments'] as $index => $payment) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $payment['seller_name'] ?? '-',
                        $payment['total_orders_amount'] ?? '-',
                        $payment['paid_amount'] ?? '-',
                        $payment['remaining_amount'] ?? '-',
                        $payment['payment_status'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'sellers_payments'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 9: Stock Balance
        if ($this->shouldInclude('stock_balance')) {
            $this->addSectionTitle($rows, 'الرصيد المتبقي', 'stock_balance');
            $this->addHeadingRow($rows, ['#', 'المكتبة', 'المنتج', 'الموزع', 'المباع', 'المرتجع', 'المتبقي'], 'stock_balance');

            if (!empty($this->data['stock'])) {
                $dataStart = $this->currentRow;
                foreach ($this->data['stock'] as $index => $item) {
                    $this->addDataRow($rows, [
                        $index + 1,
                        $item['warehouse_name'] ?? '-',
                        $item['product_name'] ?? '-',
                        $item['quantity_distributed'] ?? '-',
                        $item['quantity_sold'] ?? '-',
                        $item['quantity_returned'] ?? '-',
                        $item['quantity_remaining'] ?? '-',
                    ]);
                }
                $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'stock_balance'];
            } else {
                $this->addNoDataRow($rows);
            }
            $this->addSeparator($rows);
        }

        // Section 10: Book Requests
        if ($this->shouldInclude('book_requests')) {
            $this->addSectionTitle($rows, 'طلبات الكتب', 'book_requests');

            $includeDetails = $this->shouldIncludeField('book_details');

            if ($includeDetails) {
                $this->addHeadingRow($rows, ['#', __('messages.product_name'), __('messages.requested_quantity'), __('messages.available_quantity'), __('messages.price'), __('messages.tax'), __('messages.total_with_tax'), __('messages.status'), __('messages.date'), __('messages.notes')], 'book_requests');

                if (!empty($this->data['requests'])) {
                    $dataStart = $this->currentRow;
                    foreach ($this->data['requests'] as $index => $request) {
                        $this->addDataRow($rows, [
                            $index + 1,
                            $request['product_name'] ?? '-',
                            $request['requested_quantity'] ?? '-',
                            $request['available_quantity'] ?? '-',
                            $request['price'] ?? '-',
                            $request['tax_percentage'] ?? '-',
                            $request['total_with_tax'] ?? '-',
                            $this->getBookRequestStatusText($request['status'] ?? '-'),
                            $request['created_at'] ?? '-',
                            $request['note'] ?? '-',
                        ]);
                    }
                    $this->dataRows[] = [$dataStart, $this->currentRow - 1, 'book_requests'];
                } else {
                    $this->addNoDataRow($rows);
                }
            } else {
                $this->addNoDataRow($rows);
            }
        }

        return $rows;
    }

    /**
     * Add a section title row (merged across all columns)
     */
    private function addSectionTitle(Collection &$rows, string $title, string $sectionKey)
    {
        $row = array_pad([$title], $this->maxCols, '');
        $rows->push($row);
        $this->titleRows[] = ['row' => $this->currentRow, 'section' => $sectionKey];
        $lastCol = $this->getColumnLetter($this->maxCols - 1);
        $this->mergeRanges[] = "A{$this->currentRow}:{$lastCol}{$this->currentRow}";
        $this->currentRow++;
    }

    /**
     * Add a heading row for table columns
     */
    private function addHeadingRow(Collection &$rows, array $headings, string $sectionKey)
    {
        $row = array_pad($headings, $this->maxCols, '');
        $rows->push($row);
        $this->headingRows[] = ['row' => $this->currentRow, 'section' => $sectionKey, 'colCount' => count($headings)];
        $this->currentRow++;
    }

    /**
     * Add a data row
     */
    private function addDataRow(Collection &$rows, array $data)
    {
        $row = array_pad($data, $this->maxCols, '');
        $rows->push($row);
        $this->currentRow++;
    }

    /**
     * Add a "no data" row
     */
    private function addNoDataRow(Collection &$rows)
    {
        $row = array_pad(['لا توجد بيانات'], $this->maxCols, '');
        $rows->push($row);
        $this->noDataRows[] = $this->currentRow;
        $lastCol = $this->getColumnLetter($this->maxCols - 1);
        $this->mergeRanges[] = "A{$this->currentRow}:{$lastCol}{$this->currentRow}";
        $this->currentRow++;
    }

    /**
     * Add separator rows for spacing between sections
     */
    private function addSeparator(Collection &$rows)
    {
        $emptyRow = array_pad([''], $this->maxCols, '');
        $rows->push($emptyRow);
        $this->separatorRows[] = $this->currentRow;
        $this->currentRow++;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];

        // Style section title rows
        foreach ($this->titleRows as $info) {
            $row = $info['row'];
            $color = $this->sectionColors[$info['section']] ?? '2F5496';
            $styles[$row] = [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
            ];
        }

        // Style heading rows
        foreach ($this->headingRows as $info) {
            $row = $info['row'];
            $color = $this->sectionColors[$info['section']] ?? '2F5496';
            // Lighter shade for headings
            $lighterColor = $this->lightenColor($color);
            $styles[$row] = [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $lighterColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '666666']]],
            ];
        }

        // Style "no data" rows
        foreach ($this->noDataRows as $row) {
            $styles[$row] = [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '999999'], 'italic' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ];
        }

        // Style data rows with borders and alternating colors
        foreach ($this->dataRows as $range) {
            [$startRow, $endRow, $sectionKey] = $range;
            for ($r = $startRow; $r <= $endRow; $r++) {
                $isOdd = ($r - $startRow) % 2 === 0;
                $bgColor = $isOdd ? 'FFFFFF' : 'F2F2F2';
                $styles[$r] = [
                    'font' => ['size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                ];
            }
        }

        // Set row heights
        foreach ($this->titleRows as $info) {
            $sheet->getRowDimension($info['row'])->setRowHeight(32);
        }
        foreach ($this->headingRows as $info) {
            $sheet->getRowDimension($info['row'])->setRowHeight(26);
        }
        foreach ($this->noDataRows as $row) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }
        foreach ($this->separatorRows as $row) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // Set RTL direction
        $sheet->setRightToLeft(true);

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge cells for titles and "no data" rows
                foreach ($this->mergeRanges as $range) {
                    $sheet->mergeCells($range);
                }
            },
        ];
    }

    private function getColumnLetter(int $index): string
    {
        return chr(65 + $index); // A=0, B=1, etc.
    }

    private function lightenColor(string $hex): string
    {
        // Return a slightly lighter version for heading rows
        $lightMap = [
            '2F5496' => '4472C4',
            '548235' => '70AD47',
            '2E75B6' => '5B9BD5',
            'BF8F00' => 'FFC000',
            'C00000' => 'FF4444',
            '0070C0' => '00B0F0',
            '7030A0' => '9B59B6',
        ];

        return $lightMap[$hex] ?? $hex;
    }

    private function shouldInclude($key)
    {
        return $this->exportOptions[$key] ?? true;
    }

    private function shouldIncludeField($field)
    {
        return $this->exportOptions[$field] ?? true;
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

    private function getBookRequestStatusText($status)
    {
        $statuses = [
            'approved' => __('messages.approved'),
            'rejected' => __('messages.rejected'),
            'pending' => __('messages.pending'),
        ];

        return $statuses[$status] ?? $status;
    }
}
