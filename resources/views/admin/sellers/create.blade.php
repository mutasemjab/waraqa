@extends('layouts.admin')

@section('title', __('messages.Create_Seller'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.Create_Seller') }}</h1>
        <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Seller_Details') }}</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('sellers.store') }}" method="POST" enctype="multipart/form-data" id="seller-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <!-- Basic Information -->
                        <div class="form-group">
                            <label for="name">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">{{ __('messages.Phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('messages.Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('messages.Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Additional Information -->

                        <div class="form-group">
                            <label for="activate">{{ __('messages.Status') }}</label>
                            <select class="form-control" id="activate" name="activate">
                                <option value="1" {{ old('activate', 1) == 1 ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                                <option value="2" {{ old('activate') == 2 ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="country_id">{{ __('messages.country') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">{{ __('messages.select_country') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="commission_percentage">{{ __('messages.Default_Commission_Percentage') }}</label>
                            <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" value="{{ old('commission_percentage') }}" min="0" max="100" step="0.01" placeholder="0.00">
                            <small class="form-text text-muted">{{ __('messages.commission_percentage_hint') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="photo">{{ __('messages.Photo') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photo" name="photo">
                                <label class="custom-file-label" for="photo">{{ __('messages.Choose_file') }}</label>
                            </div>
                            <div class="mt-3" id="image-preview"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary" id="seller-submit-btn">
                        <i class="fas fa-save"></i> {{ __('messages.Save') }}
                    </button>
                    <button type="button" class="btn btn-info" id="add-event-btn">
                        <i class="fas fa-calendar-alt"></i> {{ __('messages.Add_Event') }}
                    </button>
                    <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Section -->
    <div class="card shadow mb-4" id="events-section" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Events') }}</h6>
        </div>
        <div class="card-body">
            <div id="events-list" class="mb-4"></div>

            <form id="event-form">
                <div class="form-group">
                    <label for="event_name">{{ __('messages.Event_Name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">{{ __('messages.Start_Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">{{ __('messages.End_Date') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="commission_percentage">{{ __('messages.Commission_Percentage') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="1" required>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="button" class="btn btn-success" id="save-event-btn">
                        <i class="fas fa-plus"></i> {{ __('messages.Add_Event') }}
                    </button>
                    <button type="button" class="btn btn-secondary" id="close-events">
                        <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Show filename on file select
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);

            // Image preview
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').html('<img src="' + e.target.result + '" class="img-fluid img-thumbnail" style="max-height: 200px;">');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Events handling
        let eventsData = [];
        let editingEventIndex = null;

        // Show events section
        $('#add-event-btn').on('click', function() {
            $('#events-section').slideDown();
            $('#add-event-btn').hide();
        });

        // Close events section
        $('#close-events').on('click', function() {
            $('#events-section').slideUp();
            $('#add-event-btn').show();
            $('#event-form')[0].reset();
            editingEventIndex = null;
            $('#save-event-btn').html('<i class="fas fa-plus"></i> {{ __("messages.Add_Event") }}');
            $('#save-event-btn').removeClass('btn-warning').addClass('btn-success');
        });

        // Edit event
        window.editEvent = function(index) {
            const event = eventsData[index];
            if (event) {
                editingEventIndex = index;
                $('#event_name').val(event.name);
                $('#start_date').val(event.start_date);
                $('#end_date').val(event.end_date);
                $('#commission_percentage').val(event.commission_percentage);
                $('#save-event-btn').html('<i class="fas fa-check"></i> {{ __("messages.Update_Event") }}');
                $('#save-event-btn').removeClass('btn-success').addClass('btn-warning');
                $('#events-section').slideDown();
                $('#add-event-btn').hide();
                // Scroll to events section
                $('html, body').animate({
                    scrollTop: $('#events-section').offset().top - 100
                }, 500);
            }
        };

        // Save event (new or edit)
        $('#save-event-btn').on('click', function() {
            let name = $('#event_name').val().trim();
            let startDate = $('#start_date').val().trim();
            let endDate = $('#end_date').val().trim();
            let commission = $('#commission_percentage').val().trim();

            // Check if all fields are filled
            if (!name || !startDate || !endDate || !commission) {
                alert('{{ __('messages.Please_fill_all_required_fields') }}');
                return;
            }

            // Check if end_date is after start_date
            if (new Date(endDate) <= new Date(startDate)) {
                alert('{{ __('messages.End_Date') }} يجب أن يكون بعد {{ __('messages.Start_Date') }}');
                return;
            }

            let eventData = {
                name: name,
                start_date: startDate,
                end_date: endDate,
                commission_percentage: commission
            };

            if (editingEventIndex !== null) {
                // Update existing event
                eventsData[editingEventIndex] = eventData;
                editingEventIndex = null;
                $('#save-event-btn').html('<i class="fas fa-plus"></i> {{ __("messages.Add_Event") }}');
                $('#save-event-btn').removeClass('btn-warning').addClass('btn-success');
            } else {
                // Add new event
                eventsData.push(eventData);
            }
            renderEvents();
            $('#event-form')[0].reset();
        });

        function renderEvents() {
            let html = '';
            eventsData.forEach((event, index) => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>${event.name}</strong>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editEvent(${index})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small>{{ __('messages.Start_Date') }}: ${event.start_date}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.End_Date') }}: ${event.end_date}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.Commission_Percentage') }}: ${event.commission_percentage}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#events-list').html(html);
        }

        window.removeEvent = function(index) {
            eventsData.splice(index, 1);
            renderEvents();
        };

        // Add events data to form submission
        $('#seller-form').on('submit', function(e) {
            if (eventsData.length > 0) {
                eventsData.forEach((event, index) => {
                    $(this).append(`
                        <input type="hidden" name="events[${index}][name]" value="${event.name}">
                        <input type="hidden" name="events[${index}][start_date]" value="${event.start_date}">
                        <input type="hidden" name="events[${index}][end_date]" value="${event.end_date}">
                        <input type="hidden" name="events[${index}][commission_percentage]" value="${event.commission_percentage}">
                    `);
                });
            }
        });
    });
</script>
@endsection