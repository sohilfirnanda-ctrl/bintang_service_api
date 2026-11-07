<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::where('role', 'customer')->orderBy('id')->get();
        $services = Service::all()->keyBy('name');

        // Setiap baris: [indeks pelanggan, motor, plat, +hari, jam, keluhan, status, daftar layanan]
        $rows = [
            [0, 'Honda Beat 2021',    'BL 1234 AB', -7, '09:00', 'Tarikan berat.',          'completed', ['Servis Ringan', 'Ganti Oli Mesin']],
            [1, 'Yamaha NMAX 2022',   'BL 5678 CD', -3, '10:30', 'CVT berbunyi.',           'completed', ['Servis CVT', 'Cuci Motor']],
            [2, 'Honda Vario 125',    'BL 2468 EF',  1, '13:00', null,                      'confirmed', ['Ganti Oli Mesin']],
            [3, 'Suzuki Satria F150', 'BL 1357 GH',  2, '08:30', 'Rem depan kurang pakem.', 'pending',   ['Ganti Kampas Rem', 'Servis Ringan']],
            [4, 'Yamaha Mio M3',      'BL 9753 IJ',  4, '15:00', 'Servis rutin 10.000 km.', 'pending',   ['Servis Besar', 'Ganti Oli Mesin', 'Cuci Motor']],
        ];

        foreach ($rows as [$idx, $motor, $plate, $days, $time, $complaint, $status, $serviceNames]) {
            $booking = Booking::create([
                'booking_code' => Booking::generateCode(),
                'customer_id' => $customers[$idx]->id,
                'motor_type' => $motor,
                'plate_number' => $plate,
                'booking_date' => now()->addDays($days)->toDateString(),
                'booking_time' => $time,
                'complaint' => $complaint,
                'status' => $status,
            ]);

            $total = 0;
            foreach ($serviceNames as $name) {
                $service = $services[$name];
                $booking->details()->create([
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'price' => $service->price,
                ]);
                $total += $service->price;
            }

            $booking->update(['total_price' => $total]);
        }
    }
}