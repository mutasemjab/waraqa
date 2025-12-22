@extends('layouts.admin')

@section('title', __('messages.Edit_User'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.Edit_User') }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.User_Details') }}</h6>
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

            <form id="user-form" action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Basic Information -->
                        <div class="form-group">
                            <label for="name">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">{{ __('messages.Phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">{{ __('messages.Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">{{ __('messages.Password') }}</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">{{ __('messages.Leave_blank_to_keep_current_password') }}</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Additional Information -->
                      
                        
                        <div class="form-group">
                            <label for="activate">{{ __('messages.Status') }}</label>
                            <select class="form-control" id="activate" name="activate">
                                <option value="1" {{ old('activate', $user->activate) == 1 ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                                <option value="2" {{ old('activate', $user->activate) == 2 ? 'selected' : '' }}>{{ __('messages.Inactive') }}</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="country_id">{{ __('messages.country') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">{{ __('messages.select_country') }}</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        
                        <div class="form-group">
                            <label for="photo">{{ __('messages.Photo') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photo" name="photo">
                                <label class="custom-file-label" for="photo">{{ __('messages.Choose_file') }}</label>
                            </div>
                            <div class="mt-3" id="image-preview">
                                @if($user->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $user->photo) }}" alt="{{ $user->name }}" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-primary" id="user-submit-btn">
                        <i class="fas fa-save"></i> {{ __('messages.Update') }}
                    </button>
                    <button type="button" class="btn btn-info" id="add-event-btn">
                        <i class="fas fa-calendar-alt"></i> {{ __('messages.Add_Event') }}
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
            <!-- Existing Events -->
            <div class="mb-4">
                <h5>{{ __('messages.Existing_Events') }}</h5>
                <div id="existing-events"></div>
            </div>

            <!-- Add New Event Form -->
            <div class="border-top pt-4">
                <h5>{{ __('messages.Add_New_Event') }}</h5>
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
                        <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.01" required>
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
        let newEventsData = [];
        let existingEventsData = @json($user->events ?? []);

        // Function to format date
        function formatDate(dateString) {
            let date = new Date(dateString);
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day = String(date.getDate()).padStart(2, '0');
            let hours = String(date.getHours()).padStart(2, '0');
            let minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day} ${hours}:${minutes}`;
        }

        // Load existing events
        function renderExistingEvents() {
            let html = '';
            existingEventsData.forEach((event, index) => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>${event.name}</strong>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteExistingEvent(${event.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small>{{ __('messages.Start_Date') }}: ${formatDate(event.start_date)}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.End_Date') }}: ${formatDate(event.end_date)}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.Commission_Percentage') }}: ${event.commission_percentage}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            if (html === '') {
                html = '<p class="text-muted">{{ __('messages.No_data') ?? "لا توجد بيانات" }}</p>';
            }
            $('#existing-events').html(html);
        }

        // عرض الفعاليات الموجودة مباشرة عند فتح الصفحة
        if (existingEventsData.length > 0) {
            $('#events-section').show();
            $('#add-event-btn').hide();
            renderExistingEvents();
        }

        // Show events section
        $('#add-event-btn').on('click', function() {
            $('#events-section').slideDown();
            $('#add-event-btn').hide();
            renderExistingEvents();
        });

        // Close events section
        $('#close-events').on('click', function() {
            $('#events-section').slideUp();
            $('#add-event-btn').show();
            $('#event-form')[0].reset();
        });

        // Save new event
        $('#save-event-btn').on('click', function() {
            if ($('#event-form')[0].checkValidity() === false) {
                alert('{{ __('messages.Please_fill_all_required_fields') }}');
                return;
            }

            let eventData = {
                name: $('#event_name').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                commission_percentage: $('#commission_percentage').val()
            };

            newEventsData.push(eventData);
            renderNewEvents();
            $('#event-form')[0].reset();
        });

        function renderNewEvents() {
            let html = '';
            newEventsData.forEach((event, index) => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>${event.name}</strong>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeNewEvent(${index})">
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
            $('#existing-events').append(html);
        }

        // Delete new event
        window.removeNewEvent = function(index) {
            newEventsData.splice(index, 1);
            $('#existing-events').html('');
            renderExistingEvents();
            renderNewEvents();
        };

        // Delete existing event (will be handled by backend)
        window.deleteExistingEvent = function(eventId) {
            if (confirm('{{ __('messages.Confirm_Delete_Event') }}')) {
                existingEventsData = existingEventsData.filter(e => e.id !== eventId);
                renderExistingEvents();
            }
        };

        // Add events data to form submission
        $('#user-form').on('submit', function(e) {
            // Clear any existing event inputs first
            $('#user-form').find('input[name^="events"]').remove();
            $('#user-form').find('input[name="deleted_events"]').remove();

            if (newEventsData.length > 0) {
                newEventsData.forEach((event, index) => {
                    // Create hidden inputs for new events
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'events[' + index + '][name]',
                        value: event.name
                    }).appendTo('#user-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'events[' + index + '][start_date]',
                        value: event.start_date
                    }).appendTo('#user-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'events[' + index + '][end_date]',
                        value: event.end_date
                    }).appendTo('#user-form');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'events[' + index + '][commission_percentage]',
                        value: event.commission_percentage
                    }).appendTo('#user-form');
                });
            }

            // Handle deleted events
            let deletedIds = [];
            @json($user->events ?? []).forEach(event => {
                if (!existingEventsData.find(e => e.id === event.id)) {
                    deletedIds.push(event.id);
                }
            });
            if (deletedIds.length > 0) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'deleted_events',
                    value: deletedIds.join(',')
                }).appendTo('#user-form');
            }
        });
    });
</script>
@endsection