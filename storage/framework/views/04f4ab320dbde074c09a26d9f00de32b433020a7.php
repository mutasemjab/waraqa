

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo e(__('messages.countries')); ?></h4>
                    <a href="<?php echo e(route('countries.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo e(__('messages.add_country')); ?>

                    </a>
                </div>

                <div class="card-body">
                   

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.id')); ?></th>
                                    <th><?php echo e(__('messages.country_name')); ?></th>
                                    <th><?php echo e(__('messages.actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($country->id); ?></td>
                                        <td>
                                            <strong><?php echo e($country->name_ar); ?></strong>
                                        </td>
                                   
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                                <a href="<?php echo e(route('countries.edit', $country)); ?>" 
                                                   class="btn btn-sm btn-warning" title="<?php echo e(__('messages.edit')); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                               
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center"><?php echo e(__('messages.no_countries_found')); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <?php echo e($countries->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/countries/index.blade.php ENDPATH**/ ?>