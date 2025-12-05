<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi AdminSeeder trước tiên để tạo admin account
        $this->call([AdminSeeder::class]);

        // Gọi seeder TourSeeder để thêm dữ liệu tour
        $this->call([TourSeeder::class]);

        $this->call([BlogSeeder::class]);

        $this->call([UserSeeder::class]);

        $this->call([HotelSeeder::class]);

        $this->call([RoomSeeder::class]);
    }
}
