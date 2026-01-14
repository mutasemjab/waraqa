@extends('layouts.admin')
@section('title')
{{ __('messages.events_report') }}
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">{{ __('messages.events_report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Search Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.search_event') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="event_search">{{ __('messages.event') }}</label>
                                    <input type="hidden" id="event_id" name="event_id" value="">
                                    <input type="text" id="event_search" class="form-control"
                                        placeholder="{{ __('messages.search_by_name') }}" autocomplete="off">
                                    <div id="events-dropdown" class="border rounded mt-1"
                                        style="display:none; position: absolute; width: calc(50% - 30px); max-height: 300px; overflow-y: auto; background: white; z-index: 1000;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-group" style="width: 100%;">
                                    <button type="button" class="btn btn-primary btn-block" id="searchBtn">
                                        <i class="fas fa-search"></i> {{ __('messages.Search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center" style="display:none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('messages.loading') }}</span>
            </div>
            <p>{{ __('messages.loading') }}</p>
        </div>

        <!-- Event Info Section -->
        <div id="eventInfoSection" style="display:none;">
            <!-- Event Header Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title" id="event-header-name">-</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>{{ __('messages.name') }}:</strong></p>
                                    <p id="event-name">-</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>{{ __('messages.start_date') }}:</strong></p>
                                    <p id="event-start-date">-</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>{{ __('messages.end_date') }}:</strong></p>
                                    <p id="event-end-date">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.total_orders') }}</span>
                            <span class="info-box-number" id="stat-total-orders">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.unique_customers') }}</span>
                            <span class="info-box-number" id="stat-unique-customers">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-money-bill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.total_revenue') }}</span>
                            <span class="info-box-number"><span id="stat-total-revenue">0.00</span> <x-riyal-icon /></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-percent"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.commission') }}</span>
                            <span class="info-box-number"><span id="stat-commission">0.00</span> <x-riyal-icon /></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.financial_summary') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.total_revenue') }}</p>
                                        <h4 id="stat-total-revenue-full" style="font-size: 1.2em;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.total_taxes') }}</p>
                                        <h4 id="stat-total-taxes" style="font-size: 1.2em;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.total_paid') }}</p>
                                        <h4 id="stat-total-paid" style="font-size: 1.2em;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.total_remaining') }}</p>
                                        <h4 id="stat-total-remaining" style="font-size: 1.2em;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.commission_value') }}</p>
                                        <h4 id="stat-commission-full" style="font-size: 1.2em;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="border-bottom pb-2">
                                        <p class="text-muted small">{{ __('messages.net_revenue') }}</p>
                                        <h4 id="stat-net-revenue" style="font-size: 1.2em; color: #28a745;">0.00 <x-riyal-icon /></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>{{ __('messages.completed_orders') }}</h5>
                            <h3 id="stat-completed-orders">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>{{ __('messages.cancelled_orders') }}</h5>
                            <h3 id="stat-cancelled-orders">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>{{ __('messages.commission_percentage') }}</h5>
                            <h3 id="stat-commission-percentage">0%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.orders') }}</h3>
                        </div>
                        <div class="card-body">
                            <div id="ordersTableContainer">
                                <p class="text-center text-muted">{{ __('messages.No_data') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center text-muted py-5">
            <i class="fas fa-calendar-alt" style="font-size: 3em; margin-bottom: 10px;"></i>
            <p>{{ __('messages.select_event_to_view_report') }}</p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let eventSearchTimer;
    const dropdown = $('#events-dropdown');
    const searchInput = $('#event_search');
    const searchBtn = $('#searchBtn');
    const eventIdInput = $('#event_id');
    const eventInfoSection = $('#eventInfoSection');
    const noDataMessage = $('#noDataMessage');
    const loadingSpinner = $('#loadingSpinner');

    // Perform event search
    function performEventSearch(term) {
        $.ajax({
            url: '{{ route("admin.events.search") }}',
            method: 'GET',
            data: { term: term, limit: 10 },
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(event) {
                        html += `<div class="p-2 border-bottom event-item"
                                    data-id="${event.id}"
                                    data-text="${event.text}"
                                    style="cursor: pointer; transition: 0.3s;">
                                    <strong>${event.name}</strong><br>
                                    <small class="text-muted">${event.start_date}</small>
                                </div>`;
                    });
                    dropdown.html(html).show();

                    // Add hover effect
                    $('.event-item').hover(function() {
                        $(this).css('background-color', '#f5f5f5');
                    }, function() {
                        $(this).css('background-color', 'transparent');
                    });

                    // Add click handlers
                    $('.event-item').on('click', function() {
                        const id = $(this).data('id');
                        const text = $(this).data('text');
                        eventIdInput.val(id);
                        searchInput.val(text);
                        dropdown.hide();
                    });
                } else {
                    dropdown.html('<div class="p-2 text-muted">{{ __("messages.no_results") }}</div>').show();
                }
            },
            error: function(xhr) {
                console.error('Error searching events:', xhr);
                dropdown.html('<div class="p-2 text-danger">{{ __("messages.error") }}</div>').show();
            }
        });
    }

    // Show all events when focused
    searchInput.on('focus', function() {
        const term = $(this).val().trim();
        if (term.length === 0) {
            performEventSearch('');
        }
    });

    // Search on input
    searchInput.on('input', function() {
        const term = $(this).val().trim();

        clearTimeout(eventSearchTimer);
        eventSearchTimer = setTimeout(() => {
            if (term.length >= 0) {
                performEventSearch(term);
            }
        }, 300);
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#event_search, #events-dropdown').length) {
            dropdown.hide();
        }
    });

    // Search button click
    searchBtn.on('click', function() {
        const eventId = eventIdInput.val();

        if (!eventId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.warning") }}',
                text: '{{ __("messages.select_event_first") }}',
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
            return;
        }

        loadEventData(eventId);
    });

    // Load event data via AJAX
    function loadEventData(eventId) {
        loadingSpinner.show();
        eventInfoSection.hide();
        noDataMessage.hide();

        $.ajax({
            url: '{{ route("admin.events.report.data", ":id") }}'.replace(':id', eventId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    populateEventInfo(response);
                    loadingSpinner.hide();
                    eventInfoSection.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: response.message || '{{ __("messages.error_loading_data") }}',
                        confirmButtonText: '{{ __("messages.confirm") }}'
                    });
                    loadingSpinner.hide();
                    noDataMessage.show();
                }
            },
            error: function(xhr) {
                console.error('Error loading event data:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: '{{ __("messages.error_loading_data") }}',
                    confirmButtonText: '{{ __("messages.confirm") }}'
                });
                loadingSpinner.hide();
                noDataMessage.show();
            }
        });
    }

    // Populate event information
    function populateEventInfo(data) {
        const event = data.event;
        const stats = data.statistics;
        const orders = data.orders;

        // Event info
        $('#event-header-name').text(event.name);
        $('#event-name').text(event.name);
        $('#event-start-date').text(event.start_date);
        $('#event-end-date').text(event.end_date);

        // Statistics
        $('#stat-total-orders').text(stats.total_orders);
        $('#stat-completed-orders').text(stats.completed_orders);
        $('#stat-cancelled-orders').text(stats.cancelled_orders);
        $('#stat-unique-customers').text(stats.unique_customers);
        $('#stat-commission-percentage').text(event.commission_percentage + '%');

        // Financial summary
        const riyalIcon = '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';
        $('#stat-total-revenue').text(stats.total_revenue);
        $('#stat-total-revenue-full').html(stats.total_revenue + ' ' + riyalIcon);
        $('#stat-total-taxes').html(stats.total_taxes + ' ' + riyalIcon);
        $('#stat-total-paid').html(stats.total_paid + ' ' + riyalIcon);
        $('#stat-total-remaining').html(stats.total_remaining + ' ' + riyalIcon);
        $('#stat-commission').text(stats.total_commission);
        $('#stat-commission-full').html(stats.total_commission + ' ' + riyalIcon);
        $('#stat-net-revenue').html(stats.net_revenue + ' ' + riyalIcon);

        // Orders table
        if (orders.length > 0) {
            let ordersHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.order_number') }}</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.payment_status') }}</th>
                                <th>{{ __('messages.total_prices') }}</th>
                                <th>{{ __('messages.paid_amount') }}</th>
                                <th>{{ __('messages.remaining_amount') }}</th>
                                <th>{{ __('messages.event_commission_percentage') }}</th>
                                <th>{{ __('messages.event_commission_value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const riyalIcon = '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

            orders.forEach(function(order) {
                ordersHtml += `
                    <tr>
                        <td><strong>${order.number}</strong></td>
                        <td>${order.date}</td>
                        <td>${order.customer}</td>
                        <td>${order.status}</td>
                        <td>${order.payment_status}</td>
                        <td>${order.total_prices} ${riyalIcon}</td>
                        <td>${order.paid_amount} ${riyalIcon}</td>
                        <td>${order.remaining_amount} ${riyalIcon}</td>
                        <td>${order.commission_percentage}%</td>
                        <td>${order.commission_value} ${riyalIcon}</td>
                    </tr>
                `;
            });

            ordersHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#ordersTableContainer').html(ordersHtml);
        } else {
            $('#ordersTableContainer').html('<p class="text-center text-muted">{{ __("messages.no_orders") }}</p>');
        }
    }

    // Allow Enter key to search
    searchInput.on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            searchBtn.click();
        }
    });
});
</script>
@endsection
