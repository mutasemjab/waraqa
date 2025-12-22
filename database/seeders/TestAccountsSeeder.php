<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
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
        // Get roles
        $roles = Role::all();

        // Filter roles using collection methods
        $adminRole = $roles->filter(fn($role) => $role->name === 'admin')->first();
        $providerRole = $roles->filter(fn($role) => $role->name === 'provider')->first();
        $userRole = $roles->filter(fn($role) => $role->name === 'user')->first();
        $sellerRole = $roles->filter(fn($role) => $role->name === 'seller')->first();

        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'name' => 'Admin',
                'phone' => '966501111111',
                'password' => Hash::make('admin'),
                'activate' => 1,
            ]
        );
        if ($adminRole) {
            $adminUser->syncRoles($adminRole);
        }

        // Create Provider User with Provider Profile
        $providerUser = User::firstOrCreate(
            ['email' => 'provider@example.com'],
            [
                'username' => 'provider',
                'name' => 'Provider Test Account',
                'phone' => '966501234567',
                'password' => Hash::make('123456'),
                'activate' => 1,
            ]
        );
        if ($providerRole) {
            $providerUser->syncRoles($providerRole);
        }

        // Create Provider profile linked to provider user
        Provider::firstOrCreate(
            ['user_id' => $providerUser->id],
            [
                'description' => 'Test Provider Account',
                'status' => 'active',
                'rating' => 5.0,
            ]
        );

        // Create Regular User
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'username' => 'user',
                'name' => 'User Test Account',
                'phone' => '966509876543',
                'password' => Hash::make('123456'),
                'activate' => 1,
            ]
        );
        if ($userRole) {
            $regularUser->syncRoles($userRole);
        }

        // Create Seller User
        $sellerUser = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'username' => 'seller',
                'name' => 'Seller Test Account',
                'phone' => '966505555555',
                'password' => Hash::make('123456'),
                'activate' => 1,
            ]
        );
        if ($sellerRole) {
            $sellerUser->syncRoles($sellerRole);
        }
    }
}
