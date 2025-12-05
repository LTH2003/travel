import { useState } from 'react';
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

export default function Contact() {
  useTitle("Liên Hệ");
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
  });

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Simulate form submission
    toast({
      title: "Gửi tin nhắn thành công!",
      description: "Chúng tôi sẽ phản hồi bạn trong vòng 24 giờ.",
    });

    // Reset form
    setFormData({
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: ''
    });
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
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center text-2xl">
                  <Send className="h-6 w-6 mr-2 text-blue-600" />
                  Gửi Tin Nhắn
                </CardTitle>
                <CardDescription>
                  Điền thông tin bên dưới và chúng tôi sẽ liên hệ với bạn sớm nhất
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleSubmit} className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="text-sm font-medium mb-2 block">Họ và tên *</label>
                      <Input
                        name="name"
                        value={formData.name}
                        onChange={handleInputChange}
                        placeholder="Nhập họ và tên"
                        required
                        className="bg-white border-gray-200 text-gray-900"
                      />
                    </div>
                    <div>
                      <label className="text-sm font-medium mb-2 block">Số điện thoại *</label>
                      <Input
                        name="phone"
                        value={formData.phone}
                        onChange={handleInputChange}
                        placeholder="Nhập số điện thoại"
                        required
                        className="bg-white border-gray-200 text-gray-900"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="text-sm font-medium mb-2 block">Email *</label>
                    <Input
                      name="email"
                      type="email"
                      value={formData.email}
                      onChange={handleInputChange}
                      placeholder="Nhập địa chỉ email"
                      required
                      className="bg-white border-gray-200 text-gray-900"
                    />
                  </div>

                  <div>
                    <label className="text-sm font-medium mb-2 block">Chủ đề</label>
                    <Input
                      name="subject"
                      value={formData.subject}
                      onChange={handleInputChange}
                      placeholder="Chủ đề tin nhắn"
                      className="bg-white border-gray-200 text-gray-900"
                    />
                  </div>

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

                  <Button type="submit" className="w-full bg-blue-600 hover:bg-blue-700">
                    <Send className="h-4 w-4 mr-2" />
                    Gửi Tin Nhắn
                  </Button>
                </form>
              </CardContent>
            </Card>
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