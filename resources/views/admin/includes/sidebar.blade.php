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
                @canany(['seller-table', 'seller-add', 'seller-edit', 'seller-delete'])
                <li class="nav-item">
                    <a href="{{ route('sellers.index') }}" class="nav-link {{ request()->routeIs('sellers.index', 'sellers.*') ? 'active' : '' }}">
                        <i class="fas fa-store nav-icon"></i>
                        <p>{{ __('messages.sellers') }}</p>
                    </a>
                </li>
                @endcanany

                @canany(['customer-table', 'customer-add', 'customer-edit', 'customer-delete'])
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.index', 'customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>{{ __('messages.customers') }}</p>
                    </a>
                </li>
                @endcanany

                @canany(['provider-table', 'provider-add', 'provider-edit', 'provider-delete'])
                <li class="nav-item">
                    <a href="{{ route('providers.index') }}" class="nav-link {{ request()->routeIs('providers.index', 'providers.*') ? 'active' : '' }}">
                        <i class="fas fa-car nav-icon"></i>
                        <p>{{ __('messages.providers') }}</p>
                    </a>
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


                @canany(['noteVoucher-table', 'noteVoucher-add', 'noteVoucher-edit', 'noteVoucher-delete'])
                <li class="nav-item {{ request()->is('noteVouchers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            {{ __('messages.noteVouchers') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
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

                        <li class="nav-item">
                            <a href="{{ route('noteVouchers.index') }}" class="nav-link {{ request()->routeIs('noteVouchers.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p> {{ __('messages.All') }} {{ __('messages.noteVouchers') }} </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcanany


                <!-- Notifications -->
                @canany(['order-table', 'order-add', 'order-edit', 'order-delete'])
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-car"></i>
                        <p>{{ __('messages.orders') }}</p>
                    </a>
                </li>
                @endcanany

                <!-- Seller Product Requests -->
                @can('sellerProductRequest-table')
                <li class="nav-item">
                    <a href="{{ route('sellerProductRequests.index') }}" class="nav-link {{ request()->routeIs('sellerProductRequests.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-bag"></i>
                        <p>
                            {{ __('messages.seller_product_requests') }}
                            @php
                                $pendingCount = \App\Models\SellerProductRequest::where('status', \App\Enums\SellerProductRequestStatus::PENDING)->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge badge-warning right">{{ $pendingCount }}</span>
                            @endif
                        </p>
                    </a>
                </li>
                @endcan

                <!-- Admin Seller Sales Management -->
                @can('admin-seller-sales-list')
                <li class="nav-item">
                    <a href="{{ route('admin.seller-sales.index') }}" class="nav-link {{ request()->routeIs('admin.seller-sales.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>{{ __('messages.seller_sales_management') }}</p>
                    </a>
                </li>
                @endcan

                <!-- Purchases Management -->
                @canany(['purchase-table', 'purchase-add', 'purchase-edit', 'purchase-delete'])
                <li class="nav-item">
                    <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>{{ __('messages.purchases') }}</p>
                    </a>
                </li>
                @endcanany

                <!-- Returns Management Group -->
                <li class="nav-item {{ request()->is('admin/returns*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-undo"></i>
                        <p>
                            {{ __('messages.returns') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Sales Returns -->
                        <li class="nav-item">
                            <a href="{{ route('admin.sales-returns.index') }}" class="nav-link {{ request()->routeIs('admin.sales-returns.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.sales_returns') }}</p>
                            </a>
                        </li>

                        <!-- Purchase Returns -->
                        <li class="nav-item">
                            <a href="{{ route('admin.purchase-returns.index') }}" class="nav-link {{ request()->routeIs('admin.purchase-returns.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.purchase_returns') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reports Group -->
                <li class="nav-item {{ request()->is('admin/reports/warehouse-movement*') || request()->is('admin/reports/providers*') || request()->is('admin/reports/orders*') || request()->is('admin/reports/sales-returns*') || request()->is('admin/reports/purchase-returns*') || request()->is('admin/reports/purchases*') || request()->is('admin/reports/customers*') || request()->is('admin/reports/events*') || request()->is('admin/reports/distribution-point-sales*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            {{ __('messages.reports') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.orders') }}" class="nav-link {{ request()->routeIs('admin.reports.orders') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.orders_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.salesReturns') }}" class="nav-link {{ request()->routeIs('admin.reports.salesReturns') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.sales_returns_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.purchaseReturns') }}" class="nav-link {{ request()->routeIs('admin.reports.purchaseReturns') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.purchase_returns_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.purchases') }}" class="nav-link {{ request()->routeIs('admin.reports.purchases') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.purchases_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.customers') }}" class="nav-link {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.customer_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.events') }}" class="nav-link {{ request()->routeIs('admin.reports.events') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.events_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.warehouseMovement') }}" class="nav-link {{ request()->routeIs('admin.reports.warehouseMovement') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.warehouse_movements') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.providers.index') }}" class="nav-link {{ request()->routeIs('admin.reports.providers.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.providers_report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.distributionPointSales.index') }}" class="nav-link {{ request()->routeIs('admin.reports.distributionPointSales.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('messages.distribution_point_sales_report') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>


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