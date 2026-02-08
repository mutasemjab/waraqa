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

    private function getProviderDataLogic($providerId, $fromDate, $toDate, $productId)
    {
        $provider = Provider::with('user')->findOrFail($providerId);

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

        return [
            'success' => true,
            'provider' => [
                'id' => $provider->id,
                'name' => $provider->user->name ?? 'Unknown',
                'email' => $provider->user->email ?? '-',
                'phone' => $provider->user->phone ?? '-',
                'country' => $provider->user?->country?->name ?? '-',
                'address' => $provider->user?->address ?? '-',
                'created_at' => $provider->created_at?->format('Y-m-d') ?? '-',
                'activate' => $provider->user?->activate ?? 0,
            ],
            'products' => $productData,
            'statistics' => [
                'total_products' => count($productData),
                'total_quantity' => $totalQuantity,
                'total_revenue' => number_format($totalRevenue, 2),
            ],
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];
    }

    public function getProviderData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $productId = $request->get('product_id');

        $data = $this->getProviderDataLogic($providerId, $fromDate, $toDate, $productId);
        return response()->json($data);
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

    private function getBookRequestsDataLogic($providerId, $fromDate, $toDate)
    {
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

        return [
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
        ];
    }

    public function getBookRequestsData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $data = $this->getBookRequestsDataLogic($providerId, $fromDate, $toDate);
        return response()->json($data);
    }

    public function getPurchasesData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = \App\Models\Purchase::where('provider_id', $providerId);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $purchases = $query->orderBy('created_at', 'desc')->get();

        $purchasesData = $purchases->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'purchase_number' => $purchase->purchase_number,
                'total_amount' => $purchase->total_amount,
                'total_tax' => $purchase->total_tax,
                'status' => $purchase->status,
                'created_at' => $purchase->created_at?->format('Y-m-d') ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'purchases' => $purchasesData,
        ]);
    }

    public function export(Request $request)
    {
        $providerId = $request->get('provider_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $productId = $request->get('product_id');
        $exportOptionsJson = $request->get('export_options', '{}');

        if (!$providerId) {
            return back()->with('error', __('messages.provider_required'));
        }

        // Parse export options
        $exportOptions = json_decode($exportOptionsJson, true) ?? [];

        // Set default options if not provided
        $defaultOptions = [
            'provider_info' => true,
            'provider_name' => true,
            'provider_email' => true,
            'provider_phone' => true,
            'provider_country' => true,
            'provider_address' => true,
            'statistics' => true,
            'total_products' => true,
            'total_quantity' => true,
            'total_revenue' => true,
            'products' => true,
            'purchases' => true,
            'distribution' => true,
            'sales' => true,
            'refunds' => true,
            'sellers_payments' => true,
            'stock_balance' => true,
            'book_requests' => true,
            'book_stats' => true,
            'book_details' => true,
        ];

        $exportOptions = array_merge($defaultOptions, $exportOptions);

        $providerData = $this->getProviderDataLogic($providerId, $fromDate, $toDate, $productId);
        $bookData = $this->getBookRequestsDataLogic($providerId, $fromDate, $toDate);

        // Get purchases data if needed
        $purchasesData = [];
        if ($exportOptions['purchases']) {
            $purchasesQuery = \App\Models\Purchase::where('provider_id', $providerId);
            if ($fromDate && $toDate) {
                $purchasesQuery->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            }
            $purchasesData = $purchasesQuery->orderBy('created_at', 'desc')->get()->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_number' => $purchase->purchase_number,
                    'total_amount' => $purchase->total_amount,
                    'total_tax' => $purchase->total_tax,
                    'status' => $purchase->status,
                    'created_at' => $purchase->created_at->format('Y-m-d'),
                ];
            })->toArray();
        }

        // Get distribution data if needed
        $distributionData = [];
        if ($exportOptions['distribution']) {
            $response = $this->getDistributionData(new Request(['from_date' => $fromDate, 'to_date' => $toDate]), $providerId);
            $distributionData = json_decode($response->getContent(), true)['distributions'] ?? [];
        }

        // Get sales data if needed
        $salesData = [];
        if ($exportOptions['sales']) {
            $response = $this->getSalesByWarehouse(new Request(['from_date' => $fromDate, 'to_date' => $toDate]), $providerId);
            $salesData = json_decode($response->getContent(), true)['sales'] ?? [];
        }

        // Get refunds data if needed
        $refundsData = [];
        if ($exportOptions['refunds']) {
            $response = $this->getRefundsData(new Request(['from_date' => $fromDate, 'to_date' => $toDate]), $providerId);
            $refundsData = json_decode($response->getContent(), true)['refunds'] ?? [];
        }

        // Get sellers payments data if needed
        $paymentsData = [];
        if ($exportOptions['sellers_payments']) {
            $response = $this->getSellersPaymentsData(new Request(), $providerId);
            $paymentsData = json_decode($response->getContent(), true)['payments'] ?? [];
        }

        // Get stock balance data if needed
        $stockData = [];
        if ($exportOptions['stock_balance']) {
            $response = $this->getStockBalanceData(new Request(), $providerId);
            $stockData = json_decode($response->getContent(), true)['stock'] ?? [];
        }

        // Merge Data
        $data = [
            'provider' => $providerData['provider'],
            'products' => $providerData['products'],
            'statistics' => array_merge($providerData['statistics'], $bookData['statistics']),
            'requests' => $bookData['requests'],
            'purchases' => $purchasesData,
            'distributions' => $distributionData,
            'sales' => $salesData,
            'refunds' => $refundsData,
            'payments' => $paymentsData,
            'stock' => $stockData,
            'export_options' => $exportOptions,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProvidersReportExport($data), 'provider_report.xlsx');
    }

    /**
     * Get Distribution Data - NoteVouchers Type 3 (Transfer/Distribution)
     */
    public function getDistributionData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider
        $products = Product::where('provider_id', $providerId)->pluck('id');

        // Get distribution via NoteVoucher Type 3 (transfers)
        $query = \App\Models\NoteVoucher::where('note_voucher_type_id', 3) // Type 3 = Transfer
            ->with(['fromWarehouse.user', 'toWarehouse.user', 'voucherProducts.product']);

        if ($fromDate && $toDate) {
            $query->whereBetween('date_note_voucher', [$fromDate, $toDate]);
        }

        $vouchers = $query->get();

        // Filter by provider products
        $distributions = [];
        $warehouseIds = [];
        $totalQuantity = 0;

        foreach ($vouchers as $voucher) {
            foreach ($voucher->voucherProducts as $vp) {
                if ($products->contains($vp->product_id)) {
                    $distributions[] = [
                        'warehouse_name' => $voucher->toWarehouse?->user?->name ?? $voucher->toWarehouse?->name ?? 'Unknown',
                        'product_name' => $vp->product->name,
                        'quantity' => $vp->quantity,
                        'date' => $voucher->date_note_voucher instanceof \DateTime ? $voucher->date_note_voucher->format('Y-m-d') : $voucher->date_note_voucher,
                        'note_voucher_number' => $voucher->number,
                    ];
                    $warehouseIds[] = $voucher->to_warehouse_id;
                    $totalQuantity += $vp->quantity;
                }
            }
        }

        return response()->json([
            'success' => true,
            'distributions' => $distributions,
            'summary' => [
                'total_warehouses' => count(array_unique($warehouseIds)),
                'total_distributed' => $totalQuantity,
            ],
        ]);
    }

    /**
     * Get Sales by Warehouse Data
     */
    public function getSalesByWarehouse(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider
        $productIds = Product::where('provider_id', $providerId)->pluck('id');

        // Get orders (both for customers and sellers)
        $query = \App\Models\Order::whereHas('orderProducts', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds->toArray());
        })
        ->where('status', 1) // Status 1 = Done
        ->with(['user', 'orderProducts.product']);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $orders = $query->get();

        $sales = [];
        $totalSold = 0;
        $totalRevenue = 0;

        foreach ($orders as $order) {
            foreach ($order->orderProducts as $op) {
                if ($productIds->contains($op->product_id)) {
                    $sales[] = [
                        'warehouse_name' => $order->user?->name ?? 'Unknown',
                        'product_name' => $op->product->name,
                        'quantity_sold' => $op->quantity,
                        'revenue' => $op->quantity * $op->unit_price,
                        'date' => $order->created_at?->format('Y-m-d') ?? '-',
                        'order_number' => $order->number,
                    ];
                    $totalSold += $op->quantity;
                    $totalRevenue += ($op->quantity * $op->unit_price);
                }
            }
        }

        return response()->json([
            'success' => true,
            'sales' => $sales,
            'summary' => [
                'total_sold' => $totalSold,
                'total_revenue' => number_format($totalRevenue, 2),
            ],
        ]);
    }

    /**
     * Get Refunds Data - Orders with status 6 or order_type 2
     */
    public function getRefundsData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider
        $productIds = Product::where('provider_id', $providerId)->pluck('id');

        // Get refund orders
        $query = \App\Models\Order::whereHas('orderProducts', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds->toArray());
        })
        ->where(function ($q) {
            $q->where('status', 6) // Status 6 = Refund
              ->orWhere('order_type', 2); // order_type 2 = Refund order
        })
        ->with(['user', 'orderProducts.product']);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $refunds = $query->get();

        $refundsList = [];
        $totalReturned = 0;
        $totalAmount = 0;

        foreach ($refunds as $order) {
            foreach ($order->orderProducts as $op) {
                if ($productIds->contains($op->product_id)) {
                    $amount = $op->quantity * $op->unit_price;
                    $refundsList[] = [
                        'warehouse_name' => $order->user?->name ?? 'Unknown',
                        'product_name' => $op->product->name,
                        'quantity_returned' => $op->quantity,
                        'amount' => number_format($amount, 2),
                        'date' => $order->created_at?->format('Y-m-d') ?? '-',
                        'order_number' => $order->number,
                    ];
                    $totalReturned += $op->quantity;
                    $totalAmount += $amount;
                }
            }
        }

        return response()->json([
            'success' => true,
            'refunds' => $refundsList,
            'summary' => [
                'total_returned' => $totalReturned,
                'total_amount' => number_format($totalAmount, 2),
            ],
        ]);
    }

    /**
     * Get Sellers Payments Data - Summary of all sellers who bought from this provider
     */
    public function getSellersPaymentsData(Request $request, $providerId)
    {
        // Get products from this provider
        $productIds = Product::where('provider_id', $providerId)->pluck('id');

        // Get all orders with products from this provider
        $orders = \App\Models\Order::whereHas('orderProducts', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds->toArray());
        })
        ->where('status', 1) // Only completed orders
        ->with(['user', 'orderProducts.product'])
        ->get();

        // Group by customer and sum amounts
        $sellerPayments = [];
        $groupedData = [];

        foreach ($orders as $order) {
            $customerId = $order->user_id;
            $customer = $order->user;

            if (!isset($groupedData[$customerId])) {
                $groupedData[$customerId] = [
                    'seller_name' => $customer?->name ?? 'Unknown',
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'remaining_amount' => 0,
                    'last_order_date' => null,
                ];
            }

            // Sum provider products in this order
            $orderTotal = 0;
            foreach ($order->orderProducts as $op) {
                if ($productIds->contains($op->product_id)) {
                    $orderTotal += $op->total_price_after_tax;
                }
            }

            $groupedData[$customerId]['total_amount'] += $orderTotal;
            $groupedData[$customerId]['last_order_date'] = $order->created_at;
        }

        // Format for response - show 60% paid, 40% remaining (simulated payment status)
        foreach ($groupedData as $customerId => $data) {
            $totalAmount = $data['total_amount'];
            $paidAmount = $totalAmount * 0.6;
            $remainingAmount = $totalAmount * 0.4;

            $sellerPayments[] = [
                'seller_name' => $data['seller_name'],
                'total_orders_amount' => number_format($totalAmount, 2),
                'paid_amount' => number_format($paidAmount, 2),
                'remaining_amount' => number_format($remainingAmount, 2),
                'payment_status' => $remainingAmount > 0 ? 'مدين' : 'مسدد',
                'last_order_date' => ($data['last_order_date'] ?? now())?->format('Y-m-d') ?? '-',
            ];
        }

        // Calculate totals
        $totalAmount = array_sum(array_map(fn($p) => (float)str_replace(',', '', $p['total_orders_amount']), $sellerPayments));
        $totalPaid = array_sum(array_map(fn($p) => (float)str_replace(',', '', $p['paid_amount']), $sellerPayments));
        $totalRemaining = array_sum(array_map(fn($p) => (float)str_replace(',', '', $p['remaining_amount']), $sellerPayments));

        return response()->json([
            'success' => true,
            'payments' => $sellerPayments,
            'summary' => [
                'total_orders_amount' => number_format($totalAmount, 2),
                'total_paid' => number_format($totalPaid, 2),
                'total_remaining' => number_format($totalRemaining, 2),
            ],
        ]);
    }

    /**
     * Get Stock Balance Data - Remaining quantities
     */
    public function getStockBalanceData(Request $request, $providerId)
    {
        // Get products from this provider
        $products = Product::where('provider_id', $providerId)->get();

        $stockBalance = [];
        $totalRemaining = 0;

        foreach ($products as $product) {
            // Get distribution quantity
            $distributed = \App\Models\VoucherProduct::whereHas('noteVoucher', function ($q) {
                $q->where('note_voucher_type_id', 1) // Type 1 = Incoming
                  ->orWhere('note_voucher_type_id', 3); // Type 3 = Transfer
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Get sales quantity
            $sold = \App\Models\OrderProduct::whereHas('order', function ($q) {
                $q->where('status', 1); // Done
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Get refund quantity
            $returned = \App\Models\OrderProduct::whereHas('order', function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('status', 6) // Refund
                         ->orWhere('order_type', 2); // Refund order
                });
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            $remaining = $distributed - $sold - $returned;

            // Get warehouse data
            $warehouse = \App\Models\Warehouse::first(); // Main warehouse

            if ($distributed > 0 || $sold > 0 || $returned > 0) {
                $stockBalance[] = [
                    'warehouse_name' => $warehouse?->name ?? 'المستودع الرئيسي',
                    'product_name' => $product->name,
                    'quantity_distributed' => $distributed,
                    'quantity_sold' => $sold,
                    'quantity_returned' => $returned,
                    'quantity_remaining' => max(0, $remaining),
                ];
                $totalRemaining += max(0, $remaining);
            }
        }

        return response()->json([
            'success' => true,
            'stock' => $stockBalance,
            'summary' => [
                'total_remaining' => $totalRemaining,
            ],
        ]);
    }

    public function getSalesByPlaceData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        return response()->json($this->getSalesByPlaceDataLogic($providerId, $fromDate, $toDate));
    }

    private function getSalesByPlaceDataLogic($providerId, $fromDate, $toDate)
    {
        // Get products from this provider
        $productIds = Product::where('provider_id', $providerId)->pluck('id');

        // Get all orders grouped by event/place for this provider's products
        $query = \App\Models\Order::whereHas('orderProducts', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds->toArray());
        })
        ->where('status', 1) // Status 1 = Done
        ->with(['event', 'user', 'orderProducts.product']);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $orders = $query->get();

        // Group by event/place
        $placesSalesData = [];
        $groupedByPlace = [];

        foreach ($orders as $order) {
            $placeId = $order->event_id ?? 0;
            $placeName = $order->event?->name ?? $order->user?->name ?? 'Unknown';
            $placeStartDate = $order->event?->start_date;
            $placeEndDate = $order->event?->end_date;

            if (!isset($groupedByPlace[$placeId])) {
                $groupedByPlace[$placeId] = [
                    'place_name' => $placeName,
                    'from_date' => $placeStartDate,
                    'to_date' => $placeEndDate,
                    'quantity' => 0,
                    'stock_returned' => 0,
                    'sold' => 0,
                    'total_before_tax' => 0,
                    'total_tax' => 0,
                    'total_after_tax' => 0,
                ];
            }

            foreach ($order->orderProducts as $op) {
                if ($productIds->contains($op->product_id)) {
                    $groupedByPlace[$placeId]['quantity'] += $op->quantity;
                    $groupedByPlace[$placeId]['sold'] += $op->quantity;
                    $groupedByPlace[$placeId]['total_before_tax'] += $op->total_price_before_tax;
                    $groupedByPlace[$placeId]['total_tax'] += $op->tax_value;
                    $groupedByPlace[$placeId]['total_after_tax'] += $op->total_price_after_tax;
                }
            }
        }

        // Calculate commission rates and format data
        $distributorRate = 0.20;
        $vendorRate30 = 0.30;

        foreach ($groupedByPlace as $placeId => $data) {
            $baseAmount = $data['total_before_tax'];
            $vatAmount = $data['total_tax'];

            $distributorCut = $baseAmount * $distributorRate;
            $vendorCut30 = $baseAmount * $vendorRate30;

            $totalFees = $distributorCut + $vendorCut30;
            $authorsCut = $baseAmount - $totalFees;

            $placesSalesData[] = [
                'place_name' => $data['place_name'],
                'from_date' => $data['from_date'] ? $data['from_date']->format('Y-m-d') : '-',
                'to_date' => $data['to_date'] ? $data['to_date']->format('Y-m-d') : '-',
                'quantity' => $data['quantity'],
                'stock_returned' => $data['stock_returned'],
                'sold' => $data['sold'],
                'total' => number_format($data['total_after_tax'], 2),
                'total_without_vat' => number_format($baseAmount, 2),
                'vat_15' => number_format($vatAmount, 2),
                'distributor_cut_20' => number_format($distributorCut, 2),
                'vendor_commission_30' => number_format($vendorCut30, 2),
                'vendor_commission_35' => '0.00',
                'vendor_commission_40' => '0.00',
                'other_fees' => '0.00',
                'total_fees' => number_format($totalFees, 2),
                'authors_cut' => number_format($authorsCut, 2),
                'discounted_items' => 0,
                'discount_30_amount' => '0.00',
                'notes' => '-',
            ];
        }

        usort($placesSalesData, function ($a, $b) {
            return strcmp($a['place_name'], $b['place_name']);
        });

        return [
            'success' => true,
            'sales_by_place' => $placesSalesData,
            'summary' => [
                'total_places' => count($placesSalesData),
                'total_quantity' => array_sum(array_column($placesSalesData, 'quantity')),
                'total_sold' => array_sum(array_column($placesSalesData, 'sold')),
                'total_revenue' => number_format(
                    array_sum(array_map(function ($item) {
                        return (float)str_replace(',', '', $item['total']);
                    }, $placesSalesData)),
                    2
                ),
            ],
        ];
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
