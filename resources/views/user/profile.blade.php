@extends('layouts.user')

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
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <div class="form-group mb-3">
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
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="country_id" class="form-label">{{ __('messages.country') }}</label>
                                <select name="country_id" id="country_id" class="form-select @error('country_id') is-invalid @enderror">
                                    <option value="">{{ __('messages.select_country') }}</option>
                                    @if(isset($countries))
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() === 'ar' ? $country->name_ar : $country->name_en }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('country_id')
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

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lock me-2"></i>{{ __('messages.change_password') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.password.update') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">{{ __('messages.current_password') }} <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('current_password') is-invalid @enderror" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label">{{ __('messages.new_password') }} <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       required>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password_confirmation" class="form-label">{{ __('messages.confirm_new_password') }} <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>{{ __('messages.update_password') }}
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
                <div class="profile-image-container mb-3">
                    <img src="{{ $user->photo_url }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle border" 
                         width="120" 
                         height="120"
                         style="object-fit: cover;">
                </div>
                
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email ?: __('messages.no_email') }}</p>
                <p class="text-muted mb-3">{{ $user->phone }}</p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-primary">{{ $user->orders()->count() }}</h6>
                        <small class="text-muted">{{ __('messages.total_orders') }}</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success"><x-riyal-icon /> {{ number_format($user->orders()->sum('total_prices') ?? 0, 2) }}</h6>
                        <small class="text-muted">{{ __('messages.total_spent') }}</small>
                    </div>
                </div>
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
                    <span class="text-muted">{{ $user->created_at->format('Y-m-d') }}</span>
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
                
                <div class="info-item mb-3">
                    <strong>{{ __('messages.country') }}:</strong>
                    <span class="text-muted">{{ $user->country ? (app()->getLocale() === 'ar' ? $user->country->name_ar : $user->country->name_en) : __('messages.not_specified') }}</span>
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
                    <a href="{{ route('user.orders') }}" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.view_my_orders') }}
                    </a>

                    <a href="{{ route('user.analytics') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-line me-2"></i>{{ __('messages.view_analytics') }}
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

// Password strength indicator
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);
    updatePasswordStrength(strength);
});

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}

function updatePasswordStrength(strength) {
    // Add password strength indicator if needed
    // This is optional visual enhancement
}
</script>
@endpush