<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NoteVouchersReportExport;
use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\NoteVoucherType;
use App\Models\Warehouse;
use App\Models\Provider;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:noteVoucher-table')->only(['noteVouchersReport']);
    }

    public function noteVouchersReport(Request $request)
    {
        $query = NoteVoucher::with(['voucherProducts.product', 'noteVoucherType', 'provider', 'fromWarehouse', 'toWarehouse']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date_note_voucher', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('date_note_voucher', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('date_note_voucher', '<=', $request->to_date);
        }

        // Filter by note voucher type
        if ($request->filled('note_voucher_type_id')) {
            $query->where('note_voucher_type_id', $request->note_voucher_type_id);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                    ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        // Filter by provider
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        // Get data
        $data = $query->orderBy('date_note_voucher', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($data);

        // Get filters data
        $noteVoucherTypes = NoteVoucherType::all();
        $warehouses = Warehouse::all();
        $providers = Provider::all();

        // Handle export
        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(
                new NoteVouchersReportExport($data, $statistics),
                'تقرير_السندات_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        return view('admin.reports.noteVouchersReport', compact(
            'data',
            'statistics',
            'noteVoucherTypes',
            'warehouses',
            'providers'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_vouchers' => $data->count(),
            'total_quantity' => 0,
            'total_value' => 0,
            'by_type' => [],
            'by_warehouse' => [],
            'by_provider' => [],
        ];

        foreach ($data as $voucher) {
            // Calculate quantity and value
            $voucher_quantity = 0;
            $voucher_value = 0;

            foreach ($voucher->voucherProducts as $product) {
                $quantity = $product->quantity ?? 0;
                $price = $product->purchasing_price ?? 0;

                $voucher_quantity += $quantity;
                $voucher_value += $quantity * $price;
            }

            $stats['total_quantity'] += $voucher_quantity;
            $stats['total_value'] += $voucher_value;

            // By type
            $type_name = $voucher->noteVoucherType->name ?? 'Unknown';
            if (!isset($stats['by_type'][$type_name])) {
                $stats['by_type'][$type_name] = ['count' => 0, 'quantity' => 0, 'value' => 0];
            }
            $stats['by_type'][$type_name]['count']++;
            $stats['by_type'][$type_name]['quantity'] += $voucher_quantity;
            $stats['by_type'][$type_name]['value'] += $voucher_value;

            // By provider
            if ($voucher->provider_id) {
                $provider_name = $voucher->provider->name ?? 'Unknown';
                if (!isset($stats['by_provider'][$provider_name])) {
                    $stats['by_provider'][$provider_name] = ['count' => 0, 'quantity' => 0, 'value' => 0];
                }
                $stats['by_provider'][$provider_name]['count']++;
                $stats['by_provider'][$provider_name]['quantity'] += $voucher_quantity;
                $stats['by_provider'][$provider_name]['value'] += $voucher_value;
            }

            // By warehouse
            if ($voucher->from_warehouse_id) {
                $warehouse_name = $voucher->fromWarehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouse_name])) {
                    $stats['by_warehouse'][$warehouse_name] = ['count' => 0, 'quantity' => 0, 'value' => 0];
                }
                $stats['by_warehouse'][$warehouse_name]['count']++;
                $stats['by_warehouse'][$warehouse_name]['quantity'] += $voucher_quantity;
                $stats['by_warehouse'][$warehouse_name]['value'] += $voucher_value;
            }

            if ($voucher->to_warehouse_id) {
                $warehouse_name = $voucher->toWarehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouse_name])) {
                    $stats['by_warehouse'][$warehouse_name] = ['count' => 0, 'quantity' => 0, 'value' => 0];
                }
                $stats['by_warehouse'][$warehouse_name]['count']++;
                $stats['by_warehouse'][$warehouse_name]['quantity'] += $voucher_quantity;
                $stats['by_warehouse'][$warehouse_name]['value'] += $voucher_value;
            }
        }

        return $stats;
    }
}