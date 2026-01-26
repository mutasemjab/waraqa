@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Response Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('messages.Provider_Response_Details') }}</h4>
                    <a href="{{ route('bookRequests.show', $response->bookRequest->id) }}" class="btn btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <!-- Request Summary -->
                    <div class="alert alert-info">
                        <h6 class="mb-3"><strong>{{ __('messages.Book_Request_Information') }}</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('messages.Product') }}:</strong><br>
                                    {{ app()->getLocale() == 'ar' ? $response->bookRequest->product->name_ar : $response->bookRequest->product->name_en }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('messages.Requested_Quantity') }}:</strong><br>
                                    <span class="badge bg-primary fs-6">{{ $response->bookRequest->requested_quantity }} {{ __('messages.units') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Provider Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ __('messages.Provider_Information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('messages.provider') }}:</strong><br>
                                        {{ $response->provider->name }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('messages.Status') }}:</strong><br>
                                        @php
                                            $statusClass = match($response->status) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} fs-6">
                                            {{ __('messages.' . ucfirst($response->status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Details -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ __('messages.Response_Details') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-3">
                                        <strong>{{ __('messages.Available_Quantity') }}:</strong><br>
                                        <span class="badge bg-info fs-6">{{ $response->available_quantity }} {{ __('messages.units') }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-3">
                                        <strong>{{ __('messages.Price') }}:</strong><br>
                                        <span class="fs-6">{{ number_format($response->price, 2) }} <x-riyal-icon /></span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-3">
                                        <strong>{{ __('messages.Tax_Percentage') }}:</strong><br>
                                        <span class="fs-6">{{ $response->tax_percentage }}%</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Total Price Calculation -->
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('messages.Total_Amount') }}:</strong>
                                        <span class="float-end">{{ number_format($response->available_quantity * $response->price, 2) }} <x-riyal-icon /></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('messages.Total_Tax') }}:</strong>
                                        <span class="float-end">{{ number_format(($response->available_quantity * $response->price * $response->tax_percentage) / 100, 2) }} <x-riyal-icon /></span>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0">
                                        <strong class="fs-6">{{ __('messages.Grand_Total') }}:</strong>
                                        <span class="float-end fs-6">
                                            <strong class="text-primary">
                                                {{ number_format(($response->available_quantity * $response->price) + (($response->available_quantity * $response->price * $response->tax_percentage) / 100), 2) }} <x-riyal-icon />
                                            </strong>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($response->note)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ __('messages.Note') }}</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $response->note }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Response Date -->
                    <div class="alert alert-light mb-0">
                        <small class="text-muted">
                            {{ __('messages.Response_Date') }}: {{ $response->created_at->format('Y-m-d H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($response->status == 'pending')
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success btn-lg btn-block mb-3"
                                data-toggle="modal"
                                data-target="#approveModal">
                            <i class="fas fa-check-circle"></i> {{ __('messages.Approve') }}
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger btn-lg btn-block mb-3"
                                data-toggle="modal"
                                data-target="#rejectModal">
                            <i class="fas fa-times-circle"></i> {{ __('messages.Reject') }}
                        </button>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">{{ __('messages.Approve_Response') }}</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('bookRequests.responses.approve', $response) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p class="text-muted">{{ __('messages.Confirm_Approve_Message') }}</p>

                                    <div class="form-group mt-3">
                                        <label for="quantity" class="form-label">{{ __('messages.Quantity') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="1" class="form-control" id="quantity" name="quantity" value="{{ $response->available_quantity }}" min="1" required>
                                            <span class="input-group-text">{{ __('messages.units') }}</span>
                                        </div>
                                        <small class="text-muted d-block mt-2">يمكنك تعديل الكمية المراد الموافقة عليها</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="price" class="form-label">{{ __('messages.Price') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ number_format($response->price, 2) }}" required>
                                            <span class="input-group-text"><x-riyal-icon /></span>
                                        </div>
                                        <small class="text-muted d-block mt-2">يمكنك تعديل السعر حسب التفاوض مع المورد</small>
                                    </div>

                                    <!-- Hidden Tax Field -->
                                    <input type="hidden" name="tax_percentage" value="{{ $response->tax_percentage ?? 0 }}">

                                    <!-- Summary -->
                                    <hr>
                                    <div class="alert alert-light">
                                        <p class="mb-2"><strong>{{ __('messages.Summary') }}:</strong></p>
                                        <p class="mb-1">
                                            <strong>{{ __('messages.Quantity') }}:</strong> <span id="modalQuantity">{{ $response->available_quantity }}</span> وحدة
                                        </p>
                                        <p class="mb-1">
                                            <strong>{{ __('messages.Price') }}:</strong> <span id="modalPrice">{{ number_format($response->price, 2) }}</span> <x-riyal-icon />
                                        </p>
                                        <p class="mb-1">
                                            <strong>{{ __('messages.Total_Amount') }}:</strong> <span id="totalAmount">0</span> <x-riyal-icon />
                                        </p>
                                        <p class="mb-1">
                                            <strong>{{ __('messages.Tax_Percentage') }}:</strong> {{ $response->tax_percentage ?? 0 }}% (حسب اللوائح الحكومية)
                                        </p>
                                        <p class="mb-0 border-top pt-2">
                                            <strong class="text-primary">{{ __('messages.Grand_Total') }}:</strong> <span id="grandTotal" class="text-primary fs-6">0</span> <x-riyal-icon />
                                        </p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        {{ __('messages.Cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> {{ __('messages.Approve') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">{{ __('messages.Reject_Response') }}</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('bookRequests.responses.reject', $response) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p class="text-muted">{{ __('messages.Confirm_Reject_Message') }}</p>
                                    <p class="text-danger"><strong>{{ __('messages.This_action_cannot_be_undone') }}</strong></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        {{ __('messages.Cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times"></i> {{ __('messages.Reject') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('quantity').addEventListener('input', updateTotals);
                    document.getElementById('quantity').addEventListener('change', updateTotals);
                    document.getElementById('price').addEventListener('input', updateTotals);
                    document.getElementById('price').addEventListener('change', updateTotals);

                    function updateTotals() {
                        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
                        const price = parseFloat(document.getElementById('price').value) || 0;
                        const tax = {{ $response->tax_percentage ?? 0 }};

                        const totalAmount = quantity * price;
                        const taxAmount = (totalAmount * tax) / 100;
                        const grandTotal = totalAmount + taxAmount;

                        document.getElementById('modalQuantity').textContent = quantity;
                        document.getElementById('modalPrice').textContent = price.toFixed(2);
                        document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
                        document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
                    }

                    // Initialize on load
                    updateTotals();
                </script>
            @else
                <div class="alert alert-warning">
                    <strong>{{ __('messages.Note') }}:</strong>
                    @if($response->status == 'approved')
                        {{ __('messages.This_response_has_been_approved') }}
                    @else
                        {{ __('messages.This_response_has_been_rejected') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
