{{-- resources/views/admin/purchase_returns/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.create_new') }} {{ __('messages.purchase_returns') }}</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.purchase-returns.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <x-search-select
                                    model="App\Models\Purchase"
                                    fieldName="purchase_id"
                                    label="purchase_number"
                                    placeholder="Search..."
                                    limit="10"
                                    required="true"
                                    displayColumn="purchase_number"
                                />
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('messages.return_status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" selected>{{ __('messages.pending') }}</option>
                                        <option value="sent">{{ __('messages.sent') }}</option>
                                        <option value="received">{{ __('messages.received') }}</option>
                                    </select>
                                    @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="return_date">{{ __('messages.return_date') }} *</label>
                                    <input type="date" name="return_date" id="return_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                    @error('return_date')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reason">{{ __('messages.return_reason') }}</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="2" placeholder="{{ __('messages.enter_return_reason') }}"></textarea>
                                    @error('reason')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">{{ __('messages.notes') }}</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="{{ __('messages.optional_notes') }}"></textarea>
                                    @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>{{ __('messages.products') }} *</label>
                            <div id="products-container">
                                <!-- Products will be loaded here -->
                            </div>
                            @error('products')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success">{{ __('messages.create_return') }}</button>
                                <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ __('messages.total') }}:</span>
                                            <span><x-riyal-icon /> <span id="total-amount">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const appLocale = '{{ app()->getLocale() }}';
const allPurchases = @json(\App\Models\Purchase::with('items.product')->where('status', 'received')->get());

function getProductName(product) {
    return appLocale === 'ar' ? product.name_ar : product.name_en;
}

function loadPurchaseItems(purchaseId) {
    const container = document.getElementById('products-container');

    if (!purchaseId) {
        container.innerHTML = '';
        return;
    }

    const purchase = allPurchases.find(p => p.id == purchaseId);

    if (!purchase || !purchase.items) {
        container.innerHTML = '';
        return;
    }

    renderItems(purchase.items);
}

function renderItems(items) {
    const container = document.getElementById('products-container');
    container.innerHTML = '';

    if (!items || items.length === 0) {
        return;
    }

    items.forEach((item, index) => {
        const row = document.createElement('div');
        row.className = 'product-row mb-3 p-3 border rounded';
        row.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label>{{ __('messages.product') }}</label>
                    <input type="text" class="form-control" value="${getProductName(item.product)}" disabled>
                </div>
                <div class="col-md-2">
                    <label>{{ __('messages.quantity') }}</label>
                    <input type="number" name="products[${index}][quantity_returned]" class="form-control quantity-input"
                           min="1" max="${item.quantity}" required value="${item.quantity}" onchange="updateTotal()">
                </div>
                <div class="col-md-3">
                    <label>{{ __('messages.unit_price') }}</label>
                    <input type="number" step="0.01" name="products[${index}][unit_price]" class="form-control unit-price-input"
                           required value="${(item.total_price / item.quantity).toFixed(2)}" onchange="updateTotal()">
                </div>
                <div class="col-md-2">
                    <label>{{ __('messages.total') }}</label>
                    <input type="text" class="form-control line-total" disabled>
                </div>
                <input type="hidden" name="products[${index}][product_id]" value="${item.product_id}">
            </div>
        `;
        container.appendChild(row);
    });
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.product-row').forEach((row) => {
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').value = lineTotal.toFixed(2);
        total += lineTotal;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2);
}

// Load items when purchase_id changes
document.getElementById('purchase_id').addEventListener('change', function() {
    loadPurchaseItems(this.value);
});
</script>
@endsection
