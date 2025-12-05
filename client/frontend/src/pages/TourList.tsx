import { useEffect, useState } from 'react';
import axios from 'axios';
import TourCard, { Tour } from '@/components/TourCard';

export default function TourList() {
  const [tours, setTours] = useState<Tour[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchTours = async () => {
      try {
        const res = await axios.get<Tour[]>('http://127.0.0.1:8000/tours');
        setTours(res.data);
      } catch (err) {
        console.error('Lỗi khi tải tour:', err);
      } finally {
        setLoading(false);
      }
    };
    fetchTours();
  }, []);

  if (loading) return <p>Đang tải danh sách tour...</p>;

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      {tours.map((tour) => (
        <TourCard key={tour.id} tour={tour} />
      ))}
    </div>
  );
}
