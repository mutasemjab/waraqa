<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', __('messages.user_dashboard')); ?> - <?php echo e(config('app.name')); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --sidebar-width: 260px;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-header .logo {
            font-size: 1rem;
        }
        
        .nav-menu {
            padding: 20px 0;
        }
        
        .nav-item {
            margin: 5px 15px;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Top Navigation */
        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 25px;
            display: flex;
            justify-content: between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .navbar-left {
            display: flex;
            align-items: center;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--dark-color);
            margin-right: 15px;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--light-color);
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            background: var(--light-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid var(--primary-color);
        }
        
        .user-details h6 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--dark-color);
        }
        
        .user-details span {
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: none;
            padding: 10px 0;
            min-width: 200px;
            z-index: 1000;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            color: var(--dark-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Content Area */
        .content-area {
            padding: 25px;
        }
        
        .page-header {
            margin-bottom: 25px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .page-subtitle {
            color: #6b7280;
            margin-top: 5px;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 20px;
        }
        
        .stat-icon.primary { background: linear-gradient(135deg, var(--primary-color), #6366f1); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success-color), #059669); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning-color), #d97706); }
        .stat-icon.danger { background: linear-gradient(135deg, var(--danger-color), #dc2626); }
        
        .stat-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-color);
        }
        
        .stat-content p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                margin-right: 15px;
            }
            
            .stat-content h3 {
                font-size: 1.5rem;
            }
        }
        
        /* Mobile overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        @media (max-width: 768px) {
            .mobile-overlay.show {
                display: block;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Notifications */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-user-circle me-2"></i>
                <span><?php echo e(__('messages.user_panel')); ?></span>
            </div>
        </div>
        
        <nav class="nav-menu">
            <div class="nav-item">
                <a href="<?php echo e(route('user.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('user.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i>
                    <span><?php echo e(__('messages.dashboard')); ?></span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo e(route('user.orders')); ?>" class="nav-link <?php echo e(request()->routeIs('user.orders*') ? 'active' : ''); ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span><?php echo e(__('messages.my_orders')); ?></span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo e(route('user.debts')); ?>" class="nav-link <?php echo e(request()->routeIs('user.debts*') ? 'active' : ''); ?>">
                    <i class="fas fa-credit-card"></i>
                    <span><?php echo e(__('messages.my_debts')); ?></span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo e(route('user.profile')); ?>" class="nav-link <?php echo e(request()->routeIs('user.profile*') ? 'active' : ''); ?>">
                    <i class="fas fa-user-cog"></i>
                    <span><?php echo e(__('messages.profile')); ?></span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo e(route('user.sales.index')); ?>" class="nav-link <?php echo e(request()->routeIs('user.sales.index*') ? 'active' : ''); ?>">
                    <i class="fas fa-warehouse"></i>
                    <span><?php echo e(__('messages.my_warehouse')); ?></span>
                </a>
            </div>
            
          
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-headset"></i>
                    <span><?php echo e(__('messages.support')); ?></span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <div class="top-navbar">
            <div class="navbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0"><?php echo $__env->yieldContent('page-title', __('messages.dashboard')); ?></h5>
            </div>
            
            <div class="navbar-right">
                <div class="user-dropdown">
                    <div class="user-info" data-bs-toggle="dropdown">
                        <img src="<?php echo e(auth()->user()->photo_url); ?>" alt="User" class="user-avatar">
                        <div class="user-details">
                            <h6><?php echo e(auth()->user()->name); ?></h6>
                            <span><?php echo e(__('messages.user')); ?></span>
                        </div>
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    
                    <div class="dropdown-menu">
                        <a href="<?php echo e(route('user.profile')); ?>" class="dropdown-item">
                            <i class="fas fa-user"></i><?php echo e(__('messages.profile')); ?>

                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i><?php echo e(__('messages.settings')); ?>

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i><?php echo e(__('messages.logout')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area fade-in">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    
    <!-- Logout Form -->
    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                // Mobile behavior
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');
            } else {
                // Desktop behavior
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });
        
        // Close sidebar when clicking overlay on mobile
        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
            }
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Add loading states to buttons
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                }
            });
        });
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\nasher\resources\views/layouts/user.blade.php ENDPATH**/ ?>