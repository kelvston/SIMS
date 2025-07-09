<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Create Permissions ---
        // Inventory Permissions
        Permission::firstOrCreate(['name' => 'view phones']);
        Permission::firstOrCreate(['name' => 'receive phones']);
        Permission::firstOrCreate(['name' => 'edit phones']); // For future update/delete
        Permission::firstOrCreate(['name' => 'delete phones']); // For future delete

        // Sales Permissions
        Permission::firstOrCreate(['name' => 'view sales']);
        Permission::firstOrCreate(['name' => 'create sales']);
        Permission::firstOrCreate(['name' => 'edit sales']); // For future update
        Permission::firstOrCreate(['name' => 'delete sales']); // For future delete

        // Installment Permissions
        Permission::firstOrCreate(['name' => 'view installments']);
        Permission::firstOrCreate(['name' => 'record installment payments']);

        // Report Permissions
        Permission::firstOrCreate(['name' => 'view sales reports']);
        Permission::firstOrCreate(['name' => 'view stock reports']);
        Permission::firstOrCreate(['name' => 'view profit loss reports']);

        // Admin Permissions (can do everything)
        Permission::firstOrCreate(['name' => 'manage users']); // For creating/editing users
        Permission::firstOrCreate(['name' => 'manage roles']); // For creating/editing roles/permissions

        // --- Create Roles and Assign Permissions ---

        // 1. Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Admin gets all permissions

        // 2. Sales Manager Role
        $salesManagerRole = Role::firstOrCreate(['name' => 'sales_manager']);
        $salesManagerRole->givePermissionTo([
            'view phones', 'receive phones',
            'view sales', 'create sales', 'edit sales',
            'view installments', 'record installment payments',
            'view sales reports', 'view stock reports', 'view profit loss reports',
        ]);

        // 3. Staff Role (e.g., for daily operations)
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view phones', 'receive phones',
            'view sales', 'create sales',
            'view installments', 'record installment payments',
        ]);

        // 4. Viewer Role (can only see reports and inventory)
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view phones',
            'view sales',
            'view installments',
            'view sales reports',
            'view stock reports',
            'view profit loss reports',
        ]);

        // --- Assign a Role to an Existing User (Example) ---
        // Assuming you have at least one user created by Breeze's registration.
        // You might want to make the first registered user an 'admin'.
        $user = \App\Models\User::first(); // Gets the first user in the database

        if ($user) {
            // Check if the user already has a role to prevent re-assignment on re-seeding
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $this->command->info('Assigned "admin" role to the first user: ' . $user->email);
            }
        } else {
            $this->command->warn('No user found to assign role. Please register a user first.');
        }
    }
}
