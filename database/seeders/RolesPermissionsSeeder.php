<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = config('permissions.permissions');
        $rolesConfig = config('permissions.roles');

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        foreach ($rolesConfig as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($rolePerms === '*') {
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions($rolePerms);
            }
        }
    }
}
