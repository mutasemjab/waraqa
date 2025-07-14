<?php $__env->startSection('title'); ?>
<?php echo e(__('messages.noteVouchers')); ?>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>



<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> <?php echo e(__('messages.noteVouchers')); ?> </h3>
     
        

    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">


            </div>

        </div>
        <div class="clearfix"></div>

        <div id="ajax_responce_serarchDiv" class="col-md-12">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('noteVoucher-table')): ?>
            <?php if(@isset($data) && !@empty($data) && count($data) > 0): ?>
            <table id="example2" class="table table-bordered table-hover">
                <thead class="custom_thead">


                    <th><?php echo e(__('messages.number')); ?></th>
                    <th><?php echo e(__('messages.note')); ?></th>
                    <th><?php echo e(__('messages.date_note_voucher')); ?></th>
                    <th><?php echo e(__('messages.noteVoucherTypes')); ?></th>

                    <th></th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>


                        <td><?php echo e($info->number); ?></td>
                        <td><?php echo e($info->note ?? null); ?></td>
                        <td><?php echo e($info->date_note_voucher); ?></td>
                        <td><?php echo e($info->noteVoucherType->name); ?></td>


                        <td>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('noteVoucher-edit')): ?>
                            <a href="<?php echo e(route('noteVouchers.edit', $info->id)); ?>" class="btn btn-sm  btn-primary"><?php echo e(__('messages.Edit')); ?></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('noteVoucher-delete')): ?>
                            <form action="<?php echo e(route('noteVouchers.destroy', $info->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger"><?php echo e(__('messages.Delete')); ?></button>
                            </form>
                            <?php endif; ?>

                        </td>


                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                </tbody>
            </table>
            <br>
            
            <?php else: ?>
            <div class="alert alert-danger">
                <?php echo e(__('messages.No_data')); ?> </div>
            <?php endif; ?>
            <?php endif; ?>

        </div>



    </div>

</div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(asset('assets/admin/js/sliderss.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/noteVouchers/index.blade.php ENDPATH**/ ?>