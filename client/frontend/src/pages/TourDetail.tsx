import { useEffect, useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import Header from "@/components/Header";
import TourReviews from "@/components/TourReviews";
import { tourApi } from "@/api/tourApi"; // ✅ sử dụng API
import {
  Star,
  Clock,
  MapPin,
  Users,
  Phone,
  CheckCircle,
  ArrowLeft,
  Share2,
  Heart,
  MessageCircle,
  ShoppingCart,
  Check,
  AlertCircle
} from "lucide-react";
import { useTitle } from "@/hooks/useTitle";
import { useCart } from "@/hooks/useCart";
import { useAuth } from "@/hooks/useAuth";
import { useFavorites } from "@/hooks/useFavorites";
import { toast } from "@/hooks/use-toast";
import { v4 as uuidv4 } from 'uuid';

export default function TourDetail() {
  useTitle("Chi tiết tour - TravelVN");
  const { id } = useParams();
  const navigate = useNavigate();
  const { addItem } = useCart();
  const { isLoggedIn } = useAuth();
  const { isFavorited, addFavorite, removeFavorite, loadFavorites } = useFavorites();
  const [tour, setTour] = useState<any>(null);
  const [selectedDeparture, setSelectedDeparture] = useState("");
  const [guests, setGuests] = useState(2);
  const [isBookingOpen, setIsBookingOpen] = useState(false);
  const [quantity, setQuantity] = useState(1);
  const [bookingData, setBookingData] = useState({
    specialRequests: ''
  });

  // ✅ Gọi API để lấy tour theo ID
  const loadTour = () => {
    if (id) {
      tourApi
        .getById(Number(id))
        .then((res) => setTour(res.data))
        .catch((err) => console.error("Lỗi tải tour:", err));
    }
  };

  useEffect(() => {
    loadTour();
  }, [id]);

  // Load favorites when tour is loaded
  useEffect(() => {
    if (tour && isLoggedIn) {
      loadFavorites().catch(err => console.error('Error loading favorites:', err));
    }
  }, [tour, isLoggedIn]);

  if (!tour)
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16 text-center">
          <h1 className="text-2xl font-bold text-gray-800 mb-4">
            Đang tải thông tin tour...
          </h1>
        </div>
      </div>
    );

  const formatPrice = (price: number) =>
    new Intl.NumberFormat("vi-VN").format(price) + "đ";

  const discountPercent = tour.original_price
    ? Math.round(
        ((tour.original_price - tour.price) / tour.original_price) * 100
      )
    : 0;

  const totalPrice = tour.price * guests;

  const handleBooking = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isLoggedIn) {
      toast({
        title: 'Vui lòng đăng nhập',
        description: 'Bạn cần đăng nhập để đặt tour',
        variant: 'destructive'
      });
      navigate('/login');
      return;
    }
    
    if (!tour || !selectedDeparture) {
      toast({
        title: 'Lỗi',
        description: 'Vui lòng chọn điểm khởi hành',
        variant: 'destructive'
      });
      return;
    }

    const today = new Date();
    const cartItem = {
      id: uuidv4(),
      type: 'tour' as const,
      tourId: tour.id,
      tourName: tour.title,
      tourLocation: tour.destination,
      tourImage: tour.image,
      tourPrice: tour.price,
      quantity: quantity,
      departureDate: today,
      duration: parseInt(tour.duration),
      guests: guests,
      specialRequests: bookingData.specialRequests,
      totalPrice: tour.price * quantity * guests,
    };

    addItem(cartItem);

    toast({
      title: 'Thêm vào giỏ hàng thành công!',
      description: `${tour.title} đã được thêm vào giỏ hàng`,
    });
    
    setIsBookingOpen(false);
    setBookingData({ specialRequests: '' });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      <div className="container mx-auto px-4 py-8">
        {/* Breadcrumb */}
        <div className="flex items-center space-x-2 text-sm text-gray-600 mb-6">
          <Link to="/" className="hover:text-blue-600">
            Trang chủ
          </Link>
          <span>/</span>
          <Link to="/tours" className="hover:text-blue-600">
            Tours
          </Link>
          <span>/</span>
          <span className="text-gray-800">{tour.title}</span>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2">
            <div className="mb-6">
              <div className="flex items-center justify-between mb-4">
                <Link to="/tours">
                  <Button variant="ghost" size="sm">
                    <ArrowLeft className="h-4 w-4 mr-2" />
                    Quay lại
                  </Button>
                </Link>
                <div className="flex space-x-2">
                  <Button variant="ghost" size="sm">
                    <Heart className="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="sm">
                    <Share2 className="h-4 w-4" />
                  </Button>
                </div>
              </div>

              <div className="flex items-center space-x-2 mb-2">
                <Badge className="bg-blue-600">{tour.category}</Badge>
                {discountPercent > 0 && (
                  <Badge className="bg-red-500">Giảm {discountPercent}%</Badge>
                )}
              </div>

              <div className="flex items-start justify-between mb-4">
                <div className="flex-1">
                  <h1 className="text-3xl font-bold text-gray-800 mb-4">
                    {tour.title}
                  </h1>
                </div>
                {isLoggedIn && (
                  <button
                    onClick={() => {
                      const isFav = isFavorited('tour', tour.id);
                      if (isFav) {
                        removeFavorite('tour', tour.id)
                          .then(() => toast({
                            title: 'Đã xóa khỏi yêu thích',
                          }))
                          .catch(() => toast({
                            title: 'Lỗi',
                            description: 'Không thể xóa khỏi yêu thích',
                            variant: 'destructive'
                          }));
                      } else {
                        addFavorite('tour', tour.id)
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
                        isFavorited('tour', tour.id)
                          ? 'fill-red-500 text-red-500'
                          : 'text-gray-400'
                      }`}
                    />
                  </button>
                )}
              </div>

              <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <div className="flex items-center space-x-1">
                  <MapPin className="h-4 w-4" />
                  <span>{tour.destination}</span>
                </div>
                <div className="flex items-center space-x-1">
                  <Clock className="h-4 w-4" />
                  <span>{tour.duration}</span>
                </div>
                <div className="flex items-center space-x-1">
                  <Users className="h-4 w-4" />
                  <span>Tối đa {tour.max_guests} khách</span>
                </div>
                <div className="flex items-center space-x-1">
                  <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                  <span className="font-medium">{tour.rating}</span>
                  <span>({tour.review_count} đánh giá)</span>
                </div>
              </div>
            </div>

            {/* Image */}
            <div className="mb-8">
              <img
                src={tour.image}
                alt={tour.title}
                className="w-full h-96 object-cover rounded-lg shadow-lg"
              />
            </div>

            {/* Tabs Content */}
            <Tabs defaultValue="overview" className="mb-8">
              <TabsList className="grid w-full grid-cols-4">
                <TabsTrigger value="overview">Tổng quan</TabsTrigger>
                <TabsTrigger value="itinerary">Lịch trình</TabsTrigger>
                <TabsTrigger value="includes">Bao gồm</TabsTrigger>
                <TabsTrigger value="reviews">Đánh giá</TabsTrigger>
              </TabsList>

              <TabsContent value="overview" className="space-y-6">
                <Card>
                  <CardHeader>
                    <CardTitle>Mô tả tour</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-gray-600 leading-relaxed">
                      {tour.description}
                    </p>
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle>Điểm nổi bật</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                      {tour.highlights.map((highlight, index) => (
                        <div key={index} className="flex items-start space-x-2">
                          <CheckCircle className="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0" />
                          <span className="text-gray-600">{highlight}</span>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="itinerary">
                <Card>
                  <CardHeader>
                    <CardTitle>Lịch trình chi tiết</CardTitle>
                  </CardHeader>
                  <CardContent>
                    {Array.isArray(tour.itinerary) &&
                      tour.itinerary.map((day: any, index: number) => (
                        <div key={index} className="mb-4">
                          <h4 className="font-semibold text-lg mb-2">
                            Ngày {day.day}: {day.title}
                          </h4>
                          <ul className="list-disc pl-6 text-gray-600">
                            {day.activities.map(
                              (activity: string, i: number) => (
                                <li key={i}>{activity}</li>
                              )
                            )}
                          </ul>
                        </div>
                      ))}
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="includes">
                <Card>
                  <CardHeader>
                    <CardTitle>Tour bao gồm</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <ul className="list-disc pl-6 text-gray-600">
                      {Array.isArray(tour.includes) &&
                        tour.includes.map((item: string, index: number) => (
                          <li key={index}>{item}</li>
                        ))}
                    </ul>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="reviews">
                <TourReviews tourId={tour.id} tourTitle={tour.title} onReviewAdded={loadTour} />
              </TabsContent>
            </Tabs>
          </div>

          {/* Booking Sidebar */}
          <div className="lg:col-span-1">
            <Card className="sticky top-4">
              <CardContent className="p-6">
                <div className="mb-6">
                  <div className="flex items-baseline space-x-2 mb-2">
                    {tour.originalPrice && (
                      <span className="text-lg text-gray-500 line-through">
                        {formatPrice(tour.originalPrice)}
                      </span>
                    )}
                    <span className="text-3xl font-bold text-orange-600">
                      {formatPrice(tour.price)}
                    </span>
                  </div>
                  <span className="text-gray-600">/khách</span>
                </div>

                <div className="space-y-4 mb-6">
                  <div>
                    <Label className="text-sm font-medium">Điểm khởi hành</Label>
                    <Select value={selectedDeparture} onValueChange={setSelectedDeparture}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Chọn điểm khởi hành" />
                      </SelectTrigger>
                      <SelectContent>
                        {tour.departure.map((dep) => (
                          <SelectItem key={dep} value={dep}>
                            {dep}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div>
                    <Label className="text-sm font-medium">Số khách</Label>
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
                </div>

                <div className="border-t pt-4 mb-6">
                  <div className="flex justify-between items-center text-lg font-semibold">
                    <span>Tổng cộng:</span>
                    <span className="text-orange-600">{formatPrice(totalPrice)}</span>
                  </div>
                </div>

                <Dialog open={isBookingOpen} onOpenChange={setIsBookingOpen}>
                  <DialogTrigger asChild>
                    <Button 
                      className="w-full bg-orange-500 hover:bg-orange-600 mb-4" 
                      size="lg"
                      disabled={!selectedDeparture}
                    >
                      Đặt tour ngay
                    </Button>
                  </DialogTrigger>
                  <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                      <DialogTitle>Thêm tour vào giỏ hàng</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleBooking} className="space-y-4">
                      <div className="bg-gray-50 p-4 rounded-lg">
                        <h3 className="font-semibold mb-2">Thông tin tour</h3>
                        <div className="space-y-1 text-sm">
                          <div>Tour: {tour.title}</div>
                          <div>Điểm khởi hành: {selectedDeparture}</div>
                          <div>Số khách: {guests}</div>
                          <div className="font-semibold text-lg text-orange-600 pt-2 border-t">
                            Tổng tiền: {formatPrice(tour.price * quantity * guests)}
                          </div>
                        </div>
                      </div>

                      <div>
                        <Label className="text-sm font-medium">Số lượng tour</Label>
                        <Select value={quantity.toString()} onValueChange={(value) => setQuantity(parseInt(value))}>
                          <SelectTrigger className="mt-1">
                            <SelectValue />
                          </SelectTrigger>
                          <SelectContent>
                            {[1, 2, 3, 4, 5].map((num) => (
                              <SelectItem key={num} value={num.toString()}>
                                {num}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      </div>

                      <div>
                        <Label htmlFor="note">Yêu cầu đặc biệt</Label>
                        <Textarea 
                          id="note" 
                          placeholder="Yêu cầu đặc biệt..." 
                          value={bookingData.specialRequests}
                          onChange={(e) => setBookingData({ specialRequests: e.target.value })}
                        />
                      </div>

                      <div className="flex space-x-2">
                        <Button 
                          type="button"
                          variant="outline"
                          onClick={() => setIsBookingOpen(false)}
                          className="flex-1"
                        >
                          Hủy
                        </Button>
                        <Button 
                          type="submit" 
                          className="flex-1 bg-orange-500 hover:bg-orange-600"
                        >
                          <ShoppingCart className="h-4 w-4 mr-2" />
                          Thêm vào giỏ
                        </Button>
                        <Button 
                          type="button"
                          className="flex-1 bg-blue-600 hover:bg-blue-700"
                          onClick={() => {
                            handleBooking(new Event('submit') as any);
                            setTimeout(() => navigate('/checkout'), 300);
                          }}
                        >
                          <Check className="h-4 w-4 mr-2" />
                          Thanh toán
                        </Button>
                      </div>
                    </form>
                  </DialogContent>
                </Dialog>

                <Button variant="outline" className="w-full mb-4">
                  <Phone className="h-4 w-4 mr-2" />
                  Gọi tư vấn: 1900 1234
                </Button>

                <div className="text-center text-sm text-gray-600">
                  <div className="flex items-center justify-center space-x-1 mb-2">
                    <CheckCircle className="h-4 w-4 text-green-500" />
                    <span>Đặt tour không mất phí</span>
                  </div>
                  <div className="flex items-center justify-center space-x-1">
                    <CheckCircle className="h-4 w-4 text-green-500" />
                    <span>Hỗ trợ 24/7</span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
