<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhoneModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data for common phone models
        $models = [
            // iPhone models
            ['name' => 'iPhone 15 Pro Max', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone 15 Pro', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone 15', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone 14 Pro Max', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone 14', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone 13', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'iPhone SE (3rd Gen)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // Samsung models
            ['name' => 'Galaxy S24 Ultra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Galaxy S24', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Galaxy Z Fold 5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Galaxy Z Flip 5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Galaxy A54', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // Google Pixel models
            ['name' => 'Pixel 8 Pro', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Pixel 8', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Pixel 7a', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            // Other brands
            ['name' => 'OnePlus 12', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Xiaomi 14 Ultra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Oppo Find X7 Ultra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Motorola Edge 50 Ultra', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        // Insert the data into the 'models' table
        DB::table('model')->insert($models);
    }
}
