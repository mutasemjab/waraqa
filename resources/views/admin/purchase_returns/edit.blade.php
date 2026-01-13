{{-- resources/views/admin/purchase_returns/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.edit') }} {{ __('messages.purchase_returns') }} - {{ $purchaseReturn->number }}</h4>
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

                    <form action="{{ route('admin.purchase-returns.update', $purchaseReturn) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="purchase_search">{{ __('messages.purchase_number') }} *</label>
                            <input type="hidden" name="purchase_id" id="purchase_id" required value="{{ $purchaseReturn->purchase_id }}">
                            <input type="text" id="purchase_search" class="form-control"
                                   placeholder="{{ __('messages.search') }}" autocomplete="off"
                                   value="{{ $purchaseReturn->purchase->purchase_number }} - {{ $purchaseReturn->purchase->provider->name ?? 'N/A' }} ({{ $purchaseReturn->purchase->created_at->format('M d, Y') }})">
                            <div id="purchases-dropdown" class="border rounded mt-1"
                                 style="display:none; position: absolute; width: calc(100% - 30px); max-height: 300px; overflow-y: auto;
                                 background: white; z-index: 1000;">
                            </div>
                            @error('purchase_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">{{ __('messages.return_status') }} *</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $purchaseReturn->status == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                <option value="sent" {{ $purchaseReturn->status == 'sent' ? 'selected' : '' }}>{{ __('messages.sent') }}</option>
                                <option value="received" {{ $purchaseReturn->status == 'received' ? 'selected' : '' }}>{{ __('messages.received') }}</option>
                            </select>
                            @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="return_date">{{ __('messages.return_date') }} *</label>
                            <input type="date" name="return_date" id="return_date" class="form-control"
                                   required value="{{ $purchaseReturn->return_date->format('Y-m-d') }}">
                            @error('return_date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="reason">{{ __('messages.return_reason') }}</label>
                            <textarea name="reason" id="reason" class="form-control" rows="2">{{ $purchaseReturn->reason }}</textarea>
                            @error('reason')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">{{ __('messages.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2">{{ $purchaseReturn->notes }}</textarea>
                            @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
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
                                <button type="submit" class="btn btn-success">{{ __('messages.update') }}</button>
                                <a href="{{ route('admin.purchase-returns.show', $purchaseReturn) }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
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
const existingItems = @json($purchaseReturn->returnItems);
let purchaseSearchTimer;

function getProductName(product) {
    return appLocale === 'ar' ? product.name_ar : product.name_en;
}

function loadPurchaseDetails(items) {
    const container = document.getElementById('products-container');
    container.innerHTML = '';

    if (items && items.length > 0) {
        items.forEach((purchaseItem, index) => {
            const existingItem = existingItems.find(item => item.product_id === purchaseItem.product_id);
            const row = document.createElement('div');
            row.className = 'product-row mb-3 p-3 border rounded';
            row.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('messages.product') }}</label>
                        <input type="text" class="form-control" value="${getProductName(purchaseItem.product)}" disabled>
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('messages.quantity') }}</label>
                        <input type="number" name="products[${index}][quantity_returned]" class="form-control quantity-input"
                               min="1" max="${purchaseItem.quantity}" required
                               value="${existingItem ? existingItem.quantity_returned : purchaseItem.quantity}" onchange="updateTotal()">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('messages.unit_price') }}</label>
                        <input type="number" step="0.01" name="products[${index}][unit_price]" class="form-control unit-price-input"
                               required value="${existingItem ? existingItem.unit_price : (purchaseItem.total_price / purchaseItem.quantity).toFixed(2)}" onchange="updateTotal()">
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('messages.total') }}</label>
                        <input type="text" class="form-control line-total" disabled>
                    </div>
                    <input type="hidden" name="products[${index}][product_id]" value="${purchaseItem.product_id}">
                </div>
            `;
            container.appendChild(row);
        });
        updateTotal();
    }
}

function performPurchaseSearch(term) {
    const dropdown = document.getElementById('purchases-dropdown');

    if (term.length === 0) {
        dropdown.style.display = 'none';
        return;
    }

    fetch('{{ route("admin.purchase-returns.search-purchases") }}?term=' + encodeURIComponent(term))
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(purchase => {
                    html += `<div class="p-2 border-bottom purchase-item"
                                data-id="${purchase.id}"
                                data-text="${purchase.text}"
                                data-items='${JSON.stringify(purchase.items)}'
                                style="cursor: pointer;">
                                ${purchase.text}
                            </div>`;
                });
                dropdown.innerHTML = html;
                dropdown.style.display = 'block';

                document.querySelectorAll('.purchase-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const purchaseId = this.getAttribute('data-id');
                        const purchaseText = this.getAttribute('data-text');
                        const items = JSON.parse(this.getAttribute('data-items'));

                        document.getElementById('purchase_id').value = purchaseId;
                        document.getElementById('purchase_search').value = purchaseText;
                        dropdown.style.display = 'none';

                        loadPurchaseDetails(items);
                    });
                });
            } else {
                dropdown.innerHTML = `<div class="p-2 text-muted">{{ __('messages.no_results_found') }}</div>`;
                dropdown.style.display = 'block';
            }
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('purchase_search').addEventListener('input', function() {
    const term = this.value.trim();
    clearTimeout(purchaseSearchTimer);
    purchaseSearchTimer = setTimeout(() => {
        performPurchaseSearch(term);
    }, 300);
});

document.getElementById('purchase_search').addEventListener('focus', function() {
    const term = this.value.trim();
    if (term.length === 0) {
        performPurchaseSearch('');
    }
});

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('purchases-dropdown');
    const search = document.getElementById('purchase_search');
    if (!dropdown.contains(event.target) && !search.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.product-row').forEach((row, index) => {
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').value = lineTotal.toFixed(2);
        total += lineTotal;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2);
}

// Load on page load with existing purchase items
@json($purchaseReturn->purchase->items) && loadPurchaseDetails(@json($purchaseReturn->purchase->items));
</script>
@endsection
