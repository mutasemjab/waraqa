<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.order_details')); ?> - <?php echo e($order->number); ?></h4>
                    <div>
                        <a href="<?php echo e(route('orders.edit', $order)); ?>" class="btn btn-warning"><?php echo e(__('messages.edit_order')); ?></a>
                   
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary"><?php echo e(__('messages.back_to_list')); ?></a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('messages.order_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.order_number')); ?>:</strong></td>
                                            <td><?php echo e($order->number); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.date')); ?>:</strong></td>
                                            <td><?php echo e($order->date->format('M d, Y H:i')); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.status')); ?>:</strong></td>
                                            <td>
                                                <?php if($order->status == 1): ?>
                                                    <span class="badge bg-success"><?php echo e(__('messages.done')); ?></span>
                                                <?php elseif($order->status == 2): ?>
                                                    <span class="badge bg-danger"><?php echo e(__('messages.canceled')); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-info"><?php echo e(__('messages.refund')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.payment_status')); ?>:</strong></td>
                                            <td>
                                                <?php if($order->payment_status == 1): ?>
                                                    <span class="badge bg-success"><?php echo e(__('messages.paid')); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><?php echo e(__('messages.unpaid')); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if($order->note): ?>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.note')); ?>:</strong></td>
                                            <td><?php echo e($order->note); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('messages.customer_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php if($order->user): ?>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.name')); ?>:</strong></td>
                                            <td><?php echo e($order->user->name); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.phone')); ?>:</strong></td>
                                            <td><?php echo e($order->user->phone); ?></td>
                                        </tr>
                                        <?php if($order->user->email): ?>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.email')); ?>:</strong></td>
                                            <td><?php echo e($order->user->email); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                    <?php else: ?>
                                    <p class="text-muted"><?php echo e(__('messages.no_customer_assigned')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Products -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><?php echo e(__('messages.order_products')); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('messages.product_name')); ?></th>
                                            <th><?php echo e(__('messages.unit_price')); ?></th>
                                            <th><?php echo e(__('messages.quantity')); ?></th>
                                            <th><?php echo e(__('messages.tax_percentage')); ?></th>
                                            <th><?php echo e(__('messages.tax_value')); ?></th>
                                            <th><?php echo e(__('messages.total_before_tax')); ?></th>
                                            <th><?php echo e(__('messages.total_after_tax')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $order->orderProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($orderProduct->product->name_en); ?></strong><br>
                                                <small class="text-muted"><?php echo e($orderProduct->product->name_ar); ?></small>
                                            </td>
                                            <td>$<?php echo e(number_format($orderProduct->unit_price, 2)); ?></td>
                                            <td><?php echo e($orderProduct->quantity); ?></td>
                                            <td><?php echo e($orderProduct->tax_percentage); ?>%</td>
                                            <td>$<?php echo e(number_format($orderProduct->tax_value, 2)); ?></td>
                                            <td>$<?php echo e(number_format($orderProduct->total_price_before_tax, 2)); ?></td>
                                            <td><strong>$<?php echo e(number_format($orderProduct->total_price_after_tax, 2)); ?></strong></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="4"><?php echo e(__('messages.totals')); ?></th>
                                            <th>$<?php echo e(number_format($order->total_taxes, 2)); ?></th>
                                            <th>$<?php echo e(number_format($order->total_prices - $order->total_taxes, 2)); ?></th>
                                            <th><strong>$<?php echo e(number_format($order->total_prices, 2)); ?></strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('messages.payment_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.total_amount')); ?>:</strong></td>
                                            <td>$<?php echo e(number_format($order->total_prices, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.paid_amount')); ?>:</strong></td>
                                            <td class="text-success">$<?php echo e(number_format($order->paid_amount, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.remaining_amount')); ?>:</strong></td>
                                            <td class="<?php echo e($order->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>">
                                                $<?php echo e(number_format($order->remaining_amount, 2)); ?>

                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Debt Information -->
                        <?php if($order->userDebt && $order->userDebt->status == 1): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><?php echo e(__('messages.debt_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.debt_status')); ?>:</strong></td>
                                            <td>
                                                <span class="badge bg-warning"><?php echo e(__('messages.active')); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.debt_created')); ?>:</strong></td>
                                            <td><?php echo e($order->userDebt->created_at->format('M d, Y H:i')); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.last_payment')); ?>:</strong></td>
                                            <td><?php echo e($order->userDebt->updated_at->format('M d, Y H:i')); ?></td>
                                        </tr>
                                    </table>
                                    
                                    <?php if($order->remaining_amount > 0): ?>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#paymentModal">
                                        <?php echo e(__('messages.receive_payment')); ?>

                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('messages.confirm_delete')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?php echo e(__('messages.are_you_sure')); ?></p>
                <p><strong><?php echo e(__('messages.order_number')); ?>:</strong> <?php echo e($order->number); ?></p>
                <p class="text-danger"><?php echo e(__('messages.this_action_cannot_be_undone')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('messages.cancel')); ?></button>
                <form action="<?php echo e(route('orders.destroy', $order)); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger"><?php echo e(__('messages.delete')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<?php if($order->userDebt && $order->remaining_amount > 0): ?>
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('messages.receive_payment')); ?> - <?php echo e($order->user->name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('receive-payment', $order->userDebt)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><?php echo e(__('messages.current_debt')); ?>:</strong><br>
                            $<?php echo e(number_format($order->remaining_amount, 2)); ?>

                        </div>
                        <div class="col-md-6">
                            <strong><?php echo e(__('messages.order_number')); ?>:</strong><br>
                            <?php echo e($order->number); ?>

                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="payment_amount"><?php echo e(__('messages.payment_amount')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   name="payment_amount" 
                                   id="payment_amount"
                                   class="form-control" 
                                   min="0" 
                                   max="<?php echo e($order->remaining_amount); ?>"
                                   step="0.01" 
                                   required>
                        </div>
                        <small class="text-muted"><?php echo e(__('messages.payment_amount_max')); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('messages.cancel')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('messages.confirm')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>