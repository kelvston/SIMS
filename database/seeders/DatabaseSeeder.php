<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        DB::table('users')->updateOrInsert(
//            ['email' => 'admin@example.com'], // use your existing user email or unique identifier
//            [
//                'name' => 'Admin User',
//                'email' => 'admin@example.com',
//                'phone_number' => '0610723234',
//                'password' => Hash::make('admin@123'),
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]
//        );
        $this->call([
//            RolesAndPermissionsSeeder::class, // Add your new seeder here
            BrandModelColorSeeder::class, // Add your new seeder here
        ]);
    }
}
