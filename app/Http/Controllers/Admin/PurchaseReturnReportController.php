<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\Provider;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PurchaseReturnReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-return-table')->only(['index']);
    }

    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['purchase', 'provider', 'warehouse', 'returnItems.product']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('return_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('return_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('return_date', '<=', $request->to_date);
        }

        // Filter by provider
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get data
        $data = $query->orderBy('return_date', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($data);

        // Get filters data
        $providers = Provider::all();
        $warehouses = Warehouse::all();

        return view('admin.reports.purchaseReturnsReport', compact(
            'data',
            'statistics',
            'providers',
            'warehouses'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_returns' => $data->count(),
            'total_amount' => 0,
            'total_quantity' => 0,
            'by_status' => [],
            'by_provider' => [],
            'by_warehouse' => [],
            'by_product' => [],
        ];

        foreach ($data as $return) {
            $stats['total_amount'] += $return->total_amount ?? 0;

            // Calculate total quantity
            $returnQuantity = 0;
            foreach ($return->returnItems as $item) {
                $returnQuantity += $item->quantity_returned ?? 0;

                // By product
                $productName = $item->product->name_ar ?? $item->product->name_en ?? 'Unknown';
                if (!isset($stats['by_product'][$productName])) {
                    $stats['by_product'][$productName] = ['count' => 0, 'quantity' => 0, 'amount' => 0];
                }
                $stats['by_product'][$productName]['count']++;
                $stats['by_product'][$productName]['quantity'] += $item->quantity_returned ?? 0;
                $stats['by_product'][$productName]['amount'] += $item->total_price ?? 0;
            }
            $stats['total_quantity'] += $returnQuantity;

            // By status
            $status = $return->status ?? 'pending';
            if (!isset($stats['by_status'][$status])) {
                $stats['by_status'][$status] = ['count' => 0, 'amount' => 0];
            }
            $stats['by_status'][$status]['count']++;
            $stats['by_status'][$status]['amount'] += $return->total_amount ?? 0;

            // By provider
            if ($return->provider) {
                $providerName = $return->provider->name ?? 'Unknown';
                if (!isset($stats['by_provider'][$providerName])) {
                    $stats['by_provider'][$providerName] = ['count' => 0, 'amount' => 0];
                }
                $stats['by_provider'][$providerName]['count']++;
                $stats['by_provider'][$providerName]['amount'] += $return->total_amount ?? 0;
            }

            // By warehouse
            if ($return->warehouse) {
                $warehouseName = $return->warehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouseName])) {
                    $stats['by_warehouse'][$warehouseName] = ['count' => 0, 'amount' => 0];
                }
                $stats['by_warehouse'][$warehouseName]['count']++;
                $stats['by_warehouse'][$warehouseName]['amount'] += $return->total_amount ?? 0;
            }
        }

        return $stats;
    }
}
