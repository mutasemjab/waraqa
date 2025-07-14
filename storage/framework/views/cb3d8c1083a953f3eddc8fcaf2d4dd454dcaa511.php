

<?php $__env->startSection('title', __('messages.my_sales')); ?>
<?php $__env->startSection('page-title', __('messages.my_sales')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.my_sales')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.track_your_sales_and_inventory')); ?></p>
</div>

<!-- Sales Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['total_sales']); ?></h3>
            <p><?php echo e(__('messages.total_sales')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['total_items_sold']); ?></h3>
            <p><?php echo e(__('messages.total_items_sold')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['this_month_sales']); ?></h3>
            <p><?php echo e(__('messages.this_month_sales')); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-warehouse"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($stats['current_inventory']); ?></h3>
            <p><?php echo e(__('messages.current_inventory')); ?></p>
        </div>
    </div>
</div>

<!-- Actions and Filters -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('user.sales.index')); ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.date_from')); ?></label>
                        <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo e(__('messages.date_to')); ?></label>
                        <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label"><?php echo e(__('messages.search')); ?></label>
                        <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('messages.search_by_voucher_number')); ?>" value="<?php echo e(request('search')); ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <a href="<?php echo e(route('user.sales.create')); ?>" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-plus me-2"></i><?php echo e(__('messages.record_new_sale')); ?>

                </a>
              
            </div>
        </div>
    </div>
</div>

<!-- Sales List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i><?php echo e(__('messages.sales_history')); ?>

            <span class="badge bg-primary ms-2"><?php echo e($sales->total()); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if($sales->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.voucher_number')); ?></th>
                            <th><?php echo e(__('messages.date')); ?></th>
                            <th><?php echo e(__('messages.customer_info')); ?></th>
                            <th><?php echo e(__('messages.products_count')); ?></th>
                            <th><?php echo e(__('messages.total_quantity')); ?></th>
                            <th><?php echo e(__('messages.notes')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo e($sale->number); ?></strong>
                                </td>
                                <td><?php echo e(Carbon\Carbon::parse($sale->date_note_voucher)->format('M d, Y')); ?></td>
                                <td>
                                    <?php
                                        $customerInfo = '';
                                        if (strpos($sale->note, 'Customer:') !== false) {
                                            $parts = explode('|', $sale->note);
                                            foreach ($parts as $part) {
                                                if (strpos($part, 'Customer:') !== false) {
                                                    $customerInfo = trim(str_replace('Customer:', '', $part));
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                    <?php if($customerInfo): ?>
                                        <strong><?php echo e($customerInfo); ?></strong>
                                        <?php if(strpos($sale->note, 'Phone:') !== false): ?>
                                            <?php
                                                $phone = '';
                                                foreach (explode('|', $sale->note) as $part) {
                                                    if (strpos($part, 'Phone:') !== false) {
                                                        $phone = trim(str_replace('Phone:', '', $part));
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <br><small class="text-muted"><?php echo e($phone); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo e(__('messages.customer_not_specified')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($sale->voucherProducts->count()); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo e($sale->voucherProducts->sum('quantity')); ?></span>
                                </td>
                                <td>
                                    <?php if($sale->note): ?>
                                        <?php
                                            $cleanNote = $sale->note;
                                            $cleanNote = preg_replace('/Customer:.*?\|/', '', $cleanNote);
                                            $cleanNote = preg_replace('/Phone:.*?\|/', '', $cleanNote);
                                            $cleanNote = trim($cleanNote, '| ');
                                        ?>
                                        <?php if($cleanNote): ?>
                                            <small class="text-muted"><?php echo e(Str::limit($cleanNote, 30)); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                               
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($sales->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-cash-register text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted"><?php echo e(__('messages.no_sales_recorded')); ?></h4>
                <p class="text-muted"><?php echo e(__('messages.start_recording_sales_message')); ?></p>
                <a href="<?php echo e(route('user.sales.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i><?php echo e(__('messages.record_first_sale')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Stats Summary -->
<?php if($sales->count() > 0): ?>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?php echo e($sales->sum(function($sale) { return $sale->voucherProducts->count(); })); ?></h4>
                    <p class="mb-0"><?php echo e(__('messages.total_product_types')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?php echo e($sales->sum(function($sale) { return $sale->voucherProducts->sum('quantity'); })); ?></h4>
                    <p class="mb-0"><?php echo e(__('messages.total_items_in_period')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?php echo e($sales->count()); ?></h4>
                    <p class="mb-0"><?php echo e(__('messages.total_transactions')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?php echo e(number_format($sales->sum(function($sale) { return $sale->voucherProducts->sum(function($vp) { return $vp->quantity * $vp->purchasing_price; }); }), 2)); ?></h4>
                    <p class="mb-0"><?php echo e(__('messages.total_sales_value')); ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('messages.sale_details')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> <?php echo e(__('messages.loading')); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function showSaleDetails(saleId) {
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    modal.show();
    
    // Simulate loading sale details (you would make an AJAX call here)
    setTimeout(() => {
        document.getElementById('quickViewContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo e(__('messages.sale_details_would_load_here')); ?>

            </div>
            <p><?php echo e(__('messages.click_view_details_for_full_info')); ?></p>
        `;
    }, 1000);
}

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/sales/index.blade.php ENDPATH**/ ?>