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

            ['name' => 'Plain T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oversized T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Graphic T-Shirt Nike', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Graphic T-Shirt Adidas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Polo T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Round Neck T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'V-Neck T-Shirt', 'created_at' => $now, 'updated_at' => $now],
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
            ['name' => 'Classic Vest', 'created_at' => $now, 'updated_at' => $now],
            ['name' => ' Sleeveless Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Compression Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hooded T-Shirt', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Weekend T-Shirt', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | MEN TROUSERS & JEANS
            |--------------------------------------------------------------------------
            */

            ['name' => 'Slim Fit Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Skinny Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cargo Pants ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cargo Pants Khaki', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Trousers', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Trousers ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jogger Pants ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Track Pants Adidas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Track Pants Nike', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Short Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Khaki Shorts', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cotton Shorts', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Formal Office Pants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ripped Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baggy Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Streetwear Cargo Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Classic Denim Jeans', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chino Pants Brown', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chino Pants Cream', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Leather Pants ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Elastic Waist Trousers', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Men Sweatpants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Linen Pants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Military Cargo Pants', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Checked Official Trousers', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stretch Denim Jeans', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | WOMEN DRESSES
            |--------------------------------------------------------------------------
            */

            ['name' => 'Maxi Dress Floral', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bodycon Dress ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Official Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ankara Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dinner Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Party Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Long Sleeve Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Summer Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Casual Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Abaya ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Abaya Dubai Style', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jumpsuit Official', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ladies Suit 2 Piece', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Skater Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Velvet Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Open Shoulder Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wedding Guest Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'African Print Dress', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lace Dress', 'created_at' => $now, 'updated_at' => $now],
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
