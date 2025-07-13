<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\PhoneModel;
use App\Models\Color;

class BrandModelColorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Apple' => [
                'iPhone 13' => ['Blue', 'Pink', 'Midnight', 'Starlight', 'Red'],
                'iPhone 14 Pro' => ['Deep Purple', 'Gold', 'Silver', 'Space Black'],
                'iPhone 15 Pro Max' => ['Titanium Black', 'Titanium Blue', 'Titanium White'],
                'iPhone SE' => ['Black', 'White', 'Red'],
                'iPhone XR' => ['White', 'Coral', 'Black', 'Red'],
                'iPhone 12 Mini' => ['Purple', 'Blue', 'Green'],
            ],
            'Samsung' => [
                'Galaxy S21' => ['Phantom Gray', 'Phantom White', 'Phantom Violet'],
                'Galaxy A53' => ['Awesome Black', 'Awesome White', 'Awesome Blue'],
                'Galaxy Note 20' => ['Mystic Bronze', 'Mystic Gray'],
                'Galaxy S22 Ultra' => ['Green', 'Phantom Black'],
                'Galaxy Z Flip 5' => ['Mint', 'Graphite', 'Cream', 'Lavender'],
                'Galaxy M14' => ['Berry Blue', 'Smoky Teal'],
            ],
            'Huawei' => [
                'P40 Pro' => ['Silver Frost', 'Black', 'Blush Gold'],
                'Mate 40 Pro' => ['Mystic Silver', 'White', 'Black'],
                'Nova 9' => ['Starry Blue', 'Crush Green'],
                'Y9a' => ['Midnight Black', 'Sakura Pink'],
                'Nova Y90' => ['Crystal Blue', 'Midnight Black'],
            ],
            'Xiaomi' => [
                'Redmi Note 11' => ['Graphite Gray', 'Twilight Blue'],
                'Mi 11 Ultra' => ['Ceramic White', 'Ceramic Black'],
                'Poco X5 Pro' => ['Yellow', 'Black', 'Blue'],
                'Redmi 12C' => ['Lavender Purple', 'Mint Green'],
                'Redmi K60 Pro' => ['Black', 'Green', 'White'],
                'Xiaomi 13 Pro' => ['Ceramic Black', 'Ceramic White'],
            ],
            'Oppo' => [
                'Reno8' => ['Shimmer Gold', 'Shimmer Black'],
                'F21 Pro' => ['Cosmic Black', 'Sunset Orange'],
                'A77' => ['Sky Blue', 'Sunset Orange'],
                'Find X5' => ['White', 'Black'],
                'A98 5G' => ['Cool Black', 'Dreamy Blue'],
            ],
            'Vivo' => [
                'V23 Pro' => ['Sunshine Gold', 'Stardust Black'],
                'Y33s' => ['Mirror Black', 'Midday Dream'],
                'X80' => ['Cosmic Black', 'Urban Blue'],
                'Y21' => ['Diamond Glow', 'Midnight Blue'],
                'T2 Pro 5G' => ['New Moon Black', 'Sunlit Gold'],
            ],
            'OnePlus' => [
                'OnePlus 10 Pro' => ['Volcanic Black', 'Emerald Forest'],
                'Nord CE 3' => ['Aqua Surge', 'Gray Shimmer'],
                'OnePlus 11R' => ['Sonic Black', 'Galactic Silver'],
                'OnePlus Nord N300' => ['Midnight Jade'],
            ],
            'Realme' => [
                'Realme 11 Pro' => ['Astral Black', 'Sunrise Beige'],
                'C55' => ['Sunshower', 'Rainy Night'],
                'Narzo 50' => ['Speed Blue', 'Speed Black'],
                'GT Neo 3' => ['Nitro Blue', 'Sprint White'],
                'Realme 12 Plus' => ['Navigator Beige', 'Pioneer Green'],
            ],
            'Nokia' => [
                'G21' => ['Nordic Blue', 'Dusk'],
                'C30' => ['Green', 'White'],
                'X10' => ['Forest', 'Snow'],
                '2.4' => ['Charcoal', 'Dusk'],
                'C32' => ['Autumn Green', 'Charcoal'],
            ],
            'Sony' => [
                'Xperia 1 IV' => ['Black', 'White', 'Purple'],
                'Xperia 5 III' => ['Green', 'Black'],
                'Xperia 10 Plus' => ['Navy', 'Silver'],
                'Xperia L4' => ['Blue', 'Black'],
            ],
            'Motorola' => [
                'Moto G Power' => ['Flash Gray', 'Polar Silver'],
                'Edge 20' => ['Frosted Gray', 'Frosted White'],
                'Razr 5G' => ['Polished Graphite', 'Blush Gold'],
                'Moto E7' => ['Mineral Gray', 'Satin Coral'],
                'Moto G Stylus 5G' => ['Steel Blue'],
            ],
            'Google' => [
                'Pixel 6' => ['Stormy Black', 'Sorta Seafoam'],
                'Pixel 7 Pro' => ['Hazel', 'Snow', 'Obsidian'],
                'Pixel 6a' => ['Charcoal', 'Chalk'],
                'Pixel Fold' => ['Porcelain', 'Obsidian'],
                'Pixel 8 Pro' => ['Bay', 'Porcelain'],
            ],
            'Tecno' => [
                'Camon 19' => ['Eco Black', 'Sea Salt White'],
                'Spark 9' => ['Infinity Black', 'Sky Mirror'],
                'Phantom X2' => ['Stardust Grey', 'Moonlight Silver'],
                'Pop 7' => ['Turquoise Cyan', 'Atlantic Blue'],
                'Camon 20 Premier' => ['Serenity Blue', 'Night Black'],
            ],
            'Infinix' => [
                'Zero 20' => ['Glitter Gold', 'Green Fantasy'],
                'Hot 20' => ['Luna Blue', 'Racing Black'],
                'Note 12' => ['Force Black', 'Snowfall'],
                'Smart 7' => ['Azure Blue', 'Emerald Green'],
                'Zero 30 5G' => ['Rome Green', 'Golden Hour'],
            ],
            'Itel' => [
                'S23' => ['Lake Blue', 'Mystic White'],
                'P40' => ['Luxurious Gold', 'Dreamy Blue'],
                'A60s' => ['Shadow Black', 'Moonlit Violet'],
                'Vision 2' => ['Gradation Green', 'Deep Blue'],
                'A70' => ['Brilliant Gold', 'Azure Blue'],
            ],
            'LG' => [
                'Wing' => ['Illusion Sky'],
                'Velvet' => ['Aurora Silver', 'New Black'],
                'G8 ThinQ' => ['Carmine Red', 'Aurora Black'],
                'K92' => ['Titan Gray'],
                'Stylo 6' => ['Holographic White'],
            ],
            'Asus' => [
                'ROG Phone 6' => ['Storm White', 'Phantom Black'],
                'Zenfone 9' => ['Midnight Black', 'Moonlight White'],
                'ROG Phone 5' => ['Glossy Black', 'Storm White'],
                'Zenfone 10' => ['Comet White', 'Eclipse Red'],
            ],
            'Lenovo' => [
                'Legion Duel 2' => ['Ultimate Black', 'Titanium White'],
                'K13 Note' => ['Green', 'Gray'],
                'Z6 Pro' => ['Red and Black'],
                'Legion Y90' => ['Gray'],
            ],
            'HTC' => [
                'Desire 21 Pro' => ['Ink Blue'],
                'U12+' => ['Translucent Blue', 'Flame Red'],
                'Wildfire X' => ['Sapphire Blue'],
                'Desire 20 Plus' => ['Twilight Black'],
            ],
            'ZTE' => [
                'Axon 30' => ['Black', 'Aqua'],
                'Blade V30' => ['Gray', 'Green'],
                'Nubia RedMagic 7' => ['Pulsar', 'Supernova'],
                'Axon 40 Ultra' => ['Gold', 'Black'],
            ],
        ];


        foreach ($data as $brandName => $models) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);

            foreach ($models as $modelName => $colors) {
                $model = PhoneModel::create([
                    'name' => $modelName,
                    'brand_id' => $brand->id,
                ]);

                foreach ($colors as $color) {
                    Color::create([
                        'name' => $color,
                        'phone_model_id' => $model->id,
                    ]);
                }
            }
        }

        $this->command->info('✅ Brands, models, and colors seeded successfully!');
    }
}
