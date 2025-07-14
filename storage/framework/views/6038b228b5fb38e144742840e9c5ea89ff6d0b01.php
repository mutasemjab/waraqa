

<?php $__env->startSection('title', __('messages.dashboard')); ?>
<?php $__env->startSection('page-title', __('messages.dashboard')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.welcome_back')); ?>, <?php echo e(auth()->user()->name); ?>!</h1>
    <p class="page-subtitle"><?php echo e(__('messages.user_dashboard_subtitle')); ?></p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['total_orders']); ?></h3>
            <p><?php echo e(__('messages.total_orders')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['pending_orders']); ?></h3>
            <p><?php echo e(__('messages.pending_orders')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['completed_orders']); ?></h3>
            <p><?php echo e(__('messages.completed_orders')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-content">
            <h3>$<?php echo e(number_format($stats['total_debt'], 2)); ?></h3>
            <p><?php echo e(__('messages.total_debt')); ?></p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i><?php echo e(__('messages.quick_actions')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-list-alt d-block mb-2" style="font-size: 1.5rem;"></i>
                            <?php echo e(__('messages.view_orders')); ?>

                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo e(route('user.debts')); ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-money-bill-wave d-block mb-2" style="font-size: 1.5rem;"></i>
                            <?php echo e(__('messages.manage_debts')); ?>

                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo e(route('user.profile')); ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-user-edit d-block mb-2" style="font-size: 1.5rem;"></i>
                            <?php echo e(__('messages.edit_profile')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i><?php echo e(__('messages.recent_orders')); ?>

                </h5>
                <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-sm btn-outline-primary">
                    <?php echo e(__('messages.view_all')); ?>

                </a>
            </div>
            <div class="card-body">
                <?php if(auth()->user()->orders && auth()->user()->orders->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.order_number')); ?></th>
                                    <th><?php echo e(__('messages.date')); ?></th>
                                    <th><?php echo e(__('messages.total')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = auth()->user()->orders()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($order->number); ?></td>
                                        <td><?php echo e($order->date); ?></td>
                                        <td>$<?php echo e(number_format($order->total_prices, 2)); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($order->status == 1 ? 'success' : 'warning'); ?>">
                                                <?php echo e($order->status == 1 ? __('messages.completed') : __('messages.pending')); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted"><?php echo e(__('messages.no_orders_yet')); ?></h6>
                        <p class="text-muted"><?php echo e(__('messages.start_shopping_message')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i><?php echo e(__('messages.profile_summary')); ?>

                </h5>
            </div>
            <div class="card-body text-center">
                <img src="<?php echo e(auth()->user()->photo_url); ?>" alt="Profile" class="rounded-circle mb-3" width="80" height="80">
                <h6><?php echo e(auth()->user()->name); ?></h6>
                <p class="text-muted small"><?php echo e(auth()->user()->email); ?></p>
                <p class="text-muted small"><?php echo e(auth()->user()->phone); ?></p>
                <a href="<?php echo e(route('user.profile')); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i><?php echo e(__('messages.edit_profile')); ?>

                </a>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i><?php echo e(__('messages.account_status')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><?php echo e(__('messages.account_status')); ?></span>
                    <span class="badge bg-success"><?php echo e(__('messages.active')); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span><?php echo e(__('messages.member_since')); ?></span>
                    <span class="text-muted"><?php echo e(auth()->user()->created_at->format('M Y')); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><?php echo e(__('messages.total_spent')); ?></span>
                    <span class="fw-bold">$<?php echo e(number_format(auth()->user()->orders()->sum('total_prices') ?? 0, 2)); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i><?php echo e(__('messages.quick_stats')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo e(auth()->user()->orders()->where('payment_status', 1)->count()); ?></h4>
                            <small class="text-muted"><?php echo e(__('messages.paid_orders')); ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning"><?php echo e(auth()->user()->userDepts()->where('status', 1)->count()); ?></h4>
                        <small class="text-muted"><?php echo e(__('messages.active_debts')); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications -->
<?php if(auth()->user()->userDepts()->where('status', 1)->exists()): ?>
    <div class="card border-warning">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(__('messages.payment_reminders')); ?>

            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3"><?php echo e(__('messages.you_have_outstanding_debts')); ?></p>
            <div class="row">
                <?php $__currentLoopData = auth()->user()->userDepts()->where('status', 1)->take(3)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-2">
                        <div class="bg-light p-3 rounded">
                            <strong><?php echo e(__('messages.order')); ?> #<?php echo e($debt->order->number); ?></strong><br>
                            <span class="text-danger">$<?php echo e(number_format($debt->remaining_amount, 2)); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a href="<?php echo e(route('user.debts')); ?>" class="btn btn-warning mt-3">
                <i class="fas fa-credit-card me-1"></i><?php echo e(__('messages.view_all_debts')); ?>

            </a>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Add any dashboard-specific JavaScript here
    console.log('User Dashboard Loaded');
    
    // Auto-refresh stats every 30 seconds (optional)
    // setInterval(function() {
    //     // Refresh dashboard stats via AJAX
    // }, 30000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/dashboard.blade.php ENDPATH**/ ?>