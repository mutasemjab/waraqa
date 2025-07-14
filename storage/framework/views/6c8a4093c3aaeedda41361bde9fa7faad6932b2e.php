<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand-link">
        <img src="<?php echo e(asset('assets/admin/dist/img/AdminLTELogo.png')); ?>" alt="App Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Dar Elnasher</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
              <h4 style="color: white; margin:auto;"> <?php echo e(auth()->user()->name); ?></h4>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p><?php echo e(__('messages.dashboard')); ?></p>
                    </a>
                </li>
                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['country-table', 'country-add', 'country-edit', 'country-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('countries.index')); ?>" class="nav-link <?php echo e(request()->routeIs('countries.index') ? 'active' : ''); ?>">
                                <i class="fas fa-handshake nav-icon"></i>
                                <p><?php echo e(__('messages.countries')); ?></p>
                            </a>
                        </li>
                 <?php endif; ?>
                <!-- User Management Section -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user-table', 'user-add', 'user-edit', 'user-delete', 'driver-table', 'driver-add', 'driver-edit', 'driver-delete'])): ?>
                <li class="nav-item <?php echo e(request()->is('admin/users*') || request()->is('admin/drivers*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            <?php echo e(__('messages.user_management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user-table', 'user-add', 'user-edit', 'user-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.index') ? 'active' : ''); ?>">
                                <i class="far fa-user nav-icon"></i>
                                <p><?php echo e(__('messages.users')); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['provider-table', 'provider-add', 'provider-edit', 'provider-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('providers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('providers.index') ? 'active' : ''); ?>">
                                <i class="fas fa-car nav-icon"></i>
                                <p><?php echo e(__('messages.providers')); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                  <li class="nav-item <?php echo e(request()->is('categories*') || request()->is('products*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            <?php echo e(__('messages.Catalog_Management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                     
                        <li class="nav-item">
                            <a href="<?php echo e(route('categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                                <i class="fas fa-folder nav-icon"></i>
                                <p><?php echo e(__('messages.Categories')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('products.index')); ?>" class="nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                                <i class="fas fa-box nav-icon"></i>
                                <p><?php echo e(__('messages.Products')); ?></p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(route('warehouses.index')); ?>"  class="nav-link <?php echo e(request()->routeIs('warehouses.*') ? 'active' : ''); ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p> <?php echo e(__('messages.warehouses')); ?> </p>
                            </a>
                        </li>

                    </ul>
                </li>

                
                <?php if(
                        $user->can('noteVoucherType-table') ||
                            $user->can('noteVoucherType-add') ||
                            $user->can('noteVoucherType-edit') ||
                            $user->can('noteVoucherType-delete')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('noteVoucherTypes.index')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> <?php echo e(__('messages.noteVoucherTypes')); ?> </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php
                        $noteVouchertypes = App\Models\NoteVoucherType::get();
                    ?>
                    <?php $__currentLoopData = $noteVouchertypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $noteVouchertype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('noteVouchers.create', ['id' => $noteVouchertype->id])); ?>"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> <?php echo e($noteVouchertype->name); ?> </p>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if(
                        $user->can('noteVoucher-table') ||
                            $user->can('noteVoucher-add') ||
                            $user->can('noteVoucher-edit') ||
                            $user->can('noteVoucher-delete')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('noteVouchers.index')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> <?php echo e(__('messages.noteVouchers')); ?> </p>
                            </a>
                        </li>
                    <?php endif; ?>


                <!-- Notifications -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order-table', 'order-add', 'order-edit', 'order-delete'])): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route('orders.index')); ?>" class="nav-link <?php echo e(request()->routeIs('orders.index') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-car"></i>
                        <p><?php echo e(__('messages.orders')); ?></p>
                    </a>
                </li>
                <?php endif; ?>
            
         

           
            
                         <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['userDept-table', 'userDept-add', 'userDept-edit', 'userDept-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('user_depts.index')); ?>" class="nav-link <?php echo e(request()->routeIs('user_depts.index') ? 'active' : ''); ?>">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p><?php echo e(__('messages.userDepts')); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>

                <!-- System Settings -->
                <li class="nav-item <?php echo e(request()->is('admin/settings*') || request()->is('admin/roles*') || request()->is('admin/employees*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            <?php echo e(__('messages.system_settings')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('settings.index') ? 'active' : ''); ?>">
                                <i class="fas fa-wrench nav-icon"></i>
                                <p><?php echo e(__('messages.general_settings')); ?></p>
                            </a>
                        </li>
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['role-table', 'role-add', 'role-edit', 'role-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.role.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.role.index') ? 'active' : ''); ?>">
                                <i class="fas fa-user-shield nav-icon"></i>
                                <p><?php echo e(__('messages.roles')); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['employee-table', 'employee-add', 'employee-edit', 'employee-delete'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.employee.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.employee.index') ? 'active' : ''); ?>">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p><?php echo e(__('messages.employees')); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Account -->
                <li class="nav-item">
                    <a href="<?php echo e(route('admin.login.edit', auth()->user()->id)); ?>" class="nav-link <?php echo e(request()->routeIs('admin.login.edit') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p><?php echo e(__('messages.admin_account')); ?></p>
                    </a>
                </li>

               
            </ul>
        </nav>
    </div>
</aside><?php /**PATH C:\xampp\htdocs\nasher\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>