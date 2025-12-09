import { useEffect, useState } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { useNavigate, Link } from 'react-router-dom';
import { getRecommendations } from '@/api/recommendationsApi';
import { Loader2, MapPin, Star, Wifi, Car, Coffee, Utensils } from 'lucide-react';
import { toast } from '@/hooks/use-toast';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { useTitle } from '@/hooks/useTitle';

interface Hotel {
  id: number;
  name: string;
  location: string;
  rating: number;
  price: number;
  image: string;
  amenities: string[];
  description: string;
  reviews: number;
}

interface Tour {
  id: number;
  title: string;
  destination: string;
  rating: number;
  price: number;
  image: string;
  description: string;
  duration: string;
  reviews: number;
}

export default function Recommendations() {
  useTitle('Gợi ý cho bạn');
  const { isLoggedIn, loading: authLoading } = useAuth();
  const navigate = useNavigate();
  const [hotels, setHotels] = useState<Hotel[]>([]);
  const [tours, setTours] = useState<Tour[]>([]);
  const [loading, setLoading] = useState(true);
  const [reason, setReason] = useState('');

  useEffect(() => {
    if (authLoading) return;

    if (!isLoggedIn) {
      navigate('/login');
      return;
    }

    const loadRecommendations = async () => {
      try {
        setLoading(true);
        const data = await getRecommendations();
        setHotels(data.recommended_hotels);
        setTours(data.recommended_tours);
        setReason(data.reason);
      } catch (error) {
        console.error('Error loading recommendations:', error);
        toast({
          title: 'Lỗi',
          description: 'Không thể tải đề xuất',
          variant: 'destructive',
        });
      } finally {
        setLoading(false);
      }
    };

    loadRecommendations();
  }, [isLoggedIn, authLoading, navigate]);

  const renderStars = (rating: number) => {
    return Array.from({ length: 5 }, (_, i) => (
      <Star
        key={i}
        className={`h-4 w-4 ${i < Math.floor(rating) ? 'text-yellow-400 fill-current' : 'text-gray-300'}`}
      />
    ));
  };

  const getAmenityIcon = (amenity: string) => {
    if (amenity.includes('Wifi')) return <Wifi className="h-4 w-4" />;
    if (amenity.includes('Nhà hàng')) return <Utensils className="h-4 w-4" />;
    if (amenity.includes('Xe') || amenity.includes('Đưa đón')) return <Car className="h-4 w-4" />;
    return <Coffee className="h-4 w-4" />;
  };

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
  };

  if (authLoading || loading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="flex items-center justify-center min-h-[calc(100vh-80px)]">
          <div className="text-center">
            <Loader2 className="h-8 w-8 animate-spin mx-auto mb-2" />
            <p>Đang tải đề xuất...</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-7xl mx-auto px-4 py-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900">Gợi ý cho bạn</h1>
          <p className="text-gray-600 mt-2">{reason}</p>
        </div>

        {/* Recommended Hotels */}
        {hotels.length > 0 && (
          <div className="mb-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-6">Khách sạn được đề xuất</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {hotels.map((hotel) => (
                <Card key={hotel.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                  <div className="relative">
                    <img
                      src={hotel.image}
                      alt={hotel.name}
                      className="w-full h-48 object-cover"
                    />
                  </div>
                  <CardContent className="p-4">
                    <div className="flex items-start justify-between mb-2">
                      <h3 className="font-bold text-lg text-gray-900 flex-1">{hotel.name}</h3>
                      <div className="flex items-center ml-2">
                        {renderStars(hotel.rating)}
                        <span className="ml-1 text-sm text-gray-600">({hotel.reviews})</span>
                      </div>
                    </div>
                    
                    <div className="flex items-center text-gray-600 mb-2">
                      <MapPin className="h-4 w-4 mr-1" />
                      <span className="text-sm">{hotel.location}</span>
                    </div>
                    
                    <p className="text-gray-600 text-sm mb-3">{hotel.description}</p>
                    
                    <div className="flex flex-wrap gap-2 mb-4">
                      {(hotel.amenities || []).slice(0, 3).map((amenity, index) => (
                        <div key={index} className="flex items-center text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                          {getAmenityIcon(amenity)}
                          <span className="ml-1">{amenity}</span>
                        </div>
                      ))}
                      {(hotel.amenities || []).length > 3 && (
                        <span className="text-xs text-gray-500">+{(hotel.amenities || []).length - 3} tiện ích</span>
                      )}
                    </div>
                    
                    <div className="flex items-center justify-between">
                      <div>
                        <div className="text-xl font-bold text-orange-600">
                          {formatPrice(hotel.price)}
                          <span className="text-sm text-gray-500 font-normal">/đêm</span>
                        </div>
                      </div>
                      <Link to={`/hotels/${hotel.id}`}>
                        <Button className="bg-orange-500 hover:bg-orange-600">
                          Xem chi tiết
                        </Button>
                      </Link>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}

        {/* Recommended Tours */}
        {tours.length > 0 && (
          <div className="mb-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-6">Tour được đề xuất</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {tours.map((tour) => (
                <Card key={tour.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                  <div className="relative">
                    <img
                      src={tour.image}
                      alt={tour.title}
                      className="w-full h-48 object-cover"
                    />
                  </div>
                  <CardContent className="p-4">
                    <div className="flex items-start justify-between mb-2">
                      <h3 className="font-bold text-lg text-gray-900 flex-1">{tour.title}</h3>
                      <div className="flex items-center ml-2">
                        {renderStars(tour.rating)}
                        <span className="ml-1 text-sm text-gray-600">({tour.reviews})</span>
                      </div>
                    </div>
                    
                    <div className="flex items-center text-gray-600 mb-2">
                      <MapPin className="h-4 w-4 mr-1" />
                      <span className="text-sm">{tour.destination}</span>
                    </div>
                    
                    <p className="text-gray-600 text-sm mb-3">{tour.description}</p>
                    
                    <div className="text-sm text-gray-600 mb-4">
                      <span className="font-medium">Thời gian: </span>{tour.duration}
                    </div>
                    
                    <div className="flex items-center justify-between">
                      <div>
                        <div className="text-xl font-bold text-orange-600">
                          {formatPrice(tour.price)}
                          <span className="text-sm text-gray-500 font-normal">/người</span>
                        </div>
                      </div>
                      <Link to={`/tours/${tour.id}`}>
                        <Button className="bg-blue-600 hover:bg-blue-700">
                          Xem chi tiết
                        </Button>
                      </Link>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}

        {/* Empty state */}
        {hotels.length === 0 && tours.length === 0 && (
          <div className="text-center py-12">
            <p className="text-gray-600 mb-4">
              Hãy thêm một số mục yêu thích để nhận được đề xuất cá nhân hóa
            </p>
            <Button onClick={() => navigate('/favorites')}>
              Xem mục yêu thích
            </Button>
          </div>
        )}
        <Footer />
      </div>
    </div>
  );
}
