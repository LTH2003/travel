import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Trash2, ArrowLeft, ShoppingCart, Plus, Minus, Plane, Hotel } from 'lucide-react';
import Header from '@/components/Header';
import { useCart, CartItem, HotelCartItem, TourCartItem } from '@/hooks/useCart';
import { useTitle } from '@/hooks/useTitle';
import { format } from 'date-fns';
import { vi } from 'date-fns/locale';

interface CartItemCardProps {
  formatPrice: (price: number) => string;
  updateItem: (id: string, updates: Partial<CartItem>) => void;
  removeItem: (id: string) => void;
}

function HotelCartItemCard({ item, formatPrice, calculateNights, updateItem, removeItem }: CartItemCardProps & { item: HotelCartItem; calculateNights: (c: Date, o: Date) => number }) {
  return (
    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div className="md:col-span-1">
        <img
          src={item.hotelImage}
          alt={item.hotelName}
          className="w-full h-32 object-cover rounded-lg"
        />
      </div>

      <div className="md:col-span-2 space-y-2">
        <div>
          <h3 className="font-bold text-lg text-gray-900">{item.hotelName}</h3>
          <p className="text-sm text-gray-600">{item.hotelLocation}</p>
        </div>

        <div className="border-t pt-2">
          <p className="font-medium text-sm text-gray-900">{item.roomName}</p>
          <p className="text-sm text-gray-600">
            {format(item.checkIn, 'dd/MM/yyyy', { locale: vi })} - {format(item.checkOut, 'dd/MM/yyyy', { locale: vi })}
          </p>
          <p className="text-sm text-gray-600">
            {calculateNights(item.checkIn, item.checkOut)} đêm • {item.guests} khách
          </p>
        </div>

        {item.specialRequests && (
          <div className="bg-gray-50 p-2 rounded text-xs">
            <p className="text-gray-600"><span className="font-medium">Yêu cầu:</span> {item.specialRequests}</p>
          </div>
        )}
      </div>

      <div className="md:col-span-1 flex flex-col justify-between">
        <div className="text-right">
          <div className="text-sm text-gray-600 mb-1">{formatPrice(item.roomPrice)}/đêm</div>
          <div className="text-xl font-bold text-orange-600">{formatPrice(item.totalPrice)}</div>
        </div>

        <div className="flex items-center space-x-2 mt-4">
          <button onClick={() => updateItem(item.id, { quantity: Math.max(1, item.quantity - 1) })} className="p-1 hover:bg-gray-200 rounded">
            <Minus className="h-4 w-4" />
          </button>
          <Input type="number" min="1" value={item.quantity} onChange={(e) => updateItem(item.id, { quantity: parseInt(e.target.value) || 1 })} className="w-12 text-center h-8" />
          <button onClick={() => updateItem(item.id, { quantity: item.quantity + 1 })} className="p-1 hover:bg-gray-200 rounded">
            <Plus className="h-4 w-4" />
          </button>
        </div>

        <button onClick={() => removeItem(item.id)} className="text-red-500 hover:text-red-700 text-sm mt-2 flex items-center justify-end">
          <Trash2 className="h-4 w-4 mr-1" />
          Xóa
        </button>
      </div>
    </div>
  );
}

function TourCartItemCard({ item, formatPrice, updateItem, removeItem }: CartItemCardProps & { item: TourCartItem }) {
  return (
    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div className="md:col-span-1">
        <img
          src={item.tourImage}
          alt={item.tourName}
          className="w-full h-32 object-cover rounded-lg"
        />
      </div>

      <div className="md:col-span-2 space-y-2">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <Plane className="h-4 w-4 text-blue-600" />
            <h3 className="font-bold text-lg text-gray-900">{item.tourName}</h3>
          </div>
          <p className="text-sm text-gray-600">{item.tourLocation}</p>
        </div>

        <div className="border-t pt-2">
          <p className="text-sm text-gray-600">{item.duration} ngày</p>
          <p className="text-sm text-gray-600">{item.guests} khách</p>
        </div>

        {item.specialRequests && (
          <div className="bg-gray-50 p-2 rounded text-xs">
            <p className="text-gray-600"><span className="font-medium">Yêu cầu:</span> {item.specialRequests}</p>
          </div>
        )}
      </div>

      <div className="md:col-span-1 flex flex-col justify-between">
        <div className="text-right">
          <div className="text-sm text-gray-600 mb-1">{formatPrice(item.tourPrice)}/khách</div>
          <div className="text-xl font-bold text-orange-600">{formatPrice(item.totalPrice)}</div>
        </div>

        <div className="flex items-center space-x-2 mt-4">
          <button onClick={() => updateItem(item.id, { quantity: Math.max(1, item.quantity - 1) })} className="p-1 hover:bg-gray-200 rounded">
            <Minus className="h-4 w-4" />
          </button>
          <Input type="number" min="1" value={item.quantity} onChange={(e) => updateItem(item.id, { quantity: parseInt(e.target.value) || 1 })} className="w-12 text-center h-8" />
          <button onClick={() => updateItem(item.id, { quantity: item.quantity + 1 })} className="p-1 hover:bg-gray-200 rounded">
            <Plus className="h-4 w-4" />
          </button>
        </div>

        <button onClick={() => removeItem(item.id)} className="text-red-500 hover:text-red-700 text-sm mt-2 flex items-center justify-end">
          <Trash2 className="h-4 w-4 mr-1" />
          Xóa
        </button>
      </div>
    </div>
  );
}

