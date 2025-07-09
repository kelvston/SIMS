<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to safely truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Brand::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $brands = [
            'Apple',
            'Samsung',
            'Huawei',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'OnePlus',
            'Realme',
            'Nokia',
            'Sony',
            'Motorola',
            'Google',
            'Tecno',
            'Infinix',
            'Itel',
            'LG',
            'Asus',
            'Lenovo',
            'HTC',
            'ZTE',
        ];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }

        $this->command->info('✅ Phone brands seeded successfully!');
    }
}
