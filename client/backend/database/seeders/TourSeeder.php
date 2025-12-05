<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tours')->insert([
            [
                'title' => 'Du lịch Đà Lạt - Thành phố ngàn hoa 3N2Đ',
                'destination' => 'Đà Lạt',
                'price' => 2890000,
                'original_price' => 3200000,
                'duration' => '3 ngày 2 đêm',
                'rating' => 4.8,
                'review_count' => 156,
                'image' => 'https://cdn3.ivivu.com/2023/10/du-lich-Da-Lat-ivivu.jpg?w=500&h=300&fit=crop',
                'category' => 'Trong nước',
                'highlights' => json_encode([
                    'Thác Elephant Falls hùng vĩ',
                    'Chùa Linh Phước độc đáo',
                    'Ga Đà Lạt cổ kính',
                    'Chợ đêm Đà Lạt sôi động'
                ]),
                'description' => 'Khám phá thành phố ngàn hoa với khí hậu mát mẻ quanh năm, những cảnh quan thiên nhiên tuyệt đẹp và văn hóa độc đáo.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Khách sạn 3 sao',
                    'Ăn sáng hàng ngày',
                    'Xe du lịch điều hòa',
                    'Hướng dẫn viên'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Đà Lạt', 'activities' => ['Bay từ TP.HCM đến Đà Lạt', 'Check-in khách sạn', 'Tham quan chợ đêm Đà Lạt']],
                    ['day' => 2, 'title' => 'Khám phá Đà Lạt', 'activities' => ['Thác Elephant Falls', 'Chùa Linh Phước', 'Ga Đà Lạt', 'Vườn hoa thành phố']],
                    ['day' => 3, 'title' => 'Đà Lạt - TP.HCM', 'activities' => ['Mua sắm đặc sản', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội']),
                'max_guests' => 25,
            ],
            [
                'title' => 'Tour Hạ Long - Sapa 4N3Đ',
                'destination' => 'Hạ Long - Sapa',
                'price' => 4590000,
                'original_price' => 5200000,
                'duration' => '4 ngày 3 đêm',
                'rating' => 4.9,
                'review_count' => 203,
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=500&h=300&fit=crop',
                'category' => 'Trong nước',
                'highlights' => json_encode([
                    'Du thuyền qua đêm trên vịnh Hạ Long',
                    'Chinh phục đỉnh Fansipan',
                    'Thăm bản Cát Cát',
                    'Chợ tình Sapa'
                ]),
                'description' => 'Hành trình khám phá hai điểm đến nổi tiếng nhất miền Bắc với vịnh Hạ Long kỳ vĩ và Sapa mờ sương.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Du thuyền 3 sao qua đêm',
                    'Khách sạn 4 sao tại Sapa',
                    'Các bữa ăn theo chương trình',
                    'Xe du lịch cao cấp'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'Hà Nội - Hạ Long', 'activities' => ['Đón tại sân bay', 'Di chuyển đến Hạ Long', 'Lên du thuyền', 'Thăm động Thiên Cung']],
                    ['day' => 2, 'title' => 'Hạ Long - Sapa', 'activities' => ['Ngắm bình minh trên vịnh', 'Di chuyển đến Sapa', 'Check-in khách sạn']],
                    ['day' => 3, 'title' => 'Khám phá Sapa', 'activities' => ['Chinh phục Fansipan', 'Thăm bản Cát Cát', 'Chợ tình Sapa']],
                    ['day' => 4, 'title' => 'Sapa - Hà Nội', 'activities' => ['Tham quan thị trấn Sapa', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Đà Nẵng']),
                'max_guests' => 20,
            ],
            [
                'title' => 'Bangkok - Pattaya 4N3Đ',
                'destination' => 'Bangkok - Pattaya',
                'price' => 6890000,
                'original_price' => 7500000,
                'duration' => '4 ngày 3 đêm',
                'rating' => 4.7,
                'review_count' => 189,
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=500&h=300&fit=crop',
                'category' => 'Quốc tế',
                'highlights' => json_encode([
                    'Chùa Phật Vàng nổi tiếng',
                    'Show Alcazar Pattaya',
                    'Đảo Coral Island',
                    'Chợ Chatuchak Bangkok'
                ]),
                'description' => 'Khám phá xứ sở chùa vàng với Bangkok hiện đại và Pattaya sôi động, trải nghiệm văn hóa Thái Lan độc đáo.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Khách sạn 4 sao',
                    'Ăn sáng + 2 bữa chính',
                    'Visa Thái Lan',
                    'Bảo hiểm du lịch'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Bangkok', 'activities' => ['Bay đến Bangkok', 'Tham quan chùa Phật Vàng', 'Chợ Chatuchak']],
                    ['day' => 2, 'title' => 'Bangkok - Pattaya', 'activities' => ['Di chuyển đến Pattaya', 'Tham quan đảo Coral', 'Show Alcazar']],
                    ['day' => 3, 'title' => 'Khám phá Pattaya', 'activities' => ['Núi Phật Vàng', 'Vườn Nong Nooch', 'Walking Street']],
                    ['day' => 4, 'title' => 'Pattaya - Bangkok - TP.HCM', 'activities' => ['Mua sắm tại Bangkok', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội']),
                'max_guests' => 30,
            ],
            [
                'title' => 'Singapore - Malaysia 5N4Đ',
                'destination' => 'Singapore - Malaysia',
                'price' => 8990000,
                'original_price' => 9800000,
                'duration' => '5 ngày 4 đêm',
                'rating' => 4.8,
                'review_count' => 167,
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&h=300&fit=crop',
                'category' => 'Quốc tế',
                'highlights' => json_encode([
                    'Gardens by the Bay',
                    'Universal Studios Singapore',
                    'Tháp đôi Petronas',
                    'Genting Highlands'
                ]),
                'description' => 'Hành trình khám phá hai quốc gia Đông Nam Á với Singapore hiện đại và Malaysia đa văn hóa.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Khách sạn 4-5 sao',
                    'Ăn sáng hàng ngày',
                    'Vé tham quan các điểm',
                    'Xe du lịch cao cấp'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Singapore', 'activities' => ['Bay đến Singapore', 'Tham quan Merlion Park', 'Marina Bay Sands']],
                    ['day' => 2, 'title' => 'Khám phá Singapore', 'activities' => ['Gardens by the Bay', 'Universal Studios', 'Clarke Quay']],
                    ['day' => 3, 'title' => 'Singapore - Kuala Lumpur', 'activities' => ['Di chuyển đến KL', 'Tháp đôi Petronas', 'Batu Caves']],
                    ['day' => 4, 'title' => 'Genting Highlands', 'activities' => ['Tham quan Genting', 'Casino resort', 'Mua sắm']],
                    ['day' => 5, 'title' => 'Kuala Lumpur - TP.HCM', 'activities' => ['Mua sắm cuối cùng', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội', 'Đà Nẵng']),
                'max_guests' => 25,
            ],
            [
                'title' => 'Phú Quốc - Đảo Ngọc 3N2Đ',
                'destination' => 'Phú Quốc',
                'price' => 3590000,
                'original_price' => 4100000,
                'duration' => '3 ngày 2 đêm',
                'rating' => 4.9,
                'review_count' => 234,
                'image' => 'https://ik.imagekit.io/tvlk/blog/2024/01/dao-ngoc-xanh-1.jpg?w=500&h=300&fit=crop',
                'category' => 'Trong nước',
                'highlights' => json_encode([
                    'Cáp treo Hòn Thơm dài nhất thế giới',
                    'Chợ đêm Dinh Cậu',
                    'Bãi biển Sao tuyệt đẹp',
                    'Làng chài Hàm Ninh'
                ]),
                'description' => 'Khám phá đảo ngọc Phú Quốc với những bãi biển tuyệt đẹp, hải sản tươi ngon và cáp treo nổi tiếng.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Resort 4 sao view biển',
                    'Ăn sáng buffet',
                    'Xe đưa đón sân bay',
                    'Tour 4 đảo'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Phú Quốc', 'activities' => ['Bay đến Phú Quốc', 'Check-in resort', 'Chợ đêm Dinh Cậu']],
                    ['day' => 2, 'title' => 'Tour 4 đảo', 'activities' => ['Hòn Thơm', 'Aquatopia Water Park', 'Cáp treo', 'Sunset Sanato Beach Club']],
                    ['day' => 3, 'title' => 'Phú Quốc - TP.HCM', 'activities' => ['Bãi biển Sao', 'Làng chài Hàm Ninh', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội']),
                'max_guests' => 20,
            ],
            [
                'title' => 'Nhật Bản - Tokyo Osaka 6N5Đ',
                'destination' => 'Tokyo - Osaka',
                'price' => 18900000,
                'original_price' => 21500000,
                'duration' => '6 ngày 5 đêm',
                'rating' => 4.9,
                'review_count' => 145,
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=500&h=300&fit=crop',
                'category' => 'Quốc tế',
                'highlights' => json_encode([
                    'Núi Phụ Sĩ huyền thoại',
                    'Đền Sensoji Tokyo',
                    'Lâu đài Osaka',
                    'Khu phố Shibuya'
                ]),
                'description' => 'Trải nghiệm xứ sở hoa anh đào với Tokyo hiện đại và Osaka truyền thống, khám phá văn hóa Nhật Bản độc đáo.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Khách sạn 4 sao trung tâm',
                    'JR Pass 5 ngày',
                    'Ăn sáng hàng ngày',
                    'Visa Nhật Bản'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Tokyo', 'activities' => ['Bay đến Tokyo', 'Khu phố Shibuya', 'Tokyo Tower']],
                    ['day' => 2, 'title' => 'Khám phá Tokyo', 'activities' => ['Đền Sensoji', 'Khu Asakusa', 'Tokyo Skytree']],
                    ['day' => 3, 'title' => 'Núi Phụ Sĩ', 'activities' => ['Tour núi Phụ Sĩ', 'Hồ Kawaguchi', 'Hakone']],
                    ['day' => 4, 'title' => 'Tokyo - Osaka', 'activities' => ['Shinkansen đến Osaka', 'Lâu đài Osaka', 'Dotonbori']],
                    ['day' => 5, 'title' => 'Kyoto - Nara', 'activities' => ['Đền Kiyomizu', 'Rừng tre Arashiyama', 'Công viên Nara']],
                    ['day' => 6, 'title' => 'Osaka - TP.HCM', 'activities' => ['Mua sắm cuối cùng', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội']),
                'max_guests' => 15,
            ],
            [
                'title' => 'Tour Đà Nẵng - Hội An 4N3Đ',
                'destination' => 'Đà Nẵng - Hội An',
                'price' => 15900000,
                'original_price' => 18000000,
                'duration' => '4 ngày 3 đêm',
                'rating' => 4.8,
                'review_count' => 120,
                'image' => 'https://blisshoian.com/wp-content/uploads/2025/04/central-vietnam-in-december-9.webp?w=500&h=300&fit=crop',
                'category' => 'Trong nước',
                'highlights' => json_encode([
                    'Bà Nà Hills - Cầu Vàng',
                    'Phố cổ Hội An',
                    'Ngũ Hành Sơn',
                    'Mua sắm cuối cùng'
                ]),
                'description' => 'Trải nghiệm xứ sở hoa anh đào với Đà Nẵng học đại và Hội An truyền thống, khám phá văn hóa Đà Nẵng độc đáo.',
                'includes' => json_encode([
                    'Vé máy bay khứ hồi',
                    'Khách sạn 4 sao trung tâm',
                    'JR Pass 5 ngày',
                    'Ăn sáng hàng ngày',
                    'Visa Đà Nẵng'
                ]),
                'itinerary' => json_encode([
                    ['day' => 1, 'title' => 'TP.HCM - Đà Nẵng', 'activities' => ['Bay đến Đà Nẵng', 'Bà Nà Hills', 'Câu Vàng']],
                    ['day' => 2, 'title' => 'Hội An', 'activities' => ['Phố cổ Hội An', 'Ngũ Hành Sơn', 'Mua sắm cuối cùng']],
                    ['day' => 3, 'title' => 'Đà Nẵng - TP.HCM', 'activities' => ['Mua sắm cuối cùng', 'Bay về TP.HCM']],
                ]),
                'departure' => json_encode(['TP.HCM', 'Hà Nội']),
                'max_guests' => 15,
            ],
        ]);
    }
}
