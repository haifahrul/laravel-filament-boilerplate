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
    // Buat user admin (opsional)
    $user = User::updateOrCreate([
      'email' => 'haruntofik@gmail.com',
    ], [
      'name'     => 'Admin',
      'password' => bcrypt('12345678'),
    ]);

    // Buat role admin
    $role = Role::firstOrCreate(['name' => 'admin']);

    // Hanya permission selain user management
    $permissions = Permission::all()->filter(function ($permission) {
      return !preg_match('/^(users|roles|permissions)\./', $permission->name);
    });

    // Assign permission ke role admin
    $role->syncPermissions($permissions);

    // Assign role ke user
    $user->assignRole($role);

    $this->command->info("✅ Admin user created with limited permissions.");
  }
}