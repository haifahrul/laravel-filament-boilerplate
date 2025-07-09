<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SalesUserSeeder extends Seeder
{
  public function run(): void
  {
    // Buat 10 user dan assign role sales
    for ($i = 1; $i <= 15; $i++) {
      $user = User::updateOrCreate([
        'email' => "sales{$i}@example.com",
      ], [
        'name'     => "Sales {$i}",
        'password' => bcrypt('password'),
      ]);

      $user->assignRole('sales');
    }

    $this->command->info('✅ 10 sales users created.');
  }
}