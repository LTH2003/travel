import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { CalendarIcon, MapPin, Users, Search } from 'lucide-react';
import { format } from 'date-fns';
import { vi } from 'date-fns/locale';
import { cn } from '@/lib/utils';

export interface SearchFilters {
  destination: string;
  departure?: Date;
  guests: number;
  keyword: string;
}

interface SearchFormProps {
  onSearch: (filters: SearchFilters) => void;
  className?: string;
}

export default function SearchForm({ onSearch, className }: SearchFormProps) {
  const [destination, setDestination] = useState('Tất cả điểm đến');
  const [departure, setDeparture] = useState<Date>();
  const [guests, setGuests] = useState(2);
  const [keyword, setKeyword] = useState('');

  const destinations = [
    'Tất cả điểm đến',
    'TP. Hồ Chí Minh',
    'Hà Nội',
    'Đà Nẵng',
    'Nha Trang',
    'Đà Lạt',
    'Phú Quốc',
    'Hạ Long',
    'Sapa',
    'Hội An',
    'Cần Thơ',
    'Nhật Bản',
    'Thái Lan',
    'Singapore',
    'Malaysia',
    'Hàn Quốc'
  ];

  const handleSearch = () => {
    onSearch({
      destination,
      departure,
      guests,
      keyword
    });
  };

  return (
    <div className={cn("p-6 rounded-lg shadow-lg", className)}>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        {/* Destination */}
        <div className="space-y-2">
          <label className="text-sm font-medium text-gray-700 flex items-center">
            <MapPin className="h-4 w-4 mr-1" />
            Điểm đến
          </label>
          <Select value={destination} onValueChange={setDestination}>
            <SelectTrigger className="bg-white border-gray-200 text-gray-900 hover:border-blue-300 focus:border-blue-500">
              <SelectValue placeholder="Chọn điểm đến" className="text-gray-900" />
            </SelectTrigger>
            <SelectContent className="bg-white border border-gray-200 shadow-lg">
              {destinations.map((dest) => (
                <SelectItem 
                  key={dest} 
                  value={dest}
                  className="text-gray-900 hover:bg-blue-50 focus:bg-blue-50 cursor-pointer"
                >
                  {dest}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        {/* Departure Date */}
        <div className="space-y-2">
          <label className="text-sm font-medium text-gray-700 flex items-center">
            <CalendarIcon className="h-4 w-4 mr-1" />
            Ngày khởi hành
          </label>
          <Popover>
            <PopoverTrigger asChild>
              <Button
                variant="outline"
                className={cn(
                  "w-full justify-start text-left font-normal bg-white border-gray-200 hover:border-blue-300 focus:border-blue-500",
                  !departure && "text-gray-500"
                )}
              >
                <CalendarIcon className="mr-2 h-4 w-4 text-gray-500" />
                {departure ? (
                  <span className="text-gray-900">
                    {format(departure, "dd/MM/yyyy", { locale: vi })}
                  </span>
                ) : (
                  <span className="text-gray-500">Chọn ngày</span>
                )}
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0 bg-white border border-gray-200 shadow-lg" align="start">
              <Calendar
                mode="single"
                selected={departure}
                onSelect={setDeparture}
                disabled={(date) => date < new Date()}
                initialFocus
                className="bg-white"
              />
            </PopoverContent>
          </Popover>
        </div>

        {/* Number of Guests */}
        <div className="space-y-2">
          <label className="text-sm font-medium text-gray-700 flex items-center">
            <Users className="h-4 w-4 mr-1" />
            Số khách
          </label>
          <Select value={guests.toString()} onValueChange={(value) => setGuests(parseInt(value))}>
            <SelectTrigger className="bg-white border-gray-200 text-gray-900 hover:border-blue-300 focus:border-blue-500">
              <SelectValue className="text-gray-900" />
            </SelectTrigger>
            <SelectContent className="bg-white border border-gray-200 shadow-lg">
              {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((num) => (
                <SelectItem 
                  key={num} 
                  value={num.toString()}
                  className="text-gray-900 hover:bg-blue-50 focus:bg-blue-50 cursor-pointer"
                >
                  {num} {num === 1 ? 'khách' : 'khách'}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        {/* Keyword Search */}
        <div className="space-y-2">
          <label className="text-sm font-medium text-gray-700 flex items-center">
            <Search className="h-4 w-4 mr-1" />
            Từ khóa
          </label>
          <Input
            type="text"
            placeholder="Tìm kiếm tour..."
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
            className="bg-white border-gray-200 text-gray-900 placeholder:text-gray-500 hover:border-blue-300 focus:border-blue-500"
          />
        </div>

        {/* Search Button */}
        <div className="space-y-2">
          <label className="text-sm font-medium text-transparent">Search</label>
          <Button 
            onClick={handleSearch}
            className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium"
            size="default"
          >
            <Search className="h-4 w-4 mr-2" />
            Tìm kiếm
          </Button>
        </div>
      </div>
    </div>
  );
}