<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('blogs')->insert([
            [
                'title' => '10 Điểm Đến Không Thể Bỏ Qua Khi Du Lịch Việt Nam',
                'slug' => '10-diem-den-khong-the-bo-qua-khi-du-lich-viet-nam',
                'excerpt' => 'Khám phá những điểm đến tuyệt vời nhất Việt Nam từ Bắc vào Nam, từ núi rừng hùng vĩ đến biển xanh cát trắng.',
                'content' => "# 10 Điểm Đến Không Thể Bỏ Qua Khi Du Lịch Việt Nam\n\nViệt Nam là một đất nước tuyệt đẹp với nhiều cảnh quan thiên nhiên hùng vĩ và văn hóa phong phú. Dưới đây là 10 điểm đến không thể bỏ qua khi bạn du lịch Việt Nam.\n\n## 1. Vịnh Hạ Long - Kỳ quan thiên nhiên thế giới\n\nVịnh Hạ Long với hơn 1.600 hòn đảo lớn nhỏ tạo nên một cảnh quan kỳ vĩ, được UNESCO công nhận là di sản thiên nhiên thế giới.\n\n## 2. Sapa - Thị trấn trong sương\n\nSapa nổi tiếng với những ruộng bậc thang tuyệt đẹp, khí hậu mát mẻ quanh năm và văn hóa đa dạng của các dân tộc thiểu số.\n\n## 3. Hội An - Phố cổ đầy màu sắc\n\nHội An là một thành phố cổ được bảo tồn nguyên vẹn với kiến trúc độc đáo, ẩm thực phong phú và không khí yên bình.\n\n## 4. Đà Lạt - Thành phố ngàn hoa\n\nĐà Lạt với khí hậu mát mẻ, những đồi thông xanh mướt và vô số loài hoa đẹp là điểm đến lý tưởng cho những ai yêu thích thiên nhiên.\n\n## 5. Phú Quốc - Đảo ngọc\n\nPhú Quốc sở hữu những bãi biển đẹp nhất Việt Nam với nước biển trong xanh và cát trắng mịn màng.\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Nguyễn Minh Anh',
                    'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Điểm đến',
                'tags' => json_encode(['Việt Nam', 'Du lịch', 'Điểm đến', 'Khám phá']),
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=800&h=400&fit=crop',
                'published_at' => '2024-01-15',
                'read_time' => 8,
                'views' => 1250,
                'likes' => 89,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Kinh Nghiệm Du Lịch Thái Lan Tự Túc Chi Tiết Nhất',
                'slug' => 'kinh-nghiem-du-lich-thai-lan-tu-tuc-chi-tiet-nhat',
                'excerpt' => 'Hướng dẫn chi tiết từ A-Z cho chuyến du lịch Thái Lan tự túc, từ visa, vé máy bay đến lưu trú và ăn uống.',
                'content' => "# Kinh Nghiệm Du Lịch Thái Lan Tự Túc Chi Tiết Nhất\n\nThái Lan là một trong những điểm đến phổ biến nhất Đông Nam Á với chi phí hợp lý và nhiều trải nghiệm thú vị.\n\n## Chuẩn bị trước khi đi\n\n### Visa và giấy tờ\n- Passport còn hạn ít nhất 6 tháng\n- Visa miễn phí 30 ngày cho người Việt Nam\n- Vé máy bay khứ hồi\n- Chứng minh tài chính\n\n### Vé máy bay\nNên đặt vé trước 2-3 tháng để có giá tốt nhất. Các hãng hàng không giá rẻ như VietJet, Thai Lion Air thường có khuyến mãi.\n\n## Lưu trú\n\n### Khách sạn\n- Bangkok: $20-50/đêm\n- Pattaya: $15-40/đêm  \n- Phuket: $25-60/đêm\n\n### Hostel\nGiá từ $5-15/đêm, phù hợp cho backpacker.\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Trần Văn Hùng',
                    'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Kinh nghiệm',
                'tags' => json_encode(['Thái Lan', 'Tự túc', 'Kinh nghiệm', 'Backpacker']),
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=400&fit=crop',
                'published_at' => '2024-01-12',
                'read_time' => 12,
                'views' => 2100,
                'likes' => 156,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Top 5 Món Ăn Đường Phố Không Thể Bỏ Qua Ở Singapore',
                'slug' => 'top-5-mon-an-duong-pho-khong-the-bo-qua-o-singapore',
                'excerpt' => 'Khám phá ẩm thực đường phố Singapore với 5 món ăn ngon nhất mà bạn phải thử khi đến đảo quốc sư tử.',
                'content' => "# Top 5 Món Ăn Đường Phố Không Thể Bỏ Qua Ở Singapore\n\nSingapore nổi tiếng không chỉ với kiến trúc hiện đại mà còn với nền ẩm thực đa dạng và phong phú.\n\n## 1. Hainanese Chicken Rice\n\nMón cơm gà Hải Nam là biểu tượng của ẩm thực Singapore. Gà luộc mềm, cơm thơm béo nấu với nước dùng gà.\n\n**Địa điểm nên thử:**\n- Tian Tian Hainanese Chicken Rice (Maxwell Food Centre)\n- Wee Nam Kee Chicken Rice\n\n## 2. Laksa\n\nLaksa là món bún cà ri đặc trưng với nước dùng đậm đà từ tôm, dừa và gia vị.\n\n**Giá:** $3-6 SGD\n**Địa điểm:** Katong Laksa, 328 Katong Laksa\n\n## 3. Char Kway Teow\n\nMón bánh phở xào với tôm, lạp xưởng, trứng và giá đỗ, có vị ngọt đậm đà.\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Lê Thị Mai',
                    'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Ẩm thực',
                'tags' => json_encode(['Singapore', 'Ẩm thực', 'Đường phố', 'Food']),
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&h=400&fit=crop',
                'published_at' => '2024-01-10',
                'read_time' => 6,
                'views' => 890,
                'likes' => 67,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cẩm Nang Du Lịch Nhật Bản Mùa Hoa Anh Đào',
                'slug' => 'cam-nang-du-lich-nhat-ban-mua-hoa-anh-dao',
                'excerpt' => 'Hướng dẫn chi tiết để có chuyến du lịch Nhật Bản mùa sakura hoàn hảo, từ thời điểm đi đến địa điểm ngắm hoa đẹp nhất.',
                'content' => "# Cẩm Nang Du Lịch Nhật Bản Mùa Hoa Anh Đào\n\nMùa hoa anh đào (sakura) là thời điểm đẹp nhất trong năm để du lịch Nhật Bản.\n\n## Thời điểm nở hoa\n\n### Miền Nam (Kyushu, Shikoku)\n- **Thời gian:** Cuối tháng 3 - đầu tháng 4\n- **Điểm nổi bật:** Kumamoto, Kagoshima\n\n### Kansai (Osaka, Kyoto, Nara)\n- **Thời gian:** Đầu - giữa tháng 4\n- **Điểm nổi bật:** Maruyama Park, Philosopher's Path\n\n### Kanto (Tokyo, Yokohama)\n- **Thời gian:** Cuối tháng 3 - đầu tháng 4\n- **Điểm nổi bật:** Ueno Park, Shinjuku Gyoen\n\n## Top địa điểm ngắm hoa anh đào\n\n### Tokyo\n1. **Ueno Park** - Nổi tiếng với hơn 1000 cây sakura\n2. **Shinjuku Gyoen** - Vườn hoàng gia với nhiều loài hoa anh đào\n3. **Chidorigafuchi** - Ngắm hoa từ thuyền kayak\n\n### Kyoto  \n1. **Maruyama Park** - Địa điểm hanami truyền thống\n2. **Philosopher's Path** - Con đường triết học lãng mạn\n3. **Arashiyama** - Kết hợp rừng tre và hoa anh đào\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Phạm Đức Minh',
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Điểm đến',
                'tags' => json_encode(['Nhật Bản', 'Sakura', 'Hoa anh đào', 'Tokyo', 'Kyoto']),
                'image' => 'https://images.unsplash.com/photo-1522383225653-ed111181a951?w=800&h=400&fit=crop',
                'published_at' => '2024-01-08',
                'read_time' => 10,
                'views' => 1800,
                'likes' => 142,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Bí Quyết Chụp Ảnh Du Lịch Đẹp Như Travel Blogger',
                'slug' => 'bi-quyet-chup-anh-du-lich-dep-nhu-travel-blogger',
                'excerpt' => 'Chia sẻ những tips và tricks để có những bức ảnh du lịch ấn tượng, từ góc chụp đến cách tạo dáng và chỉnh sửa.',
                'content' => "# Bí Quyết Chụp Ảnh Du Lịch Đẹp Như Travel Blogger\n\nNhững bức ảnh đẹp không chỉ lưu giữ kỷ niệm mà còn giúp bạn chia sẻ trải nghiệm du lịch một cách sinh động.\n\n## Chuẩn bị thiết bị\n\n### Camera/Điện thoại\n- **DSLR/Mirrorless:** Canon EOS M50, Sony A6000\n- **Smartphone:** iPhone 13 Pro, Samsung Galaxy S22\n- **Action Camera:** GoPro Hero 10 cho hoạt động thể thao\n\n### Phụ kiện cần thiết\n- Tripod mini cho selfie và ảnh nhóm\n- Lens góc rộng cho cảnh quan\n- Power bank và thẻ nhớ dự phòng\n\n## Kỹ thuật chụp ảnh\n\n### Quy tắc 1/3\nChia khung hình thành 9 phần bằng nhau, đặt chủ thể tại các điểm giao nhau.\n\n### Golden Hour\n- **Sunrise:** 30 phút trước và sau khi mặt trời mọc\n- **Sunset:** 30 phút trước và sau khi mặt trời lặn\n- Ánh sáng mềm mại, tạo màu sắc ấm áp\n\n### Composition Tips\n1. **Leading Lines:** Sử dụng đường dẫn mắt\n2. **Framing:** Tạo khung tự nhiên\n3. **Symmetry:** Đối xứng tạo sự cân bằng\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Hoàng Thị Lan',
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Tips & Tricks',
                'tags' => json_encode(['Photography', 'Travel', 'Tips', 'Instagram']),
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&h=400&fit=crop',
                'published_at' => '2024-01-05',
                'read_time' => 7,
                'views' => 1450,
                'likes' => 98,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Du Lịch Bụi Đông Nam Á: Lộ Trình 30 Ngày Hoàn Hảo',
                'slug' => 'du-lich-bui-dong-nam-a-lo-trinh-30-ngay-hoan-hao',
                'excerpt' => 'Lộ trình chi tiết cho chuyến du lịch bụi 30 ngày khám phá 6 nước Đông Nam Á với ngân sách tiết kiệm.',
                'content' => "# Du Lịch Bụi Đông Nam Á: Lộ Trình 30 Ngày Hoàn Hảo\n\nĐông Nam Á là điểm đến lý tưởng cho những ai yêu thích du lịch bụi với chi phí thấp và nhiều trải nghiệm thú vị.\n\n## Lộ trình tổng quan (30 ngày)\n\n### Tuần 1: Thái Lan (7 ngày)\n- **Bangkok (2 ngày):** Khám phá chùa chiền, chợ nổi\n- **Chiang Mai (3 ngày):** Văn hóa, đền chùa, trekking\n- **Phuket (2 ngày):** Biển đảo, party\n\n### Tuần 2: Malaysia - Singapore (7 ngày)  \n- **Kuala Lumpur (3 ngày):** Petronas Towers, street food\n- **Penang (2 ngày):** Georgetown UNESCO, ẩm thực\n- **Singapore (2 ngày):** Gardens by the Bay, Marina Bay\n\n### Tuần 3: Indonesia (7 ngày)\n- **Jakarta (1 ngày):** Transit, khám phá sơ qua\n- **Yogyakarta (3 ngày):** Borobudur, Prambanan\n- **Bali (3 ngày):** Ubud, Kuta Beach\n\n### Tuần 4: Vietnam - Cambodia (9 ngày)\n- **Ho Chi Minh City (2 ngày):** Pho, Cu Chi Tunnels\n- **Siem Reap (3 ngày):** Angkor Wat\n- **Phnom Penh (2 ngày):** Royal Palace, Killing Fields\n- **Hanoi (2 ngày):** Old Quarter, Ha Long Bay day trip\n\n## Ngân sách dự kiến\n\n### Tổng chi phí: $1,200-1,800\n- **Vé máy bay:** $400-600\n- **Lưu trú:** $300-500 (hostel/guesthouse)\n- **Ăn uống:** $200-400\n- **Di chuyển:** $150-250\n- **Tham quan:** $150-250\n\n*Bài viết còn tiếp...*",
                'author' => json_encode([
                    'name' => 'Ngô Văn Đức',
                    'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=50&h=50&fit=crop&crop=face'
                ]),
                'category' => 'Kinh nghiệm',
                'tags' => json_encode(['Backpacking', 'Đông Nam Á', 'Budget travel', 'Lộ trình']),
                'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&h=400&fit=crop',
                'published_at' => '2024-01-03',
                'read_time' => 15,
                'views' => 2800,
                'likes' => 201,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
