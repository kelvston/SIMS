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

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@tari.com'],
            [
                'name' => 'JAdmin',
                'email' => 'admin@tari.com',
                'phone_number' => '0784252900',
                'password' => Hash::make('admin@123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->call([
            RolesAndPermissionsSeeder::class, // Add your new seeder here
            CashewProductSeeder::class, // Add your new seeder here
        ]);
    }
}
