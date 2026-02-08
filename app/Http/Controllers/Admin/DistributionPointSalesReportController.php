<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerSale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DistributionPointSalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_distribution_point_sales_report');
    }

    public function index()
    {
        return view('admin.reports.distribution-point-sales.index');
    }

    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $sellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'seller');
        })
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($seller) {
                return [
                    'id' => $seller->id,
                    'text' => $seller->name . ' (' . $seller->email . ')',
                    'name' => $seller->name,
                ];
            });

        return response()->json($sellers);
    }

    public function getData(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (!$sellerId) {
            return response()->json([
                'success' => false,
                'message' => __('messages.please_select_seller')
            ]);
        }

        // Verify seller exists
        $seller = User::findOrFail($sellerId);

        // Get all sales with items and products
        $query = SellerSale::with(['items.product']);

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        // Get all sales for the seller
        $query->where('user_id', $sellerId);
        $sales = $query->latest('sale_date')->get();

        // Calculate statistics (only for filtered sales)
        $totalSales = $sales->count();
        $totalAmount = $sales->sum('total_amount');
        $totalTax = $sales->sum('total_tax');
        $totalQuantity = $sales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });

        // Format sales for display
        $formattedSales = $sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'sale_date' => Carbon::parse($sale->sale_date)->format('Y-m-d'),
                'customer_name' => $sale->customer_name,
                'customer_phone' => $sale->customer_phone,
                'products_count' => $sale->items->count(),
                'total_quantity' => $sale->items->sum('quantity'),
                'total_amount' => number_format($sale->total_amount, 2),
                'total_amount_raw' => $sale->total_amount,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'seller' => [
                'id' => $seller->id,
                'name' => $seller->name,
            ],
            'stats' => [
                'total_sales' => $totalSales,
                'total_amount' => number_format($totalAmount, 2),
                'total_tax' => number_format($totalTax, 2),
                'total_quantity' => $totalQuantity,
            ],
            'sales' => $formattedSales,
        ]);
    }

    public function showSaleDetails($id)
    {
        $sale = SellerSale::with('items.product')->findOrFail($id);
        return view('admin.sales.details', compact('sale'));
    }
}
