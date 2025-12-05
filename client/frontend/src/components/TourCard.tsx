import { Link } from 'react-router-dom';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Star, Clock, MapPin, Users } from 'lucide-react';

export interface Tour {
  id: number;
  title: string;
  category?: string;
  image?: string;
  destination?: string;
  duration?: string;
  maxGuests?: number;
  price: number;
  originalPrice?: number;
  rating?: number;
  reviewCount?: number;
  highlights?: string[] | string; // ✅ cho phép string hoặc array
}

interface TourCardProps {
  tour: Tour;
}

export default function TourCard({ tour }: TourCardProps) {
  const formatPrice = (price: number) =>
    new Intl.NumberFormat('vi-VN').format(price) + 'đ';

  const discountPercent = tour.originalPrice
    ? Math.round(((tour.originalPrice - tour.price) / tour.originalPrice) * 100)
    : 0;

  // ✅ Xử lý highlights để đảm bảo luôn là mảng
  const highlights = Array.isArray(tour.highlights)
    ? tour.highlights
    : typeof tour.highlights === 'string'
    ? tour.highlights.split(',').map((s) => s.trim())
    : [];

  return (
    <Card className="overflow-hidden hover:shadow-lg transition-shadow duration-300 group">
      <div className="relative">
        <img
          src={tour.image || '/placeholder.jpg'}
          alt={tour.title}
          className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        />
        {discountPercent > 0 && (
          <Badge className="absolute top-2 left-2 bg-red-500 hover:bg-red-600">
            -{discountPercent}%
          </Badge>
        )}
        {tour.category && (
          <Badge className="absolute top-2 right-2 bg-blue-600 hover:bg-blue-700">
            {tour.category}
          </Badge>
        )}
      </div>

      <CardContent className="p-4">
        <div className="flex items-center justify-between mb-2">
          {tour.destination && (
            <div className="flex items-center space-x-1">
              <MapPin className="h-4 w-4 text-gray-500" />
              <span className="text-sm text-gray-600">{tour.destination}</span>
            </div>
          )}
          {tour.rating && (
            <div className="flex items-center space-x-1">
              <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
              <span className="text-sm font-medium">{tour.rating}</span>
              {tour.reviewCount && (
                <span className="text-sm text-gray-500">
                  ({tour.reviewCount})
                </span>
              )}
            </div>
          )}
        </div>

        <h3 className="font-semibold text-lg mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
          {tour.title}
        </h3>

        {(tour.duration || tour.maxGuests) && (
          <div className="flex items-center justify-between text-sm text-gray-600 mb-3">
            {tour.duration && (
              <div className="flex items-center space-x-1">
                <Clock className="h-4 w-4" />
                <span>{tour.duration}</span>
              </div>
            )}
            {tour.maxGuests && (
              <div className="flex items-center space-x-1">
                <Users className="h-4 w-4" />
                <span>Tối đa {tour.maxGuests} khách</span>
              </div>
            )}
          </div>
        )}

        {/* ✅ Highlights đã được xử lý an toàn */}
        {highlights.length > 0 && (
          <div className="space-y-2">
            <div className="text-sm text-gray-600 font-medium">
              Điểm nổi bật:
            </div>
            <ul className="text-sm text-gray-600 space-y-1">
              {highlights.slice(0, 2).map((highlight, index) => (
                <li key={index} className="flex items-start">
                  <span className="text-green-500 mr-1">✓</span>
                  <span className="line-clamp-1">{highlight}</span>
                </li>
              ))}
            </ul>
          </div>
        )}
      </CardContent>

      <CardFooter className="p-4 pt-0">
        <div className="w-full">
          <div className="flex items-center justify-between mb-3">
            <div>
              {tour.originalPrice && (
                <span className="text-sm text-gray-500 line-through">
                  {formatPrice(tour.originalPrice)}
                </span>
              )}
              <div className="text-xl font-bold text-orange-600">
                {formatPrice(tour.price)}
              </div>
              <span className="text-sm text-gray-600">/khách</span>
            </div>
          </div>

          <div className="flex space-x-2">
            <Link to={`/tours/${tour.id}`} className="flex-1">
              <Button variant="outline" className="w-full">
                Xem chi tiết
              </Button>
            </Link>
            <Link to={`/tours/${tour.id}`} className="flex-1">
              <Button className="w-full bg-orange-500 hover:bg-orange-600">
                Đặt ngay
              </Button>
            </Link>
          </div>
        </div>
      </CardFooter>
    </Card>
  );
}
