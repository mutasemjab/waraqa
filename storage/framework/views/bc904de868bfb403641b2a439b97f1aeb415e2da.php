<?php $__env->startSection('title', __('messages.providers')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('messages.providers')); ?></h1>
        <a href="<?php echo e(route('providers.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?php echo e(__('messages.Add_New_provider')); ?>

        </a>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('messages.provider_List')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('messages.ID')); ?></th>
                            <th><?php echo e(__('messages.Photo')); ?></th>
                            <th><?php echo e(__('messages.Name')); ?></th>
                            <th><?php echo e(__('messages.Phone')); ?></th>
                            <th><?php echo e(__('messages.Email')); ?></th>
                            <th><?php echo e(__('messages.Balance')); ?></th>
                            <th><?php echo e(__('messages.Status')); ?></th>
                            <th><?php echo e(__('messages.Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($provider->id); ?></td>
                            <td>
                                <?php if($provider->photo): ?>
                                <img src="<?php echo e(asset('assets/admin/uploads/' . $provider->photo)); ?>" alt="<?php echo e($provider->name); ?>" width="50">
                                <?php else: ?>
                                <img src="<?php echo e(asset('assets/admin/img/no-image.png')); ?>" alt="No Image" width="50">
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($provider->name); ?></td>
                            <td> <?php echo e($provider->phone); ?></td>
                            <td><?php echo e($provider->email); ?></td>
                            <td><?php echo e($provider->balance); ?></td>
                            <td>
                                <?php if($provider->activate == 1): ?>
                                <span class="badge badge-success"><?php echo e(__('messages.Active')); ?></span>
                                <?php else: ?>
                                <span class="badge badge-danger"><?php echo e(__('messages.Inactive')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo e(route('providers.show', $provider->id)); ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('providers.edit', $provider->id)); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                 
                                   
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/providers/index.blade.php ENDPATH**/ ?>