<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashewProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $products = [

            /*
            |--------------------------------------------------------------------------
            | MEN T-SHIRTS
            |--------------------------------------------------------------------------
            */

            ['name' => 'Plain White T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Plain Black T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oversized T-Shirt Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oversized T-Shirt White', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Graphic T-Shirt Nike', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Graphic T-Shirt Adidas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Polo T-Shirt Red', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Polo T-Shirt Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Polo T-Shirt Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Round Neck T-Shirt Grey', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'V-Neck T-Shirt White', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Long Sleeve T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Slim Fit T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cotton T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Designer T-Shirt LV', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Designer T-Shirt Gucci', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Football Jersey Arsenal', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Football Jersey Chelsea', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Football Jersey Barcelona', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Football Jersey Real Madrid', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gym T-Shirt Dry Fit', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tie Dye T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Streetwear T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Printed Anime T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cargo Style T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Classic White Vest', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Black Sleeveless Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Compression Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hooded T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Weekend T-Shirt', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | MEN TROUSERS & JEANS
            |--------------------------------------------------------------------------
            */

            ['name' => 'Slim Fit Jeans Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Slim Fit Jeans Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Skinny Jeans Grey', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cargo Pants Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cargo Pants Khaki', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Trousers Navy Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Trousers Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jogger Pants Grey', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jogger Pants Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Track Pants Adidas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Track Pants Nike', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Short Jeans Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Short Jeans Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Khaki Shorts', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cotton Shorts Grey', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Formal Office Pants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ripped Jeans Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ripped Jeans Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baggy Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Streetwear Cargo Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Classic Denim Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chino Pants Brown', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chino Pants Cream', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Leather Pants Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Elastic Waist Trousers', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Men Sweatpants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Linen Pants White', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Military Cargo Pants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Checked Official Trousers', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stretch Denim Jeans', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | WOMEN DRESSES
            |--------------------------------------------------------------------------
            */

            ['name' => 'Maxi Dress Floral', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bodycon Dress Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bodycon Dress Red', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Dress Navy Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ankara Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dinner Dress Gold', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Party Dress Silver', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Long Sleeve Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Summer Dress Yellow', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Dress Pink', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Abaya Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Abaya Dubai Style', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jumpsuit Official', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ladies Suit 2 Piece', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Skater Dress White', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Velvet Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Open Shoulder Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wedding Guest Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'African Print Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lace Dress White', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maternity Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kimono Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Office Pencil Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sleeveless Maxi Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pleated Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chiffon Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Beach Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wrap Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Turkey Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Luxury Evening Dress', 'created_at' => $now, 'updated_at' => $now],


        ];

        DB::table('products')->insertOrIgnore($products);
    }
}
