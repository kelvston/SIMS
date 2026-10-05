<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ([
            'edit expenses', 'delete expenses',
            'view motor services', 'create motor service jobs', 'manage motor services',
            'update assigned service jobs', 'view service reports',
        ] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        foreach (['mechanic', 'mechanics'] as $roleName) {
            Role::findOrCreate($roleName, 'web')->syncPermissions(['update assigned service jobs']);
        }

        foreach (['admin'] as $roleName) {
            if ($role = Role::where('name', $roleName)->where('guard_name', 'web')->first()) {
                $role->givePermissionTo(Permission::all());
            }
        }

        if ($role = Role::where('name', 'sales_manager')->where('guard_name', 'web')->first()) {
            $role->givePermissionTo(['view motor services', 'create motor service jobs', 'manage motor services', 'view service reports']);
        }

        if ($role = Role::where('name', 'staff')->where('guard_name', 'web')->first()) {
            $role->givePermissionTo(['view motor services', 'create motor service jobs']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Permissions and roles may have been assigned by administrators; keep them on rollback.
    }
};
