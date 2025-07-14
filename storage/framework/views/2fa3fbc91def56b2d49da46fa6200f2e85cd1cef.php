<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__('messages.login')); ?> - <?php echo e(config('app.name')); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .user-type-selector {
            display: flex;
            margin-bottom: 25px;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 8px;
        }
        
        .user-type-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .user-type-option:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .user-type-option.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .user-type-option i {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 15px;
            height: auto;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-floating > label {
            padding: 12px 15px;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
     
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
        }
        
        .loading-spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .btn-login.loading .btn-text {
            opacity: 0;
        }
        
        .btn-login.loading .loading-spinner {
            display: block;
        }
        
        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        
        .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        @media (max-width: 768px) {
            .login-container {
                padding: 10px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .user-type-option {
                padding: 10px 5px;
            }
            
            .user-type-option span {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-circle"></i>
                <h4 class="mb-0"><?php echo e(__('messages.welcome_back')); ?></h4>
                <p class="mb-0 opacity-75"><?php echo e(__('messages.login_to_continue')); ?></p>
            </div>
            
            <div class="login-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <form id="loginForm" method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <!-- User Type Selector -->
                    <div class="user-type-selector">
                        <div class="user-type-option active" data-type="user">
                            <i class="fas fa-user"></i>
                            <span><?php echo e(__('messages.user')); ?></span>
                        </div>
                        <div class="user-type-option" data-type="provider">
                            <i class="fas fa-store"></i>
                            <span><?php echo e(__('messages.provider')); ?></span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="user_type" id="user_type" value="user">
                    
                    <!-- Phone Input -->
                    <div class="form-floating">
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
                               placeholder="<?php echo e(__('messages.phone_number')); ?>"
                               value="<?php echo e(old('phone')); ?>" 
                               required 
                               autocomplete="phone">
                        <label for="phone">
                            <i class="fas fa-phone me-2"></i><?php echo e(__('messages.phone_number')); ?>

                        </label>
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
                    
                    <!-- Password Input -->
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               id="password" 
                               name="password" 
                               placeholder="<?php echo e(__('messages.password')); ?>"
                               required 
                               autocomplete="current-password">
                        <label for="password">
                            <i class="fas fa-lock me-2"></i><?php echo e(__('messages.password')); ?>

                        </label>
                        <?php $__errorArgs = ['password'];
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
                    
                    <!-- Remember Me -->
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            <?php echo e(__('messages.remember_me')); ?>

                        </label>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="btn btn-login">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i><?php echo e(__('messages.login')); ?>

                        </span>
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </form>
                
            
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // User type selector
        document.querySelectorAll('.user-type-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.user-type-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Update hidden input
                document.getElementById('user_type').value = this.dataset.type;
            });
        });
        
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.querySelector('.btn-login');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\nasher\resources\views/auth/login.blade.php ENDPATH**/ ?>