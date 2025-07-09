<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
  public function run()
  {
    // ✅ Buat user super admin
    $user = User::updateOrCreate([
      'email' => 'superadmin@example.com',
    ], [
      'name'     => 'Super Admin',
      'password' => bcrypt('password'),
    ]);

    // ✅ Buat role super_admin
    $role = Role::firstOrCreate(['name' => 'super_admin']);

    // ✅ Assign semua permission ke role
    $permissions = Permission::all();
    $role->syncPermissions($permissions);

    // ✅ Assign role ke user
    $user->assignRole($role);

    $this->command->info("✅ User 'superadmin@example.com' created and assigned role + permissions.");
  }
}
