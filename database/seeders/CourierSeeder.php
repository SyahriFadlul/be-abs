<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $data = [
            ['courier' => 'Instant', 'price' => 50000,'estimated_delivery_time' => 5],
            ['courier' => 'Reguler', 'price' => 20000,'estimated_delivery_time' => 24],
            ['courier' => 'Economy', 'price' => 10000,'estimated_delivery_time' => 48]
        ];

        foreach ($data as $item) {
            Courier::create($item);            
        }
    }
}
