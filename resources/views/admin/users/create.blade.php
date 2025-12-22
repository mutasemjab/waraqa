@extends('layouts.admin')

@section('title', __('messages.Create_User'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.Create_User') }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.User_Details') }}</h6>
        </div>
        <div class="card-body">

            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="user-form">
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
                    <button type="submit" class="btn btn-primary" id="user-submit-btn">
                        <i class="fas fa-save"></i> {{ __('messages.Save') }}
                    </button>
                    <button type="button" class="btn btn-info" id="add-event-btn">
                        <i class="fas fa-calendar-alt"></i> {{ __('messages.Add_Event') ?? 'إضافة فعالية' }}
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
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Events') ?? 'الفعاليات' }}</h6>
        </div>
        <div class="card-body">
            <div id="events-list" class="mb-4"></div>

            <form id="event-form">
                <div class="form-group">
                    <label for="event_name">{{ __('messages.Event_Name') ?? 'اسم الفعالية' }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">{{ __('messages.Start_Date') ?? 'تاريخ البدء' }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">{{ __('messages.End_Date') ?? 'تاريخ الانتهاء' }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="commission_percentage">{{ __('messages.Commission_Percentage') ?? 'نسبة العمولة (%)' }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.01" required>
                </div>

                <div class="form-group text-center mt-4">
                    <button type="button" class="btn btn-success" id="save-event-btn">
                        <i class="fas fa-plus"></i> {{ __('messages.Add_Event') ?? 'إضافة الفعالية' }}
                    </button>
                    <button type="button" class="btn btn-secondary" id="close-events">
                        <i class="fas fa-times"></i> {{ __('messages.Cancel') ?? 'إلغاء' }}
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
        });

        // Save event
        $('#save-event-btn').on('click', function() {
            if ($('#event-form')[0].checkValidity() === false) {
                alert('{{ __('messages.Please_fill_all_required_fields') ?? 'يرجى ملء جميع الحقول المطلوبة' }}');
                return;
            }

            let eventData = {
                name: $('#event_name').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                commission_percentage: $('#commission_percentage').val()
            };

            eventsData.push(eventData);
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
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(${index})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small>{{ __('messages.Start_Date') ?? 'تاريخ البدء' }}: ${event.start_date}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.End_Date') ?? 'تاريخ الانتهاء' }}: ${event.end_date}</small>
                                </div>
                                <div class="col-md-4">
                                    <small>{{ __('messages.Commission_Percentage') ?? 'نسبة العمولة' }}: ${event.commission_percentage}%</small>
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
        $('#user-form').on('submit', function(e) {
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