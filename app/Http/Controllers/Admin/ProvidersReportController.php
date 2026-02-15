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

    private function getProviderDataLogic($providerId, $fromDate, $toDate)
    {
        $provider = Provider::with('user')->findOrFail($providerId);

        // Get approved book requests from this provider
        $bookResponsesQuery = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product');

        if ($fromDate && $toDate) {
            $bookResponsesQuery->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $bookResponses = $bookResponsesQuery->get();

        // Build report data from approved book requests
        $productData = [];
        $totalRevenue = 0;
        $totalQuantity = 0;
        $uniqueProducts = [];

        foreach ($bookResponses as $response) {
            $productId = $response->bookRequestItem->product_id;
            $product = $response->bookRequestItem->product;
            $quantity = $response->available_quantity;
            $revenue = $quantity * ($response->price ?? 0);

            $totalQuantity += $quantity;
            $totalRevenue += $revenue;

            // Track unique products
            if (!isset($uniqueProducts[$productId])) {
                $uniqueProducts[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name_en ?? $product->name_ar ?? 'Unknown',
                    'sku' => $product->sku ?? '-',
                    'unit_price' => number_format($product->selling_price ?? 0, 2),
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            }

            $uniqueProducts[$productId]['quantity'] += $quantity;
            $uniqueProducts[$productId]['revenue'] += $revenue;
        }

        // Format product data
        $productData = array_map(function ($product) {
            $product['revenue'] = number_format($product['revenue'], 2);
            return $product;
        }, $uniqueProducts);

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
            'products' => array_values($productData),
            'statistics' => [
                'total_products' => count($uniqueProducts),
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

        $data = $this->getProviderDataLogic($providerId, $fromDate, $toDate);
        return response()->json($data);
    }


    private function getBookRequestsDataLogic($providerId, $fromDate, $toDate)
    {
        // Get book requests responses for this provider
        $query = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->with('bookRequestItem.product');

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
                'product_name' => $response->bookRequestItem->product->name ?? '-',
                'requested_quantity' => $response->bookRequestItem->requested_quantity,
                'available_quantity' => $response->available_quantity,
                'price' => number_format($response->price ?? 0, 2),
                'tax_percentage' => $response->tax_percentage . '%',
                'total_with_tax' => number_format(($response->available_quantity * ($response->price ?? 0)), 2),  // Price already includes tax
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
                'total_with_tax' => number_format($totalImportValue, 2),  // Already includes tax
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

        $providerData = $this->getProviderDataLogic($providerId, $fromDate, $toDate);
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
     * Get Distribution Data - BookRequestResponse linked to NoteVoucher (Transfer to Sellers)
     */
    public function getDistributionData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get approved book requests from this provider
        $query = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $bookResponses = $query->get();
        $productIds = $bookResponses->pluck('bookRequestItem.product_id')->unique();

        // Get NoteVouchers Type 3 (transfers to sellers) for these products
        $vouchersQuery = \App\Models\NoteVoucher::where('note_voucher_type_id', 3)
            ->with(['toWarehouse.user', 'voucherProducts.product']);

        if ($fromDate && $toDate) {
            $vouchersQuery->whereBetween('date_note_voucher', [$fromDate, $toDate]);
        }

        $vouchers = $vouchersQuery->get();

        // Build distribution data by linking book requests with vouchers
        $distributions = [];
        $warehouseIds = [];
        $totalQuantity = 0;

        foreach ($bookResponses as $response) {
            $productId = $response->bookRequestItem->product_id;
            $productName = $response->bookRequestItem->product?->name ?? 'Unknown';

            // Find matching vouchers for this product
            foreach ($vouchers as $voucher) {
                $toUser = $voucher->toWarehouse?->user;
                if (!$toUser || !$toUser->hasRole('seller')) {
                    continue;
                }

                foreach ($voucher->voucherProducts as $vp) {
                    if ($vp->product_id == $productId) {
                        $distributions[] = [
                            'warehouse_name' => $toUser->name ?? 'Unknown',
                            'product_name' => $productName,
                            'quantity' => $vp->quantity,
                            'date' => $voucher->date_note_voucher instanceof \DateTime ? $voucher->date_note_voucher->format('Y-m-d') : $voucher->date_note_voucher,
                            'note_voucher_number' => $voucher->number,
                        ];
                        $warehouseIds[] = $voucher->to_warehouse_id;
                        $totalQuantity += $vp->quantity;
                    }
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
     * Get Sales by Warehouse Data - From SellerSales (Seller to Customer)
     * Uses BookRequestResponse to identify provider products
     */
    public function getSalesByWarehouse(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider via BookRequestResponse
        $bookRequests = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product')
            ->get();

        $productIds = $bookRequests->pluck('bookRequestItem.product_id')->unique()->toArray();

        // If no products, return empty
        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'sales' => [],
                'summary' => [
                    'total_sold' => 0,
                    'total_revenue' => '0.00',
                ],
            ]);
        }

        // Get seller sales that contain this provider's products
        $query = \App\Models\SellerSale::whereHas('items', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds);
        })
        ->with(['user', 'items' => function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds)->with('product');
        }]);

        if ($fromDate && $toDate) {
            $query->whereBetween('sale_date', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $sellerSales = $query->get();

        $sales = [];
        $totalSold = 0;
        $totalRevenue = 0;

        foreach ($sellerSales as $sellerSale) {
            foreach ($sellerSale->items as $item) {
                $revenue = $item->quantity * ($item->unit_price ?? 0);
                $sales[] = [
                    'warehouse_name' => $sellerSale->user?->name ?? 'Unknown',
                    'product_name' => $item->product?->name ?? 'Unknown',
                    'quantity_sold' => $item->quantity,
                    'revenue' => $revenue,
                    'date' => $sellerSale->sale_date instanceof \DateTime ? $sellerSale->sale_date->format('Y-m-d') : substr($sellerSale->sale_date, 0, 10),
                    'order_number' => $sellerSale->sale_number,
                ];
                $totalSold += $item->quantity;
                $totalRevenue += $revenue;
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
     * Uses BookRequestResponse to identify provider products
     */
    public function getRefundsData(Request $request, $providerId)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider via BookRequestResponse
        $bookRequests = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product')
            ->get();

        $productIds = $bookRequests->pluck('bookRequestItem.product_id')->unique()->toArray();

        // If no products, return empty
        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'refunds' => [],
                'summary' => [
                    'total_returned' => 0,
                    'total_amount' => '0.00',
                ],
            ]);
        }

        // Get refund orders for these products
        $query = \App\Models\Order::whereHas('orderProducts', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds);
        })
        ->where('status', \App\Enums\OrderStatus::REFUNDED->value)
        ->with(['user', 'orderProducts' => function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds)->with('product');
        }]);

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $refunds = $query->get();

        $refundsList = [];
        $totalReturned = 0;
        $totalAmount = 0;

        foreach ($refunds as $order) {
            foreach ($order->orderProducts as $op) {
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
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get products from this provider via BookRequestResponse
        $bookRequests = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product')
            ->get();

        $productIds = $bookRequests->pluck('bookRequestItem.product_id')->unique()->toArray();

        // If no products, return empty
        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'payments' => [],
                'summary' => [
                    'total_distributed' => 0,
                    'total_sold' => 0,
                    'total_remaining' => 0,
                ],
            ]);
        }

        // Get all NoteVouchers Type 3 (transfers to sellers) for these products
        $vouchersQuery = \App\Models\NoteVoucher::where('note_voucher_type_id', 3)
            ->with(['toWarehouse.user', 'voucherProducts']);

        if ($fromDate && $toDate) {
            $vouchersQuery->whereBetween('date_note_voucher', [$fromDate, $toDate]);
        }

        $vouchers = $vouchersQuery->get();

        // Group data by seller
        $sellerData = [];

        // First, collect distributed quantities for each seller
        foreach ($vouchers as $voucher) {
            $seller = $voucher->toWarehouse?->user;
            if (!$seller || !$seller->hasRole('seller')) {
                continue;
            }

            $sellerId = $seller->id;
            if (!isset($sellerData[$sellerId])) {
                $sellerData[$sellerId] = [
                    'seller_name' => $seller->name,
                    'distributed_qty' => 0,
                    'sold_qty' => 0,
                    'remaining_qty' => 0,
                ];
            }

            // Sum quantities for this seller's products
            foreach ($voucher->voucherProducts as $vp) {
                if (in_array($vp->product_id, $productIds)) {
                    $sellerData[$sellerId]['distributed_qty'] += $vp->quantity;
                }
            }
        }

        // Then, get sales data for each seller
        $sellerSalesQuery = \App\Models\SellerSale::whereHas('items', function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds);
        })
        ->with(['user', 'items' => function ($q) use ($productIds) {
            $q->whereIn('product_id', $productIds);
        }]);

        if ($fromDate && $toDate) {
            $sellerSalesQuery->whereBetween('sale_date', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $sellerSales = $sellerSalesQuery->get();

        foreach ($sellerSales as $sale) {
            $sellerId = $sale->user_id;
            if (!isset($sellerData[$sellerId])) {
                $sellerData[$sellerId] = [
                    'seller_name' => $sale->user?->name ?? 'Unknown',
                    'distributed_qty' => 0,
                    'sold_qty' => 0,
                    'remaining_qty' => 0,
                ];
            }

            foreach ($sale->items as $item) {
                $sellerData[$sellerId]['sold_qty'] += $item->quantity;
            }
        }

        // Calculate remaining for each seller
        $payments = [];
        $totalOrdersAmount = 0;
        $totalPaid = 0;
        $totalRemaining = 0;

        foreach ($sellerData as $data) {
            $remaining = $data['distributed_qty'] - $data['sold_qty'];
            $totalAmount = $data['distributed_qty']; // Total distributed = total orders amount
            $paidAmount = $data['sold_qty']; // Sold = paid amount
            $remainingAmount = max(0, $remaining);

            $payments[] = [
                'seller_name' => $data['seller_name'],
                'total_orders_amount' => number_format($totalAmount, 2),
                'paid_amount' => number_format($paidAmount, 2),
                'remaining_amount' => number_format($remainingAmount, 2),
                'payment_status' => $remainingAmount > 0 ? 'in_debt' : 'paid',
                'last_order_date' => '-',
            ];
            $totalOrdersAmount += $totalAmount;
            $totalPaid += $paidAmount;
            $totalRemaining += $remainingAmount;
        }

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'summary' => [
                'total_orders_amount' => number_format($totalOrdersAmount, 2),
                'total_paid' => number_format($totalPaid, 2),
                'total_remaining' => number_format($totalRemaining, 2),
            ],
        ]);
    }

    /**
     * Get Stock Balance Data - Remaining quantities
     * Uses BookRequestResponse to identify provider products
     */
    public function getStockBalanceData(Request $request, $providerId)
    {
        // Get products from this provider via BookRequestResponse
        $bookRequests = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem.product')
            ->get();

        $productIds = $bookRequests->pluck('bookRequestItem.product_id')->unique()->toArray();

        // If no products, return empty
        if (empty($productIds)) {
            return response()->json([
                'success' => true,
                'stock' => [],
                'summary' => [
                    'total_remaining' => 0,
                ],
            ]);
        }

        $stockBalance = [];
        $totalRemaining = 0;

        // Get unique products from book requests
        $products = \App\Models\Product::whereIn('id', $productIds)->get();

        foreach ($products as $product) {
            // Get purchased quantity (NoteVoucher Type 1 - incoming purchases)
            $purchased = \App\Models\VoucherProduct::whereHas('noteVoucher', function ($q) {
                $q->where('note_voucher_type_id', 1); // Type 1 = Incoming/Purchased
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Get transferred quantity (NoteVoucher Type 3 only - transfers to sellers)
            $transferred = \App\Models\VoucherProduct::whereHas('noteVoucher', function ($q) {
                $q->where('note_voucher_type_id', 3); // Type 3 = Transfer only
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Get sales quantity from Orders (to customers)
            $soldViaOrder = \App\Models\OrderProduct::whereHas('order', function ($q) {
                $q->where('status', \App\Enums\OrderStatus::DONE->value);
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Get sales quantity from SellerSales (from sellers themselves)
            $soldViaSellerSales = \App\Models\SellerSaleItem::where('product_id', $product->id)
                ->sum('quantity');

            // Total sold = Order sales + SellerSales
            $totalSold = $soldViaOrder + $soldViaSellerSales;

            // Get refund quantity (Status = Refunded)
            $returned = \App\Models\OrderProduct::whereHas('order', function ($q) {
                $q->where('status', \App\Enums\OrderStatus::REFUNDED->value);
            })
            ->where('product_id', $product->id)
            ->sum('quantity');

            // Total remaining = Purchased - Sold - Returned
            $remaining = $purchased - $totalSold - $returned;

            // Get warehouse data
            $warehouse = \App\Models\Warehouse::first(); // Main warehouse

            if ($transferred > 0 || $totalSold > 0 || $returned > 0) {
                $stockBalance[] = [
                    'warehouse_name' => $warehouse?->name ?? 'المستودع الرئيسي',
                    'product_name' => $product->name,
                    'quantity_distributed' => $transferred,
                    'quantity_sold' => $totalSold,
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
