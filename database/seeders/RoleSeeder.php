<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
  public function run()
  {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'supervisor']);
    Role::create(['name' => 'sales']);
  }
}