<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Provider;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order-table')->only(['index']);
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['provider', 'items.product', 'warehouse']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
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
        $data = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($data);

        // Get filters data
        $providers = Provider::whereHas('purchases')->get();
        $warehouses = Warehouse::all();
        $statuses = ['pending', 'confirmed', 'received', 'paid'];

        return view('admin.reports.purchasesReport', compact(
            'data',
            'statistics',
            'providers',
            'warehouses',
            'statuses'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_purchases' => $data->count(),
            'total_amount' => 0,
            'total_tax' => 0,
            'total_with_tax' => 0,
            'total_items' => 0,
            'by_status' => [],
            'by_provider' => [],
            'by_warehouse' => [],
        ];

        foreach ($data as $purchase) {
            $stats['total_amount'] += $purchase->total_amount ?? 0;
            $stats['total_tax'] += $purchase->total_tax ?? 0;
            $stats['total_with_tax'] += ($purchase->total_amount ?? 0) + ($purchase->total_tax ?? 0);
            $stats['total_items'] += $purchase->items->sum('quantity');

            // By status
            $status = $purchase->status ?? 'pending';
            if (!isset($stats['by_status'][$status])) {
                $stats['by_status'][$status] = ['count' => 0, 'amount' => 0];
            }
            $stats['by_status'][$status]['count']++;
            $stats['by_status'][$status]['amount'] += ($purchase->total_amount ?? 0) + ($purchase->total_tax ?? 0);

            // By provider
            if ($purchase->provider_id) {
                $providerName = $purchase->provider->name ?? 'Unknown';
                if (!isset($stats['by_provider'][$providerName])) {
                    $stats['by_provider'][$providerName] = ['count' => 0, 'amount' => 0];
                }
                $stats['by_provider'][$providerName]['count']++;
                $stats['by_provider'][$providerName]['amount'] += ($purchase->total_amount ?? 0) + ($purchase->total_tax ?? 0);
            }

            // By warehouse
            if ($purchase->warehouse_id) {
                $warehouseName = $purchase->warehouse->name ?? 'Unknown';
                if (!isset($stats['by_warehouse'][$warehouseName])) {
                    $stats['by_warehouse'][$warehouseName] = ['count' => 0, 'amount' => 0];
                }
                $stats['by_warehouse'][$warehouseName]['count']++;
                $stats['by_warehouse'][$warehouseName]['amount'] += ($purchase->total_amount ?? 0) + ($purchase->total_tax ?? 0);
            }
        }

        return $stats;
    }
}
