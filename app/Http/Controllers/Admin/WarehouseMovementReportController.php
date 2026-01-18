<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Http\Request;

class WarehouseMovementReportController extends Controller
{
    public function index(Request $request)
    {
        $query = NoteVoucher::with([
            'voucherProducts.product',
            'noteVoucherType',
            'provider',
            'fromWarehouse',
            'toWarehouse'
        ]);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date_note_voucher', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('date_note_voucher', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('date_note_voucher', '<=', $request->to_date);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                    ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->whereHas('voucherProducts', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by provider
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by movement type
        if ($request->filled('movement_type')) {
            $query->where('note_voucher_type_id', $request->movement_type);
        }

        // Get data
        $movements = $query->orderBy('date_note_voucher', 'desc')->paginate(20);

        // Calculate statistics
        $statistics = $this->calculateStatistics($query->get());

        // Get filters data
        $warehouses = Warehouse::all();
        $products = Product::all();
        $providers = Provider::all();

        return view('admin.reports.warehouseMovement', compact(
            'movements',
            'statistics',
            'warehouses',
            'products',
            'providers'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_movements' => $data->count(),
            'total_quantity_in' => 0,
            'total_quantity_out' => 0,
            'total_value_in' => 0,
            'total_value_out' => 0,
            'by_warehouse' => [],
            'by_product' => [],
            'movement_summary' => []
        ];

        foreach ($data as $movement) {
            $is_incoming = $movement->note_voucher_type_id == 1; // Assuming 1 is receipt
            $is_outgoing = $movement->note_voucher_type_id == 2; // Assuming 2 is release

            foreach ($movement->voucherProducts as $voucherProduct) {
                $quantity = $voucherProduct->quantity ?? 0;
                $price = $voucherProduct->purchasing_price ?? ($voucherProduct->product->selling_price ?? 0);
                $value = $quantity * $price;

                // Track incoming and outgoing
                if ($is_incoming) {
                    $stats['total_quantity_in'] += $quantity;
                    $stats['total_value_in'] += $value;
                } elseif ($is_outgoing) {
                    $stats['total_quantity_out'] += $quantity;
                    $stats['total_value_out'] += $value;
                }

                // By product
                $product_name = $voucherProduct->product->name ?? 'Unknown';
                if (!isset($stats['by_product'][$product_name])) {
                    $stats['by_product'][$product_name] = [
                        'quantity_in' => 0,
                        'quantity_out' => 0,
                        'value_in' => 0,
                        'value_out' => 0
                    ];
                }

                if ($is_incoming) {
                    $stats['by_product'][$product_name]['quantity_in'] += $quantity;
                    $stats['by_product'][$product_name]['value_in'] += $value;
                } elseif ($is_outgoing) {
                    $stats['by_product'][$product_name]['quantity_out'] += $quantity;
                    $stats['by_product'][$product_name]['value_out'] += $value;
                }
            }

            // By warehouse
            if ($movement->from_warehouse_id) {
                $warehouse_name = $movement->fromWarehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouse_name])) {
                    $stats['by_warehouse'][$warehouse_name] = [
                        'outgoing' => 0,
                        'incoming' => 0,
                        'balance' => 0
                    ];
                }
                $stats['by_warehouse'][$warehouse_name]['outgoing']++;
            }

            if ($movement->to_warehouse_id) {
                $warehouse_name = $movement->toWarehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouse_name])) {
                    $stats['by_warehouse'][$warehouse_name] = [
                        'outgoing' => 0,
                        'incoming' => 0,
                        'balance' => 0
                    ];
                }
                $stats['by_warehouse'][$warehouse_name]['incoming']++;
            }
        }

        return $stats;
    }

    public function show($id)
    {
        $movement = NoteVoucher::with([
            'voucherProducts.product',
            'noteVoucherType',
            'provider',
            'fromWarehouse',
            'toWarehouse',
            'event',
            'order',
            'user'
        ])->findOrFail($id);

        return view('admin.reports.warehouseMovementDetails', compact('movement'));
    }
}