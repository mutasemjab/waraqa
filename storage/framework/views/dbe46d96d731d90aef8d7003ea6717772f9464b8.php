<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4><?php echo e(__('messages.Products')); ?></h4>
                    <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary">
                        <?php echo e(__('messages.Add_Product')); ?>

                    </a>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.Image')); ?></th>
                                    <th><?php echo e(__('messages.Name')); ?></th>
                                    <th><?php echo e(__('messages.Category')); ?></th>
                                    <th><?php echo e(__('messages.provider')); ?></th>
                                    <th><?php echo e(__('messages.Price')); ?></th>
                                    <th><?php echo e(__('messages.Actions')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <?php if($product->photo): ?>
                                                <img src="<?php echo e(asset('assets/admin/uploads/' . $product->photo)); ?>" 
                                                     alt="<?php echo e($product->name_en); ?>" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <small class="text-muted"><?php echo e(__('messages.No_Image')); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e(app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en); ?></strong>
                                        </td>
                                        <td>
                                            <strong><?php echo e(app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en); ?></strong>
                                        </td>
                                        <td>
                                            <strong><?php echo e($product->provider->name); ?></strong>
                                        </td>
                                       
                                        <td><?php echo e(number_format($product->selling_price, 2)); ?> </td>
                                      
                                      
                                        <td>
                                            <a href="<?php echo e(route('products.edit', $product->id)); ?>" 
                                               class="btn btn-sm btn-warning">
                                                <?php echo e(__('messages.Edit')); ?>

                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <?php echo e(__('messages.No_Products_Found')); ?>

                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/products/index.blade.php ENDPATH**/ ?>