<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="App Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Dar Waraqa</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
              <h4 style="color: white; margin:auto;"> {{ auth()->user()->name }}</h4>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('messages.dashboard') }}</p>
                    </a>
                </li>
                 @canany(['country-table', 'country-add', 'country-edit', 'country-delete'])
                        <li class="nav-item">
                            <a href="{{ route('countries.index') }}" class="nav-link {{ request()->routeIs('countries.index') ? 'active' : '' }}">
                                <i class="fas fa-handshake nav-icon"></i>
                                <p>{{ __('messages.countries') }}</p>
                            </a>
                        </li>
                 @endcanany
                <!-- User Management Section -->
                @canany(['user-table', 'user-add', 'user-edit', 'user-delete', 'driver-table', 'driver-add', 'driver-edit', 'driver-delete'])
                <li class="nav-item {{ request()->is('admin/users*') || request()->is('admin/drivers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            {{ __('messages.user_management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @canany(['user-table', 'user-add', 'user-edit', 'user-delete'])
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <i class="far fa-user nav-icon"></i>
                                <p>{{ __('messages.users') }}</p>
                            </a>
                        </li>
                        @endcanany
                        
                        @canany(['provider-table', 'provider-add', 'provider-edit', 'provider-delete'])
                        <li class="nav-item">
                            <a href="{{ route('providers.index') }}" class="nav-link {{ request()->routeIs('providers.index') ? 'active' : '' }}">
                                <i class="fas fa-car nav-icon"></i>
                                <p>{{ __('messages.providers') }}</p>
                            </a>
                        </li>
                        @endcanany
                    </ul>
                </li>
                @endcanany
                
                  <li class="nav-item {{ request()->is('categories*') || request()->is('products*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            {{ __('messages.Catalog_Management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                     
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="fas fa-folder nav-icon"></i>
                                <p>{{ __('messages.Categories') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>{{ __('messages.Products') }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('warehouses.index') }}"  class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p> {{ __('messages.warehouses') }} </p>
                            </a>
                        </li>

                    </ul>
                </li>

                
                @if (
                        $user->can('noteVoucherType-table') ||
                            $user->can('noteVoucherType-add') ||
                            $user->can('noteVoucherType-edit') ||
                            $user->can('noteVoucherType-delete'))
                        <li class="nav-item">
                            <a href="{{ route('noteVoucherTypes.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> {{ __('messages.noteVoucherTypes') }} </p>
                            </a>
                        </li>
                    @endif
                    @php
                        $noteVouchertypes = App\Models\NoteVoucherType::get();
                    @endphp
                    @foreach ($noteVouchertypes as $noteVouchertype)
                        <li class="nav-item">
                            <a href="{{ route('noteVouchers.create', ['id' => $noteVouchertype->id]) }}"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> {{ $noteVouchertype->name }} </p>
                            </a>
                        </li>
                    @endforeach

                    @if (
                        $user->can('noteVoucher-table') ||
                            $user->can('noteVoucher-add') ||
                            $user->can('noteVoucher-edit') ||
                            $user->can('noteVoucher-delete'))
                        <li class="nav-item">
                            <a href="{{ route('noteVouchers.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p> {{ __('messages.noteVouchers') }} </p>
                            </a>
                        </li>
                    @endif


                <!-- Notifications -->
                @canany(['order-table', 'order-add', 'order-edit', 'order-delete'])
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-car"></i>
                        <p>{{ __('messages.orders') }}</p>
                    </a>
                </li>
                @endcanany
            
         

           
            
                         @canany(['userDept-table', 'userDept-add', 'userDept-edit', 'userDept-delete'])
                        <li class="nav-item">
                            <a href="{{ route('user_depts.index') }}" class="nav-link {{ request()->routeIs('user_depts.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-money-bill-wave"></i>
                                <p>{{ __('messages.userDepts') }}</p>
                            </a>
                        </li>
                        @endcanany

                <!-- System Settings -->
                <li class="nav-item {{ request()->is('admin/settings*') || request()->is('admin/roles*') || request()->is('admin/employees*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            {{ __('messages.system_settings') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                                <i class="fas fa-wrench nav-icon"></i>
                                <p>{{ __('messages.general_settings') }}</p>
                            </a>
                        </li>
                        
                        @canany(['role-table', 'role-add', 'role-edit', 'role-delete'])
                        <li class="nav-item">
                            <a href="{{ route('admin.role.index') }}" class="nav-link {{ request()->routeIs('admin.role.index') ? 'active' : '' }}">
                                <i class="fas fa-user-shield nav-icon"></i>
                                <p>{{ __('messages.roles') }}</p>
                            </a>
                        </li>
                        @endcanany
                        
                        @canany(['employee-table', 'employee-add', 'employee-edit', 'employee-delete'])
                        <li class="nav-item">
                            <a href="{{ route('admin.employee.index') }}" class="nav-link {{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                                <i class="fas fa-user-tie nav-icon"></i>
                                <p>{{ __('messages.employees') }}</p>
                            </a>
                        </li>
                        @endcanany
                    </ul>
                </li>

                <!-- Account -->
                <li class="nav-item">
                    <a href="{{ route('admin.login.edit', auth()->user()->id) }}" class="nav-link {{ request()->routeIs('admin.login.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>{{ __('messages.admin_account') }}</p>
                    </a>
                </li>

               
            </ul>
        </nav>
    </div>
</aside>