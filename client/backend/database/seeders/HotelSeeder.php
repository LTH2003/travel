<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            [
                'id' => 1,
                'name' => "Lotte Hotel Saigon",
                'location' => "123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh",
                'rating' => 4.8,
                'price' => 2500000,
                'original_price' => 3000000,
                'image' => "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=250&fit=crop",
                'images' => [
                    "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=500&fit=crop",
                    "https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&h=500&fit=crop",
                    "https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&h=500&fit=crop",
                ],
                'amenities' => [
                    "Wifi miễn phí", "Bể bơi", "Gym", "Spa", "Nhà hàng", 
                    "Bar", "Dịch vụ phòng 24h", "Xe đưa đón sân bay"
                ],
                'description' => "Khách sạn 5 sao sang trọng tại trung tâm Sài Gòn với tầm nhìn tuyệt đẹp ra sông Sài Gòn.",
                'reviews' => 1250,
                'check_in' => "14:00",
                'check_out' => "12:00",
                'cancellation' => "Miễn phí hủy trước 24 giờ",
                'children' => "Trẻ em dưới 12 tuổi được miễn phí",
            ],
        ];
        
        foreach ($hotels as $h) {
            \App\Models\Hotel::updateOrCreate(
                ['name' => $h['name']],
                $h
            );
        }
    }
}
