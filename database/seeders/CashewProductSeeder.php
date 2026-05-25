<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashewProductSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $products = [

            /*
            |--------------------------------------------------------------------------
            | Vinywaji (Beverages)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Coca Cola 350ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coca Cola 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coca Cola 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coca Cola 2L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pepsi 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pepsi 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fanta Orange 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fanta Blackcurrant 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fanta Pineapple 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sprite 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mirinda 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mountain Dew 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Azam Energy Drink', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sayona Energy Drink', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Red Bull 250ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maji Uhai 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maji Uhai 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maji Uhai 1.5L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maji Kilimanjaro 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maji Pwani 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Juisi ya Embe 300ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Juisi ya Nanasi 300ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Juisi ya Chungwa 300ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Juisi ya Zabibu 300ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maziwa Fresh 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maziwa Fresh 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mtindi 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Yoghurt Strawberry', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Yoghurt Vanilla', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Milo Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Horlicks Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ribena 300ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chai Lipton Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kahawa Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ovaltine Sachet', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Nafaka na Vyakula Vikavu (Grains & Dry Foods)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Mchele Super 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mchele Super 2Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mchele Mbeya 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mchele Basmati 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maharage 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Maharage Nusu Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dengu 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Njegere 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Choroko 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sukari 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sukari 2Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chumvi Packet 500g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Unga wa Sembe 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Unga wa Sembe 2Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Unga wa Dona 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Unga wa Ngano 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oat Meal Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mtama 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ulezi 1Kg', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Mafuta na Viungo (Oils & Spices)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Mafuta ya Kupikia 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mafuta ya Kupikia 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mafuta ya Kupikia 2L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mafuta ya Ufuta 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mafuta ya Nazi 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Blue Band Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Blue Band Medium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Royco Cube 10pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mchuzi Mix Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pilipili Manga Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bizari ya Supu Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bizari ya Kuku Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tangawizi Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Karafuu Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dalasini Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coriander Powder', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Turmeric Powder', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tomato Sauce Bottle', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pilipili Sauce Bottle', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Soy Sauce Bottle', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mayonnaise Jar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jam Jar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Honey Jar 250g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Peanut Butter Jar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Coconut Cream Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vinegar Bottle', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Vyakula vya Makopo na Pakiti (Canned & Packaged)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Sardine Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tuna Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dagaa Pack 100g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Corned Beef Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baked Beans Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tomato Paste Tin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Spaghetti Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Macaroni Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vermicelli Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Instant Noodles Indomie', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Instant Noodles Supermama', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Uji wa Mtoto Packet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Corn Flakes Pack', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Mkate na Keki (Bread & Snacks)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Mkate Large', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mkate Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Biscuits Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cookies Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cracker Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chips Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Popcorn Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Karanga Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pipi Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Big G', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chewing Gum', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chocolate Bar', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mayai Tray 30pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mayai 6pcs', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Sabuni ya Kufulia na Vyombo (Laundry & Dishes)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Omo 500g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Omo 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Omo 2Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ariel 500g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sunlight Washing Powder 500g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni ya Taifa 1Kg', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni ya Mche 500g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Comfort Fabric Softener 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jik Bleach 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jik Bleach 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sunlight Dish Liquid 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vim Powder', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Harpic Toilet Cleaner 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Domestos Toilet Gel', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Flash Floor Cleaner 500ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Msafi Floor Cleaner 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Broom', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mop', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dustpan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Trash Bag Roll', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sponge ya Vyombo', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Sabuni ya Kuogea na Mwili (Personal Care)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Sabuni Lux', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni Dettol', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni Imperial Leather', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni Lifebuoy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sabuni Dove', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shower Gel 250ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shampoo Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shampoo Bottle 200ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Conditioner Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mafuta ya Nywele', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Petroleum Jelly 50g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Petroleum Jelly 250g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Body Lotion 200ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Body Lotion 400ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Deo Spray Men', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Deo Spray Women', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Roll-on Deodorant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cologne Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Perfume Small Bottle', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Toothpaste Colgate 75ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Toothpaste Closeup 75ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Toothpaste Sensodyne', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mswaki wa Plastic', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Floss ya Meno', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Razor Blade Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shaving Cream', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cotton Buds Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cotton Wool Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hand Sanitizer 100ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Antiseptic Liquid 100ml', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Taulo na Usafi wa Kike (Feminine & Baby Care)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Taulo za Kike Always', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Taulo za Kike Kotex', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Taulo za Kike Stayfree', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Diapers Pampers Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Diapers Pampers Medium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Diapers Pampers Large', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Diapers Molfix Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Diapers Molfix Medium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baby Wipes Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baby Powder 100g', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baby Lotion 200ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Baby Shampoo 200ml', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Dawa za Kawaida OTC (Basic Medicines)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Panadol Tablet Strip', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Brufen 200mg Strip', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Aspirin Tablet Strip', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ORS Sachet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vitamin C Tablet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Antacid Tablet', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cough Syrup 100ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Eye Drops', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Betadine 30ml', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Plaster Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bandage Roll', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mosquito Coil Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mosquito Repellent Spray', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Condom Pack 3pcs', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Simu na Umeme (Mobile & Electronics)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Luku Voucher 1000', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Luku Voucher 2000', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Luku Voucher 5000', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Luku Voucher 10000', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Airtime Tigo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Airtime Vodacom', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Airtime Airtel', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Airtime Halotel', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Airtime TTCL', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Charging Cable Type-C', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Charging Cable Micro USB', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Earphones', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Phone Case', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Screen Protector', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'USB Adapter 5W', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'AA Batteries Pack 4pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'AAA Batteries Pack 4pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Torch / Tochi', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Extension Cable 3m', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bulb LED 9W', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bulb LED 15W', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Jikoni na Nyumbani (Kitchen & Household)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Sufuria Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sufuria Medium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sufuria Large', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bakuli Pack 6pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sahani Pack 6pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Glasi Pack 6pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kisu cha Jikoni', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mwiko wa Mbao', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mwiko wa Plastic', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ndoo Small 5L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ndoo Large 10L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ndoo Large 20L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Beseni la Kufulia', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chombo cha Maji Plastiki 20L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hanger Pack 10pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pegs Pack 20pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Aluminium Foil Roll', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cling Wrap Roll', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Plastic Bag Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Thermos Flask 1L', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cutting Board', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grater ya Jikoni', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Vitu vya Shule na Ofisi (Stationery)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Exercise Book 96 pages', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Exercise Book 64 pages', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kalamu ya Wino Blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kalamu ya Wino Black', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pencil HB', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rula 30cm', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rubber / Eraser', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sharpener', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Marker Pen', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Highlighter Pen', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stapler Small', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Staple Pins Box', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Selotape Roll', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Envelope Pack 10pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'A4 Paper Ream', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Scissors', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Glue Stick', 'created_at' => $now, 'updated_at' => $now],

            /*
            |--------------------------------------------------------------------------
            | Kiberiti, Mishumaa na Mengine (Misc)
            |--------------------------------------------------------------------------
            */
            ['name' => 'Kiberiti Box', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lighter', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mishumaa Pack 10pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mishumaa Kubwa 2pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rubber Band Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Safety Pin Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sewing Thread', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sindano Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wire Hanger 5pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tissue Pack 200 sheets', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tissue Roll 6pcs', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Newspaper', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ufunguo wa Mkanda / Tape Measure', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Nails Pack', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Superglue', 'created_at' => $now, 'updated_at' => $now],

        ];

//        DB::table('products')->insert($products);
        DB::table('products')->insertOrIgnore($products);

    }
}
