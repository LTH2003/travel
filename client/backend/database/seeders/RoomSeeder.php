<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'id' => 1,
                'hotel_id' => 1,
                'name' => "Phòng thường",
                'size' => 35,
                'capacity' => 2,
                'beds' => "1 giường đôi King",
                'price' => 2500000,
                'original_price' => 3000000,
                'amenities' => [
                    "Wifi miễn phí", "TV 55 inch", "Minibar", "Két sắt", "Máy pha cà phê"
                ],
                'images' => [
                    "https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=400&h=250&fit=crop",
                    "https://images.unsplash.com/photo-1590490360182-c33d57733427?w=400&h=250&fit=crop"
                ],
                'description' => "Phòng deluxe rộng rãi.",
                'available' => 5,
            ],
            [
                'id' => 2,
                'hotel_id' => 1,
                'name' => "Phòng thương gia",
                'size' => 65,
                'capacity' => 3,
                'beds' => "1 giường đôi King + 1 sofa bed",
                'price' => 4200000,
                'original_price' => 5000000,
                'amenities' => [
                    "Wifi miễn phí", "TV 65 inch", "Minibar", "Két sắt", 
                    "Máy pha cà phê", "Phòng khách riêng", "Bồn tắm Jacuzzi"
                ],
                'images' => [
                    "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&h=250&fit=crop",
                    "https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=400&h=250&fit=crop"
                ],
                'description' => "Suite cao cấp với tầm nhìn panoramic ra sông Sài Gòn.",
                'available' => 3,
            ],
            
        ];
        
        foreach ($rooms as $r) {
            \App\Models\Room::updateOrCreate(
                ['hotel_id' => $r['hotel_id'], 'name' => $r['name']],
                $r
            );
        }
    }
}
