<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Password semua akun seeder: password123
        $customers = [
            ['name' => 'Admin Bintang Service', 'email' => 'admin@bintangservice.test', 'phone' => '081200000001', 'role' => 'admin'],
            ['name' => 'Rahmat Hidayat',        'email' => 'rahmat@example.com',        'phone' => '081277700011', 'role' => 'customer'],
            ['name' => 'Nurul Aini',            'email' => 'nurul@example.com',         'phone' => '081277700022', 'role' => 'customer'],
            ['name' => 'Fajar Maulana',         'email' => 'fajar@example.com',         'phone' => '081277700033', 'role' => 'customer'],
            ['name' => 'Siti Rahmah',           'email' => 'siti@example.com',          'phone' => '081277700044', 'role' => 'customer'],
            ['name' => 'Teuku Rizki',           'email' => 'rizki@example.com',         'phone' => '081277700055', 'role' => 'customer'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer + ['password' => 'password123']);
        }
    }
}