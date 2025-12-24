{{-- resources/views/admin/orders/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.edit_order') }} - {{ $order->number }}</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Store products data for JavaScript -->
                    @php
                        $productsArray = $products->map(function($p) {
                            return [
                                'id' => $p->id,
                                'name_en' => $p->name_en,
                                'name_ar' => $p->name_ar ?? $p->name_en,
                                'price' => $p->selling_price,
                                'tax' => $p->tax ?? 15
                            ];
                        })->toArray();
                    @endphp
                    <script>
                        const productsData = @json($productsArray);
                        const currentLocale = '{{ app()->getLocale() }}';
                    </script>

                    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">{{ __('messages.select_user') }}</label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="">{{ __('messages.choose_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('messages.order_status') }}</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>{{ __('messages.done') }}</option>
                                        <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>{{ __('messages.canceled') }}</option>
                                        <option value="6" {{ $order->status == 6 ? 'selected' : '' }}>{{ __('messages.refund') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>{{ __('messages.products') }}</label>
                            <div id="products-container">
                                @foreach($order->orderProducts as $index => $orderProduct)
                                <div class="product-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="products[{{ $index }}][id]" class="form-control product-select" required>
                                                <option value="">{{ __('messages.select_product') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-price="{{ $product->selling_price }}"
                                                            data-tax="{{ $product->tax }}"
                                                            {{ $orderProduct->product_id == $product->id ? 'selected' : '' }}>
                                                        @if(app()->getLocale() === 'ar')
                                                            {{ $product->name_ar ?? $product->name_en }} - {{ $product->selling_price }}
                                                        @else
                                                            {{ $product->name_en }} - {{ $product->selling_price }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="products[{{ $index }}][quantity]" 
                                                   class="form-control quantity-input" 
                                                   placeholder="{{ __('messages.quantity') }}" 
                                                   min="1" 
                                                   value="{{ $orderProduct->quantity }}"
                                                   required>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="line-total"><x-riyal-icon style="width: 12px; height: 12px;" /> {{ number_format($orderProduct->total_price_after_tax, 2) }}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger remove-product" 
                                                    {{ $loop->first && $order->orderProducts->count() == 1 ? 'disabled' : '' }}>×</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success" id="add-product">{{ __('messages.add_product') }}</button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                    <input type="number" name="paid_amount" id="paid_amount" 
                                           class="form-control" min="0" step="0.01" 
                                           value="{{ $order->paid_amount }}"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('messages.order_summary') }}</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('messages.subtotal') }}:</span>
                                                <span id="subtotal"><x-riyal-icon style="width: 14px; height: 14px;" /> {{ number_format($order->total_prices - $order->total_taxes, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>{{ __('messages.tax') }}:</span>
                                                <span id="tax-total"><x-riyal-icon style="width: 14px; height: 14px;" /> {{ number_format($order->total_taxes, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between font-weight-bold">
                                                <span>{{ __('messages.total') }}:</span>
                                                <span id="grand-total"><x-riyal-icon style="width: 14px; height: 14px;" /> {{ number_format($order->total_prices, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span>{{ __('messages.remaining') }}:</span>
                                                <span id="remaining-amount"><x-riyal-icon style="width: 14px; height: 14px;" /> {{ number_format($order->remaining_amount, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="note">{{ __('messages.note') }}</label>
                            <textarea name="note" id="note" class="form-control" rows="3" 
                                      placeholder="{{ __('messages.optional_note') }}">{{ $order->note }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('messages.update_order') }}</button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-info">{{ __('messages.view_order') }}</a>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = {{ $order->orderProducts->count() }};
    
    // Add product row
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newRow = container.children[0].cloneNode(true);

        // Update indices
        newRow.querySelectorAll('[name]').forEach(input => {
            const currentName = input.name;
            const newName = currentName.replace(/\[\d+\]/, `[${productIndex}]`);
            input.name = newName;
            if (input.type !== 'button') {
                input.value = input.type === 'number' ? '' : '';
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            }
        });

        // Regenerate product options with correct language
        const productSelect = newRow.querySelector('.product-select');
        productSelect.innerHTML = '<option value="">{{ __('messages.select_product') }}</option>';

        productsData.forEach(product => {
            const displayName = currentLocale === 'ar' ? product.name_ar : product.name_en;
            const option = document.createElement('option');
            option.value = product.id;
            option.dataset.price = product.price;
            option.dataset.tax = product.tax;
            option.textContent = `${displayName} - ${product.price}`;
            productSelect.appendChild(option);
        });

        newRow.querySelector('.line-total').textContent = '0.00';
        newRow.querySelector('.remove-product').disabled = false;

        container.appendChild(newRow);
        productIndex++;

        attachEventListeners(newRow);
    });
    
    // Remove product row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product') && !e.target.disabled) {
            const productRows = document.querySelectorAll('.product-row');
            if (productRows.length > 1) {
                e.target.closest('.product-row').remove();
                calculateTotals();
            } else {
                alert('{{ __('messages.cannot_remove_last_product') }}');
            }
        }
    });
    
    // Attach event listeners to existing rows
    document.querySelectorAll('.product-row').forEach(row => {
        attachEventListeners(row);
    });
    
    // Paid amount change
    document.getElementById('paid_amount').addEventListener('input', calculateTotals);
    
    function attachEventListeners(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        
        productSelect.addEventListener('change', function() {
            calculateLineTotal(row);
        });
        
        quantityInput.addEventListener('input', function() {
            calculateLineTotal(row);
        });
    }
    
    function calculateLineTotal(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const lineTotalSpan = row.querySelector('.line-total');

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const sellingPrice = parseFloat(selectedOption.dataset.price) || 0; // السعر مع الضريبة
        const tax = parseFloat(selectedOption.dataset.tax) || 0;
        const quantity = parseInt(quantityInput.value) || 0;

        // حساب السعر بدون ضريبة من السعر مع الضريبة
        const priceBeforeTax = sellingPrice / (1 + (tax / 100));
        const subtotal = priceBeforeTax * quantity;
        const taxAmount = (subtotal * tax) / 100;
        const total = subtotal + taxAmount;

        lineTotalSpan.innerHTML = '<x-riyal-icon style="width: 12px; height: 12px;" /> ' + total.toFixed(2);
        
        calculateTotals();
    }
    
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        document.querySelectorAll('.product-row').forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const sellingPrice = parseFloat(selectedOption.dataset.price) || 0; // السعر مع الضريبة
            const tax = parseFloat(selectedOption.dataset.tax) || 0;
            const quantity = parseInt(quantityInput.value) || 0;

            // حساب السعر بدون ضريبة من السعر مع الضريبة
            const priceBeforeTax = sellingPrice / (1 + (tax / 100));
            const lineSubtotal = priceBeforeTax * quantity;
            const lineTax = (lineSubtotal * tax) / 100;

            subtotal += lineSubtotal;
            totalTax += lineTax;
        });
        
        const grandTotal = subtotal + totalTax;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const remainingAmount = Math.max(0, grandTotal - paidAmount);

        const riyalIcon = '<svg viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg" style="width: 14px; height: 14px; display: inline; margin-right: 4px; vertical-align: middle;"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

        document.getElementById('subtotal').innerHTML = riyalIcon + subtotal.toFixed(2);
        document.getElementById('tax-total').innerHTML = riyalIcon + totalTax.toFixed(2);
        document.getElementById('grand-total').innerHTML = riyalIcon + grandTotal.toFixed(2);
        document.getElementById('remaining-amount').innerHTML = riyalIcon + remainingAmount.toFixed(2);
    }
    
    // Initial calculation
    calculateTotals();
});
</script>
@endsection