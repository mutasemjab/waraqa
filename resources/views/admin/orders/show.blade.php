{{-- resources/views/admin/orders/show.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>{{ __('messages.order_details') }} - {{ $order->number }}</h4>
                        <div>
                            <a href="{{ route('orders.edit', $order) }}"
                                class="btn btn-warning">{{ __('messages.edit_order') }}</a>

                            <a href="{{ route('orders.index') }}"
                                class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row mb-4">
                            <!-- Order Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.order_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('messages.order_number') }}:</strong></td>
                                                <td>{{ $order->number }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.date') }}:</strong></td>
                                                <td>{{ $order->date->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.status') }}:</strong></td>
                                                <td>
                                                    {!! \App\Enums\OrderStatus::tryFrom($order->status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.payment_status') }}:</strong></td>
                                                <td>
                                                    {!! \App\Enums\PaymentStatus::tryFrom($order->payment_status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                                                </td>
                                            </tr>
                                            @if($order->note)
                                                <tr>
                                                    <td><strong>{{ __('messages.note') }}:</strong></td>
                                                    <td>{{ $order->note }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.customer_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($order->user)
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.name') }}:</strong></td>
                                                    <td>{{ $order->user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.phone') }}:</strong></td>
                                                    <td>{{ $order->user->phone }}</td>
                                                </tr>
                                                @if($order->user->email)
                                                    <tr>
                                                        <td><strong>{{ __('messages.email') }}:</strong></td>
                                                        <td>{{ $order->user->email }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @else
                                            <p class="text-muted">{{ __('messages.no_customer_assigned') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Order Products Table (Show View) --}}
                        {{-- Displays all products in the order with pricing and tax calculations --}}
                        {{-- Each row shows item details, quantities, prices, and tax information --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>{{ __('messages.order_products') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('messages.product_name') }}</th>
                                                {{-- Unit price: selling_price (price with tax) from product table --}}
                                                <th>{{ __('messages.unit_price') }}</th>
                                                {{-- Quantity ordered from product row --}}
                                                <th>{{ __('messages.quantity') }}</th>
                                                {{-- Tax percentage from product table --}}
                                                <th>{{ __('messages.tax_percentage') }}</th>
                                                {{-- Tax amount: (total_price_before_tax × tax_percentage) / 100 --}}
                                                <th>{{ __('messages.tax_value') }}</th>
                                                {{-- Subtotal before tax: (unit_price ÷ (1 + tax_percentage/100)) × quantity --}}
                                                <th>{{ __('messages.total_before_tax') }}</th>
                                                {{-- Total including tax: total_price_before_tax + tax_value --}}
                                                <th>{{ __('messages.total_after_tax') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->orderProducts as $orderProduct)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $orderProduct->product->name_en }}</strong><br>
                                                        <small class="text-muted">{{ $orderProduct->product->name_ar }}</small>
                                                    </td>
                                                    {{-- Selling price (with tax) per unit --}}
                                                    <td><x-riyal-icon /> {{ number_format($orderProduct->unit_price, 2) }}</td>
                                                    {{-- Quantity ordered --}}
                                                    <td>{{ $orderProduct->quantity }}</td>
                                                    {{-- Tax percentage applied to this product --}}
                                                    <td>{{ $orderProduct->tax_percentage }}%</td>
                                                    {{-- Calculated tax amount for this line item --}}
                                                    <td><x-riyal-icon /> {{ number_format($orderProduct->tax_value, 2) }}</td>
                                                    {{-- Total price before tax for this line item --}}
                                                    <td><x-riyal-icon />
                                                        {{ number_format($orderProduct->total_price_before_tax, 2) }}</td>
                                                    {{-- Total price including tax for this line item --}}
                                                    <td><strong><x-riyal-icon />
                                                            {{ number_format($orderProduct->total_price_after_tax, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        {{-- Summary row showing total amounts --}}
                                        <tfoot>
                                            <tr class="table-active">
                                                <th colspan="4">{{ __('messages.totals') }}</th>
                                                {{-- Sum of all tax amounts: Σ(tax_value) --}}
                                                <th><x-riyal-icon /> {{ number_format($order->total_taxes, 2) }}</th>
                                                {{-- Sum of all before-tax amounts: order.total_prices - order.total_taxes --}}
                                                <th><x-riyal-icon />
                                                    {{ number_format($order->total_prices - $order->total_taxes, 2) }}</th>
                                                {{-- Grand total with tax: order.total_prices --}}
                                                <th><strong><x-riyal-icon />
                                                        {{ number_format($order->total_prices, 2) }}</strong></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.payment_information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>{{ __('messages.total_amount') }}:</strong></td>
                                                <td><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.paid_amount') }}:</strong></td>
                                                <td class="text-success"><x-riyal-icon />
                                                    {{ number_format($order->paid_amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.remaining_amount') }}:</strong></td>
                                                <td
                                                    class="{{ $order->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                    <x-riyal-icon /> {{ number_format($order->remaining_amount, 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Event Commission Display Section (Show View) --}}
                            {{-- Only displayed if the order is linked to an event --}}
                            {{-- Shows event details, commission percentage, and calculated values --}}
                            @if($order->event)
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-light">
                                            <h5>{{ __('messages.event_commission') ?? 'Event Commission' }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    {{-- Event Commission Percentage --}}
                                                    {{-- Comes from: event.commission_percentage --}}
                                                    <strong>{{ __('messages.commission_percentage') ?? 'Commission %' }}:</strong><br>
                                                    <span class="badge badge-info"
                                                        style="font-size: 0.95rem;">{{ number_format($order->event->commission_percentage, 2) }}%</span>
                                                </div>
                                                <div class="col-md-6">
                                                    {{-- Event Commission Value Calculation --}}
                                                    {{-- Formula: (total_before_tax × event.commission_percentage) / 100 --}}
                                                    <strong>{{ __('messages.commission_value') ?? 'Commission Value' }}:</strong><br>
                                                    @php
                                                        // Calculate total before tax (order.total_prices - order.total_taxes)
                                                        $totalBeforeTax = $order->total_prices - $order->total_taxes;
                                                        // Calculate event commission value
                                                        $commissionValue = ($totalBeforeTax * $order->event->commission_percentage) / 100;
                                                        // Calculate amount due to supplier after commission deduction
                                                        $amountDueToSupplier = $totalBeforeTax - $commissionValue;
                                                    @endphp
                                                    <span class="badge badge-success" style="font-size: 0.95rem;"><x-riyal-icon
                                                            style="width: 12px; height: 12px;" />
                                                        {{ number_format($commissionValue, 2) }}</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row mb-2">
                                                <div class="col-md-12">
                                                    {{-- Amount Due to Supplier After Commission --}}
                                                    {{-- Formula: total_before_tax - commission_value --}}
                                                    {{-- This is what the supplier/seller receives after commission deduction --}}
                                                    <strong class="text-primary">{{ __('messages.amount_due_to_supplier') ?? 'المبلغ المستحق للمورد' }} <small>({{ __('messages.without_tax') ?? 'بدون الضريبة' }})</small>:</strong><br>
                                                    <span class="badge badge-warning" style="font-size: 1.1rem; width: 100%;">
                                                        <x-riyal-icon style="width: 14px; height: 14px;" />
                                                        {{ number_format($amountDueToSupplier, 2) }}
                                                    </span>
                                                    {{-- Show the formula used for transparency --}}
                                                    <small class="d-block mt-1 text-muted">
                                                        {{ __('messages.calculation_formula') ?? 'المعادلة' }}: {{ number_format($totalBeforeTax, 2) }} ({{ __('messages.total_before_tax') ?? 'الإجمالي قبل الضريبة' }}) - {{ number_format($commissionValue, 2) }} ({{ __('messages.commission') ?? 'العمولة' }})
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- Event Name --}}
                                            {{-- Shows which event the order is linked to --}}
                                            <small class="text-muted">{{ __('messages.event') }}:
                                                {{ $order->event->name }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Commission Display Section (Show View) --}}
                            {{-- Displayed based on user role (seller or customer) --}}
                            {{-- Shows commission details and calculated amounts --}}
                            @if($order->user && ($order->user->commission_percentage ?? 0) > 0)
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-light">
                                            @if($order->user->hasRole('seller'))
                                                <h5>{{ __('messages.seller_commission') }}</h5>
                                            @else
                                                <h5>{{ __('messages.commission_percentage') }}</h5>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    {{-- Commission Percentage --}}
                                                    <strong>{{ __('messages.commission_percentage') ?? 'Commission %' }}:</strong><br>
                                                    <span class="badge badge-info"
                                                        style="font-size: 0.95rem;">{{ number_format($order->user->commission_percentage, 2) }}%</span>
                                                </div>
                                                <div class="col-md-6">
                                                    {{-- Commission Value Calculation --}}
                                                    {{-- Formula: (total_before_tax × user.commission_percentage) / 100 --}}
                                                    <strong>{{ __('messages.commission_value') ?? 'Commission Value' }}:</strong><br>
                                                    @php
                                                        // Calculate total before tax
                                                        $totalBeforeTax = $order->total_prices - $order->total_taxes;
                                                        // Calculate commission value
                                                        $commissionValue = ($totalBeforeTax * $order->user->commission_percentage) / 100;
                                                        // Calculate amount due after commission deduction
                                                        $amountDueToSupplier = $totalBeforeTax - $commissionValue;
                                                    @endphp
                                                    <span class="badge badge-success" style="font-size: 0.95rem;"><x-riyal-icon
                                                            style="width: 12px; height: 12px;" />
                                                        {{ number_format($commissionValue, 2) }}</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row mb-2">
                                                <div class="col-md-12">
                                                    {{-- Amount Due After Commission --}}
                                                    {{-- Formula: total_before_tax - commission_value --}}
                                                    <strong class="text-primary">{{ __('messages.amount_due_to_supplier') ?? 'المبلغ المستحق للمورد' }} <small>({{ __('messages.without_tax') ?? 'بدون الضريبة' }})</small>:</strong><br>
                                                    <span class="badge badge-warning" style="font-size: 1.1rem; width: 100%;">
                                                        <x-riyal-icon style="width: 14px; height: 14px;" />
                                                        {{ number_format($amountDueToSupplier, 2) }}
                                                    </span>
                                                    {{-- Show the formula used for transparency --}}
                                                    <small class="d-block mt-1 text-muted">
                                                        {{ __('messages.calculation_formula') ?? 'المعادلة' }}: {{ number_format($totalBeforeTax, 2) }} ({{ __('messages.total_before_tax') ?? 'الإجمالي قبل الضريبة' }}) - {{ number_format($commissionValue, 2) }} ({{ __('messages.commission') ?? 'العمولة' }})
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- User Name --}}
                                            {{-- Shows the name of the seller or customer --}}
                                            <small class="text-muted">
                                                @if($order->user->hasRole('seller'))
                                                    {{ __('messages.seller') }}:
                                                @else
                                                    {{ __('messages.name') }}:
                                                @endif
                                                {{ $order->user->name }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Debt Information -->
                        @if($order->userDebt && $order->userDebt->status == 1)
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('messages.debt_information') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.debt_status') }}:</strong></td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ __('messages.active') }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.debt_created') }}:</strong></td>
                                                    <td>{{ $order->userDebt->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.last_payment') }}:</strong></td>
                                                    <td>{{ $order->userDebt->updated_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            </table>

                                            @if($order->remaining_amount > 0)
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#paymentModal">
                                                    {{ __('messages.receive_payment') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('messages.are_you_sure') }}</p>
                    <p><strong>{{ __('messages.order_number') }}:</strong> {{ $order->number }}</p>
                    <p class="text-danger">{{ __('messages.this_action_cannot_be_undone') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($order->userDebt && $order->remaining_amount > 0)
        <div class="modal fade" id="paymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.receive_payment') }} - {{ $order->user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('receive-payment', $order->userDebt) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>{{ __('messages.current_debt') }}:</strong><br>
                                    <x-riyal-icon /> {{ number_format($order->remaining_amount, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('messages.order_number') }}:</strong><br>
                                    {{ $order->number }}
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="payment_amount">{{ __('messages.payment_amount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><x-riyal-icon style="width: 16px; height: 16px;" /></span>
                                    <input type="number" name="payment_amount" id="payment_amount" class="form-control" min="0"
                                        max="{{ $order->remaining_amount }}" step="0.01" required>
                                </div>
                                <small class="text-muted">{{ __('messages.payment_amount_max') }}</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('messages.confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection