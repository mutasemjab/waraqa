


<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo e(__('messages.create_new_order')); ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('orders.store')); ?>" method="POST" id="orderForm">
                        <?php echo csrf_field(); ?>
                        
                        <div class="form-group mb-3">
                            <label for="user_id"><?php echo e(__('messages.select_user')); ?></label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value=""><?php echo e(__('messages.choose_user')); ?></option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label><?php echo e(__('messages.products')); ?></label>
                            <div id="products-container">
                                <div class="product-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="products[0][id]" class="form-control product-select" required>
                                                <option value=""><?php echo e(__('messages.select_product')); ?></option>
                                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($product->id); ?>" 
                                                            data-price="<?php echo e($product->selling_price); ?>"
                                                            data-tax="<?php echo e($product->tax); ?>">
                                                        <?php echo e($product->name_en); ?> - $<?php echo e($product->selling_price); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="products[0][quantity]" 
                                                   class="form-control quantity-input" 
                                                   placeholder="<?php echo e(__('messages.quantity')); ?>" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="line-total">$0.00</span>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger remove-product" disabled>×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success" id="add-product"><?php echo e(__('messages.add_product')); ?></button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paid_amount"><?php echo e(__('messages.paid_amount')); ?></label>
                                    <input type="number" name="paid_amount" id="paid_amount" 
                                           class="form-control" min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><?php echo e(__('messages.order_summary')); ?></label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo e(__('messages.subtotal')); ?>:</span>
                                                <span id="subtotal">$0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo e(__('messages.tax')); ?>:</span>
                                                <span id="tax-total">$0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between font-weight-bold">
                                                <span><?php echo e(__('messages.total')); ?>:</span>
                                                <span id="grand-total">$0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted">
                                                <span><?php echo e(__('messages.remaining')); ?>:</span>
                                                <span id="remaining-amount">$0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="note"><?php echo e(__('messages.note')); ?></label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="<?php echo e(__('messages.optional_note')); ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><?php echo e(__('messages.create_order')); ?></button>
                            <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary"><?php echo e(__('messages.cancel')); ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 1;
    
    // Add product row
    document.getElementById('add-product').addEventListener('click', function() {
        const container = document.getElementById('products-container');
        const newRow = container.children[0].cloneNode(true);
        
        // Update indices
        newRow.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/\[0\]/, `[${productIndex}]`);
            input.value = '';
        });
        
        newRow.querySelector('.line-total').textContent = '$0.00';
        newRow.querySelector('.remove-product').disabled = false;
        
        container.appendChild(newRow);
        productIndex++;
        
        attachEventListeners(newRow);
    });
    
    // Remove product row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product') && !e.target.disabled) {
            e.target.closest('.product-row').remove();
            calculateTotals();
        }
    });
    
    // Attach event listeners to existing rows
    document.querySelectorAll('.product-row').forEach(row => {
        attachEventListeners(row);
    });
    
    // Paid amount change
    document.getElementById('paid_amount').addEventListener('input', calculateTotals);
    
    function attachEventListeners(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        
        productSelect.addEventListener('change', function() {
            calculateLineTotal(row);
        });
        
        quantityInput.addEventListener('input', function() {
            calculateLineTotal(row);
        });
    }
    
    function calculateLineTotal(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const lineTotalSpan = row.querySelector('.line-total');
        
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const tax = parseFloat(selectedOption.dataset.tax) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        
        const subtotal = price * quantity;
        const taxAmount = (subtotal * tax) / 100;
        const total = subtotal + taxAmount;
        
        lineTotalSpan.textContent = `$${total.toFixed(2)}`;
        
        calculateTotals();
    }
    
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        
        document.querySelectorAll('.product-row').forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const tax = parseFloat(selectedOption.dataset.tax) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            
            const lineSubtotal = price * quantity;
            const lineTax = (lineSubtotal * tax) / 100;
            
            subtotal += lineSubtotal;
            totalTax += lineTax;
        });
        
        const grandTotal = subtotal + totalTax;
        const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
        const remainingAmount = Math.max(0, grandTotal - paidAmount);
        
        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('tax-total').textContent = `$${totalTax.toFixed(2)}`;
        document.getElementById('grand-total').textContent = `$${grandTotal.toFixed(2)}`;
        document.getElementById('remaining-amount').textContent = `$${remainingAmount.toFixed(2)}`;
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/orders/create.blade.php ENDPATH**/ ?>