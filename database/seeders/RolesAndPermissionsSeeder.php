<?php
////
////namespace Database\Seeders;
////
////use Illuminate\Database\Seeder;
////use Spatie\Permission\Models\Role;
////use Spatie\Permission\Models\Permission;
////
////class RolesAndPermissionsSeeder extends Seeder
////{
////    /**
////     * Run the database seeds.
////     */
////    public function run(): void
////    {
////        // Reset cached roles and permissions
////        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
////
////        // --- Create Permissions ---
////        // Inventory Permissions
////        Permission::firstOrCreate(['name' => 'view cashews']);
////        Permission::firstOrCreate(['name' => 'receive cashews']);
////        Permission::firstOrCreate(['name' => 'edit cashews']); // For future update/delete
////        Permission::firstOrCreate(['name' => 'delete cashews']); // For future delete
////
////        // Sales Permissions
////        Permission::firstOrCreate(['name' => 'view sales']);
////        Permission::firstOrCreate(['name' => 'create sales']);
////        Permission::firstOrCreate(['name' => 'edit sales']); // For future update
////        Permission::firstOrCreate(['name' => 'delete sales']); // For future delete
////
////        // Installment Permissions
////        Permission::firstOrCreate(['name' => 'view installments']);
////        Permission::firstOrCreate(['name' => 'record installment payments']);
////
////        // Report Permissions
////        Permission::firstOrCreate(['name' => 'view sales reports']);
////        Permission::firstOrCreate(['name' => 'view stock reports']);
////        Permission::firstOrCreate(['name' => 'view profit loss reports']);
////
////        // Admin Permissions (can do everything)
////        Permission::firstOrCreate(['name' => 'manage users']); // For creating/editing users
////        Permission::firstOrCreate(['name' => 'manage roles']); // For creating/editing roles/permissions
////
////        // --- Create Roles and Assign Permissions ---
////
////        // 1. Admin Role
////        $adminRole = Role::firstOrCreate(['name' => 'admin']);
////        $adminRole->givePermissionTo(Permission::all()); // Admin gets all permissions
////
////        // 2. Sales Manager Role
////        $salesManagerRole = Role::firstOrCreate(['name' => 'sales_manager']);
////        $salesManagerRole->givePermissionTo([
////            'view cashews', 'receive cashews',
////            'view sales', 'create sales', 'edit sales',
////            'view installments', 'record installment payments',
////            'view sales reports', 'view stock reports', 'view profit loss reports',
////        ]);
////
////        // 3. Staff Role (e.g., for daily operations)
////        $staffRole = Role::firstOrCreate(['name' => 'staff']);
////        $staffRole->givePermissionTo([
////            'view cashews', 'receive cashews',
////            'view sales', 'create sales',
////            'view installments', 'record installment payments',
////        ]);
////
////        // 4. Viewer Role (can only see reports and inventory)
////        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
////        $viewerRole->givePermissionTo([
////            'view cashews',
////            'view sales',
////            'view installments',
////            'view sales reports',
////            'view stock reports',
////            'view profit loss reports',
////        ]);
////
////        // --- Assign a Role to an Existing User (Example) ---
////        // Assuming you have at least one user created by Breeze's registration.
////        // You might want to make the first registered user an 'admin'.
////        $user = \App\Models\User::first(); // Gets the first user in the database
////
////        if ($user) {
////            // Check if the user already has a role to prevent re-assignment on re-seeding
////            if (!$user->hasRole('admin')) {
////                $user->assignRole('admin');
////                $this->command->info('Assigned "admin" role to the first user: ' . $user->email);
////            }
////        } else {
////            $this->command->warn('No user found to assign role. Please register a user first.');
////        }
////    }
////}
//
//
//namespace Database\Seeders;
//
//use Illuminate\Database\Seeder;
//use Spatie\Permission\Models\Role;
//use Spatie\Permission\Models\Permission;
//
//class RolesAndPermissionsSeeder extends Seeder
//{
//    /**
//     * Run the database seeds.
//     */
//    public function run(): void
//    {
//        // Reset cached roles and permissions
//        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
//
//        // --- Create Permissions ---
//
//        // Inventory Permissions
//        Permission::firstOrCreate(['name' => 'view cashews']);
//        Permission::firstOrCreate(['name' => 'receive cashews']);
//        Permission::firstOrCreate(['name' => 'edit cashews']);
//        Permission::firstOrCreate(['name' => 'delete cashews']);
//
//        // Sales Permissions
//        Permission::firstOrCreate(['name' => 'view sales']);
//        Permission::firstOrCreate(['name' => 'create sales']);
//        Permission::firstOrCreate(['name' => 'edit sales']);
//        Permission::firstOrCreate(['name' => 'delete sales']);
//
//        // Expense Permissions
//        Permission::firstOrCreate(['name' => 'create expenses']);
//        Permission::firstOrCreate(['name' => 'view expenses']); // new
//
//        // Installment Permissions
//        Permission::firstOrCreate(['name' => 'view installments']);
//        Permission::firstOrCreate(['name' => 'record installment payments']);
//
//        // Report Permissions
//        Permission::firstOrCreate(['name' => 'view sales reports']);
//        Permission::firstOrCreate(['name' => 'view stock reports']);
//        Permission::firstOrCreate(['name' => 'view profit loss reports']);
//
//        // Admin / Management Permissions
//        Permission::firstOrCreate(['name' => 'manage users']);
//        Permission::firstOrCreate(['name' => 'manage roles']);
//        Permission::firstOrCreate(['name' => 'manage brands']); // new
//
//        // Dashboard Permission
//        Permission::firstOrCreate(['name' => 'view dashboard']); // new
//
//        // --- Create Roles and Assign Permissions ---
//
//        $adminRole = Role::firstOrCreate(['name' => 'admin']);
//        $adminRole->givePermissionTo(Permission::all());
//
//        $salesManagerRole = Role::firstOrCreate(['name' => 'sales_manager']);
//        $salesManagerRole->givePermissionTo([
//            'view cashews', 'receive cashews',
//            'view sales', 'create sales', 'edit sales',
//            'view installments', 'record installment payments',
//            'view sales reports', 'view stock reports', 'view profit loss reports',
//        ]);
//
//        $staffRole = Role::firstOrCreate(['name' => 'staff']);
//        $staffRole->givePermissionTo([
//            'view cashews', 'receive cashews',
//            'view sales', 'create sales',
//            'view installments', 'record installment payments',
//        ]);
//
//        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
//        $viewerRole->givePermissionTo([
//            'view cashews', 'view sales', 'view installments',
//            'view sales reports', 'view stock reports', 'view profit loss reports',
//        ]);
//
//        // --- Assign all roles and permissions to the first admin user ---
//        $user = \App\Models\User::where('email', 'admin@tari.com')->first();
//
//        if ($user) {
//            $user->syncRoles(Role::all());
//            $user->syncPermissions(Permission::all());
//            $this->command->info('Admin user assigned all roles and permissions.');
//        } else {
//            $this->command->warn('No admin user found. Create a user with email admin@yoga.com first.');
//        }
//    }
//}


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | ALL PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Phones
            'view phones',
            'receive phones',
            'edit phones',
            'delete phones',

            // Sales
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',

            // Expenses
            'create expenses',
            'view expenses',

            // Installments
            'view installments',
            'record installment payments',

            // Reports
            'view sales reports',
            'view stock reports',
            'view profit loss reports',
            'general reports',
            'view general reports',

            // Dashboard
            'view dashboard',

            // Products & Brands
            'manage products',
            'manage brands',

            // Users & Roles
            'manage users',
            'manage roles',

            // Medicines
            'receive medicines',

            // Cashew
            'receive cashew',
        ];

        /*
        |--------------------------------------------------------------------------
        | CREATE PERMISSIONS
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        // Admin
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions(Permission::all());

        // Manager
        $managerRole = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'web',
        ]);

        $managerRole->syncPermissions([
            'view phones',
            'receive phones',
            'edit phones',

            'view sales',
            'create sales',
            'edit sales',

            'create expenses',
            'view expenses',

            'view installments',
            'record installment payments',

            'view sales reports',
            'view stock reports',
            'view profit loss reports',

            'general reports',
            'view general reports',

            'view dashboard',

            'manage products',
            'manage brands',
        ]);

        // Staff
        $staffRole = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web',
        ]);

        $staffRole->syncPermissions([
            'view phones',
            'receive phones',

            'view sales',
            'create sales',

            'view installments',
            'record installment payments',

            'view dashboard',
        ]);

        // Viewer
        $viewerRole = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => 'web',
        ]);

        $viewerRole->syncPermissions([
            'view phones',
            'view sales',
            'view expenses',

            'view installments',

            'view sales reports',
            'view stock reports',
            'view profit loss reports',

            'general reports',
            'view general reports',

            'view dashboard',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASSIGN ADMIN ROLE TO USER
        |--------------------------------------------------------------------------
        */

        $user = \App\Models\User::where('email', 'admin@tari.com')->first();

        if ($user) {

            $user->syncRoles([$adminRole]);

            $user->syncPermissions(Permission::all());

            $this->command->info(
                'Admin user assigned all roles and permissions.'
            );

        } else {

            $this->command->warn(
                'No admin user found. Create admin@yoga.com first.'
            );
        }
    }
}
