import { useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { 
  Star, 
  MapPin, 
  Wifi, 
  Car, 
  Coffee, 
  Utensils, 
  ArrowLeft, 
  Users, 
  Bed,
  Bath,
  Maximize,
  CalendarIcon,
  CreditCard,
  User,
  Phone,
  Mail,
  Check,
  ShoppingCart,
  AlertCircle,
  Heart
} from 'lucide-react';
import Header from '@/components/Header';
import { hotelApi } from '@/api/hotelApi';
import { useEffect } from 'react';
import { format } from 'date-fns';
import { vi } from 'date-fns/locale';
import { cn } from '@/lib/utils';
import { toast } from '@/hooks/use-toast';
import { useTitle } from "@/hooks/useTitle";
import { useCart } from '@/hooks/useCart';
import { useAuth } from '@/hooks/useAuth';
import { useFavorites } from '@/hooks/useFavorites';
import { v4 as uuidv4 } from 'uuid';

interface Room {
  id: number;
  name: string;
  size: number;
  capacity: number;
  beds: string;
  price: number;
  originalPrice?: number;
  amenities: string[];
  images: string[];
  description: string;
  available: number;
}

interface Hotel {
  id: number;
  name: string;
  location: string;
  rating: number;
  price: number;
  originalPrice?: number;
  images: string[];
  amenities: string[];
  description: string;
  reviews: number;
  rooms: Room[];
  policies: {
    checkIn: string;
    checkOut: string;
    cancellation: string;
    children: string;
  };
}

const HotelPlaceholder: Hotel = {
  id: 0,
  name: '',
  location: '',
  rating: 0,
  price: 0,
  images: [],
  amenities: [],
  description: '',
  reviews: 0,
  rooms: [],
  policies: {
    checkIn: '',
    checkOut: '',
    cancellation: '',
    children: ''
  }
};

export default function HotelDetail() {
  useTitle("Chi tiết phòng - TravelVN");
  const { id } = useParams();
  const navigate = useNavigate();
  const { addItem } = useCart();
  const { isLoggedIn } = useAuth();
  const { isFavorited, addFavorite, removeFavorite, loadFavorites } = useFavorites();
  const [hotelData, setHotelData] = useState<Hotel | null>(null);
  const [selectedRoom, setSelectedRoom] = useState<Room | null>(null);
  const [checkIn, setCheckIn] = useState<Date>();
  const [checkOut, setCheckOut] = useState<Date>();
  const [guests, setGuests] = useState(2);
  const [rooms, setRooms] = useState(1);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [showBookingForm, setShowBookingForm] = useState(false);
  const [bookingData, setBookingData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    specialRequests: ''
  });

  // Load favorites when hotel is loaded
  useEffect(() => {
    if (hotelData && isLoggedIn) {
      loadFavorites().catch(err => console.error('Error loading favorites:', err));
    }
  }, [hotelData, isLoggedIn]);

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(price);
  };

  useEffect(() => {
    (async () => {
      if (!id) return;
      try {
        const res = await hotelApi.getById(id);
        const data = (res as any).data;
        // Normalize API response (snake_case -> camelCase, group policies)
        const mapped = {
          id: data.id,
          name: data.name,
          location: data.location,
          rating: data.rating,
          price: data.price,
          originalPrice: data.original_price ?? data.originalPrice,
          images: data.images ?? (data.image ? [data.image] : []),
          image: data.image ?? (data.images && data.images.length ? data.images[0] : undefined),
          amenities: data.amenities ?? [],
          description: data.description ?? '',
          reviews: data.reviews ?? 0,
          rooms: (data.rooms ?? []).map((r: any) => ({
            id: r.id,
            hotel_id: r.hotel_id,
            name: r.name,
            size: r.size,
            capacity: r.capacity,
            beds: r.beds,
            price: r.price,
            originalPrice: r.original_price ?? r.originalPrice,
            amenities: r.amenities ?? [],
            images: r.images ?? (r.image ? [r.image] : []),
            description: r.description ?? '',
            available: r.available ?? 0,
          })),
          policies: {
            checkIn: data.check_in ?? data.checkIn ?? '',
            checkOut: data.check_out ?? data.checkOut ?? '',
            cancellation: data.cancellation ?? '',
            children: data.children ?? '',
          },
        } as Hotel;

        setHotelData(mapped);
      } catch (err) {
        console.error('Failed to fetch hotel', err);
      }
    })();
  }, [id]);

  const renderStars = (rating: number) => {
    return Array.from({ length: 5 }, (_, i) => (
      <Star
        key={i}
        className={`h-4 w-4 ${i < Math.floor(rating) ? 'text-yellow-400 fill-current' : 'text-gray-300'}`}
      />
    ));
  };

  const calculateNights = () => {
    if (!checkIn || !checkOut) return 0;
    const diffTime = Math.abs(checkOut.getTime() - checkIn.getTime());
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  };

  const calculateTotal = () => {
    if (!selectedRoom) return 0;
    const nights = calculateNights();
    return selectedRoom.price * nights * rooms;
  };

  const handleBookingSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isLoggedIn) {
      toast({
        title: 'Vui lòng đăng nhập',
        description: 'Bạn cần đăng nhập để đặt phòng',
        variant: 'destructive'
      });
      navigate('/login');
      return;
    }
    
    if (!hotelData || !selectedRoom || !checkIn || !checkOut) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng kiểm tra thông tin đặt phòng',
        variant: 'destructive'
      });
      return;
    }

    // Add to cart
    const cartItem = {
      id: uuidv4(),
      type: 'hotel' as const,
      hotelId: hotelData.id,
      hotelName: hotelData.name,
      hotelLocation: hotelData.location,
      hotelImage: hotelData.images[0] || '',
      roomId: selectedRoom.id,
      roomName: selectedRoom.name,
      roomPrice: selectedRoom.price,
      quantity: rooms,
      checkIn,
      checkOut,
      guests,
      specialRequests: bookingData.specialRequests,
      totalPrice: calculateTotal(),
    };

    addItem(cartItem);

    toast({
      title: 'Thêm vào giỏ hàng thành công!',
      description: `${selectedRoom.name} tại ${hotelData.name} đã được thêm vào giỏ hàng`,
    });
    
    setShowBookingForm(false);
    setBookingData({
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      specialRequests: ''
    });
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setBookingData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      {!hotelData && (
        <div className="container mx-auto px-4 py-8 text-center text-gray-600">Đang tải thông tin khách sạn...</div>
      )}
      {hotelData && (
      
      <div className="container mx-auto px-4 py-8">
        {/* Breadcrumb */}
        <div className="flex items-center space-x-2 text-sm text-gray-600 mb-6">
          <Link to="/hotels" className="hover:text-blue-600 flex items-center">
            <ArrowLeft className="h-4 w-4 mr-1" />
            Khách sạn
          </Link>
          <span>/</span>
          <span className="text-gray-900">{hotelData.name}</span>
        </div>

        {/* Hotel Images */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
          <div className="lg:col-span-2">
            <img
              src={hotelData.images[currentImageIndex]}
              alt={hotelData.name}
              className="w-full h-96 object-cover rounded-lg"
            />
            <div className="flex space-x-2 mt-4">
              {hotelData.images.map((image, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentImageIndex(index)}
                  className={`flex-1 h-20 rounded-lg overflow-hidden border-2 ${
                    currentImageIndex === index ? 'border-blue-500' : 'border-transparent'
                  }`}
                >
                  <img src={image} alt="" className="w-full h-full object-cover" />
                </button>
              ))}
            </div>
          </div>
          
          {/* Booking Card */}
          <Card className="h-fit sticky top-24">
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  {hotelData.originalPrice && (
                    <span className="text-sm text-gray-500 line-through">
                      {formatPrice(hotelData.originalPrice)}
                    </span>
                  )}
                  <div className="text-2xl font-bold text-orange-600">
                    {formatPrice(hotelData.price)}
                    <span className="text-sm text-gray-500 font-normal">/đêm</span>
                  </div>
                </div>
                <div className="flex items-center">
                  {renderStars(hotelData.rating)}
                  <span className="ml-1 text-sm text-gray-600">({hotelData.reviews})</span>
                </div>
              </div>
            </CardHeader>
            <CardContent className="space-y-4">
              {/* Date Selection */}
              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="text-sm font-medium mb-1 block">Nhận phòng</label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        className={cn(
                          "w-full justify-start text-left font-normal",
                          !checkIn && "text-muted-foreground"
                        )}
                      >
                        <CalendarIcon className="mr-2 h-4 w-4" />
                        {checkIn ? format(checkIn, "dd/MM", { locale: vi }) : "Chọn ngày"}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={checkIn}
                        onSelect={setCheckIn}
                        disabled={(date) => date < new Date()}
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">Trả phòng</label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        className={cn(
                          "w-full justify-start text-left font-normal",
                          !checkOut && "text-muted-foreground"
                        )}
                      >
                        <CalendarIcon className="mr-2 h-4 w-4" />
                        {checkOut ? format(checkOut, "dd/MM", { locale: vi }) : "Chọn ngày"}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={checkOut}
                        onSelect={setCheckOut}
                        disabled={(date) => date < new Date() || (checkIn && date <= checkIn)}
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                </div>
              </div>

              {/* Guests and Rooms */}
              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="text-sm font-medium mb-1 block">Khách</label>
                  <Select value={guests.toString()} onValueChange={(value) => setGuests(parseInt(value))}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {[1, 2, 3, 4, 5, 6].map((num) => (
                        <SelectItem key={num} value={num.toString()}>
                          {num} khách
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">Phòng</label>
                  <Select value={rooms.toString()} onValueChange={(value) => setRooms(parseInt(value))}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {[1, 2, 3, 4].map((num) => (
                        <SelectItem key={num} value={num.toString()}>
                          {num} phòng
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {checkIn && checkOut && (
                <div className="bg-blue-50 p-3 rounded-lg">
                  <div className="text-sm text-gray-600">
                    {calculateNights()} đêm × {rooms} phòng
                  </div>
                  <div className="text-lg font-bold text-blue-600">
                    Tổng: {formatPrice(calculateTotal())}
                  </div>
                </div>
              )}

              <Button 
                className="w-full bg-orange-500 hover:bg-orange-600"
                disabled={!checkIn || !checkOut}
              >
                Xem phòng trống
              </Button>
            </CardContent>
          </Card>
        </div>

        {/* Hotel Info */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-6">
            {/* Basic Info */}
            <Card>
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <CardTitle className="text-2xl">{hotelData.name}</CardTitle>
                    <div className="flex items-center space-x-4 mt-2">
                      <div className="flex items-center">
                        {renderStars(hotelData.rating)}
                        <span className="ml-2 font-medium">{hotelData.rating}/5</span>
                      </div>
                      <div className="flex items-center text-gray-600">
                        <MapPin className="h-4 w-4 mr-1" />
                        <span className="text-sm">{hotelData.location}</span>
                      </div>
                    </div>
                  </div>
                  {isLoggedIn && (
                    <button
                      onClick={() => {
                        const isFav = isFavorited('hotel', hotelData.id);
                        if (isFav) {
                          removeFavorite('hotel', hotelData.id)
                            .then(() => toast({
                              title: 'Đã xóa khỏi yêu thích',
                            }))
                            .catch(() => toast({
                              title: 'Lỗi',
                              description: 'Không thể xóa khỏi yêu thích',
                              variant: 'destructive'
                            }));
                        } else {
                          addFavorite('hotel', hotelData.id)
                            .then(() => toast({
                              title: 'Đã thêm vào yêu thích',
                            }))
                            .catch(() => toast({
                              title: 'Lỗi',
                              description: 'Không thể thêm vào yêu thích',
                              variant: 'destructive'
                            }));
                        }
                      }}
                      className="p-2 hover:bg-gray-100 rounded-lg transition"
                    >
                      <Heart
                        className={`h-6 w-6 ${
                          isFavorited('hotel', hotelData.id)
                            ? 'fill-red-500 text-red-500'
                            : 'text-gray-400'
                        }`}
                      />
                    </button>
                  )}
                </div>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600 mb-4">{hotelData.description}</p>
                <div className="flex flex-wrap gap-2">
                  {hotelData.amenities.map((amenity, index) => (
                    <Badge key={index} variant="secondary" className="text-xs">
                      {amenity}
                    </Badge>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Available Rooms */}
            {checkIn && checkOut && (
              <Card>
                <CardHeader>
                  <CardTitle>Phòng trống ({format(checkIn, 'dd/MM')} - {format(checkOut, 'dd/MM')})</CardTitle>
                  <CardDescription>
                    {calculateNights()} đêm • {guests} khách • {rooms} phòng
                  </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  {hotelData.rooms.map((room) => (
                    <div key={room.id} className="border rounded-lg p-4">
                      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="md:col-span-1">
                          <img
                            src={room.images[0]}
                            alt={room.name}
                            className="w-full h-32 object-cover rounded-lg"
                          />
                        </div>
                        <div className="md:col-span-2">
                          <div className="flex justify-between items-start mb-2">
                            <div>
                              <h3 className="font-bold text-lg">{room.name}</h3>
                              <div className="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                                <div className="flex items-center">
                                  <Maximize className="h-4 w-4 mr-1" />
                                  {room.size}m²
                                </div>
                                <div className="flex items-center">
                                  <Users className="h-4 w-4 mr-1" />
                                  {room.capacity} khách
                                </div>
                                <div className="flex items-center">
                                  <Bed className="h-4 w-4 mr-1" />
                                  {room.beds}
                                </div>
                              </div>
                              <p className="text-sm text-gray-600 mb-2">{room.description}</p>
                              <div className="flex flex-wrap gap-1">
                                {room.amenities.slice(0, 3).map((amenity, index) => (
                                  <span key={index} className="text-xs bg-gray-100 px-2 py-1 rounded">
                                    {amenity}
                                  </span>
                                ))}
                                {room.amenities.length > 3 && (
                                  <span className="text-xs text-gray-500">+{room.amenities.length - 3}</span>
                                )}
                              </div>
                            </div>
                            <div className="text-right">
                              {room.originalPrice && (
                                <span className="text-sm text-gray-500 line-through block">
                                  {formatPrice(room.originalPrice)}
                                </span>
                              )}
                              <div className="text-xl font-bold text-orange-600">
                                {formatPrice(room.price)}
                                <span className="text-sm text-gray-500 font-normal">/đêm</span>
                              </div>
                              <div className="text-sm text-gray-600 mb-2">
                                Còn {room.available} phòng
                              </div>
                              <Dialog open={showBookingForm} onOpenChange={setShowBookingForm}>
                                <DialogTrigger asChild>
                                  <Button 
                                    onClick={() => setSelectedRoom(room)}
                                    className="bg-orange-500 hover:bg-orange-600"
                                    disabled={room.available === 0}
                                  >
                                    {room.available === 0 ? 'Hết phòng' : 'Đặt phòng'}
                                  </Button>
                                </DialogTrigger>
                                <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                                  <DialogHeader>
                                    <DialogTitle>Đặt phòng - {selectedRoom?.name}</DialogTitle>
                                    <DialogDescription>
                                      Điền thông tin để hoàn tất đặt phòng
                                    </DialogDescription>
                                  </DialogHeader>
                                  
                                  <form onSubmit={handleBookingSubmit} className="space-y-6">
                                    {/* Booking Summary */}
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                      <h3 className="font-semibold mb-2">Thông tin đặt phòng</h3>
                                      <div className="space-y-1 text-sm">
                                        <div>Khách sạn: {hotelData.name}</div>
                                        <div>Phòng: {selectedRoom?.name}</div>
                                        <div>Nhận phòng: {checkIn && format(checkIn, 'dd/MM/yyyy', { locale: vi })}</div>
                                        <div>Trả phòng: {checkOut && format(checkOut, 'dd/MM/yyyy', { locale: vi })}</div>
                                        <div>Số đêm: {calculateNights()}</div>
                                        <div>Số phòng: {rooms}</div>
                                        <div>Số khách: {guests}</div>
                                        <div className="font-semibold text-lg text-orange-600 pt-2 border-t">
                                          Tổng tiền: {formatPrice(calculateTotal())}
                                        </div>
                                      </div>
                                    </div>

                                    {/* Guest Information */}
                                    <div className="space-y-4">
                                      <h3 className="font-semibold flex items-center">
                                        <User className="h-5 w-5 mr-2" />
                                        Thông tin khách hàng
                                      </h3>
                                      <div className="grid grid-cols-2 gap-4">
                                        <div>
                                          <label className="text-sm font-medium mb-1 block">Họ *</label>
                                          <Input
                                            name="firstName"
                                            value={bookingData.firstName}
                                            onChange={handleInputChange}
                                            placeholder="Nhập họ"
                                            required
                                          />
                                        </div>
                                        <div>
                                          <label className="text-sm font-medium mb-1 block">Tên *</label>
                                          <Input
                                            name="lastName"
                                            value={bookingData.lastName}
                                            onChange={handleInputChange}
                                            placeholder="Nhập tên"
                                            required
                                          />
                                        </div>
                                      </div>
                                      <div>
                                        <label className="text-sm font-medium mb-1 block">Email *</label>
                                        <Input
                                          name="email"
                                          type="email"
                                          value={bookingData.email}
                                          onChange={handleInputChange}
                                          placeholder="Nhập email"
                                          required
                                        />
                                      </div>
                                      <div>
                                        <label className="text-sm font-medium mb-1 block">Số điện thoại *</label>
                                        <Input
                                          name="phone"
                                          value={bookingData.phone}
                                          onChange={handleInputChange}
                                          placeholder="Nhập số điện thoại"
                                          required
                                        />
                                      </div>
                                      <div>
                                        <label className="text-sm font-medium mb-1 block">Yêu cầu đặc biệt</label>
                                        <textarea
                                          name="specialRequests"
                                          value={bookingData.specialRequests}
                                          onChange={handleInputChange}
                                          placeholder="Ví dụ: Phòng tầng cao, giường đôi..."
                                          className="w-full p-2 border border-gray-200 rounded-md resize-none"
                                          rows={3}
                                        />
                                      </div>
                                    </div>

                                    {/* Payment Method */}
                                    <div className="space-y-4">
                                      <h3 className="font-semibold flex items-center">
                                        <CreditCard className="h-5 w-5 mr-2" />
                                        Phương thức thanh toán
                                      </h3>
                                      <div className="space-y-2">
                                        <label className="flex items-center space-x-2">
                                          <input type="radio" name="payment" value="card" defaultChecked />
                                          <span>Thẻ tín dụng/ghi nợ</span>
                                        </label>
                                        <label className="flex items-center space-x-2">
                                          <input type="radio" name="payment" value="transfer" />
                                          <span>Chuyển khoản ngân hàng</span>
                                        </label>
                                        <label className="flex items-center space-x-2">
                                          <input type="radio" name="payment" value="cash" />
                                          <span>Thanh toán tại khách sạn</span>
                                        </label>
                                      </div>
                                    </div>

                                    {/* Terms */}
                                    <div className="space-y-2">
                                      <label className="flex items-start space-x-2">
                                        <input type="checkbox" required className="mt-1" />
                                        <span className="text-sm text-gray-600">
                                          Tôi đồng ý với <a href="#" className="text-blue-600 hover:underline">điều khoản và điều kiện</a> của khách sạn
                                        </span>
                                      </label>
                                      <label className="flex items-start space-x-2">
                                        <input type="checkbox" className="mt-1" />
                                        <span className="text-sm text-gray-600">
                                          Tôi muốn nhận thông tin khuyến mãi qua email
                                        </span>
                                      </label>
                                    </div>

                                    <div className="flex space-x-4">
                                      <Button 
                                        type="button" 
                                        variant="outline" 
                                        onClick={() => setShowBookingForm(false)}
                                        className="flex-1"
                                      >
                                        Hủy
                                      </Button>
                                      <Button 
                                        type="submit" 
                                        className="flex-1 bg-orange-500 hover:bg-orange-600"
                                      >
                                        <ShoppingCart className="h-4 w-4 mr-2" />
                                        Thêm vào giỏ hàng
                                      </Button>
                                      <Button 
                                        type="button"
                                        className="flex-1 bg-blue-600 hover:bg-blue-700"
                                        onClick={() => {
                                          handleBookingSubmit(new Event('submit') as any);
                                          setTimeout(() => navigate('/checkout'), 300);
                                        }}
                                      >
                                        <Check className="h-4 w-4 mr-2" />
                                        Thanh toán ngay
                                      </Button>
                                    </div>
                                  </form>
                                </DialogContent>
                              </Dialog>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </CardContent>
              </Card>
            )}
          </div>

          {/* Hotel Policies */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Chính sách khách sạn</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <h4 className="font-medium mb-1">Giờ nhận/trả phòng</h4>
                  <p className="text-sm text-gray-600">Nhận phòng: {hotelData.policies.checkIn}</p>
                  <p className="text-sm text-gray-600">Trả phòng: {hotelData.policies.checkOut}</p>
                </div>
                <div>
                  <h4 className="font-medium mb-1">Chính sách hủy</h4>
                  <p className="text-sm text-gray-600">{hotelData.policies.cancellation}</p>
                </div>
                <div>
                  <h4 className="font-medium mb-1">Chính sách trẻ em</h4>
                  <p className="text-sm text-gray-600">{hotelData.policies.children}</p>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Tiện ích khách sạn</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 gap-2">
                  {hotelData.amenities.map((amenity, index) => (
                    <div key={index} className="flex items-center space-x-2 text-sm">
                      <Check className="h-4 w-4 text-green-500" />
                      <span>{amenity}</span>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
      )}
    </div>
  );
}