import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Star, MapPin, Wifi, Car, Coffee, Utensils, Search, Filter } from 'lucide-react';
import Header from '@/components/Header';
import { hotelApi } from '@/api/hotelApi';
import { useTitle } from '@/hooks/useTitle';

interface Hotel {
  id: number;
  name: string;
  location: string;
  rating: number;
  price: number;
  originalPrice?: number;
  image: string;
  amenities: string[];
  description: string;
  reviews: number;
}

const HotelsPlaceholder: Hotel[] = [];

const locations = [
  "Tất cả địa điểm",
  "TP. Hồ Chí Minh",
  "Hà Nội",
  "Đà Nẵng",
  "Nha Trang",
  "Đà Lạt",
  "Phú Quốc",
  "Hạ Long",
  "Sapa",
  "Hội An"
];

export default function Hotels() {
  useTitle("Khách Sạn & Resort");
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedLocation, setSelectedLocation] = useState('');
  const [priceRange, setPriceRange] = useState('');
  const [rating, setRating] = useState('');
  const [hotels, setHotels] = useState<Hotel[]>(HotelsPlaceholder);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      setLoading(true);
      try {
        const res = await hotelApi.getAll();
        const data = (res as any).data as any[] || [];
        const mapped = data.map(d => ({
          id: d.id,
          name: d.name,
          location: d.location,
          rating: d.rating ?? 0,
          price: d.price ?? 0,
          originalPrice: d.original_price ?? d.originalPrice,
          image: d.image ?? (Array.isArray(d.images) && d.images.length ? d.images[0] : ''),
          amenities: d.amenities ?? [],
          description: d.description ?? '',
          reviews: d.reviews ?? 0,
        })) as Hotel[];
        setHotels(mapped || []);
      } catch (err) {
        console.error('Failed to fetch hotels', err);
        setError('Không thể tải danh sách khách sạn');
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const filteredHotels = hotels.filter(hotel => {
    const matchesSearch = hotel.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         hotel.location.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesLocation = !selectedLocation || selectedLocation === 'Tất cả địa điểm' || 
                           hotel.location.includes(selectedLocation);
    const matchesPrice = !priceRange || 
                        (priceRange === 'under-2m' && hotel.price < 2000000) ||
                        (priceRange === '2m-3m' && hotel.price >= 2000000 && hotel.price < 3000000) ||
                        (priceRange === 'over-3m' && hotel.price >= 3000000);
    const matchesRating = !rating || hotel.rating >= parseFloat(rating);
    
    return matchesSearch && matchesLocation && matchesPrice && matchesRating;
  });

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(price);
  };

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

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      {loading && (
        <div className="container mx-auto px-4 py-6 text-center text-gray-600">Đang tải dữ liệu khách sạn...</div>
      )}
      {error && (
        <div className="container mx-auto px-4 py-6 text-center text-red-600">{error}</div>
      )}
      
      {/* Hero Section */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">
            Khách Sạn & Resort
          </h1>
          <p className="text-xl mb-8">
            Tìm kiếm và đặt phòng khách sạn tốt nhất với giá ưu đãi
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        {/* Search and Filter Section */}
        <Card className="mb-8">
          <CardHeader>
            <CardTitle className="flex items-center">
              <Filter className="h-5 w-5 mr-2" />
              Tìm kiếm & Lọc khách sạn
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
              <div>
                <label className="text-sm font-medium mb-2 block">Tìm kiếm</label>
                <div className="relative">
                  <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    placeholder="Tên khách sạn, địa điểm..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10 bg-white border-gray-200 text-gray-900"
                  />
                </div>
              </div>

              <div>
                <label className="text-sm font-medium mb-2 block">Địa điểm</label>
                <Select value={selectedLocation} onValueChange={setSelectedLocation}>
                  <SelectTrigger className="bg-white border-gray-200 text-gray-900">
                    <SelectValue placeholder="Chọn địa điểm" />
                  </SelectTrigger>
                  <SelectContent className="bg-white">
                    {locations.map((location) => (
                      <SelectItem key={location} value={location} className="text-gray-900">
                        {location}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label className="text-sm font-medium mb-2 block">Khoảng giá</label>
                <Select value={priceRange} onValueChange={setPriceRange}>
                  <SelectTrigger className="bg-white border-gray-200 text-gray-900">
                    <SelectValue placeholder="Chọn giá" />
                  </SelectTrigger>
                  <SelectContent className="bg-white">
                    <SelectItem value="under-2m" className="text-gray-900">Dưới 2 triệu</SelectItem>
                    <SelectItem value="2m-3m" className="text-gray-900">2 - 3 triệu</SelectItem>
                    <SelectItem value="over-3m" className="text-gray-900">Trên 3 triệu</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label className="text-sm font-medium mb-2 block">Đánh giá</label>
                <Select value={rating} onValueChange={setRating}>
                  <SelectTrigger className="bg-white border-gray-200 text-gray-900">
                    <SelectValue placeholder="Chọn sao" />
                  </SelectTrigger>
                  <SelectContent className="bg-white">
                    <SelectItem value="4.5" className="text-gray-900">4.5+ sao</SelectItem>
                    <SelectItem value="4.0" className="text-gray-900">4.0+ sao</SelectItem>
                    <SelectItem value="3.5" className="text-gray-900">3.5+ sao</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <label className="text-sm font-medium mb-2 block text-transparent">Action</label>
                <Button 
                  onClick={() => {
                    setSearchTerm('');
                    setSelectedLocation('');
                    setPriceRange('');
                    setRating('');
                  }}
                  variant="outline"
                  className="w-full"
                >
                  Xóa bộ lọc
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Results */}
        <div className="mb-6">
          <h2 className="text-2xl font-bold text-gray-900">
            Tìm thấy {filteredHotels.length} khách sạn
          </h2>
        </div>

        {/* Hotels Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredHotels.map((hotel) => (
            <Card key={hotel.id} className="overflow-hidden hover:shadow-lg transition-shadow">
              <div className="relative">
                <img
                  src={hotel.image}
                  alt={hotel.name}
                  className="w-full h-48 object-cover"
                />
                {hotel.originalPrice && (
                  <Badge className="absolute top-2 left-2 bg-red-500">
                    Giảm {Math.round((1 - hotel.price / hotel.originalPrice) * 100)}%
                  </Badge>
                )}
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
                  {hotel.amenities.slice(0, 3).map((amenity, index) => (
                    <div key={index} className="flex items-center text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                      {getAmenityIcon(amenity)}
                      <span className="ml-1">{amenity}</span>
                    </div>
                  ))}
                  {hotel.amenities.length > 3 && (
                    <span className="text-xs text-gray-500">+{hotel.amenities.length - 3} tiện ích</span>
                  )}
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    {hotel.originalPrice && (
                      <span className="text-sm text-gray-500 line-through">
                        {formatPrice(hotel.originalPrice)}
                      </span>
                    )}
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

        {filteredHotels.length === 0 && (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">Không tìm thấy khách sạn phù hợp với bộ lọc của bạn.</p>
            <Button 
              onClick={() => {
                setSearchTerm('');
                setSelectedLocation('');
                setPriceRange('');
                setRating('');
              }}
              className="mt-4"
              variant="outline"
            >
              Xóa bộ lọc
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}