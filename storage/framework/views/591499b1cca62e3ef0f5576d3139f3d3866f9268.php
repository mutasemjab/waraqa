

<?php $__env->startSection('title', __('messages.my_debts')); ?>
<?php $__env->startSection('page-title', __('messages.my_debts')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.my_debts')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.manage_your_outstanding_debts')); ?></p>
</div>

<!-- Debt Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3>$<?php echo e(number_format($totalDebt, 2)); ?></h3>
                <p class="mb-0"><?php echo e(__('messages.total_outstanding_debt')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3><?php echo e($debts->where('status', 1)->count()); ?></h3>
                <p class="mb-0"><?php echo e(__('messages.active_debts')); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3><?php echo e($debts->where('status', 2)->count()); ?></h3>
                <p class="mb-0"><?php echo e(__('messages.paid_debts')); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('user.debts')); ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><?php echo e(__('messages.status')); ?></label>
                <select name="status" class="form-select">
                    <option value=""><?php echo e(__('messages.all_statuses')); ?></option>
                    <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.active')); ?></option>
                    <option value="2" <?php echo e(request('status') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.paid')); ?></option>
                </select>
            </div>
            
            <div class="col-md-8">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i><?php echo e(__('messages.filter')); ?>

                    </button>
                    <a href="<?php echo e(route('user.debts')); ?>" class="btn btn-secondary">
                        <i class="fas fa-refresh me-1"></i><?php echo e(__('messages.clear')); ?>

                    </a>
                    <?php if($totalDebt > 0): ?>
                        <button type="button" class="btn btn-success" onclick="requestFullPayment()">
                            <i class="fas fa-credit-card me-1"></i><?php echo e(__('messages.pay_all_debts')); ?>

                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Debts List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-credit-card me-2"></i><?php echo e(__('messages.debts_list')); ?>

            <span class="badge bg-primary ms-2"><?php echo e($debts->total()); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if($debts->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.debt_id')); ?></th>
                            <th><?php echo e(__('messages.related_order')); ?></th>
                            <th><?php echo e(__('messages.total_amount')); ?></th>
                            <th><?php echo e(__('messages.paid_amount')); ?></th>
                            <th><?php echo e(__('messages.remaining_amount')); ?></th>
                            <th><?php echo e(__('messages.status')); ?></th>
                            <th><?php echo e(__('messages.created_date')); ?></th>
                            <th><?php echo e(__('messages.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($debt->status == 1 ? 'table-warning' : 'table-success'); ?>">
                                <td>
                                    <strong>#<?php echo e($debt->id); ?></strong>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('user.orders.show', $debt->order->id)); ?>" class="text-decoration-none">
                                        <strong><?php echo e($debt->order->number); ?></strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo e(Carbon\Carbon::parse($debt->order->date)->format('M d, Y')); ?>

                                    </small>
                                </td>
                                <td class="fw-bold">$<?php echo e(number_format($debt->total_amount, 2)); ?></td>
                                <td class="text-success">$<?php echo e(number_format($debt->paid_amount, 2)); ?></td>
                                <td>
                                    <?php if($debt->remaining_amount > 0): ?>
                                        <span class="text-danger fw-bold">$<?php echo e(number_format($debt->remaining_amount, 2)); ?></span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><?php echo e(__('messages.fully_paid')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($debt->status == 1): ?>
                                        <span class="badge bg-warning"><?php echo e(__('messages.active')); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo e(__('messages.paid')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($debt->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('user.orders.show', $debt->order->id)); ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="<?php echo e(__('messages.view_order')); ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if($debt->status == 1 && $debt->remaining_amount > 0): ?>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    title="<?php echo e(__('messages.request_payment')); ?>"
                                                    onclick="showPaymentModal(<?php echo e($debt->id); ?>, '<?php echo e($debt->order->number); ?>', <?php echo e($debt->remaining_amount); ?>)">
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
                <?php echo e($debts->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-success"><?php echo e(__('messages.no_debts_found')); ?></h4>
                <p class="text-muted"><?php echo e(__('messages.all_payments_completed')); ?></p>
                <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i><?php echo e(__('messages.view_orders')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Progress -->
<?php if($debts->count() > 0): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i><?php echo e(__('messages.payment_progress')); ?>

            </h5>
        </div>
        <div class="card-body">
            <?php
                $totalDebtAmount = $debts->sum('total_amount');
                $totalPaidAmount = $debts->sum('paid_amount');
                $progressPercentage = $totalDebtAmount > 0 ? ($totalPaidAmount / $totalDebtAmount) * 100 : 0;
            ?>
            
            <div class="row">
                <div class="col-md-8">
                    <h6><?php echo e(__('messages.overall_payment_progress')); ?></h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             style="width: <?php echo e($progressPercentage); ?>%"
                             aria-valuenow="<?php echo e($progressPercentage); ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?php echo e(number_format($progressPercentage, 1)); ?>%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted"><?php echo e(__('messages.paid')); ?>: $<?php echo e(number_format($totalPaidAmount, 2)); ?></small>
                        <small class="text-muted"><?php echo e(__('messages.total')); ?>: $<?php echo e(number_format($totalDebtAmount, 2)); ?></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-danger">$<?php echo e(number_format($totalDebt, 2)); ?></h4>
                        <p class="text-muted"><?php echo e(__('messages.remaining_to_pay')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Tips -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-lightbulb me-2"></i><?php echo e(__('messages.payment_tips')); ?>

            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-check text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2"><?php echo e(__('messages.pay_on_time')); ?></h6>
                        <p class="text-muted small"><?php echo e(__('messages.pay_on_time_description')); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-piggy-bank text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2"><?php echo e(__('messages.partial_payments')); ?></h6>
                        <p class="text-muted small"><?php echo e(__('messages.partial_payments_description')); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-handshake text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2"><?php echo e(__('messages.contact_support')); ?></h6>
                        <p class="text-muted small"><?php echo e(__('messages.contact_support_description')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Payment Request Modal -->
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
                    <?php echo e(__('messages.payment_request_info')); ?>

                </div>
                
                <form id="paymentRequestForm">
                    <input type="hidden" id="debt_id" name="debt_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.order_number')); ?></label>
                        <input type="text" id="order_number" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.remaining_amount')); ?></label>
                        <input type="text" id="remaining_amount" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.payment_amount')); ?> <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="payment_amount" 
                               id="payment_amount"
                               class="form-control" 
                               step="0.01" 
                               min="0.01"
                               required
                               placeholder="<?php echo e(__('messages.enter_amount_to_pay')); ?>">
                        <div class="form-text"><?php echo e(__('messages.enter_full_amount_or_partial')); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.payment_method')); ?></label>
                        <select name="payment_method" class="form-select" required>
                            <option value=""><?php echo e(__('messages.select_payment_method')); ?></option>
                            <option value="cash"><?php echo e(__('messages.cash')); ?></option>
                            <option value="bank_transfer"><?php echo e(__('messages.bank_transfer')); ?></option>
                            <option value="credit_card"><?php echo e(__('messages.credit_card')); ?></option>
                            <option value="digital_wallet"><?php echo e(__('messages.digital_wallet')); ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.notes')); ?></label>
                        <textarea name="notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="<?php echo e(__('messages.payment_notes_placeholder')); ?>"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?php echo e(__('messages.cancel')); ?>

                </button>
                <button type="button" class="btn btn-success" onclick="submitPaymentRequest()">
                    <i class="fas fa-paper-plane me-1"></i><?php echo e(__('messages.send_request')); ?>

                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pay All Debts Modal -->
<div class="modal fade" id="payAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('messages.pay_all_debts')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo e(__('messages.pay_all_debts_confirmation')); ?>

                </div>
                
                <div class="text-center">
                    <h3 class="text-danger">$<?php echo e(number_format($totalDebt, 2)); ?></h3>
                    <p class="text-muted"><?php echo e(__('messages.total_amount_to_pay')); ?></p>
                </div>
                
                <form id="payAllForm">
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.payment_method')); ?></label>
                        <select name="payment_method" class="form-select" required>
                            <option value=""><?php echo e(__('messages.select_payment_method')); ?></option>
                            <option value="cash"><?php echo e(__('messages.cash')); ?></option>
                            <option value="bank_transfer"><?php echo e(__('messages.bank_transfer')); ?></option>
                            <option value="credit_card"><?php echo e(__('messages.credit_card')); ?></option>
                            <option value="digital_wallet"><?php echo e(__('messages.digital_wallet')); ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('messages.notes')); ?></label>
                        <textarea name="notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="<?php echo e(__('messages.full_payment_notes_placeholder')); ?>"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?php echo e(__('messages.cancel')); ?>

                </button>
                <button type="button" class="btn btn-success" onclick="submitPayAllRequest()">
                    <i class="fas fa-money-bill-wave me-1"></i><?php echo e(__('messages.request_full_payment')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentMaxAmount = 0;

function showPaymentModal(debtId, orderNumber, remainingAmount) {
    currentMaxAmount = remainingAmount;
    
    document.getElementById('debt_id').value = debtId;
    document.getElementById('order_number').value = orderNumber;
    document.getElementById('remaining_amount').value = '$' + remainingAmount.toFixed(2);
    document.getElementById('payment_amount').max = remainingAmount;
    document.getElementById('payment_amount').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function requestFullPayment() {
    const modal = new bootstrap.Modal(document.getElementById('payAllModal'));
    modal.show();
}

function submitPaymentRequest() {
    const form = document.getElementById('paymentRequestForm');
    const formData = new FormData(form);
    const paymentAmount = parseFloat(formData.get('payment_amount'));
    
    // Validate payment amount
    if (paymentAmount <= 0 || paymentAmount > currentMaxAmount) {
        alert('<?php echo e(__("messages.invalid_payment_amount")); ?>');
        return;
    }
    
    if (!formData.get('payment_method')) {
        alert('<?php echo e(__("messages.please_select_payment_method")); ?>');
        return;
    }
    
    // Here you would normally send an AJAX request to submit the payment request
    // For now, we'll just show a success message
    alert('<?php echo e(__("messages.payment_request_sent_successfully")); ?>');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    modal.hide();
    
    // Reset form
    form.reset();
}

function submitPayAllRequest() {
    const form = document.getElementById('payAllForm');
    const formData = new FormData(form);
    
    if (!formData.get('payment_method')) {
        alert('<?php echo e(__("messages.please_select_payment_method")); ?>');
        return;
    }
    
    if (confirm('<?php echo e(__("messages.confirm_pay_all_debts")); ?>')) {
        // Here you would normally send an AJAX request
        alert('<?php echo e(__("messages.full_payment_request_sent")); ?>');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('payAllModal'));
        modal.hide();
        
        // Reset form
        form.reset();
    }
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
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/debts.blade.php ENDPATH**/ ?>