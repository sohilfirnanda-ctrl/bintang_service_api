<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Ganti Oli Mesin',  'description' => 'Penggantian oli mesin beserta pengecekan level oli.',          'price' => 55000,  'duration_minutes' => 20],
            ['name' => 'Servis Ringan',    'description' => 'Pembersihan karburator/injektor, setel rantai, dan cek busi.', 'price' => 75000,  'duration_minutes' => 45],
            ['name' => 'Servis Besar',     'description' => 'Pemeriksaan menyeluruh mesin, kelistrikan, dan kaki-kaki.',    'price' => 250000, 'duration_minutes' => 180],
            ['name' => 'Ganti Kampas Rem', 'description' => 'Penggantian kampas rem depan atau belakang.',                  'price' => 60000,  'duration_minutes' => 30],
            ['name' => 'Servis CVT',       'description' => 'Pembersihan dan pelumasan komponen CVT motor matic.',          'price' => 90000,  'duration_minutes' => 60],
            ['name' => 'Cuci Motor',       'description' => 'Cuci bersih bodi dan mesin motor.',                            'price' => 20000,  'duration_minutes' => 20],
        ];

        foreach ($services as $service) {
            Service::create($service + ['is_active' => true]);
        }
    }
}