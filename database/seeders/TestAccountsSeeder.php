<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Provider;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin Account
        $admin = Admin::firstOrCreate(
            ['username' => 'admin_test'],
            [
                'name' => 'Admin Test Account',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'is_super' => true,
            ]
        );

        // Assign all admin permissions to the test admin
        $permissions = Permission::where('guard_name', 'admin')->get();
        if ($permissions->count() > 0) {
            $admin->syncPermissions($permissions);
        }

        // Create Provider Account
        Provider::firstOrCreate(
            ['phone' => '966501234567'],
            [
                'name' => 'Provider Test Account',
                'email' => 'provider@example.com',
                'password' => Hash::make('password123'),
                'activate' => 1,
            ]
        );

        // Create User Account
        User::firstOrCreate(
            ['phone' => '966509876543'],
            [
                'name' => 'User Test Account',
                'email' => 'user@example.com',
                'password' => Hash::make('password123'),
                'activate' => 1,
            ]
        );

    }
}