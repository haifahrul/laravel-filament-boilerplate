<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Product;

class DummyDataSeeder extends Seeder
{
  public function run(): void
  {
    $products = [
      ['name' => 'Produk A', 'price' => 15000, 'sku' => 'SKU-A001'],
      ['name' => 'Produk B', 'price' => 25000, 'sku' => 'SKU-B001'],
      ['name' => 'Produk C', 'price' => 35000, 'sku' => 'SKU-C001'],
      ['name' => 'Produk D', 'price' => 50000, 'sku' => 'SKU-D001'],
      ['name' => 'Produk E', 'price' => 100000, 'sku' => 'SKU-E001'],
    ];

    foreach ($products as $product) {
      Product::updateOrCreate(['name' => $product['name']], $product);
    }

    $this->command->info('✅ Produk berhasil dibuat.');

    // Ambil semua sales
    $salesUsers = User::role('sales')->pluck('id');

    if ($salesUsers->isEmpty()) {
      $this->command->warn('⚠️ Tidak ada user dengan role sales. Jalankan SalesUserSeeder dulu.');
      return;
    }

    $customers = [
      [
        'name'          => 'Toko Sinar Abadi',
        'address'       => 'Jl. Merdeka No.12',
        'contact'       => '081234567890',
        'business_type' => 'Retail',
        'latitude'      => -6.917464,
        'longitude'     => 107.619123,
      ],
      [
        'name'          => 'Warung Makmur',
        'address'       => 'Jl. Cihampelas No.7',
        'contact'       => '081298765432',
        'business_type' => 'Warung',
        'latitude'      => -6.900000,
        'longitude'     => 107.600000,
      ],
      [
        'name'          => 'Grosir Jaya Abadi',
        'address'       => 'Jl. Soekarno Hatta No.22',
        'contact'       => '089512345678',
        'business_type' => 'Grosir',
        'latitude'      => -6.930000,
        'longitude'     => 107.610000,
      ],
      [
        'name'          => 'MiniMart Sejati',
        'address'       => 'Jl. Setiabudi No.88',
        'contact'       => '082112345678',
        'business_type' => 'Minimarket',
        'latitude'      => -6.920000,
        'longitude'     => 107.630000,
      ],
      [
        'name'          => 'Toko Amanah',
        'address'       => 'Jl. Asia Afrika No.30',
        'contact'       => '083856789012',
        'business_type' => 'Retail',
        'latitude'      => -6.926789,
        'longitude'     => 107.622345,
      ],
    ];

    foreach ($customers as $customer) {
      Customer::updateOrCreate(
        ['name' => $customer['name']],
        array_merge($customer, [
          'user_id' => $salesUsers->random(),
        ])
      );
    }

    $this->command->info('✅ 5 customer berhasil dibuat & di-assign ke sales.');
  }
}
