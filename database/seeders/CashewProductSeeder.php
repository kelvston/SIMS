<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashewProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data for common cashew products
        $products = [
            ['name' => 'Raw Cashew Nuts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Roasted Cashew Nuts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Salted Cashew Nuts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Kernels W180', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Kernels W240', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Kernels W320', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Kernels W450', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Broken Cashew Pieces', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Nut Flour', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Butter', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Cashew Milk', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Honey Roasted Cashews', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Spicy Cashew Nuts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Chocolate Coated Cashews', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Organic Cashew Nuts', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Premium Export Cashews', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        // Insert the data into the 'model' table
        DB::table('products')->insert($products);
    }
}
