

<?php $__env->startSection('title', __('messages.record_sale')); ?>
<?php $__env->startSection('page-title', __('messages.record_sale')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><?php echo e(__('messages.record_new_sale')); ?></h1>
    <p class="page-subtitle"><?php echo e(__('messages.record_products_sold_from_warehouse')); ?></p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cash-register me-2"></i><?php echo e(__('messages.sale_information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('user.sales.store')); ?>" method="POST" id="salesForm">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Sale Date -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="date_note_voucher" class="form-label"><?php echo e(__('messages.sale_date')); ?> <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control <?php $__errorArgs = ['date_note_voucher'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="date_note_voucher" 
                                   name="date_note_voucher" 
                                   value="<?php echo e(old('date_note_voucher', date('Y-m-d'))); ?>" 
                                   required>
                            <?php $__errorArgs = ['date_note_voucher'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <!-- Products Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6><?php echo e(__('messages.products_sold')); ?></h6>
                            <button type="button" class="btn btn-success btn-sm" id="addProductBtn">
                                <i class="fas fa-plus me-1"></i><?php echo e(__('messages.add_product')); ?>

                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo e(__('messages.product')); ?></th>
                                        <th width="120"><?php echo e(__('messages.available')); ?></th>
                                        <th width="120"><?php echo e(__('messages.quantity_sold')); ?></th>
                                        <th width="120"><?php echo e(__('messages.selling_price')); ?></th>
                                        <th width="80"><?php echo e(__('messages.actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <?php $__errorArgs = ['products'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="note" class="form-label"><?php echo e(__('messages.notes')); ?></label>
                        <textarea class="form-control <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="note" 
                                  name="note" 
                                  rows="3" 
                                  placeholder="<?php echo e(__('messages.sale_notes_placeholder')); ?>"><?php echo e(old('note')); ?></textarea>
                        <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="text-end">
                        <a href="<?php echo e(route('user.sales.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i><?php echo e(__('messages.cancel')); ?>

                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i><?php echo e(__('messages.record_sale')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Available Products Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i><?php echo e(__('messages.available_products')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="productSearch" class="form-control" placeholder="<?php echo e(__('messages.search_products')); ?>">
                </div>
                
                <div class="available-products-list" style="max-height: 400px; overflow-y: auto;">
                    <?php if($availableProducts->count() > 0): ?>
                        <?php $__currentLoopData = $availableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="product-item border rounded p-3 mb-2 cursor-pointer" 
                                 data-product-id="<?php echo e($product->id); ?>"
                                 data-product-name="<?php echo e(strtolower($product->name_ar . ' ' . $product->name_en)); ?>"
                                 data-available="<?php echo e($product->available_quantity); ?>"
                                 data-price="<?php echo e($product->selling_price); ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?php echo e($product->name_ar); ?></h6>
                                        <?php if($product->name_en): ?>
                                            <small class="text-muted"><?php echo e($product->name_en); ?></small>
                                        <?php endif; ?>
                                        <div class="mt-1">
                                            <span class="badge bg-info"><?php echo e($product->available_quantity); ?> <?php echo e(__('messages.available')); ?></span>
                                            <span class="badge bg-success">$<?php echo e(number_format($product->selling_price, 2)); ?></span>
                                        </div>
                                        <?php if($product->category): ?>
                                            <small class="text-muted"><?php echo e($product->category->name_ar); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm add-product-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open text-muted" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-muted"><?php echo e(__('messages.no_products_available')); ?></h6>
                            <p class="text-muted small"><?php echo e(__('messages.order_products_first')); ?></p>
                            <a href="<?php echo e(route('user.orders')); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-shopping-cart me-1"></i><?php echo e(__('messages.view_orders')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sale Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i><?php echo e(__('messages.sale_summary')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span><?php echo e(__('messages.total_items')); ?>:</span>
                    <span id="totalItems">0</span>
                </div>
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span><?php echo e(__('messages.total_value')); ?>:</span>
                    <span id="totalValue">$0.00</span>
                </div>
                <hr>
                <div class="summary-item d-flex justify-content-between">
                    <strong><?php echo e(__('messages.products_count')); ?>:</strong>
                    <strong id="productsCount">0</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <tr class="product-row">
        <td>
            <select name="products[INDEX][product_id]" class="form-select product-select" required>
                <option value=""><?php echo e(__('messages.select_product')); ?></option>
                <?php $__currentLoopData = $availableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>" 
                            data-available="<?php echo e($product->available_quantity); ?>" 
                            data-price="<?php echo e($product->selling_price); ?>">
                        <?php echo e($product->name_ar); ?> (<?php echo e($product->available_quantity); ?> <?php echo e(__('messages.available')); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td>
            <span class="available-quantity">-</span>
        </td>
        <td>
            <input type="number" 
                   name="products[INDEX][quantity]" 
                   class="form-control quantity-input" 
                   min="1" 
                   max="0"
                   required>
        </td>
        <td>
            <input type="number" 
                   name="products[INDEX][selling_price]" 
                   class="form-control price-input" 
                   step="0.01" 
                   min="0">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-product-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 0;

    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .cursor-pointer { cursor: pointer; }
        .product-item:hover { background-color: #f8f9fa; }
    `;
    document.head.appendChild(style);

    // Add product row function
    function addProductRow() {
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX placeholder with actual index
        const htmlString = clone.querySelector('tr').outerHTML.replace(/INDEX/g, productIndex);
        
        const tbody = document.getElementById('productsTableBody');
        tbody.insertAdjacentHTML('beforeend', htmlString);
        
        productIndex++;
        
        // Add event listeners to the new row
        const newRow = tbody.lastElementChild;
        setupRowEvents(newRow);
        
        updateSummary();
    }

    // Setup event listeners for a row
    function setupRowEvents(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const removeBtn = row.querySelector('.remove-product-btn');

        productSelect.addEventListener('change', function() {
            updateProductInfo(this);
        });

        quantityInput.addEventListener('input', updateSummary);
        priceInput.addEventListener('input', updateSummary);

        removeBtn.addEventListener('click', function() {
            row.remove();
            updateSummary();
        });
    }

    // Add product to sale from sidebar
    function addProductToSale(productId, productName, availableQty, price) {
        // Check if product already added
        const existingSelects = document.querySelectorAll('.product-select');
        for (let select of existingSelects) {
            if (select.value == productId) {
                alert('<?php echo e(__("messages.product_already_added")); ?>');
                return;
            }
        }
        
        addProductRow();
        
        // Set the values in the new row
        const rows = document.querySelectorAll('.product-row');
        const lastRow = rows[rows.length - 1];
        
        const select = lastRow.querySelector('.product-select');
        const quantityInput = lastRow.querySelector('.quantity-input');
        const priceInput = lastRow.querySelector('.price-input');
        const availableSpan = lastRow.querySelector('.available-quantity');
        
        select.value = productId;
        quantityInput.max = availableQty;
        quantityInput.value = 1;
        priceInput.value = price;
        availableSpan.textContent = availableQty;
        
        updateSummary();
    }

    // Update product info when selection changes
    function updateProductInfo(select) {
        const row = select.closest('tr');
        const option = select.selectedOptions[0];
        const availableSpan = row.querySelector('.available-quantity');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        
        if (option && option.value) {
            const available = option.dataset.available;
            const price = option.dataset.price;
            
            availableSpan.textContent = available;
            quantityInput.max = available;
            quantityInput.value = Math.min(1, available);
            priceInput.value = price;
        } else {
            availableSpan.textContent = '-';
            quantityInput.max = 0;
            quantityInput.value = '';
            priceInput.value = '';
        }
        
        updateSummary();
    }

    // Update summary
    function updateSummary() {
        let totalItems = 0;
        let totalValue = 0;
        let productsCount = 0;
        
        const rows = document.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            const productSelect = row.querySelector('.product-select');
            
            if (productSelect.value && quantityInput.value && priceInput.value) {
                const quantity = parseInt(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                
                totalItems += quantity;
                totalValue += quantity * price;
                productsCount++;
            }
        });
        
        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalValue').textContent = '$' + totalValue.toFixed(2);
        document.getElementById('productsCount').textContent = productsCount;
    }

    // Event listeners
    document.getElementById('addProductBtn').addEventListener('click', addProductRow);

    // Product search functionality
    document.getElementById('productSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            const productName = item.dataset.productName;
            if (productName.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Product item click handlers
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.querySelector('h6').textContent;
            const availableQty = this.dataset.available;
            const price = this.dataset.price;
            
            addProductToSale(productId, productName, availableQty, price);
        });
    });

    // Form validation
    document.getElementById('salesForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.product-row');
        let hasValidProducts = false;
        
        rows.forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            
            if (productSelect.value && quantityInput.value && parseInt(quantityInput.value) > 0) {
                hasValidProducts = true;
            }
        });
        
        if (!hasValidProducts) {
            e.preventDefault();
            alert('<?php echo e(__("messages.please_add_at_least_one_product")); ?>');
        }
    });

    // Add one initial row
    addProductRow();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/user/sales/create.blade.php ENDPATH**/ ?>