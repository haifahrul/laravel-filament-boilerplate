<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
  public function run()
  {
    $roles = ['super_admin', 'admin', 'supervisor', 'sales'];

    foreach ($roles as $role) {
      Role::firstOrCreate(['name' => $role]);
    }

    $this->command->info('✅ Roles created.');
  }
}