export default function Cart() {
  useTitle('Giỏ hàng - TravelVN');
  const { items, removeItem, updateItem, getTotal, clearCart } = useCart();

  const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(price);
  };

  const calculateNights = (checkIn: Date, checkOut: Date) => {
    const diffTime = Math.abs(checkOut.getTime() - checkIn.getTime());
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  };

  if (items.length === 0) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        <div className="container mx-auto px-4 py-16">
          <div className="max-w-2xl mx-auto text-center">
            <ShoppingCart className="h-16 w-16 mx-auto text-gray-300 mb-4" />
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Giỏ hàng trống</h1>
            <p className="text-gray-600 mb-8">Bạn chưa thêm bất kỳ khách sạn hoặc tour nào vào giỏ hàng</p>
            <div className="flex gap-4 justify-center">
              <Link to="/hotels">
                <Button className="bg-orange-500 hover:bg-orange-600">
                  <Hotel className="h-4 w-4 mr-2" />
                  Chọn khách sạn
                </Button>
              </Link>
              <Link to="/tours">
                <Button variant="outline">
                  <Plane className="h-4 w-4 mr-2" />
                  Chọn tour
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="container mx-auto px-4 py-8">
        {/* Breadcrumb */}
        <div className="flex items-center space-x-2 text-sm text-gray-600 mb-6">
          <Link to="/hotels" className="hover:text-blue-600 flex items-center">
            <ArrowLeft className="h-4 w-4 mr-1" />
            Tiếp tục chọn phòng
          </Link>
          <span>/</span>
          <span className="text-gray-900">Giỏ hàng</span>
        </div>

        <h1 className="text-3xl font-bold text-gray-900 mb-8">Giỏ hàng của bạn</h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Cart Items */}
          <div className="lg:col-span-2 space-y-4">
            {items.map((item) => (
              <Card key={item.id} className="overflow-hidden">
                <CardContent className="p-4">
                  {item.type === 'hotel' ? (
                    <HotelCartItemCard 
                      item={item as HotelCartItem}
                      formatPrice={formatPrice}
                      calculateNights={calculateNights}
                      updateItem={updateItem}
                      removeItem={removeItem}
                    />
                  ) : (
                    <TourCartItemCard 
                      item={item as TourCartItem}
                      formatPrice={formatPrice}
                      updateItem={updateItem}
                      removeItem={removeItem}
                    />
                  )}
                </CardContent>
              </Card>
            ))}
          </div>

          {/* Order Summary */}
          <div className="lg:col-span-1">
            <Card className="sticky top-24">
              <CardHeader>
                <CardTitle>Tóm tắt đơn hàng</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {/* Itemized List */}
                <div className="space-y-2 border-b pb-4">
                  {items.map((item) => (
                    <div key={item.id} className="flex justify-between text-sm">
                      <span className="text-gray-600">
                        {item.type === 'hotel' ? (item as HotelCartItem).roomName : (item as TourCartItem).tourName}
                      </span>
                      <span className="font-medium">{formatPrice(item.totalPrice)}</span>
                    </div>
                  ))}
                </div>

                {/* Totals */}
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Tổng mục</span>
                    <span>{items.reduce((sum, item) => sum + item.quantity, 0)}</span>
                  </div>
                  <div className="flex justify-between text-lg font-bold text-orange-600 border-t pt-2">
                    <span>Tổng cộng</span>
                    <span>{formatPrice(getTotal())}</span>
                  </div>
                </div>

                {/* Actions */}
                <div className="space-y-2 pt-4 border-t">
                  <Link to="/checkout" className="block">
                    <Button className="w-full bg-orange-500 hover:bg-orange-600">
                      Thanh toán
                    </Button>
                  </Link>
                  <Button
                    variant="outline"
                    className="w-full"
                    onClick={() => clearCart()}
                  >
                    Xóa giỏ hàng
                  </Button>
                  <Link to="/hotels" className="block">
                    <Button variant="ghost" className="w-full">
                      Tiếp tục chọn phòng
                    </Button>
                  </Link>
                  <Link to="/tours" className="block">
                    <Button variant="ghost" className="w-full">
                      Tiếp tục chọn tour
                    </Button>
                  </Link>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
