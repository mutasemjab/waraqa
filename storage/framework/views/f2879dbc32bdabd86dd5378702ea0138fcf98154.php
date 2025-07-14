

<?php $__env->startSection('title', __('messages.order_details')); ?>
<?php $__env->startSection('page-title', __('messages.order_details')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><?php echo e(__('messages.order_details')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('messages.order_number')); ?>: <strong><?php echo e($order->number); ?></strong></p>
        </div>
        <div>
            <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i><?php echo e(__('messages.back_to_orders')); ?>

            </a>
        </div>
    </div>
</div>

<!-- Order Summary -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i><?php echo e(__('messages.order_information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><?php echo e(__('messages.order_number')); ?>:</strong> <?php echo e($order->number); ?></p>
                        <p><strong><?php echo e(__('messages.order_date')); ?>:</strong> <?php echo e(Carbon\Carbon::parse($order->date)->format('Y-m-d H:i')); ?></p>
                        <p><strong><?php echo e(__('messages.total_items')); ?>:</strong> <?php echo e($order->orderProducts->sum('quantity')); ?></p>
                        <?php if($order->note): ?>
                            <p><strong><?php echo e(__('messages.order_note')); ?>:</strong> <?php echo e($order->note); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <p><strong><?php echo e(__('messages.order_status')); ?>:</strong> 
                            <?php if($order->status == 1): ?>
                                <span class="badge bg-success"><?php echo e(__('messages.completed')); ?></span>
                            <?php elseif($order->status == 2): ?>
                                <span class="badge bg-warning"><?php echo e(__('messages.cancelled')); ?></span>
                            <?php elseif($order->status == 6): ?>
                                <span class="badge bg-info"><?php echo e(__('messages.refund')); ?></span>
                            <?php endif; ?>
                        </p>
                        <p><strong><?php echo e(__('messages.payment_status')); ?>:</strong> 
                            <?php if($order->payment_status == 1): ?>
                                <span class="badge bg-success"><?php echo e(__('messages.paid')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning"><?php echo e(__('messages.unpaid')); ?></span>
                            <?php endif; ?>
                        </p>
                        <p><strong><?php echo e(__('messages.created_at')); ?>:</strong> <?php echo e($order->created_at->format('Y-m-d H:i')); ?></p>
                        <p><strong><?php echo e(__('messages.updated_at')); ?>:</strong> <?php echo e($order->updated_at->format('Y-m-d H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i><?php echo e(__('messages.order_summary')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo e(__('messages.subtotal')); ?>:</span>
                    <span>$<?php echo e(number_format($order->orderProducts->sum('total_price_after_tax'), 2)); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo e(__('messages.tax')); ?>:</span>
                    <span>$<?php echo e(number_format($order->orderProducts->sum(function($item) { return $item->total_price_after_tax - $item->total_price; }), 2)); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong><?php echo e(__('messages.total_amount')); ?>:</strong>
                    <strong class="text-primary">$<?php echo e(number_format($order->total_prices, 2)); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-success"><?php echo e(__('messages.paid_amount')); ?>:</span>
                    <span class="text-success">$<?php echo e(number_format($order->paid_amount, 2)); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="<?php echo e($order->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e(__('messages.remaining_amount')); ?>:</span>
                    <span class="<?php echo e($order->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>">
                        <?php if($order->remaining_amount > 0): ?>
                            $<?php echo e(number_format($order->remaining_amount, 2)); ?>

                        <?php else: ?>
                            <?php echo e(__('messages.fully_paid')); ?>

                        <?php endif; ?>
                    </span>
                </div>
                
                <?php if($order->remaining_amount > 0): ?>
                    <button class="btn btn-success w-100" onclick="showPaymentModal(<?php echo e($order->id); ?>, '<?php echo e($order->number); ?>', <?php echo e($order->remaining_amount); ?>)">
                        <i class="fas fa-credit-card me-1"></i><?php echo e(__('messages.make_payment')); ?>

                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Products -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i><?php echo e(__('messages.order_products')); ?>

            <span class="badge bg-primary ms-2"><?php echo e($order->orderProducts->count()); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?php echo e(__('messages.product')); ?></th>
                        <th><?php echo e(__('messages.unit_price')); ?></th>
                        <th><?php echo e(__('messages.quantity')); ?></th>
                        <th><?php echo e(__('messages.subtotal')); ?></th>
                        <th><?php echo e(__('messages.tax')); ?></th>
                        <th><?php echo e(__('messages.total')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->orderProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($orderProduct->product && $orderProduct->product->photo): ?>
                                        <img src="<?php echo e(asset('assets/admin/uploads/' . $orderProduct->product->photo)); ?>" 
                                             alt="<?php echo e($orderProduct->product->name_en); ?>" 
                                             class="me-3 rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="me-3 rounded bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?php echo e($orderProduct->product ? (app()->getLocale() == 'ar' ? $orderProduct->product->name_ar : $orderProduct->product->name_en) : __('messages.product_not_found')); ?></strong>
                                        <?php if($orderProduct->product && $orderProduct->product->code): ?>
                                            <br><small class="text-muted"><?php echo e(__('messages.code')); ?>: <?php echo e($orderProduct->product->code); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo e(number_format($orderProduct->unit_price, 2)); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo e($orderProduct->quantity); ?></span>
                            </td>
                            <td>$<?php echo e(number_format($orderProduct->total_price_before_tax, 2)); ?></td>
                            <td>$<?php echo e(number_format($orderProduct->tax_percentage, 2)); ?></td>
                            <td class="fw-bold">$<?php echo e(number_format($orderProduct->total_price_after_tax, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="3"><?php echo e(__('messages.total')); ?></th>
                        <th>$<?php echo e(number_format($order->orderProducts->sum('total_price'), 2)); ?></th>
                        <th>$<?php echo e(number_format($order->orderProducts->sum(function($item) { return $item->total_price_after_tax - $item->total_price; }), 2)); ?></th>
                        <th class="text-primary">$<?php echo e(number_format($order->orderProducts->sum('total_price_after_tax'), 2)); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Debt Information -->
<?php if($order->userDepts && $order->userDepts->count() > 0): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(__('messages.debt_information')); ?>

            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.debt_amount')); ?></th>
                            <th><?php echo e(__('messages.remaining_amount')); ?></th>
                            <th><?php echo e(__('messages.status')); ?></th>
                            <th><?php echo e(__('messages.created_at')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->userDepts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>$<?php echo e(number_format($debt->debt_amount, 2)); ?></td>
                                <td class="text-danger">$<?php echo e(number_format($debt->remaining_amount, 2)); ?></td>
                                <td>
                                    <?php if($debt->status == 1): ?>
                                        <span class="badge bg-warning"><?php echo e(__('messages.active')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo e(__('messages.paid')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($debt->created_at->format('Y-m-d H:i')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i><?php echo e(__('messages.back_to_orders')); ?>

                </a>
            </div>
            <div>
                <?php if($order->remaining_amount > 0): ?>
                    <button class="btn btn-success" onclick="showPaymentModal(<?php echo e($order->id); ?>, '<?php echo e($order->number); ?>', <?php echo e($order->remaining_amount); ?>)">
                        <i class="fas fa-credit-card me-1"></i><?php echo e(__('messages.make_payment')); ?>

                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

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

<?php $__env->startPush('styles'); ?>
<style>
@media print {
    .btn, .modal, .page-header .btn, .card .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .page-header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .table {
        border-collapse: collapse !important;
    }
    
    .table th, .table td {
        border: 1px solid #dee2e6 !important;
    }
}

.product-image {
    transition: transform 0.3s ease;
}

.product-image:hover {
    transform: scale(1.1);
}

.badge {
    font-size: 0.875em;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table-responsive {
    border-radius: 0.375rem;
}
</style>
<?php $__env->stopPush(); ?>

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

// Print functionality
function printOrder() {
    window.print();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/order-details.blade.php ENDPATH**/ ?>