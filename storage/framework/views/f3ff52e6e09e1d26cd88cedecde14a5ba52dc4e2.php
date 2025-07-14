

<?php $__env->startSection('title', __('messages.my_products')); ?>
<?php $__env->startSection('page-title', __('messages.my_products')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.my_products')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.manage_your_products_and_inventory')); ?></p>
</div>

<!-- Products Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo e($products->total()); ?></h3>
            <p><?php echo e(__('messages.total_products')); ?></p>
        </div>
    </div>
    
   
</div>

<!-- Actions and Filters -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('provider.products')); ?>" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label"><?php echo e(__('messages.category')); ?></label>
                        <select name="category_id" class="form-select">
                            <option value=""><?php echo e(__('messages.all_categories')); ?></option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e(app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label"><?php echo e(__('messages.search')); ?></label>
                        <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('messages.search_by_product_name')); ?>" value="<?php echo e(request('search')); ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
 
</div>

<!-- Products List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i><?php echo e(__('messages.products_list')); ?>

            <span class="badge bg-primary ms-2"><?php echo e($products->total()); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <?php if($products->count() > 0): ?>
            <div class="row">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                <?php if($product->image): ?>
                                    <img src="<?php echo e(asset('assets/admin/uploads/' . $product->image)); ?>" class="card-img-top" alt="<?php echo e($product->name_en); ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                        
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?php echo e(app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en); ?></h6>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-folder me-1"></i>
                                        <?php echo e(app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en); ?>

                                    </small>
                                </div>
                                
                                <?php if($product->selling_price): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-info"><?php echo e(number_format($product->selling_price, 2)); ?> </span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($product->description_en || $product->description_ar): ?>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo e(Str::limit(app()->getLocale() == 'ar' ? $product->description_ar : $product->description_en, 80)); ?>

                                    </p>
                                <?php endif; ?>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i><?php echo e($product->views ?? 0); ?>

                                        </small>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('provider.products.details', $product->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo e(__('messages.view')); ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($products->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted"><?php echo e(__('messages.no_products_found')); ?></h4>
                <p class="text-muted"><?php echo e(__('messages.start_adding_products_message')); ?></p>
                <a href="<?php echo e(route('provider.products.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i><?php echo e(__('messages.add_first_product')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.product-card {
    transition: transform 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.primary { background-color: #007bff; }
.stat-icon.success { background-color: #28a745; }
.stat-icon.warning { background-color: #ffc107; }
.stat-icon.info { background-color: #17a2b8; }

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteProduct(productId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/provider/products/${productId}`;
    modal.show();
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
<?php echo $__env->make('layouts.provider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/provider/products/index.blade.php ENDPATH**/ ?>