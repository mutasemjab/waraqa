

<?php $__env->startSection('title', __('messages.analytics')); ?>
<?php $__env->startSection('page-title', __('messages.analytics')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.shopping_analytics')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.analyze_your_shopping_patterns')); ?></p>
</div>

<!-- Quick Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($topProducts->sum('total_quantity')); ?></h3>
            <p><?php echo e(__('messages.total_items_purchased')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <h3>$<?php echo e(number_format($topProducts->sum('total_spent'), 2)); ?></h3>
            <p><?php echo e(__('messages.total_amount_spent')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <h3>$<?php echo e(number_format($topProducts->avg('total_spent'), 2)); ?></h3>
            <p><?php echo e(__('messages.average_order_value')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e(number_format($paymentStats['fully_paid'] / max(array_sum($paymentStats), 1) * 100, 1)); ?>%</h3>
            <p><?php echo e(__('messages.payment_completion_rate')); ?></p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Spending Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area me-2"></i><?php echo e(__('messages.monthly_spending_trend')); ?>

                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlySpendingChart" height="300"></canvas>
            </div>
        </div>
        
        <!-- Category Spending -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i><?php echo e(__('messages.spending_by_category')); ?>

                </h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Products & Payment Stats -->
    <div class="col-lg-4">
        <!-- Payment Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i><?php echo e(__('messages.payment_patterns')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?php echo e(__('messages.fully_paid')); ?></span>
                        <span><?php echo e($paymentStats['fully_paid']); ?></span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-warning" style="width: <?php echo e(array_sum($paymentStats) > 0 ? ($paymentStats['partially_paid'] / array_sum($paymentStats)) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?php echo e(__('messages.unpaid')); ?></span>
                        <span><?php echo e($paymentStats['unpaid']); ?></span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-danger" style="width: <?php echo e(array_sum($paymentStats) > 0 ? ($paymentStats['unpaid'] / array_sum($paymentStats)) * 100 : 0); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i><?php echo e(__('messages.most_purchased_products')); ?>

                </h5>
            </div>
            <div class="card-body">
                <?php if($topProducts->count() > 0): ?>
                    <?php $__currentLoopData = $topProducts->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="product-image-small me-3">
                                <?php if($product->photo): ?>
                                    <img src="<?php echo e(asset('storage/' . $product->photo)); ?>" alt="<?php echo e($product->name_ar); ?>" class="rounded" width="40" height="40">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo e($product->name_ar); ?></h6>
                                <small class="text-muted">
                                    <?php echo e($product->total_quantity); ?> <?php echo e(__('messages.items')); ?> • 
                                    $<?php echo e(number_format($product->total_spent, 2)); ?>

                                </small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-box text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2"><?php echo e(__('messages.no_products_purchased')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Shopping Insights -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i><?php echo e(__('messages.shopping_insights')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h6 class="mb-1"><?php echo e(__('messages.favorite_category')); ?></h6>
                            <small class="text-muted">
                                <?php if($categorySpending->count() > 0): ?>
                                    <?php echo e($categorySpending->first()->name_ar); ?>

                                <?php else: ?>
                                    <?php echo e(__('messages.no_data')); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-success text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1"><?php echo e(__('messages.shopping_frequency')); ?></h6>
                            <small class="text-muted">
                                <?php if($monthlySpending->count() > 0): ?>
                                    <?php echo e(number_format($monthlySpending->count() / 12, 1)); ?> <?php echo e(__('messages.orders_per_month')); ?>

                                <?php else: ?>
                                    <?php echo e(__('messages.no_data')); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-warning text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <h6 class="mb-1"><?php echo e(__('messages.savings_potential')); ?></h6>
                            <small class="text-muted">
                                <?php
                                    $avgSpending = $monthlySpending->avg('total');
                                    $lastMonthSpending = $monthlySpending->first()->total ?? 0;
                                    $savings = $avgSpending > $lastMonthSpending ? $avgSpending - $lastMonthSpending : 0;
                                ?>
                                <?php if($savings > 0): ?>
                                    $<?php echo e(number_format($savings, 2)); ?> <?php echo e(__('messages.saved_last_month')); ?>

                                <?php else: ?>
                                    <?php echo e(__('messages.maintain_budget')); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export & Report Actions -->
<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1"><?php echo e(__('messages.generate_detailed_report')); ?></h5>
                <p class="text-muted mb-0"><?php echo e(__('messages.export_comprehensive_analytics')); ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo e(route('user.report')); ?>" class="btn btn-primary">
                    <i class="fas fa-file-export me-1"></i><?php echo e(__('messages.generate_report')); ?>

                </a>
                <button class="btn btn-outline-success" onclick="exportToCSV()">
                    <i class="fas fa-file-csv me-1"></i><?php echo e(__('messages.export_csv')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Monthly Spending Chart
const monthlyCtx = document.getElementById('monthlySpendingChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [
            <?php $__currentLoopData = $monthlySpending->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e(Carbon\Carbon::create($spending->year, $spending->month)->format("M Y")); ?>',
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            label: '<?php echo e(__("messages.monthly_spending")); ?>',
            data: [
                <?php $__currentLoopData = $monthlySpending->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($spending->total); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            borderColor: 'rgb(79, 70, 229)',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'success" style="width: <?php echo e(array_sum($paymentStats) > 0 ? ($paymentStats['fully_paid'] / array_sum($paymentStats)) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?php echo e(__('messages.partially_paid')); ?></span>
                        <span><?php echo e($paymentStats['partially_paid']); ?></span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg- + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Spending Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php $__currentLoopData = $categorySpending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo e($category->name_ar); ?>',
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            data: [
                <?php $__currentLoopData = $categorySpending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($category->total_spent); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            backgroundColor: [
                '#4f46e5',
                '#06b6d4',
                '#10b981',
                '#f59e0b',
                '#ef4444',
                '#8b5cf6',
                '#ec4899',
                '#6b7280'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Export to CSV function
function exportToCSV() {
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "<?php echo e(__('messages.analytics_export')); ?>\n\n";
    
    // Monthly spending data
    csvContent += "<?php echo e(__('messages.monthly_spending')); ?>\n";
    csvContent += "<?php echo e(__('messages.month')); ?>,<?php echo e(__('messages.amount')); ?>\n";
    <?php $__currentLoopData = $monthlySpending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        csvContent += "<?php echo e(Carbon\Carbon::create($spending->year, $spending->month)->format('M Y')); ?>,<?php echo e($spending->total); ?>\n";
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    csvContent += "\n<?php echo e(__('messages.top_products')); ?>\n";
    csvContent += "<?php echo e(__('messages.product')); ?>,<?php echo e(__('messages.quantity')); ?>,<?php echo e(__('messages.total_spent')); ?>\n";
    <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        csvContent += "<?php echo e($product->name_ar); ?>,<?php echo e($product->total_quantity); ?>,<?php echo e($product->total_spent); ?>\n";
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "shopping_analytics_<?php echo e(auth()->user()->name); ?>_<?php echo e(date('Y-m-d')); ?>.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/analytics.blade.php ENDPATH**/ ?>