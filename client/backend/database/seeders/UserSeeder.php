<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'huyhoahien86@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ],
            [
                'name' => 'Le Thanh Huy',
                'email' => 'huyhoahien876@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'role' => 'user',
            ],
            [
                'name' => 'Alex',
                'email' => 'huyhoahien89@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'role' => 'khachsan',
            ],
            [
                'name' => 'Mimi',
                'email' => 'huyhoahien81@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'role' => 'tour',
            ],
        ]);
    }
}
