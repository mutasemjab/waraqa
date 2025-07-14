

<?php $__env->startSection('title', __('messages.profile')); ?>
<?php $__env->startSection('page-title', __('messages.profile')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.my_profile')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.manage_your_profile_information')); ?></p>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i><?php echo e(__('messages.profile_information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('user.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label"><?php echo e(__('messages.full_name')); ?> <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo e(old('name', $user->name)); ?>" 
                                       required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label"><?php echo e(__('messages.phone_number')); ?> <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo e(old('phone', $user->phone)); ?>" 
                                       required>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label"><?php echo e(__('messages.email_address')); ?></label>
                                <input type="email" 
                                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo e(old('email', $user->email)); ?>">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="country_id" class="form-label"><?php echo e(__('messages.country')); ?></label>
                                <select name="country_id" id="country_id" class="form-select <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value=""><?php echo e(__('messages.select_country')); ?></option>
                                    <?php if(isset($countries)): ?>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>" <?php echo e(old('country_id', $user->country_id) == $country->id ? 'selected' : ''); ?>>
                                                <?php echo e($country->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                                <?php $__errorArgs = ['country_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="photo" class="form-label"><?php echo e(__('messages.profile_photo')); ?></label>
                        <input type="file" 
                               class="form-control <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               id="photo" 
                               name="photo" 
                               accept="image/*">
                        <div class="form-text"><?php echo e(__('messages.profile_photo_requirements')); ?></div>
                        <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo e(__('messages.save_changes')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lock me-2"></i><?php echo e(__('messages.change_password')); ?>

                </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('user.password.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label"><?php echo e(__('messages.current_password')); ?> <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label"><?php echo e(__('messages.new_password')); ?> <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="new_password" 
                                       name="new_password" 
                                       required>
                                <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password_confirmation" class="form-label"><?php echo e(__('messages.confirm_new_password')); ?> <span class="text-danger">*</span></label>
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
                            <i class="fas fa-key me-1"></i><?php echo e(__('messages.update_password')); ?>

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
                    <i class="fas fa-id-card me-2"></i><?php echo e(__('messages.current_profile')); ?>

                </h5>
            </div>
            <div class="card-body text-center">
                <div class="profile-image-container mb-3">
                    <img src="<?php echo e($user->photo_url); ?>" 
                         alt="<?php echo e($user->name); ?>" 
                         class="rounded-circle border" 
                         width="120" 
                         height="120"
                         style="object-fit: cover;">
                </div>
                
                <h5 class="mb-1"><?php echo e($user->name); ?></h5>
                <p class="text-muted mb-2"><?php echo e($user->email ?: __('messages.no_email')); ?></p>
                <p class="text-muted mb-3"><?php echo e($user->phone); ?></p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-primary"><?php echo e($user->orders()->count()); ?></h6>
                        <small class="text-muted"><?php echo e(__('messages.total_orders')); ?></small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success">$<?php echo e(number_format($user->orders()->sum('total_prices') ?? 0, 2)); ?></h6>
                        <small class="text-muted"><?php echo e(__('messages.total_spent')); ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i><?php echo e(__('messages.account_information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <strong><?php echo e(__('messages.member_since')); ?>:</strong>
                    <span class="text-muted"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                </div>
                
                <div class="info-item mb-3">
                    <strong><?php echo e(__('messages.account_status')); ?>:</strong>
                    <span class="badge bg-<?php echo e($user->activate == 1 ? 'success' : 'danger'); ?>">
                        <?php echo e($user->activate == 1 ? __('messages.active') : __('messages.inactive')); ?>

                    </span>
                </div>
                
                <div class="info-item mb-3">
                    <strong><?php echo e(__('messages.last_login')); ?>:</strong>
                    <span class="text-muted"><?php echo e($user->updated_at->diffForHumans()); ?></span>
                </div>
                
                <div class="info-item mb-3">
                    <strong><?php echo e(__('messages.country')); ?>:</strong>
                    <span class="text-muted"><?php echo e($user->country->name ?? __('messages.not_specified')); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Warehouse Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i><?php echo e(__('messages.my_warehouse')); ?>

                </h5>
            </div>
       
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i><?php echo e(__('messages.quick_actions')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-cart me-2"></i><?php echo e(__('messages.view_my_orders')); ?>

                    </a>
                    
                    <a href="<?php echo e(route('user.debts')); ?>" class="btn btn-outline-warning">
                        <i class="fas fa-credit-card me-2"></i><?php echo e(__('messages.manage_my_debts')); ?>

                    </a>
                    
                    <a href="<?php echo e(route('user.analytics')); ?>" class="btn btn-outline-info">
                        <i class="fas fa-chart-line me-2"></i><?php echo e(__('messages.view_analytics')); ?>

                    </a>
                 
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/profile.blade.php ENDPATH**/ ?>