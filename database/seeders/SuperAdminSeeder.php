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
      'email' => 'haifahrul@gmail.com',
    ], [
      'name'     => 'Super Admin',
      'password' => bcrypt('1qaz@WSXkahf'),
    ]);

    // ✅ Buat role super_admin
    $role = Role::firstOrCreate(['name' => 'super_admin']);

    // ✅ Assign semua permission ke role
    $permissions = Permission::all();
    $role->syncPermissions($permissions);

    // ✅ Assign role ke user
    $user->assignRole($role);

    $this->command->info("✅ User 'haifahrul@gmail.com' created and assigned role + permissions.");
  }
}
