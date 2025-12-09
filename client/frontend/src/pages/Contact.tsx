import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
  Phone,
  Mail,
  MapPin,
  Clock,
  Send,
  MessageSquare,
  Users,
  Award,
  Facebook,
  Instagram,
  Youtube,
  Twitter
} from 'lucide-react';
import Header from '@/components/Header';
import { toast } from '@/hooks/use-toast';
import { useTitle } from '@/hooks/useTitle';
import { useAuth } from '@/hooks/useAuth';
import axiosClient from '@/api/axiosClient';

export default function Contact() {
  useTitle("Liên Hệ");
  const navigate = useNavigate();
  const { user, loading, isLoggedIn } = useAuth();
  const [formData, setFormData] = useState({
    subject: '',
    message: ''
  });
  const [isLoading, setIsLoading] = useState(false);

  // Debug logging
  useEffect(() => {
    console.log('📍 Contact page load:');
    console.log('  - isLoggedIn:', isLoggedIn);
    console.log('  - loading:', loading);
    console.log('  - user:', user);
    const token = localStorage.getItem('token');
    console.log('  - token in localStorage:', token ? '✅ ' + token.substring(0, 20) + '...' : '❌ No token');
  }, [isLoggedIn, loading, user]);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    try {
      console.log('📤 Sending contact form:', formData);
      const response = await axiosClient.post<{ status: boolean; message?: string }>('/contacts', formData);
      console.log('✅ Contact response:', response.data);
      
      if (response.data.status) {
        toast({
          title: "Gửi tin nhắn thành công!",
          description: "Chúng tôi sẽ phản hồi bạn trong vòng 24 giờ.",
        });

        // Reset form
        setFormData({
          subject: '',
          message: ''
        });
      }
    } catch (error: any) {
      console.error('❌ Contact form error:', error);
      console.error('   Error response:', error.response?.data);
      console.error('   Error status:', error.response?.status);
      const errorMessage = error.response?.data?.message || error.message || 'Có lỗi xảy ra. Vui lòng thử lại.';
      toast({
        title: "Lỗi",
        description: errorMessage,
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const contactInfo = [
    {
      icon: <Phone className="h-6 w-6" />,
      title: "Hotline",
      content: "0889421997",
      description: "Hỗ trợ 24/7"
    },
    {
      icon: <Mail className="h-6 w-6" />,
      title: "Email",
      content: "huyhoahien86@gmail.com",
      description: "Phản hồi trong 2 giờ"
    },
    {
      icon: <MapPin className="h-6 w-6" />,
      title: "Địa chỉ",
      content: "182 Lê Duẩn, TP Vinh, Nghệ An",
      description: "Văn phòng chính"
    },
    {
      icon: <Clock className="h-6 w-6" />,
      title: "Giờ làm việc",
      content: "8:00 - 22:00",
      description: "Thứ 2 - Chủ nhật"
    }
  ];

  const stats = [
    {
      icon: <Users className="h-8 w-8" />,
      number: "50,000+",
      label: "Khách hàng tin tưởng"
    },
    {
      icon: <Award className="h-8 w-8" />,
      number: "5 năm",
      label: "Kinh nghiệm du lịch"
    },
    {
      icon: <MessageSquare className="h-8 w-8" />,
      number: "4.9/5",
      label: "Đánh giá trung bình"
    }
  ];

  const socialLinks = [
    { icon: <Facebook className="h-5 w-5" />, name: "Facebook", color: "bg-blue-600" },
    { icon: <Instagram className="h-5 w-5" />, name: "Instagram", color: "bg-pink-500" },
    { icon: <Youtube className="h-5 w-5" />, name: "Youtube", color: "bg-red-600" },
    { icon: <Twitter className="h-5 w-5" />, name: "Twitter", color: "bg-blue-400" }
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      {/* Hero Section */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-5xl font-bold mb-4">
            Liên Hệ Với Chúng Tôi
          </h1>
          <p className="text-xl mb-8">
            Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7 cho chuyến du lịch hoàn hảo
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        {/* Stats Section */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          {stats.map((stat, index) => (
            <Card key={index} className="text-center">
              <CardContent className="p-6">
                <div className="flex justify-center mb-4 text-blue-600">
                  {stat.icon}
                </div>
                <div className="text-3xl font-bold text-gray-900 mb-2">{stat.number}</div>
                <div className="text-gray-600">{stat.label}</div>
              </CardContent>
            </Card>
          ))}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          {/* Contact Form */}
          <div>
            {!loading && !isLoggedIn ? (
              // Show login prompt if not authenticated
              <Card className="border-blue-200 bg-blue-50">
                <CardContent className="p-8 text-center">
                  <div className="mb-4">
                    <MessageSquare className="h-12 w-12 text-blue-600 mx-auto" />
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">
                    Vui lòng đăng nhập
                  </h3>
                  <p className="text-gray-600 mb-6">
                    Để gửi tin nhắn cho chúng tôi, bạn cần đăng nhập vào tài khoản của mình.
                  </p>
                  <Button 
                    onClick={() => navigate('/login')}
                    className="w-full bg-blue-600 hover:bg-blue-700"
                  >
                    Đăng Nhập
                  </Button>
                  <p className="text-sm text-gray-600 mt-4">
                    Chưa có tài khoản? <a href="/register" className="text-blue-600 hover:underline">Đăng ký ngay</a>
                  </p>
                </CardContent>
              </Card>
            ) : loading ? (
              // Show loading state
              <Card>
                <CardContent className="p-8 text-center">
                  <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto"></div>
                </CardContent>
              </Card>
            ) : (
              // Show contact form
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center text-2xl">
                    <Send className="h-6 w-6 mr-2 text-blue-600" />
                    Gửi Tin Nhắn
                  </CardTitle>
                  <CardDescription>
                    Chỉ cần nhập chủ đề và nội dung, thông tin của bạn sẽ được tự động điền
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <form onSubmit={handleSubmit} className="space-y-4">
                    {/* Display user info */}
                    <div className="bg-blue-50 p-4 rounded-lg border border-blue-200">
                      <p className="text-sm text-gray-600">Gửi từ tài khoản:</p>
                      <p className="font-semibold text-gray-900">{user?.name}</p>
                      <p className="text-sm text-gray-600">{user?.email}</p>
                      {user?.phone && <p className="text-sm text-gray-600">{user?.phone}</p>}
                    </div>

                    {/* Subject field */}
                    <div>
                      <label className="text-sm font-medium mb-2 block">Chủ đề *</label>
                      <Input
                        name="subject"
                        value={formData.subject}
                        onChange={handleInputChange}
                        placeholder="Nhập chủ đề tin nhắn"
                        required
                        className="bg-white border-gray-200 text-gray-900"
                      />
                    </div>

                    {/* Message field */}
                    <div>
                      <label className="text-sm font-medium mb-2 block">Nội dung *</label>
                      <Textarea
                        name="message"
                        value={formData.message}
                        onChange={handleInputChange}
                        placeholder="Nhập nội dung tin nhắn..."
                        rows={5}
                        required
                        className="bg-white border-gray-200 text-gray-900"
                      />
                    </div>

                    <Button 
                      type="submit" 
                      disabled={isLoading}
                      className="w-full bg-blue-600 hover:bg-blue-700"
                    >
                      <Send className="h-4 w-4 mr-2" />
                      {isLoading ? 'Đang gửi...' : 'Gửi Tin Nhắn'}
                    </Button>
                  </form>
                </CardContent>
              </Card>
            )}
          </div>

          {/* Contact Information */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="text-2xl">Thông Tin Liên Hệ</CardTitle>
                <CardDescription>
                  Liên hệ trực tiếp với chúng tôi qua các kênh sau
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-6">
                {contactInfo.map((info, index) => (
                  <div key={index} className="flex items-start space-x-4">
                    <div className="flex-shrink-0 p-3 bg-blue-100 rounded-lg text-blue-600">
                      {info.icon}
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{info.title}</h3>
                      <p className="text-lg font-medium text-gray-900">{info.content}</p>
                      <p className="text-sm text-gray-600">{info.description}</p>
                    </div>
                  </div>
                ))}
              </CardContent>
            </Card>

            {/* Social Media */}
            <Card>
              <CardHeader>
                <CardTitle>Theo Dõi Chúng Tôi</CardTitle>
                <CardDescription>
                  Kết nối với chúng tôi trên các mạng xã hội
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex space-x-4">
                  {socialLinks.map((social, index) => (
                    <Button
                      key={index}
                      variant="outline"
                      size="icon"
                      className={`${social.color} text-white border-none hover:opacity-90`}
                    >
                      {social.icon}
                    </Button>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Map */}
            <Card>
              <CardHeader>
                <CardTitle>Vị Trí Văn Phòng</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="rounded-lg overflow-hidden h-64">
                  <iframe
                    title="Đại học Vinh"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15120.46538770673!2d105.6898183894673!3d18.658774323040948!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3139cddf0bf20f23%3A0x86154b56a284fa6d!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBWaW5o!5e0!3m2!1svi!2s!4v1759677456861!5m2!1svi!2s"
                    width="100%"
                    height="100%"
                    style={{ border: 0 }}
                    allowFullScreen={true}
                    loading="lazy"
                    referrerPolicy="no-referrer-when-downgrade"
                  ></iframe>
                </div>
              </CardContent>
            </Card>

          </div>
        </div>

        {/* FAQ Section */}
        <div className="mt-12">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl text-center">Câu Hỏi Thường Gặp</CardTitle>
              <CardDescription className="text-center">
                Một số câu hỏi khách hàng thường quan tâm
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div>
                    <h3 className="font-semibold text-gray-900 mb-2">Làm thế nào để đặt tour?</h3>
                    <p className="text-gray-600 text-sm">Bạn có thể đặt tour trực tuyến trên website hoặc gọi hotline 0889421997 để được hỗ trợ.</p>
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900 mb-2">Chính sách hủy tour như thế nào?</h3>
                    <p className="text-gray-600 text-sm">Chúng tôi có chính sách hủy linh hoạt, tùy thuộc vào thời gian hủy và loại tour.</p>
                  </div>
                </div>
                <div className="space-y-4">
                  <div>
                    <h3 className="font-semibold text-gray-900 mb-2">Có hỗ trợ visa không?</h3>
                    <p className="text-gray-600 text-sm">Có, chúng tôi hỗ trợ làm visa cho các tour quốc tế với phí dịch vụ hợp lý.</p>
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900 mb-2">Thanh toán như thế nào?</h3>
                    <p className="text-gray-600 text-sm">Chúng tôi chấp nhận thanh toán qua thẻ tín dụng, chuyển khoản và tiền mặt.</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}