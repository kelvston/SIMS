<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Cables & Chargers',
            'Cases & Protectors',
            'Headphones & Audio',
            'Storage Devices',
            'Wearables',
            'Power Banks',
            'Car Mounts',
            'Gaming Accessories',
            'Stylus Pens',
            'Drones',
        ];

        foreach ($categories as $category) {
            DB::table('cosmetic_categories')->insert([
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
