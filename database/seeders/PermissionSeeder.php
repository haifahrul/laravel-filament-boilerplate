<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $resources = [
            'users',
            'roles',
            'permissions',
            'news',
            'ppdbs',
        ];

        $actions = [
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
        ];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionName = "{$resource}.{$action}";
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        $this->command->info('✅ Permissions created.');
    }
}
