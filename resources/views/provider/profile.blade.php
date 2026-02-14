@extends('layouts.provider')

@section('title', __('messages.profile'))
@section('page-title', __('messages.profile'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.my_profile') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_your_profile_information') }}</p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>{{ __('messages.profile_information') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">{{ __('messages.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">{{ __('messages.phone_number') }} <span class="text-danger">*</span></label>
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label for="email" class="form-label">{{ __('messages.email_address') }}</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="photo" class="form-label">{{ __('messages.profile_photo') }}</label>
                        <input type="file"
                               class="form-control @error('photo') is-invalid @enderror"
                               id="photo"
                               name="photo"
                               accept="image/*">
                        <div class="form-text">{{ __('messages.profile_photo_requirements') }}</div>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>{{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Summary -->
    <div class="col-lg-4">
        <!-- Current Profile -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-id-card me-2"></i>{{ __('messages.current_profile') }}
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="profile-image-container mb-4" style="margin-bottom: 2rem;">
                    <img src="{{ $user->photo_url }}"
                         alt="{{ $user->name }}"
                         class="rounded-circle border"
                         width="140"
                         height="140"
                         style="object-fit: cover; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); border-width: 5px;">
                </div>

                <h5 class="mb-2 fw-bold" style="font-size: 1.3rem;">{{ $user->name }}</h5>
                <p class="text-muted mb-2">
                    <i class="fas fa-envelope me-1"></i>{{ $user->email ?: __('messages.no_email') }}
                </p>
                <p class="text-muted mb-0">
                    <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                </p>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.account_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <strong>{{ __('messages.member_since') }}:</strong>
                    <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                </div>

                <div class="info-item mb-3">
                    <strong>{{ __('messages.account_status') }}:</strong>
                    <span class="badge bg-{{ $user->activate == 1 ? 'success' : 'danger' }}">
                        {{ $user->activate == 1 ? __('messages.active') : __('messages.inactive') }}
                    </span>
                </div>

                <div class="info-item mb-3">
                    <strong>{{ __('messages.last_login') }}:</strong>
                    <span class="text-muted">{{ $user->updated_at->diffForHumans() }}</span>
                </div>

            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>{{ __('messages.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('provider.orders') }}" class="btn btn-outline-success">
                        <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.orders') }}
                    </a>

                    <a href="{{ route('provider.bookRequests') }}" class="btn btn-outline-info">
                        <i class="fas fa-book me-2"></i>{{ __('messages.book_requests') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview uploaded image
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-image-container img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
