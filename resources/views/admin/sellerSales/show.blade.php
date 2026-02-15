@extends("layouts.admin")

@section("title", __("messages.sale_details"))
@section("page-title", __("messages.sale_details"))

@section("content")
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __("messages.sale_details") }}</h1>
            <p class="page-subtitle">{{ $sale->sale_number }}</p>
        </div>
        <a href="{{ route("admin.seller-sales.index") }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __("messages.back") }}
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Sale Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __("messages.sale_information") }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __("messages.sale_number") }}:</strong> {{ $sale->sale_number }}</p>
                        <p><strong>{{ __("messages.seller") }}:</strong> {{ $sale->user->name }}</p>
                        <p><strong>{{ __("messages.sale_date") }}:</strong> {{ $sale->sale_date }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __("messages.status") }}:</strong> <span class="badge bg-{{ $sale->status->getColor() }}">{{ $sale->status->getLabel() }}</span></p>
                        <p><strong>{{ __("messages.total_amount") }}:</strong> {{ number_format($sale->total_amount, 2) }}</p>
                        <p><strong>{{ __("messages.total_tax") }}:</strong> {{ number_format($sale->total_tax, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __("messages.products") }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __("messages.product") }}</th>
                                <th>{{ __("messages.quantity") }}</th>
                                <th>{{ __("messages.unit_price") }} ({{ __("messages.after_tax") ?? "شامل الضريبة" }})</th>
                                <th>{{ __("messages.tax_percentage") }}</th>
                                <th>{{ __("messages.total_price") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->tax_percentage }}%</td>
                                    <td>{{ number_format($item->total_price_after_tax, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($sale->status === App\Enums\SellerSaleStatus::PENDING)
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __("messages.actions") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route("admin.seller-sales.approve", $sale->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm(\"{{ __("messages.confirm_approve_sale") }}\")">
                                    <i class="fas fa-check me-2"></i>{{ __("messages.approve") }}
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-2"></i>{{ __("messages.reject") }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route("admin.seller-sales.reject", $sale->id) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __("messages.reject_sale") }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>{{ __("messages.rejection_reason") }} *</label>
                                    <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("messages.cancel") }}</button>
                                <button type="submit" class="btn btn-danger">{{ __("messages.reject") }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Rejection Information -->
        @if($sale->status === App\Enums\SellerSaleStatus::REJECTED)
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ __("messages.rejection_information") }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ __("messages.rejected_by") }}:</strong> {{ $sale->approvedBy->name }}</p>
                    <p><strong>{{ __("messages.rejected_at") }}:</strong> {{ $sale->approved_at->format("Y-m-d H:i") }}</p>
                    <div class="alert alert-danger mt-3">
                        <strong>{{ __("messages.rejection_reason") }}:</strong><br>
                        {{ $sale->rejection_reason }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.sale_summary') ?? 'ملخص المبيعة' }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('messages.total_items') ?? 'إجمالي المنتجات' }}</label>
                    <p class="h5 fw-bold">{{ $sale->items->count() }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('messages.total_quantity') ?? 'إجمالي الكمية' }}</label>
                    <p class="h5 fw-bold">{{ $sale->items->sum('quantity') }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('messages.subtotal') ?? 'المجموع قبل الضريبة' }}</label>
                    <p class="h5 fw-bold">{{ number_format($sale->total_amount - $sale->total_tax, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('messages.total_tax') }}</label>
                    <p class="h5 fw-bold text-danger">{{ number_format($sale->total_tax, 2) }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('messages.total_amount') }}</label>
                    <p class="h4 fw-bold text-success">{{ number_format($sale->total_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.status_information') ?? 'معلومات الحالة' }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.current_status') ?? 'الحالة الحالية' }}</label>
                    <p>
                        <span class="badge bg-{{ $sale->status->getColor() }} fs-6">
                            {{ $sale->status->getLabel() }}
                        </span>
                    </p>
                </div>
                @if($sale->status !== App\Enums\SellerSaleStatus::PENDING)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.processed_by') ?? 'تمت المعالجة بواسطة' }}</label>
                        <p class="mb-0">{{ $sale->approvedBy?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.processed_at') ?? 'تاريخ المعالجة' }}</label>
                        <p class="mb-0">{{ $sale->approved_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Seller Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.seller_information') ?? 'معلومات البائع' }}</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ __('messages.name') ?? 'الاسم' }}:</strong><br>{{ $sale->user->name }}</p>
                <p><strong>{{ __('messages.email') ?? 'البريد الإلكتروني' }}:</strong><br>{{ $sale->user->email }}</p>
                <p><strong>{{ __('messages.phone') ?? 'الهاتف' }}:</strong><br>{{ $sale->user->phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
