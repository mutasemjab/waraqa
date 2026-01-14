@extends('layouts.provider')

@section('title', __('messages.my_products'))
@section('page-title', __('messages.my_products'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.my_products') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_your_products_and_inventory') }}</p>
</div>


<!-- Products List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>{{ __('messages.products_list') }}
            <span class="badge bg-primary ms-2">{{ $products->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.image') }}</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.sku') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('assets/admin/uploads/' . $product->image) }}"
                                             alt="{{ $product->name_en }}"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px;">
                                            <small class="text-muted">{{ __('messages.no_image') }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}</strong>
                                </td>
                                <td>
                                    {{ $product->sku ?? '-' }}
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-info">{{ app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en }}</span>
                                    @else
                                        <span class="text-muted">{{ __('messages.uncategorized') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-success"><x-riyal-icon /> {{ number_format($product->selling_price, 2) }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('provider.products.details', $product->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">{{ __('messages.no_products_found') }}</h4>
                <p class="text-muted">{{ __('messages.start_adding_products_message') }}</p>
                <a href="{{ route('provider.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>{{ __('messages.add_first_product') }}
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush