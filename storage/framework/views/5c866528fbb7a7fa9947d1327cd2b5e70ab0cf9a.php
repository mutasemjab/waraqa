

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__('messages.debt_details')); ?> #<?php echo e($userDept->id); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('user_depts.index')); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> <?php echo e(__('messages.back')); ?>

                        </a>
                        <a href="<?php echo e(route('user_depts.edit', $userDept)); ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> <?php echo e(__('messages.edit')); ?>

                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title"><?php echo e(__('messages.user_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.name')); ?>:</strong></td>
                                            <td><?php echo e($userDept->user->name); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.email')); ?>:</strong></td>
                                            <td><?php echo e($userDept->user->email); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.phone')); ?>:</strong></td>
                                            <td><?php echo e($userDept->user->phone ?? __('messages.not_specified')); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Debt Information -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title"><?php echo e(__('messages.debt_information')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong><?php echo e(__('messages.total_amount')); ?>:</strong></td>
                                            <td class="text-right"><?php echo e(number_format($userDept->total_amount, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.paid_amount')); ?>:</strong></td>
                                            <td class="text-right text-success"><?php echo e(number_format($userDept->paid_amount, 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.remaining_amount')); ?>:</strong></td>
                                            <td class="text-right <?php echo e($userDept->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>">
                                                <strong><?php echo e(number_format($userDept->remaining_amount, 2)); ?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.status')); ?>:</strong></td>
                                            <td>
                                                <span class="badge <?php echo e($userDept->status_badge); ?>">
                                                    <?php echo e($userDept->status_label); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php echo e(__('messages.created_at')); ?>:</strong></td>
                                            <td><?php echo e($userDept->created_at->format('Y-m-d H:i')); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title"><?php echo e(__('messages.related_order')); ?></h5>
                                    <div class="card-tools">
                                        <a href="<?php echo e(route('orders.show', $userDept->order)); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> <?php echo e(__('messages.view_order')); ?>

                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong><?php echo e(__('messages.order_number')); ?>:</strong><br>
                                            <?php echo e($userDept->order->number); ?>

                                        </div>
                                        <div class="col-md-3">
                                            <strong><?php echo e(__('messages.order_date')); ?>:</strong><br>
                                            <?php echo e($userDept->order->date); ?>

                                        </div>
                                        <div class="col-md-3">
                                            <strong><?php echo e(__('messages.order_total')); ?>:</strong><br>
                                            <?php echo e(number_format($userDept->order->total_prices, 2)); ?>

                                        </div>
                                        <div class="col-md-3">
                                            <strong><?php echo e(__('messages.order_status')); ?>:</strong><br>
                                            <span class="badge badge-<?php echo e($userDept->order->status == 1 ? 'success' : 'warning'); ?>">
                                                <?php echo e($userDept->order->status == 1 ? __('messages.completed') : __('messages.pending')); ?>

                                            </span>
                                        </div>
                                    </div>

                                    <?php if($userDept->order->orderProducts && $userDept->order->orderProducts->count() > 0): ?>
                                        <div class="table-responsive mt-3">
                                            <h6><?php echo e(__('messages.order_products')); ?>:</h6>
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo e(__('messages.product')); ?></th>
                                                        <th><?php echo e(__('messages.quantity')); ?></th>
                                                        <th><?php echo e(__('messages.unit_price')); ?></th>
                                                        <th><?php echo e(__('messages.total_price')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $userDept->order->orderProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($orderProduct->product->name_ar ?? $orderProduct->product->name); ?></td>
                                                            <td class="text-center"><?php echo e($orderProduct->quantity); ?></td>
                                                            <td class="text-right"><?php echo e(number_format($orderProduct->unit_price, 2)); ?></td>
                                                            <td class="text-right"><?php echo e(number_format($orderProduct->total_price_after_tax, 2)); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Actions -->
                    <?php if($userDept->status == 1 && $userDept->remaining_amount > 0): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h5 class="card-title"><?php echo e(__('messages.payment_actions')); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#paymentModal">
                                            <i class="fas fa-money-bill"></i> <?php echo e(__('messages.make_payment')); ?>

                                        </button>
                                        
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i>
                                            <?php echo e(__('messages.remaining_amount_to_pay')); ?>: <strong><?php echo e(number_format($userDept->remaining_amount, 2)); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<?php if($userDept->status == 1 && $userDept->remaining_amount > 0): ?>
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('user_depts.make_payment', $userDept)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e(__('messages.make_payment')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><?php echo e(__('messages.user')); ?></label>
                            <input type="text" class="form-control" value="<?php echo e($userDept->user->name); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label><?php echo e(__('messages.remaining_amount')); ?></label>
                            <input type="text" class="form-control" value="<?php echo e(number_format($userDept->remaining_amount, 2)); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label><?php echo e(__('messages.payment_amount')); ?> <span class="text-danger">*</span></label>
                            <input type="number" name="payment_amount" class="form-control" step="0.01" max="<?php echo e($userDept->remaining_amount); ?>" required>
                            <small class="form-text text-muted"><?php echo e(__('messages.maximum_payment_amount')); ?>: <?php echo e(number_format($userDept->remaining_amount, 2)); ?></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('messages.cancel')); ?></button>
                        <button type="submit" class="btn btn-success"><?php echo e(__('messages.record_payment')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/user_depts/show.blade.php ENDPATH**/ ?>