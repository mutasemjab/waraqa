

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__('messages.user_debts_management')); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('user_depts.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> <?php echo e(__('messages.add_new_debt')); ?>

                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="<?php echo e(route('user_depts.index')); ?>" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="user_id" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_users')); ?></option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value=""><?php echo e(__('messages.all_statuses')); ?></option>
                                    <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>><?php echo e(__('messages.active')); ?></option>
                                    <option value="2" <?php echo e(request('status') == '2' ? 'selected' : ''); ?>><?php echo e(__('messages.paid')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('messages.search_by_user_name_or_email')); ?>" value="<?php echo e(request('search')); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info"><?php echo e(__('messages.filter')); ?></button>
                                <a href="<?php echo e(route('user_depts.index')); ?>" class="btn btn-secondary"><?php echo e(__('messages.clear')); ?></a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.id')); ?></th>
                                    <th><?php echo e(__('messages.user')); ?></th>
                                    <th><?php echo e(__('messages.order_number')); ?></th>
                                    <th><?php echo e(__('messages.total_amount')); ?></th>
                                    <th><?php echo e(__('messages.paid_amount')); ?></th>
                                    <th><?php echo e(__('messages.remaining_amount')); ?></th>
                                    <th><?php echo e(__('messages.status')); ?></th>
                                    <th><?php echo e(__('messages.created_at')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $userDepts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($debt->id); ?></td>
                                        <td>
                                            <strong><?php echo e($debt->user->name); ?></strong><br>
                                            <small class="text-muted"><?php echo e($debt->user->email); ?></small>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('orders.show', $debt->order)); ?>">
                                                <?php echo e($debt->order->number); ?>

                                            </a>
                                        </td>
                                        <td class="text-right"><?php echo e(number_format($debt->total_amount, 2)); ?></td>
                                        <td class="text-right"><?php echo e(number_format($debt->paid_amount, 2)); ?></td>
                                        <td class="text-right">
                                            <strong class="<?php echo e($debt->remaining_amount > 0 ? 'text-danger' : 'text-success'); ?>">
                                                <?php echo e(number_format($debt->remaining_amount, 2)); ?>

                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo e($debt->status_badge); ?>">
                                                <?php echo e($debt->status_label); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($debt->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('user_depts.show', $debt)); ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('user_depts.edit', $debt)); ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if($debt->status == 1): ?>
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal<?php echo e($debt->id); ?>">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <form action="<?php echo e(route('user_depts.destroy', $debt)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('<?php echo e(__('messages.are_you_sure')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Modal -->
                                    <?php if($debt->status == 1): ?>
                                        <div class="modal fade" id="paymentModal<?php echo e($debt->id); ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?php echo e(route('user_depts.make_payment', $debt)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"><?php echo e(__('messages.make_payment')); ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label><?php echo e(__('messages.remaining_amount')); ?></label>
                                                                <input type="text" class="form-control" value="<?php echo e(number_format($debt->remaining_amount, 2)); ?>" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label><?php echo e(__('messages.payment_amount')); ?> <span class="text-danger">*</span></label>
                                                                <input type="number" name="payment_amount" class="form-control" step="0.01" max="<?php echo e($debt->remaining_amount); ?>" required>
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
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center"><?php echo e(__('messages.no_debts_found')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <?php echo e($userDepts->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Auto-hide success/error messages
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/user_depts/index.blade.php ENDPATH**/ ?>