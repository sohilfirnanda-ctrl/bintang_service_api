<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,  // 6 data (1 admin + 5 pelanggan)
            ServiceSeeder::class,   // 6 data
            BookingSeeder::class,   // 5 booking + 11 booking_details
        ]);
    }
}