<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Product;
use Illuminate\Http\Request;

class ProvidersReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.providers.index');
    }

    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $providers = Provider::with('user')
            ->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'text' => $provider->user->name ?? 'Unknown',
                    'name' => $provider->user->name ?? 'Unknown',
                    'email' => $provider->user->email ?? '-',
                ];
            });

        return response()->json($providers);
    }

    public function getProviderData(Request $request, $providerId)
    {
        $provider = Provider::with('user')->findOrFail($providerId);
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $productId = $request->get('product_id');

        // Get products purchased from this provider
        $query = Product::where('provider_id', $providerId);

        if ($productId && $productId != 'all') {
            $query->where('id', $productId);
        }

        $products = $query->get();

        // Build report data
        $productData = [];
        $totalRevenue = 0;
        $totalQuantity = 0;

        foreach ($products as $product) {
            $quantity = 0;
            $revenue = 0;

            // Get order products (regular orders from this provider)
            $orderProductsQuery = $product->orderProducts();

            if ($fromDate && $toDate) {
                $orderProductsQuery->whereHas('order', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                });
            }

            $orderProducts = $orderProductsQuery->with('order')->get();

            if ($orderProducts->isNotEmpty()) {
                $quantity += $orderProducts->sum('quantity');
                $revenue += $orderProducts->sum(function ($op) {
                    return $op->quantity * $op->price;
                });
            }

            // Get book request responses (approved requests from this provider)
            $bookResponsesQuery = \App\Models\BookRequestResponse::whereHas('bookRequest', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->where('provider_id', $providerId)->where('status', 'approved');

            if ($fromDate && $toDate) {
                $bookResponsesQuery->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            }

            $bookResponses = $bookResponsesQuery->get();

            if ($bookResponses->isNotEmpty()) {
                $quantity += $bookResponses->sum('available_quantity');
                $revenue += $bookResponses->sum(function ($br) {
                    return $br->available_quantity * ($br->price ?? 0);
                });
            }

            // Only add to report if there's data
            if ($quantity > 0) {
                $productData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? '-',
                    'unit_price' => number_format($product->selling_price, 2),
                    'quantity' => $quantity,
                    'revenue' => number_format($revenue, 2),
                ];

                $totalQuantity += $quantity;
                $totalRevenue += $revenue;
            }
        }

        return response()->json([
            'success' => true,
            'provider' => [
                'id' => $provider->id,
                'name' => $provider->user->name ?? 'Unknown',
                'email' => $provider->user->email ?? '-',
                'phone' => $provider->user->phone ?? '-',
                'country' => $provider->user?->country?->name ?? '-',
            ],
            'products' => $productData,
            'statistics' => [
                'total_products' => count($productData),
                'total_quantity' => $totalQuantity,
                'total_revenue' => number_format($totalRevenue, 2),
            ],
            'from_date' => $fromDate,
            'to_date' => $toDate,
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

    public function getBookRequestsData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get book requests responses for this provider
        $query = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->with('bookRequest.product');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $bookResponses = $query->get();

        // Calculate statistics
        $totalRequests = $bookResponses->count();
        $approvedCount = $bookResponses->where('status', 'approved')->count();
        $rejectedCount = $bookResponses->where('status', 'rejected')->count();
        $pendingCount = $bookResponses->where('status', 'pending')->count();

        $approvalRate = $totalRequests > 0 ? round(($approvedCount / $totalRequests) * 100, 2) : 0;

        // Get total import value (purchases) from this provider
        $purchasesQuery = \App\Models\Purchase::where('provider_id', $providerId);

        if ($fromDate && $toDate) {
            $purchasesQuery->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $purchases = $purchasesQuery->get();
        $totalImportValue = $purchases->sum('total_amount');
        $totalImportTax = $purchases->sum('total_tax');

        // Format book requests data
        $requestsData = $bookResponses->map(function ($response) {
            return [
                'id' => $response->id,
                'product_name' => $response->bookRequest->product->name ?? '-',
                'requested_quantity' => $response->bookRequest->requested_quantity,
                'available_quantity' => $response->available_quantity,
                'price' => number_format($response->price ?? 0, 2),
                'tax_percentage' => $response->tax_percentage . '%',
                'total_with_tax' => number_format(($response->available_quantity * ($response->price ?? 0) * (1 + ($response->tax_percentage ?? 0) / 100)), 2),
                'status' => $response->status,
                'status_badge' => $this->getStatusBadge($response->status),
                'note' => $response->note ?? '-',
                'created_at' => $response->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_requests' => $totalRequests,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
                'pending' => $pendingCount,
                'approval_rate' => $approvalRate,
                'total_import_value' => number_format($totalImportValue, 2),
                'total_import_tax' => number_format($totalImportTax, 2),
                'total_with_tax' => number_format($totalImportValue + $totalImportTax, 2),
            ],
            'requests' => $requestsData,
        ]);
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'approved' => '<span class="badge badge-success">موافق عليه</span>',
            'rejected' => '<span class="badge badge-danger">مرفوض</span>',
            'pending' => '<span class="badge badge-warning">قيد الانتظار</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">غير معروف</span>';
    }
}
