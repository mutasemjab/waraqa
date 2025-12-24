<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NoteVouchersReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    private $data;
    private $statistics;

    public function __construct($data, $statistics)
    {
        $this->data = $data;
        $this->statistics = $statistics;
    }

    public function collection()
    {
        $rows = new Collection();

        // Add statistics header
        $rows->push([
            __('messages.Statistics'),
            '',
            '',
            '',
            '',
            '',
            '',
        ]);

        $rows->push([
            __('messages.Total') . ' ' . __('messages.noteVouchers') . ': ' . $this->statistics['total_vouchers'],
            __('messages.Total') . ' ' . __('messages.Quantity') . ': ' . number_format($this->statistics['total_quantity'], 2),
            __('messages.Total') . ' ' . __('messages.Value') . ': ' . number_format($this->statistics['total_value'], 2) . ' ' . __('messages.KD'),
            '',
            '',
            '',
            '',
        ]);

        $rows->push([]); // Empty row

        // Add detailed data headers
        $rows->push([
            __('messages.number'),
            __('messages.date_note_voucher'),
            __('messages.noteVoucherTypes'),
            __('messages.Provider'),
            __('messages.Quantity'),
            __('messages.Value'),
            __('messages.note'),
        ]);

        foreach ($this->data as $voucher) {
            $voucher_quantity = 0;
            $voucher_value = 0;

            foreach ($voucher->voucherProducts as $product) {
                $quantity = $product->quantity ?? 0;
                $price = $product->purchasing_price ?? 0;
                $voucher_quantity += $quantity;
                $voucher_value += $quantity * $price;
            }

            $warehouse = '';
            if ($voucher->fromWarehouse) {
                $warehouse .= __('messages.from_date') . ': ' . $voucher->fromWarehouse->name;
            }
            if ($voucher->toWarehouse) {
                if ($warehouse) $warehouse .= ' | ';
                $warehouse .= __('messages.to_date') . ': ' . $voucher->toWarehouse->name;
            }

            $rows->push([
                $voucher->number,
                $voucher->date_note_voucher->format('Y-m-d'),
                $voucher->noteVoucherType->name ?? __('messages.Unknown'),
                $warehouse ?: ($voucher->provider->name ?? '-'),
                $voucher_quantity,
                $voucher_value,
                $voucher->note ?? '-',
            ]);

            // Add products details
            foreach ($voucher->voucherProducts as $product) {
                $product_value = ($product->quantity ?? 0) * ($product->purchasing_price ?? 0);
                $rows->push([
                    '  - ' . ($product->product->name_ar ?? __('messages.Unknown')),
                    '',
                    '',
                    '',
                    $product->quantity ?? 0,
                    $product_value,
                    $product->note ?? '',
                ]);
            }

            $rows->push([]); // Empty row between vouchers
        }

        // Add statistics summary by type
        $rows->push([]); // Empty row
        $rows->push([__('messages.Statistics_by_Type'), '', '', '', '', '', '']);

        foreach ($this->statistics['by_type'] as $type => $stats) {
            $rows->push([
                $type,
                __('messages.Count') . ': ' . $stats['count'],
                __('messages.Quantity') . ': ' . number_format($stats['quantity'], 2),
                __('messages.Value') . ': ' . number_format($stats['value'], 2) . ' ' . __('messages.KD'),
                '',
                '',
                '',
            ]);
        }

        // Add provider statistics
        $rows->push([]); // Empty row
        $rows->push([__('messages.Statistics_by_Provider'), '', '', '', '', '', '']);

        foreach ($this->statistics['by_provider'] as $provider => $stats) {
            $rows->push([
                $provider,
                __('messages.Count') . ': ' . $stats['count'],
                __('messages.Quantity') . ': ' . number_format($stats['quantity'], 2),
                __('messages.Value') . ': ' . number_format($stats['value'], 2) . ' ' . __('messages.KD'),
                '',
                '',
                '',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            __('messages.Report'),
            '',
            '',
            '',
            '',
            '',
            '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Title styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Summary row
            2 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E6E6'],
                ],
            ],
        ];
    }
}