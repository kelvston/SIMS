<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Color;
use App\Models\PhoneModel;
use App\Models\PhoneStorageCapacity;
use Illuminate\Database\Seeder;

class BrandModelColorSeeder extends Seeder
{
    public function run(): void
    {
        $defaultColors = ['Black', 'White', 'Blue', 'Gold', 'Silver', 'Green', 'Purple', 'Red', 'Gray'];
        $defaultStorage = ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'];

        $data = [
            'Apple' => [
                'colors' => ['Black', 'White', 'Silver', 'Gold', 'Space Gray', 'Midnight', 'Starlight', 'Blue', 'Pink', 'Green', 'Purple', 'Red', 'Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium', 'Desert Titanium'],
                'storage' => ['64GB', '128GB', '256GB', '512GB', '1TB', '2TB'],
                'models' => [
                    'iPhone 6', 'iPhone 6 Plus', 'iPhone 6s', 'iPhone 6s Plus',
                    'iPhone 7', 'iPhone 7 Plus', 'iPhone 8', 'iPhone 8 Plus',
                    'iPhone X', 'iPhone XR', 'iPhone XS', 'iPhone XS Max',
                    'iPhone 11', 'iPhone 11 Pro', 'iPhone 11 Pro Max',
                    'iPhone 12 Mini', 'iPhone 12', 'iPhone 12 Pro', 'iPhone 12 Pro Max',
                    'iPhone 13 Mini', 'iPhone 13', 'iPhone 13 Pro', 'iPhone 13 Pro Max',
                    'iPhone 14', 'iPhone 14 Plus', 'iPhone 14 Pro', 'iPhone 14 Pro Max',
                    'iPhone 15', 'iPhone 15 Plus', 'iPhone 15 Pro', 'iPhone 15 Pro Max',
                    'iPhone 16', 'iPhone 16 Plus', 'iPhone 16 Pro', 'iPhone 16 Pro Max',
                    'iPhone 17', 'iPhone 17 Plus', 'iPhone 17 Pro', 'iPhone 17 Pro Max',
                    'iPhone SE', 'iPhone SE 2nd Gen', 'iPhone SE 3rd Gen',
                ],
            ],
            'Samsung' => [
                'colors' => ['Phantom Black', 'Phantom White', 'Phantom Gray', 'Cream', 'Green', 'Lavender', 'Graphite', 'Silver', 'Blue', 'Navy', 'Mint', 'Violet'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'],
                'models' => [
                    'Galaxy S20', 'Galaxy S20 Plus', 'Galaxy S20 Ultra',
                    'Galaxy S21', 'Galaxy S21 Plus', 'Galaxy S21 Ultra', 'Galaxy S21 FE',
                    'Galaxy S22', 'Galaxy S22 Plus', 'Galaxy S22 Ultra',
                    'Galaxy S23', 'Galaxy S23 Plus', 'Galaxy S23 Ultra', 'Galaxy S23 FE',
                    'Galaxy S24', 'Galaxy S24 Plus', 'Galaxy S24 Ultra', 'Galaxy S24 FE',
                    'Galaxy S25', 'Galaxy S25 Plus', 'Galaxy S25 Ultra',
                    'Galaxy Note 20', 'Galaxy Note 20 Ultra',
                    'Galaxy Z Flip 3', 'Galaxy Z Flip 4', 'Galaxy Z Flip 5', 'Galaxy Z Flip 6',
                    'Galaxy Z Fold 3', 'Galaxy Z Fold 4', 'Galaxy Z Fold 5', 'Galaxy Z Fold 6',
                    'Galaxy A03', 'Galaxy A04', 'Galaxy A05', 'Galaxy A06',
                    'Galaxy A13', 'Galaxy A14', 'Galaxy A15', 'Galaxy A16',
                    'Galaxy A23', 'Galaxy A24', 'Galaxy A25',
                    'Galaxy A33', 'Galaxy A34', 'Galaxy A35',
                    'Galaxy A53', 'Galaxy A54', 'Galaxy A55',
                    'Galaxy M14', 'Galaxy M15', 'Galaxy M33', 'Galaxy M34',
                ],
            ],
            'Huawei' => [
                'colors' => ['Black', 'White', 'Silver Frost', 'Mystic Silver', 'Blush Gold', 'Crystal Blue', 'Midnight Black', 'Emerald Green'],
                'storage' => ['64GB', '128GB', '256GB', '512GB'],
                'models' => ['P30', 'P30 Pro', 'P40', 'P40 Pro', 'P50', 'P50 Pro', 'P60', 'P60 Pro', 'Mate 30', 'Mate 40 Pro', 'Mate 50 Pro', 'Mate 60 Pro', 'Nova 7i', 'Nova 9', 'Nova 10', 'Nova 11', 'Nova Y70', 'Nova Y90', 'Y9a', 'Y7a'],
            ],
            'Xiaomi' => [
                'colors' => ['Black', 'White', 'Blue', 'Green', 'Gray', 'Purple', 'Yellow', 'Ceramic Black', 'Ceramic White', 'Graphite Gray'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'],
                'models' => ['Mi 10', 'Mi 11', 'Mi 11 Ultra', 'Xiaomi 12', 'Xiaomi 12 Pro', 'Xiaomi 13', 'Xiaomi 13 Pro', 'Xiaomi 13 Ultra', 'Xiaomi 14', 'Xiaomi 14 Pro', 'Redmi 10', 'Redmi 12', 'Redmi 12C', 'Redmi 13C', 'Redmi Note 10', 'Redmi Note 11', 'Redmi Note 12', 'Redmi Note 13', 'Poco X3 Pro', 'Poco X4 Pro', 'Poco X5 Pro', 'Poco X6 Pro', 'Redmi K60 Pro'],
            ],
            'Oppo' => [
                'colors' => ['Black', 'White', 'Blue', 'Green', 'Gold', 'Orange', 'Shimmer Gold', 'Shimmer Black', 'Dreamy Blue'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['A16', 'A17', 'A18', 'A38', 'A57', 'A58', 'A77', 'A78', 'A98 5G', 'F19 Pro', 'F21 Pro', 'F23', 'Reno6', 'Reno7', 'Reno8', 'Reno8 T', 'Reno10', 'Reno11', 'Find X5', 'Find X6 Pro', 'Find X7 Ultra'],
            ],
            'Vivo' => [
                'colors' => ['Black', 'Blue', 'Gold', 'Green', 'Purple', 'Sunshine Gold', 'Stardust Black', 'Midnight Blue'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['Y15s', 'Y16', 'Y17s', 'Y21', 'Y22', 'Y27', 'Y33s', 'Y35', 'Y36', 'V21', 'V23', 'V23 Pro', 'V25', 'V27', 'V29', 'V30', 'X80', 'X90 Pro', 'X100 Pro', 'T2 Pro 5G'],
            ],
            'OnePlus' => [
                'colors' => ['Black', 'Green', 'Silver', 'Blue', 'Red', 'Volcanic Black', 'Emerald Forest', 'Aqua Surge'],
                'storage' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'models' => ['OnePlus 8', 'OnePlus 8 Pro', 'OnePlus 9', 'OnePlus 9 Pro', 'OnePlus 10 Pro', 'OnePlus 10T', 'OnePlus 11', 'OnePlus 11R', 'OnePlus 12', 'OnePlus 12R', 'Nord', 'Nord 2', 'Nord CE 3', 'Nord N300'],
            ],
            'Realme' => [
                'colors' => ['Black', 'Blue', 'Green', 'Gold', 'Silver', 'Sunrise Beige', 'Navigator Beige', 'Pioneer Green'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['C25', 'C30', 'C33', 'C35', 'C53', 'C55', 'C67', 'Realme 8', 'Realme 9', 'Realme 10', 'Realme 11 Pro', 'Realme 12 Plus', 'GT Neo 3', 'GT Neo 5', 'Narzo 50', 'Narzo 60'],
            ],
            'Google' => [
                'colors' => ['Obsidian', 'Snow', 'Hazel', 'Porcelain', 'Bay', 'Charcoal', 'Chalk', 'Sorta Seafoam'],
                'storage' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'models' => ['Pixel 5', 'Pixel 5a', 'Pixel 6', 'Pixel 6 Pro', 'Pixel 6a', 'Pixel 7', 'Pixel 7 Pro', 'Pixel 7a', 'Pixel 8', 'Pixel 8 Pro', 'Pixel 8a', 'Pixel 9', 'Pixel 9 Pro', 'Pixel Fold', 'Pixel 9 Pro Fold'],
            ],
            'Tecno' => [
                'colors' => ['Black', 'White', 'Blue', 'Green', 'Gold', 'Serenity Blue', 'Night Black', 'Eco Black'],
                'storage' => ['32GB', '64GB', '128GB', '256GB'],
                'models' => ['Spark 8', 'Spark 9', 'Spark 10', 'Spark 20', 'Pop 6', 'Pop 7', 'Pop 8', 'Camon 18', 'Camon 19', 'Camon 20', 'Camon 20 Premier', 'Camon 30', 'Phantom X2', 'Phantom V Fold', 'Phantom V Flip'],
            ],
            'Infinix' => [
                'colors' => ['Black', 'Blue', 'Green', 'Gold', 'Silver', 'Racing Black', 'Luna Blue', 'Rome Green'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['Smart 6', 'Smart 7', 'Smart 8', 'Hot 10', 'Hot 11', 'Hot 12', 'Hot 20', 'Hot 30', 'Hot 40', 'Note 11', 'Note 12', 'Note 30', 'Note 40', 'Zero 20', 'Zero 30 5G'],
            ],
            'Itel' => [
                'colors' => ['Black', 'Blue', 'Gold', 'Green', 'Purple', 'Mystic White', 'Lake Blue'],
                'storage' => ['16GB', '32GB', '64GB', '128GB', '256GB'],
                'models' => ['A48', 'A49', 'A58', 'A60', 'A60s', 'A70', 'P37', 'P38', 'P40', 'P55', 'S17', 'S18', 'S23', 'S24', 'Vision 2'],
            ],
            'Nokia' => [
                'colors' => ['Black', 'Blue', 'Gray', 'Green', 'White', 'Charcoal', 'Dusk', 'Nordic Blue'],
                'storage' => ['16GB', '32GB', '64GB', '128GB'],
                'models' => ['C20', 'C21', 'C30', 'C31', 'C32', 'G10', 'G11', 'G20', 'G21', 'G22', 'X10', 'X20', 'XR20', '2.4', '5.4'],
            ],
            'Sony' => [
                'colors' => ['Black', 'White', 'Purple', 'Green', 'Blue', 'Silver'],
                'storage' => ['64GB', '128GB', '256GB', '512GB'],
                'models' => ['Xperia 1 III', 'Xperia 1 IV', 'Xperia 1 V', 'Xperia 5 III', 'Xperia 5 IV', 'Xperia 5 V', 'Xperia 10 III', 'Xperia 10 IV', 'Xperia 10 V', 'Xperia L4'],
            ],
            'Motorola' => [
                'colors' => ['Black', 'Blue', 'Gray', 'Silver', 'Green', 'Coral', 'Polar Silver'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['Moto E7', 'Moto E13', 'Moto E22', 'Moto G Power', 'Moto G Stylus', 'Moto G Stylus 5G', 'Moto G Play', 'Moto G54', 'Moto G84', 'Edge 20', 'Edge 30', 'Edge 40', 'Edge 50', 'Razr 5G', 'Razr 40 Ultra'],
            ],
            'LG' => [
                'colors' => ['Black', 'White', 'Silver', 'Gray', 'Aurora Black', 'Aurora Silver', 'Illusion Sky'],
                'storage' => ['32GB', '64GB', '128GB', '256GB'],
                'models' => ['G7 ThinQ', 'G8 ThinQ', 'V50 ThinQ', 'V60 ThinQ', 'Velvet', 'Wing', 'K51', 'K61', 'K92', 'Stylo 5', 'Stylo 6'],
            ],
            'Asus' => [
                'colors' => ['Black', 'White', 'Red', 'Blue', 'Storm White', 'Phantom Black'],
                'storage' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'models' => ['Zenfone 8', 'Zenfone 9', 'Zenfone 10', 'Zenfone 11 Ultra', 'ROG Phone 5', 'ROG Phone 6', 'ROG Phone 7', 'ROG Phone 8'],
            ],
            'Lenovo' => [
                'colors' => ['Black', 'White', 'Gray', 'Green', 'Red'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['K13 Note', 'K14 Plus', 'Z6 Pro', 'Legion Duel', 'Legion Duel 2', 'Legion Y70', 'Legion Y90'],
            ],
            'HTC' => [
                'colors' => ['Black', 'Blue', 'Red', 'White', 'Translucent Blue', 'Flame Red'],
                'storage' => ['32GB', '64GB', '128GB', '256GB'],
                'models' => ['U11', 'U12+', 'U20 5G', 'Desire 20 Plus', 'Desire 21 Pro', 'Desire 22 Pro', 'Wildfire E', 'Wildfire X'],
            ],
            'ZTE' => [
                'colors' => ['Black', 'Gray', 'Green', 'Gold', 'Aqua', 'Pulsar', 'Supernova'],
                'storage' => ['32GB', '64GB', '128GB', '256GB', '512GB'],
                'models' => ['Blade A52', 'Blade A72', 'Blade V30', 'Blade V40', 'Blade V50', 'Axon 30', 'Axon 40 Ultra', 'Axon 50 Ultra', 'Nubia RedMagic 7', 'Nubia RedMagic 8 Pro', 'Nubia RedMagic 9 Pro'],
            ],
        ];

        foreach ($data as $brandName => $brandData) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);

            foreach ($brandData['models'] as $modelEntry) {
                $modelName = is_array($modelEntry) ? $modelEntry['name'] : $modelEntry;
                $colors = is_array($modelEntry) ? ($modelEntry['colors'] ?? $brandData['colors'] ?? $defaultColors) : ($brandData['colors'] ?? $defaultColors);
                $storageCapacities = is_array($modelEntry) ? ($modelEntry['storage'] ?? $brandData['storage'] ?? $defaultStorage) : ($brandData['storage'] ?? $defaultStorage);

                $model = PhoneModel::firstOrCreate([
                    'name' => $modelName,
                    'brand_id' => $brand->id,
                ]);

                foreach ($colors as $color) {
                    Color::firstOrCreate([
                        'name' => $color,
                        'phone_model_id' => $model->id,
                    ]);
                }

                foreach ($storageCapacities as $storageCapacity) {
                    PhoneStorageCapacity::firstOrCreate([
                        'name' => $storageCapacity,
                        'phone_model_id' => $model->id,
                    ]);
                }
            }
        }

        $this->command->info('✅ Brands, models, colors, and storage capacities seeded successfully!');
    }
}
