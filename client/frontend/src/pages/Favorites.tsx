import { useEffect, useState, useMemo } from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, MapPin, Star, Trash2, Heart } from 'lucide-react';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { useTitle } from '@/hooks/useTitle';
import { useAuth } from '@/hooks/useAuth';
import { useFavorites } from '@/hooks/useFavorites';
import { useNavigate } from 'react-router-dom';
import { toast } from '@/hooks/use-toast';
import { getFavorites as fetchFavoritesFromAPI } from '@/api/favoritesApi';

interface HotelFavorite {
  id: number;
  name: string;
  location: string;
  price: number;
  rating: number;
  images: string[];
}

interface TourFavorite {
  id: number;
  title: string;
  destination: string;
  price: number;
  rating: number;
  image: string;
  duration: string;
}

let favoritesCacheData: any = null;

export default function Favorites() {
  useTitle('Yêu thích - TravelVN');
  const navigate = useNavigate();
  const { isLoggedIn, loading: authLoading } = useAuth();
  const { loadFavorites, removeFavorite } = useFavorites();
  const [hotels, setHotels] = useState<HotelFavorite[]>([]);
  const [tours, setTours] = useState<TourFavorite[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Chỉ redirect nếu auth loading xong và thực sự không login
    if (!authLoading && !isLoggedIn) {
      navigate('/login');
      return;
    }

    // Nếu auth còn loading, đợi
    if (authLoading) {
      return;
    }

    const fetchFavorites = async () => {
      try {
        setLoading(true);
        
        // 🚀 Use cached data if available, only refetch if cache is empty
        if (favoritesCacheData) {
          setHotels(favoritesCacheData.hotels || []);
          setTours(favoritesCacheData.tours || []);
          setLoading(false);
          return;
        }

        // Fetch favorites từ API - trả về full hotel và tour data
        const favoritesData: any = await fetchFavoritesFromAPI();
        
        // Cache the data for subsequent visits
        favoritesCacheData = favoritesData;
        
        setHotels(favoritesData.hotels || []);
        setTours(favoritesData.tours || []);
      } catch (error) {
        console.error('Error loading favorites:', error);
        toast({
          title: 'Lỗi',
          description: 'Không thể tải danh sách yêu thích',
          variant: 'destructive'
        });
      } finally {
        setLoading(false);
      }
    };

    fetchFavorites();
  }, [isLoggedIn, authLoading, navigate]);

  const handleRemoveFavorite = async (type: 'hotel' | 'tour', id: number) => {
    try {
      await removeFavorite(type, id);
      if (type === 'hotel') {
        const updated = hotels.filter(h => h.id !== id);
        setHotels(updated);
        // Update cache
        if (favoritesCacheData) {
          favoritesCacheData.hotels = updated;
        }
      } else {
        const updated = tours.filter(t => t.id !== id);
        setTours(updated);
        // Update cache
        if (favoritesCacheData) {
          favoritesCacheData.tours = updated;
        }
      }
      toast({
        title: 'Đã xóa khỏi yêu thích',
      });
    } catch (error) {
      toast({
        title: 'Lỗi',
        description: 'Không thể xóa khỏi yêu thích',
        variant: 'destructive'
      });
    }
  };

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(price);
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="container mx-auto px-4 py-8">
        {/* Breadcrumb */}
        <div className="flex items-center space-x-2 text-sm text-gray-600 mb-6">
          <Link to="/" className="hover:text-blue-600 flex items-center">
            <ArrowLeft className="h-4 w-4 mr-1" />
            Trang chủ
          </Link>
          <span>/</span>
          <span className="text-gray-900">Yêu thích</span>
        </div>

        <h1 className="text-3xl font-bold text-gray-900 mb-8">Yêu thích của tôi</h1>

        {authLoading || loading && (
          <div className="text-center py-16">
            <p className="text-gray-600">Đang tải...</p>
          </div>
        )}

        {!authLoading && !loading && hotels.length === 0 && tours.length === 0 && (
          <div className="text-center py-16">
            <Heart className="h-16 w-16 mx-auto text-gray-300 mb-4" />
            <h2 className="text-2xl font-bold text-gray-900 mb-2">Chưa có yêu thích nào</h2>
            <p className="text-gray-600 mb-8">Bắt đầu thêm khách sạn và tour yêu thích của bạn</p>
            <div className="flex gap-4 justify-center">
              <Link to="/hotels">
                <Button className="bg-orange-500 hover:bg-orange-600">
                  Khám phá khách sạn
                </Button>
              </Link>
              <Link to="/tours">
                <Button variant="outline">
                  Khám phá tour
                </Button>
              </Link>
            </div>
          </div>
        )}

        {!authLoading && !loading && (
          <>
            {/* Hotels Section */}
            {hotels.length > 0 && (
              <div className="mb-12">
                <h2 className="text-2xl font-bold text-gray-900 mb-6">Khách sạn yêu thích ({hotels.length})</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {hotels.map((hotel) => (
                    <Card key={hotel.id} className="overflow-hidden hover:shadow-lg transition">
                      <div className="relative">
                        <img
                          src={hotel.images?.[0] || 'https://via.placeholder.com/300x200'}
                          alt={hotel.name}
                          className="w-full h-48 object-cover"
                        />
                        <button
                          onClick={() => handleRemoveFavorite('hotel', hotel.id)}
                          className="absolute top-2 right-2 p-2 bg-white rounded-full hover:bg-gray-100 transition"
                        >
                          <Trash2 className="h-4 w-4 text-red-500" />
                        </button>
                      </div>
                      <CardContent className="p-4">
                        <Link to={`/hotels/${hotel.id}`}>
                          <h3 className="font-bold text-lg text-gray-900 hover:text-blue-600 truncate">
                            {hotel.name}
                          </h3>
                        </Link>
                        <div className="flex items-center text-sm text-gray-600 mt-1 mb-3">
                          <MapPin className="h-4 w-4 mr-1" />
                          <span className="truncate">{hotel.location}</span>
                        </div>
                        <div className="flex items-center justify-between">
                          <div className="flex items-center">
                            {[...Array(5)].map((_, i) => (
                              <Star
                                key={i}
                                className={`h-4 w-4 ${
                                  i < Math.floor(hotel.rating)
                                    ? 'text-yellow-400 fill-current'
                                    : 'text-gray-300'
                                }`}
                              />
                            ))}
                            <span className="ml-1 text-sm font-medium">{hotel.rating}/5</span>
                          </div>
                          <span className="font-bold text-orange-600">
                            {formatPrice(hotel.price)}
                          </span>
                        </div>
                        <Link to={`/hotels/${hotel.id}`}>
                          <Button className="w-full mt-4 bg-orange-500 hover:bg-orange-600">
                            Chi tiết
                          </Button>
                        </Link>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            )}

            {/* Tours Section */}
            {tours.length > 0 && (
              <div>
                <h2 className="text-2xl font-bold text-gray-900 mb-6">Tour yêu thích ({tours.length})</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {tours.map((tour) => (
                    <Card key={tour.id} className="overflow-hidden hover:shadow-lg transition">
                      <div className="relative">
                        <img
                          src={tour.image || 'https://via.placeholder.com/300x200'}
                          alt={tour.title}
                          className="w-full h-48 object-cover"
                        />
                        <button
                          onClick={() => handleRemoveFavorite('tour', tour.id)}
                          className="absolute top-2 right-2 p-2 bg-white rounded-full hover:bg-gray-100 transition"
                        >
                          <Trash2 className="h-4 w-4 text-red-500" />
                        </button>
                      </div>
                      <CardContent className="p-4">
                        <Link to={`/tours/${tour.id}`}>
                          <h3 className="font-bold text-lg text-gray-900 hover:text-blue-600 truncate">
                            {tour.title}
                          </h3>
                        </Link>
                        <div className="flex items-center text-sm text-gray-600 mt-1 mb-3">
                          <MapPin className="h-4 w-4 mr-1" />
                          <span className="truncate">{tour.destination}</span>
                        </div>
                        <div className="flex items-center justify-between mb-3">
                          <div className="flex items-center">
                            {[...Array(5)].map((_, i) => (
                              <Star
                                key={i}
                                className={`h-4 w-4 ${
                                  i < Math.floor(tour.rating || 0)
                                    ? 'text-yellow-400 fill-current'
                                    : 'text-gray-300'
                                }`}
                              />
                            ))}
                            <span className="ml-1 text-sm font-medium">{tour.rating || 0}/5</span>
                          </div>
                          <span className="text-sm text-gray-500">{tour.duration}</span>
                        </div>
                        <div className="flex items-center justify-between mb-3">
                          <span className="font-bold text-orange-600">
                            {formatPrice(tour.price)}
                          </span>
                        </div>
                        <Link to={`/tours/${tour.id}`}>
                          <Button className="w-full bg-orange-500 hover:bg-orange-600">
                            Chi tiết
                          </Button>
                        </Link>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            )}
        </>
        )}
        <Footer />
      </div>
    </div>
  );
}
