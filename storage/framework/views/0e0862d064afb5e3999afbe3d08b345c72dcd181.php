

<?php $__env->startSection('title', __('messages.my_orders')); ?>
<?php $__env->startSection('page-title', __('messages.my_orders')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.my_orders')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.view_and_manage_your_orders')); ?></p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('user.orders')); ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label"><?php echo e(__('messages.order_status')); ?></label>
                <select name="status" class="form-select">
                    <option value=""><?php echo e(__('messages.all_statuses')); ?></option>
                    <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.completed')); ?></option>
                    <option value="2" <?php echo e(request('status') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.cancelled')); ?></option>
                    <option value="6" <?php echo e(request('status') == '6' ? 'selected' : ''); ?>><?php echo e(__('messages.refund')); ?></option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><?php echo e(__('messages.payment_status')); ?></label>
                <select name="payment_status" class="form-select">
                    <option value=""><?php echo e(__('messages.all_payments')); ?></option>
                    <option value="1" <?php echo e(request('payment_status') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.paid')); ?></option>
                    <option value="2" <?php echo e(request('payment_status') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.unpaid')); ?></option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label"><?php echo e(__('messages.date_from')); ?></label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label"><?php echo e(__('messages.date_to')); ?></label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label"><?php echo e(__('messages.search')); ?></label>
                <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('messages.order_number')); ?>" value="<?php echo e(request('search')); ?>">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i><?php echo e(__('messages.filter')); ?>

                </button>
                <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-secondary">
                    <i class="fas fa-refresh me-1"></i><?php echo e(__('messages.clear')); ?>

                </a>
                <a href="<?php echo e(route('user.analytics')); ?>" class="btn btn-info">
                    <i class="fas fa-chart-line me-1"></i><?php echo e(__('messages.view_analytics')); ?>

                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i><?php echo e(__('messages.orders_list')); ?>

            <span class="badge bg-primary ms-2"><?php echo e($orders->total()); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if($orders->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.order_number')); ?></th>
                            <th><?php echo e(__('messages.date')); ?></th>
                            <th><?php echo e(__('messages.items')); ?></th>
                            <th><?php echo e(__('messages.total_amount')); ?></th>
                            <th><?php echo e(__('messages.paid_amount')); ?></th>
                            <th><?php echo e(__('messages.remaining')); ?></th>
                            <th><?php echo e(__('messages.order_status')); ?></th>
                            <th><?php echo e(__('messages.payment_status')); ?></th>
                            <th><?php echo e(__('messages.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($order->number); ?></strong>
                                    <?php if($order->note): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($order->note, 30)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(Carbon\Carbon::parse($order->date)->format('Y-m-d H:i')); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($order->orderProducts->count()); ?> <?php echo e(__('messages.items')); ?></span>
                                </td>
                                <td class="fw-bold">$<?php echo e(number_format($order->total_prices, 2)); ?></td>
                                <td class="text-success">$<?php echo e(number_format($order->paid_amount, 2)); ?></td>
                                <td>
                                    <?php if($order->remaining_amount > 0): ?>
                                        <span class="text-danger fw-bold">$<?php echo e(number_format($order->remaining_amount, 2)); ?></span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo e(__('messages.fully_paid')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->status == 1): ?>
                                        <span class="badge bg-success"><?php echo e(__('messages.completed')); ?></span>
                                    <?php elseif($order->status == 2): ?>
                                        <span class="badge bg-warning"><?php echo e(__('messages.cancelled')); ?></span>
                                    <?php elseif($order->status == 6): ?>
                                        <span class="badge bg-info"><?php echo e(__('messages.refund')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->payment_status == 1): ?>
                                        <span class="badge bg-success"><?php echo e(__('messages.paid')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?php echo e(__('messages.unpaid')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('user.orders.show', $order->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo e(__('messages.view_details')); ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if($order->remaining_amount > 0): ?>
                                            <button class="btn btn-sm btn-outline-success" title="<?php echo e(__('messages.make_payment')); ?>" onclick="showPaymentModal(<?php echo e($order->id); ?>, '<?php echo e($order->number); ?>', <?php echo e($order->remaining_amount); ?>)">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($orders->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted"><?php echo e(__('messages.no_orders_found')); ?></h4>
                <p class="text-muted"><?php echo e(__('messages.no_orders_match_criteria')); ?></p>
                <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-primary">
                    <i class="fas fa-refresh me-1"></i><?php echo e(__('messages.clear_filters')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Summary Cards -->
<?php if($orders->count() > 0): ?>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>$<?php echo e(number_format($orders->sum('total_prices'), 2)); ?></h3>
                    <p class="mb-0"><?php echo e(__('messages.total_value')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>$<?php echo e(number_format($orders->sum('paid_amount'), 2)); ?></h3>
                    <p class="mb-0"><?php echo e(__('messages.total_paid')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>$<?php echo e(number_format($orders->sum('remaining_amount'), 2)); ?></h3>
                    <p class="mb-0"><?php echo e(__('messages.total_remaining')); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3><?php echo e($orders->sum(function($order) { return $order->orderProducts->sum('quantity'); })); ?></h3>
                    <p class="mb-0"><?php echo e(__('messages.total_items')); ?></p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('messages.payment_request')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo e(__('messages.payment_request_note')); ?>

                </div>
                <form id="paymentRequestForm">
                    <input type="hidden" id="order_id" name="order_id">
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.order_number')); ?></label>
                        <input type="text" id="order_number" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.remaining_amount')); ?></label>
                        <input type="text" id="remaining_amount" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.payment_method')); ?></label>
                        <select name="payment_method" class="form-select" required>
                            <option value=""><?php echo e(__('messages.select_payment_method')); ?></option>
                            <option value="cash"><?php echo e(__('messages.cash')); ?></option>
                            <option value="bank_transfer"><?php echo e(__('messages.bank_transfer')); ?></option>
                            <option value="credit_card"><?php echo e(__('messages.credit_card')); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.notes')); ?></label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="<?php echo e(__('messages.payment_notes_placeholder')); ?>"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('messages.cancel')); ?></button>
                <button type="button" class="btn btn-success" onclick="submitPaymentRequest()">
                    <i class="fas fa-paper-plane me-1"></i><?php echo e(__('messages.send_request')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function showPaymentModal(orderId, orderNumber, remainingAmount) {
    document.getElementById('order_id').value = orderId;
    document.getElementById('order_number').value = orderNumber;
    document.getElementById('remaining_amount').value = '$' + remainingAmount.toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function submitPaymentRequest() {
    // Here you would normally send an AJAX request to submit the payment request
    // For now, we'll just show a success message
    alert('<?php echo e(__("messages.payment_request_sent")); ?>');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    modal.hide();
    
    // Reset form
    document.getElementById('paymentRequestForm').reset();
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
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/orders.blade.php ENDPATH**/ ?>