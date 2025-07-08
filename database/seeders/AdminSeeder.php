<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
  public function run()
  {
    $admin = User::updateOrCreate([
      'email' => 'admin@example.com',
    ], [
      'name'     => 'Super Admin',
      'password' => bcrypt('password'),
    ]);

    $role = Role::firstOrCreate(['name' => 'admin']);

    // Tambahkan permissions global
    $permissions = ['manage users', 'view dashboard'];

    foreach ($permissions as $perm) {
      $p = Permission::firstOrCreate(['name' => $perm]);
      $role->givePermissionTo($p);
    }

    $admin->assignRole($role);
  }
}
