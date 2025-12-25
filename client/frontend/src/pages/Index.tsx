import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Plane, Shield, Clock, Award, Star, ArrowRight } from 'lucide-react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import SearchForm, { SearchFilters } from '@/components/SearchForm';
import TourCard from '@/components/TourCard';
import { useTitle } from '@/hooks/useTitle';
import { tourApi } from '@/api/tourApi';

export default function Index() {
  useTitle('Trang Chủ - TravelVN'); 
  const navigate = useNavigate();

  const [searchFilters, setSearchFilters] = useState<SearchFilters>({
    destination: '',
    departure: undefined,
    guests: 2,
    keyword: ''
  });

  const [featuredTours, setFeaturedTours] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);


  useEffect(() => {
    const fetchTours = async () => {
      try {
        setLoading(true);
        const res = await tourApi.getAll(); 
        const payload = (res as any)?.data ?? res;
        const tours = (payload as any)?.data ?? payload ?? [];
        setFeaturedTours(Array.isArray(tours) ? tours : []);
      } catch (err) {
        setError("Không thể tải danh sách tour nổi bật.");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };
    fetchTours();
  }, []);

  const handleSearch = (filters: SearchFilters) => {
    setSearchFilters(filters);
    const searchParams = new URLSearchParams();
    if (filters.destination && filters.destination !== 'Tất cả điểm đến') {
      searchParams.set('destination', filters.destination);
    }
    if (filters.keyword) {
      searchParams.set('keyword', filters.keyword);
    }
    if (filters.guests) {
      searchParams.set('guests', filters.guests.toString());
    }
    if (filters.departure) {
      searchParams.set('departure', filters.departure.toISOString());
    }
    navigate(`/tours?${searchParams.toString()}`);
  };

  const features = [
    { icon: Shield, title: 'Đảm bảo chất lượng', description: 'Cam kết hoàn tiền 100% nếu không hài lòng' },
    { icon: Clock, title: 'Hỗ trợ 24/7', description: 'Đội ngũ tư vấn chuyên nghiệp luôn sẵn sàng' },
    { icon: Award, title: 'Giá tốt nhất', description: 'Cam kết giá tour tốt nhất thị trường' },
    { icon: Plane, title: 'Tour đa dạng', description: 'Hơn 1000+ tour trong và ngoài nước' },
  ];

  const stats = [
    { number: '50,000+', label: 'Khách hàng hài lòng' },
    { number: '1,000+', label: 'Tour du lịch' },
    { number: '15+', label: 'Năm kinh nghiệm' },
    { number: '24/7', label: 'Hỗ trợ khách hàng' },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      {/* Hero Section */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="absolute inset-0 bg-black/20"></div>
        <div 
          className="relative bg-cover bg-center py-20"
          style={{
            backgroundImage: 'url(https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1200&h=600&fit=crop)',
            backgroundBlendMode: 'overlay'
          }}
        >
          <div className="container mx-auto px-4 text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Khám Phá Thế Giới
              <span className="block text-orange-400">Cùng TravelVN</span>
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
              Trải nghiệm những chuyến đi tuyệt vời với dịch vụ chuyên nghiệp và giá cả hợp lý
            </p>
            <div className="max-w-6xl mx-auto">
              <SearchForm onSearch={handleSearch} className="bg-white/95 backdrop-blur-sm" />
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className="text-3xl md:text-4xl font-bold text-blue-600 mb-2">
                  {stat.number}
                </div>
                <div className="text-gray-600">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Tours Section */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Tour Nổi Bật
              </h2>
              <p className="text-lg text-gray-600">
                Khám phá những điểm đến hot nhất hiện nay
              </p>
            </div>
            <Button 
              variant="outline" 
              onClick={() => navigate('/tours')}
              className="hidden md:flex items-center"
            >
              Xem tất cả
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </div>

          {/* Loading / Error / Data */}
          {loading ? (
            <p className="text-center text-gray-500">Đang tải dữ liệu...</p>
          ) : error ? (
            <p className="text-center text-red-500">{error}</p>
          ) : featuredTours.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {featuredTours.slice(0, 6).map((tour) => (
                <TourCard key={tour.id} tour={tour} />
              ))}
            </div>
          ) : (
            <p className="text-center text-gray-500">Không có tour nổi bật nào.</p>
          )}

          <div className="text-center mt-8 md:hidden">
            <Button onClick={() => navigate('/tours')}>
              Xem tất cả tour
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </div>
        </div>
      </section>
<section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
              Khách Hàng Nói Gì Về Chúng Tôi
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {[
              {
                name: 'Nguyễn Thị Lan',
                location: 'TP. Hồ Chí Minh',
                rating: 5,
                comment: 'Tour Đà Lạt rất tuyệt vời! Hướng dẫn viên nhiệt tình, lịch trình hợp lý. Sẽ đặt tour khác với TravelVN.'
              },
              {
                name: 'Trần Văn Minh',
                location: 'Hà Nội',
                rating: 5,
                comment: 'Chuyến đi Nhật Bản không thể tuyệt vời hơn. Mọi thứ đều được sắp xếp chu đáo, giá cả hợp lý.'
              },
              {
                name: 'Lê Thị Hương',
                location: 'Đà Nẵng',
                rating: 5,
                comment: 'Dịch vụ chuyên nghiệp, hỗ trợ tận tình. Tour Thái Lan vượt ngoài mong đợi của gia đình tôi.'
              }
            ].map((testimonial, index) => (
              <Card key={index}>
                <CardContent className="p-6">
                  <div className="flex items-center mb-4">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star key={i} className="h-5 w-5 fill-yellow-400 text-yellow-400" />
                    ))}
                  </div>
                  <p className="text-gray-600 mb-4 italic">"{testimonial.comment}"</p>
                  <div>
                    <div className="font-semibold">{testimonial.name}</div>
                    <div className="text-sm text-gray-500">{testimonial.location}</div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 bg-blue-600 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Sẵn Sàng Cho Chuyến Đi Tiếp Theo?
          </h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto">
            Đặt tour ngay hôm nay và nhận ưu đãi đặc biệt lên đến 20%
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button 
              size="lg" 
              className="bg-orange-500 hover:bg-orange-600"
              onClick={() => navigate('/tours')}
            >
              Khám phá tour ngay
            </Button>
            <Button size="lg" variant="outline" className="text-blue-600 border-white hover:bg-white" onClick={() => navigate('/contact')}>
              Liên hệ tư vấn
            </Button>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}





