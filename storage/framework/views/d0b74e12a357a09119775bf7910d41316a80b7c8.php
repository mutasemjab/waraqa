<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.orders_management')); ?></h4>
                    <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-primary"><?php echo e(__('messages.create_new_order')); ?></a>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.order_number')); ?></th>
                                    <th><?php echo e(__('messages.user')); ?></th>
                                    <th><?php echo e(__('messages.date')); ?></th>
                                    <th><?php echo e(__('messages.total')); ?></th>
                                    <th><?php echo e(__('messages.paid')); ?></th>
                                    <th><?php echo e(__('messages.remaining')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.payment_status')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($order->number); ?></td>
                                    <td><?php echo e($order->user->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($order->date->format('M d, Y')); ?></td>
                                    <td>$<?php echo e(number_format($order->total_prices, 2)); ?></td>
                                    <td>$<?php echo e(number_format($order->paid_amount, 2)); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($order->remaining_amount > 0 ? 'bg-warning' : 'bg-success'); ?>">
                                            $<?php echo e(number_format($order->remaining_amount, 2)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($order->status == 1): ?>
                                            <span class="badge bg-success"><?php echo e(__('messages.done')); ?></span>
                                        <?php elseif($order->status == 2): ?>
                                            <span class="badge bg-danger"><?php echo e(__('messages.canceled')); ?></span>
                                        <?php else: ?>
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
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-info"><?php echo e(__('messages.view')); ?></a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center"><?php echo e(__('messages.no_orders_found')); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php echo e($orders->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>