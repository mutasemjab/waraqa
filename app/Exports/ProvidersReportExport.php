<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProvidersReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    private $provider;
    private $products;
    private $requests;
    private $statistics;

    public function __construct($data)
    {
        $this->provider = $data['provider'];
        $this->products = $data['products'];
        $this->requests = $data['requests'];
        $this->statistics = $data['statistics'];
    }

    public function collection()
    {
        $rows = new Collection();

        // 1. Provider Information
        $rows->push([__('messages.provider_details'), '', '', '', '', '', '', '']);
        $rows->push([__('messages.name'), $this->provider['name']]);
        $rows->push([__('messages.email'), $this->provider['email']]);
        $rows->push([__('messages.phone'), $this->provider['phone']]);
        $rows->push([__('messages.country'), $this->provider['country']]);
        $rows->push([]); // Empty row

        // 2. General Statistics
        $rows->push([__('messages.statistics'), '', '', '', '', '', '', '']);
        $rows->push([__('messages.total_products'), $this->statistics['total_products']]);
        $rows->push([__('messages.total_quantity'), $this->statistics['total_quantity']]);
        $rows->push([__('messages.total_revenue'), $this->statistics['total_revenue'] . ' ' . __('messages.SAR')]);
        $rows->push([]); // Empty row
        
        // 3. Products List
        $rows->push([__('messages.products_details'), '', '', '', '', '', '', '']);
        $rows->push([
            '#',
            __('messages.product_name'),
            __('messages.sku'),
            __('messages.unit_price'),
            __('messages.total_quantity'),
            __('messages.total_revenue'),
        ]);

        if (empty($this->products)) {
            $rows->push([__('messages.no_data_available')]);
        } else {
            foreach ($this->products as $index => $product) {
                $rows->push([
                    $index + 1,
                    $product['name'],
                    $product['sku'],
                    $product['unit_price'],
                    $product['quantity'],
                    $product['revenue']
                ]);
            }
        }
        $rows->push([]); // Empty row

        // 4. Book Requests
        $rows->push([__('messages.book_requests'), '', '', '', '', '', '', '']);
        
        // Book Requests Stats
        $rows->push([__('messages.total_requests'), $this->statistics['total_requests']]);
        $rows->push([__('messages.approved'), $this->statistics['approved']]);
        $rows->push([__('messages.rejected'), $this->statistics['rejected']]);
        $rows->push([__('messages.pending'), $this->statistics['pending']]);
        $rows->push([__('messages.approval_rate'), $this->statistics['approval_rate'] . '%']);
        $rows->push([__('messages.total_import_value'), $this->statistics['total_import_value']]);
        $rows->push([]);

        // Book Requests Table
        $rows->push([
            '#',
            __('messages.product_name'),
            __('messages.requested_quantity'),
            __('messages.available_quantity'),
            __('messages.price'),
            __('messages.tax'),
            __('messages.total_with_tax'),
            __('messages.status'),
            __('messages.date'),
            __('messages.notes')
        ]);

        if (empty($this->requests)) {
            $rows->push([__('messages.no_data_available')]);
        } else {
            foreach ($this->requests as $index => $request) {
                $rows->push([
                    $index + 1,
                    $request['product_name'],
                    $request['requested_quantity'],
                    $request['available_quantity'],
                    $request['price'],
                    $request['tax_percentage'],
                    $request['total_with_tax'],
                    __('messages.' . $request['status']), // Assuming keys exist or using status directly
                    $request['created_at'],
                    $request['note']
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            __('messages.providers_report'),
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Section headers styling
            'A7' => ['font' => ['bold' => true, 'underline' => true]], // Provider Details
            'A2' => ['font' => ['bold' => true, 'underline' => true]], 
             // We can't strictly modify rows by index easily if they are dynamic. 
             // Instead, let's keep it simple. The default styling is usually acceptable.
        ];
    }
}
