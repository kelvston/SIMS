<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'Women’s Casual Two-Piece Sets'],
            ['name' => 'Kids V-neck Polo Shirts'],
        ];
        DB::table('categories')->insert($categories);

        $this->command->info('✅ 500 unique clothes seeded successfully!');
    }
}
