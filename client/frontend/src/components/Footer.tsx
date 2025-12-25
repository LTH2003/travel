import { Plane } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="bg-gray-800 text-white py-12">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div className="flex items-center space-x-2 mb-4">
              <div className="bg-blue-600 p-2 rounded-lg">
                <Plane className="h-6 w-6 text-white" />
              </div>
              <div>
                <h3 className="text-xl font-bold">TravelVN</h3>
                <p className="text-sm text-gray-400">Du lịch trọn vẹn</p>
              </div>
            </div>
            <p className="text-gray-400">
              Đối tác tin cậy cho mọi chuyến đi của bạn với hơn 15 năm kinh nghiệm trong ngành du lịch.
            </p>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Dịch vụ</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="/tours" className="hover:text-white transition">Tour trong nước</a></li>
              <li><a href="/tours" className="hover:text-white transition">Tour quốc tế</a></li>
              <li><a href="/hotels" className="hover:text-white transition">Đặt khách sạn</a></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Hỗ trợ</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="/contact" className="hover:text-white transition">Trung tâm trợ giúp</a></li>
              <li className="hover:text-white transition cursor-pointer">Chính sách hoàn tiền</li>
              <li className="hover:text-white transition cursor-pointer">Điều khoản dịch vụ</li>
              <li className="hover:text-white transition cursor-pointer">Bảo mật thông tin</li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Liên hệ</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="tel:0889421997" className="hover:text-white transition">Hotline: 0889421997</a></li>
              <li><a href="mailto:huyhoahien86@gmail.com" className="hover:text-white transition">Email: huyhoahien86@gmail.com</a></li>
              <li>Địa chỉ: 182 Lê Duẩn, TP Vinh, Nghệ An</li>
            </ul>
          </div>
        </div>
        
        <div className="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2025 TravelVN. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </footer>
  );
}
