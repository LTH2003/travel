import { useState, useEffect, useMemo } from 'react';
import { useSearchParams } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Slider } from "@/components/ui/slider";
import { Checkbox } from "@/components/ui/checkbox";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import SearchForm, { SearchFilters } from "@/components/SearchForm";
import TourCard from "@/components/TourCard";
import { Filter, SlidersHorizontal, X, ChevronLeft, ChevronRight } from "lucide-react";
import { useTitle } from "@/hooks/useTitle";
import { tourApi } from "@/api/tourApi";

export interface Tour {
  id: number;
  title: string;
  destination: string;
  price: number;
  original_price?: number;
  duration: string;
  rating: number;
  review_count: number;
  image: string;
  category: string;
  highlights: string[];
  description: string;
  includes: string[];
  itinerary: string[];
  departure: string[];
  max_guests: number;
}

export default function Tours() {
  useTitle("Tours - TravelVN");
  const [searchParams, setSearchParams] = useSearchParams();
  const ITEMS_PER_PAGE = 9; // Show 9 tours per page

  const [tours, setTours] = useState<Tour[]>([]);
  const [filteredTours, setFilteredTours] = useState<Tour[]>([]);
  const [loading, setLoading] = useState(true);
  const [showFilters, setShowFilters] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);

  const [selectedDestination, setSelectedDestination] = useState(searchParams.get("destination") || "Tất cả điểm đến");
  const [selectedCategory, setSelectedCategory] = useState("Tất cả");
  const [priceRange, setPriceRange] = useState([0, 25000000]);
  const [selectedDurations, setSelectedDurations] = useState<string[]>([]);
  const [sortBy, setSortBy] = useState("popular");
  const [keyword, setKeyword] = useState(searchParams.get("keyword") || "");

  const durations = ["2-3 ngày", "4-5 ngày", "6-7 ngày", "8+ ngày"];

  // 🧩 Memoized lists derived from tours (only recalculate when tours change)
  const destinations = useMemo(
    () => ["Tất cả điểm đến", ...Array.from(new Set(tours.map((t) => t.destination).filter(Boolean)))],
    [tours]
  );

  const categories = useMemo(
    () => ["Tất cả", ...Array.from(new Set(tours.map((t) => t.category).filter(Boolean)))],
    [tours]
  );


  const totalPages = Math.ceil(filteredTours.length / ITEMS_PER_PAGE);
  const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
  const endIdx = startIdx + ITEMS_PER_PAGE;
  const paginatedTours = filteredTours.slice(startIdx, endIdx);

  const formatPrice = (value: number) =>
    new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND", maximumFractionDigits: 0 }).format(value);

  useEffect(() => {
    const fetchTours = async () => {
      try {
        const res = await tourApi.getAll();
        const data = Array.isArray(res?.data) ? (res.data as Tour[]) : [];
        setTours(data);
        setFilteredTours(data);
      } catch (error) {
        console.error("❌ Lỗi khi tải danh sách tour:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchTours();
  }, []);


  useEffect(() => {
    let filtered = [...tours];

    if (selectedDestination && selectedDestination !== "Tất cả điểm đến") {
      filtered = filtered.filter((t) =>
        t.destination.toLowerCase().includes(selectedDestination.toLowerCase())
      );
    }

    if (selectedCategory && selectedCategory !== "Tất cả") {
      filtered = filtered.filter((t) => t.category === selectedCategory);
    }

    filtered = filtered.filter((t) => t.price >= priceRange[0] && t.price <= priceRange[1]);

    if (selectedDurations.length > 0) {
      filtered = filtered.filter((t) => {
        const tourDays = parseInt(t.duration.split(" ")[0]);
        return selectedDurations.some((d) => {
          if (d === "2-3 ngày") return tourDays >= 2 && tourDays <= 3;
          if (d === "4-5 ngày") return tourDays >= 4 && tourDays <= 5;
          if (d === "6-7 ngày") return tourDays >= 6 && tourDays <= 7;
          if (d === "8+ ngày") return tourDays >= 8;
          return false;
        });
      });
    }

    if (keyword) {
      filtered = filtered.filter(
        (t) =>
          t.title.toLowerCase().includes(keyword.toLowerCase()) ||
          t.destination.toLowerCase().includes(keyword.toLowerCase()) ||
          (Array.isArray(t.highlights) &&
            t.highlights.some((h) => h.toLowerCase().includes(keyword.toLowerCase())))
      );
    }

    switch (sortBy) {
      case "price-low":
        filtered.sort((a, b) => a.price - b.price);
        break;
      case "price-high":
        filtered.sort((a, b) => b.price - a.price);
        break;
      case "rating":
        filtered.sort((a, b) => b.rating - a.rating);
        break;
      case "duration":
        filtered.sort((a, b) => {
          const aDays = parseInt(a.duration.split(" ")[0]);
          const bDays = parseInt(b.duration.split(" ")[0]);
          return aDays - bDays;
        });
        break;
      default:
        filtered.sort((a, b) => b.rating * b.review_count - a.rating * a.review_count);
    }

    setFilteredTours(filtered);
    setCurrentPage(1); 
  }, [tours, selectedDestination, selectedCategory, priceRange, selectedDurations, sortBy, keyword]);

  const handleSearch = (filters: SearchFilters) => {
    const newSearchParams = new URLSearchParams();

    // Nếu chọn "Tất cả điểm đến" thì reset destination
    if (!filters.destination || filters.destination === "Tất cả điểm đến") {
      setSelectedDestination("Tất cả điểm đến");
    } else {
      newSearchParams.set("destination", filters.destination);
      setSelectedDestination(filters.destination);
    }

    // Reset keyword nếu không có, hoặc set keyword mới
    if (!filters.keyword) {
      setKeyword("");
    } else {
      newSearchParams.set("keyword", filters.keyword);
      setKeyword(filters.keyword);
    }

    setSearchParams(newSearchParams);
  };

  const handleDurationChange = (duration: string, checked: boolean) => {
    if (checked) {
      setSelectedDurations([...selectedDurations, duration]);
    } else {
      setSelectedDurations(selectedDurations.filter((d) => d !== duration));
    }
  };

  const clearFilters = () => {
    setSelectedDestination("Tất cả điểm đến");
    setSelectedCategory("Tất cả");
    setPriceRange([0, 25000000]);
    setSelectedDurations([]);
    setKeyword("");
    setSearchParams(new URLSearchParams());
  };

  if (loading) {
    return (
      <div className="min-h-screen flex justify-center items-center text-lg">
        Đang tải danh sách tour...
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      <section className="bg-white py-8 shadow-sm">
        <div className="container mx-auto px-4">
          <SearchForm onSearch={handleSearch} />
        </div>
      </section>

      <div className="container mx-auto px-4 py-8">
        <div className="flex flex-col lg:flex-row gap-8">
          {/* Sidebar Filter */}
          <div className="lg:w-80">
            <div className="lg:hidden mb-4">
              <Button
                variant="outline"
                onClick={() => setShowFilters(!showFilters)}
                className="w-full"
              >
                <SlidersHorizontal className="h-4 w-4 mr-2" />
                Bộ lọc
              </Button>
            </div>

            <Card className={`${showFilters ? 'block' : 'hidden'} lg:block`}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between mb-6">
                  <h3 className="text-lg font-semibold flex items-center">
                    <Filter className="h-5 w-5 mr-2" />
                    Bộ lọc
                  </h3>
                  <Button variant="ghost" size="sm" onClick={clearFilters}>
                    <X className="h-4 w-4 mr-1" />
                    Xóa
                  </Button>
                </div>

                <div className="space-y-6">
                  {/* Destination Filter */}
                  <div>
                    <label className="text-sm font-medium mb-2 block">Điểm đến</label>
                    <Select value={selectedDestination} onValueChange={setSelectedDestination}>
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {destinations.map((dest) => (
                          <SelectItem key={dest} value={dest}>
                            {dest}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  {/* Category Filter */}
                  <div>
                    <label className="text-sm font-medium mb-2 block">Loại tour</label>
                    <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {categories.map((cat) => (
                          <SelectItem key={cat} value={cat}>
                            {cat}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  {/* Price Range */}
                  <div>
                    <label className="text-sm font-medium mb-2 block">
                      Khoảng giá: {formatPrice(priceRange[0])} - {formatPrice(priceRange[1])}
                    </label>
                    <Slider
                      value={priceRange}
                      onValueChange={setPriceRange}
                      max={25000000}
                      min={0}
                      step={500000}
                      className="mt-2"
                    />
                  </div>

                  {/* Duration Filter */}
                  <div>
                    <label className="text-sm font-medium mb-2 block">Thời gian</label>
                    <div className="space-y-2">
                      {durations.map((duration) => (
                        <div key={duration} className="flex items-center space-x-2">
                          <Checkbox
                            id={duration}
                            checked={selectedDurations.includes(duration)}
                            onCheckedChange={(checked) => 
                              handleDurationChange(duration, checked as boolean)
                            }
                          />
                          <label htmlFor={duration} className="text-sm">
                            {duration}
                          </label>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Tour List */}
          <div className="flex-1">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
              <div>
                <h2 className="text-2xl font-bold text-gray-800">Kết quả tìm kiếm</h2>
                <p className="text-gray-600">Tìm thấy {filteredTours.length} tour phù hợp</p>
              </div>

              <Select value={sortBy} onValueChange={setSortBy}>
                <SelectTrigger className="w-48">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="popular">Phổ biến nhất</SelectItem>
                  <SelectItem value="price-low">Giá thấp đến cao</SelectItem>
                  <SelectItem value="price-high">Giá cao đến thấp</SelectItem>
                  <SelectItem value="rating">Đánh giá cao nhất</SelectItem>
                  <SelectItem value="duration">Thời gian ngắn nhất</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {filteredTours.length > 0 ? (
              <>
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                  {paginatedTours.map((tour) => (
                    <TourCard
                      key={tour.id}
                      tour={{
                        id: tour.id,
                        title: tour.title,
                        image: tour.image,
                        destination: tour.destination,
                        price: tour.price,
                        originalPrice: tour.original_price,
                        duration: tour.duration,
                        rating: tour.rating,
                        reviewCount: tour.review_count,
                        maxGuests: tour.max_guests,
                      }}
                    />
                  ))}
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                  <div className="flex items-center justify-center gap-2 mt-8">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                      disabled={currentPage === 1}
                    >
                      <ChevronLeft className="h-4 w-4" />
                    </Button>

                    {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                      let pageNum;
                      if (totalPages <= 5) {
                        pageNum = i + 1;
                      } else if (currentPage <= 3) {
                        pageNum = i + 1;
                      } else if (currentPage >= totalPages - 2) {
                        pageNum = totalPages - 4 + i;
                      } else {
                        pageNum = currentPage - 2 + i;
                      }
                      return (
                        <Button
                          key={pageNum}
                          variant={currentPage === pageNum ? "default" : "outline"}
                          size="sm"
                          onClick={() => setCurrentPage(pageNum)}
                        >
                          {pageNum}
                        </Button>
                      );
                    })}

                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                      disabled={currentPage === totalPages}
                    >
                      <ChevronRight className="h-4 w-4" />
                    </Button>
                  </div>
                )}
              </>
            ) : (
              <div className="text-center py-12">
                <div className="text-gray-400 mb-4">
                  <Filter className="h-16 w-16 mx-auto" />
                </div>
                <h3 className="text-xl font-semibold text-gray-600 mb-2">Không tìm thấy tour phù hợp</h3>
                <p className="text-gray-500 mb-4">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <Button onClick={clearFilters}>Xóa tất cả bộ lọc</Button>
              </div>
            )}
          </div>
        </div>
      </div>
      <Footer />
    </div>
  );
}
