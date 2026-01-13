<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Product;
use Illuminate\Http\Request;

class ProvidersReportController extends Controller
{
    public function index(Request $request)
    {
        $providerId = $request->get('provider_id');
        $productId = $request->get('product_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $provider = null;
        $reportData = [];
        $totalItems = 0;
        $grandTotal = 0;

        // If provider is selected, get the report data
        if ($providerId) {
            $provider = Provider::find($providerId);
            if (!$provider) {
                return redirect()->back()->with('error', __('messages.provider_not_found'));
            }

            $query = Product::where('provider_id', $providerId);

            // If specific product is selected and not "all"
            if ($productId && $productId != 'all') {
                $query->where('id', $productId);
            }

            $products = $query->get();

            // Build the report data with sales information
            foreach ($products as $product) {
                $orderProductsQuery = $product->orderProducts();

                // Filter by date if provided
                if ($fromDate && $toDate) {
                    $orderProductsQuery->whereHas('order', function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                    });
                }

                $orderProducts = $orderProductsQuery->with('order')->get();

                if ($orderProducts->isNotEmpty()) {
                    $totalQuantity = $orderProducts->sum('quantity');
                    $totalRevenue = $orderProducts->sum(function ($op) {
                        return $op->quantity * $op->price;
                    });

                    $reportData[] = [
                        'product' => $product,
                        'order_products' => $orderProducts,
                        'total_quantity' => $totalQuantity,
                        'total_revenue' => $totalRevenue,
                    ];
                }
            }

            $totalItems = count($reportData);
            $grandTotal = collect($reportData)->sum('total_revenue');
        }

        return view('admin.reports.providers.index', [
            'provider' => $provider,
            'reportData' => $reportData,
            'totalItems' => $totalItems,
            'grandTotal' => $grandTotal,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'providerId' => $providerId,
            'productId' => $productId,
        ]);
    }

    public function getProducts($providerId)
    {
        $products = Product::where('provider_id', $providerId)
            ->select('id', 'name_en', 'name_ar', 'sku', 'selling_price', 'tax')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }
}
