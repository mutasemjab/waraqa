<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Dashboard
            'dashboard-view',

            // Role and Permission Management
            'role-table',
            'role-add',
            'role-edit',
            'role-delete',

            // Employee Management
            'employee-table',
            'employee-add',
            'employee-edit',
            'employee-delete',

            // User Management
            'user-table',
            'user-add',
            'user-edit',
            'user-delete',

            // Order Management
            'order-table',
            'order-add',
            'order-edit',
            'order-delete',

            // Setting Management
            'setting-table',
            'setting-add',
            'setting-edit',
            'setting-delete',

            // Category Management
            'category-table',
            'category-add',
            'category-edit',
            'category-delete',

            // Product Management
            'product-table',
            'product-add',
            'product-edit',
            'product-delete',
            'product-search',
            'product-available-quantity',

            // Provider Management
            'provider-table',
            'provider-add',
            'provider-edit',
            'provider-delete',

            // Country Management
            'country-table',
            'country-add',
            'country-edit',
            'country-delete',

            // Warehouse Management
            'warehouse-table',
            'warehouse-add',
            'warehouse-edit',
            'warehouse-delete',

            // Note Voucher Type Management
            'noteVoucherType-table',
            'noteVoucherType-add',
            'noteVoucherType-edit',
            'noteVoucherType-delete',

            // Note Voucher Management
            'noteVoucher-table',
            'noteVoucher-add',
            'noteVoucher-edit',
            'noteVoucher-delete',

            // User Department Management
            'user_dept-table',
            'user_dept-add',
            'user_dept-edit',
            'user_dept-delete',
            'user_dept-make_payment',
            'user_dept-view_summary',

            // Book Request Management
            'bookRequest-table',
            'bookRequest-add',
            'bookRequest-edit',
            'bookRequest-delete',
        ];

        $data = array_map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $permissions);

        Permission::insert($data);

        $adminRole = Role::where('name', 'admin')->first();

        // Assign all permissions to admin role
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }
    }
}
