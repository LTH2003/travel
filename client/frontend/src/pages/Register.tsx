import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Separator } from '@/components/ui/separator';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Eye, EyeOff, Mail, Lock, User, Phone, Calendar, Plane, Facebook, Chrome } from 'lucide-react';
import { toast } from '@/hooks/use-toast';
import { register } from '@/api/auth';

export default function Register() {
  const navigate = useNavigate();
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    birthDate: '',
    gender: '',
    password: '',
    confirmPassword: '',
    agreeTerms: false,
    receivePromotions: false
  });
  const [isLoading, setIsLoading] = useState(false);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  const handleSelectChange = (name: string, value: string) => {
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const validateForm = () => {
    if (!formData.firstName || !formData.lastName) {
      toast({
        title: "Lỗi",
        description: "Vui lòng nhập đầy đủ họ và tên.",
        variant: "destructive"
      });
      return false;
    }

    if (!formData.email || !formData.email.includes('@')) {
      toast({
        title: "Lỗi",
        description: "Vui lòng nhập email hợp lệ.",
        variant: "destructive"
      });
      return false;
    }

    if (!formData.phone || formData.phone.length < 10) {
      toast({
        title: "Lỗi",
        description: "Vui lòng nhập số điện thoại hợp lệ.",
        variant: "destructive"
      });
      return false;
    }

    if (!formData.password || formData.password.length < 6) {
      toast({
        title: "Lỗi",
        description: "Mật khẩu phải có ít nhất 6 ký tự.",
        variant: "destructive"
      });
      return false;
    }

    if (formData.password !== formData.confirmPassword) {
      toast({
        title: "Lỗi",
        description: "Mật khẩu xác nhận không khớp.",
        variant: "destructive"
      });
      return false;
    }

    if (!formData.agreeTerms) {
      toast({
        title: "Lỗi",
        description: "Vui lòng đồng ý với điều khoản sử dụng.",
        variant: "destructive"
      });
      return false;
    }

    return true;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    setIsLoading(true);

    try {
      const fullName = `${formData.firstName} ${formData.lastName}`;
      
      const res = await register({
        name: fullName,
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.confirmPassword,
      });

      toast({
        title: "Đăng ký thành công!",
        description: `Chào mừng ${formData.firstName} đến với TravelVN!`,
      });
      
      navigate('/');
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 
                          error.response?.data?.errors?.[0] ||
                          'Đăng ký thất bại. Vui lòng thử lại.';
      
      toast({
        title: "Đăng ký thất bại",
        description: errorMessage,
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const handleSocialRegister = (provider: string) => {
    toast({
      title: `Đăng ký với ${provider}`,
      description: "Tính năng này sẽ được cập nhật sớm!",
    });
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 flex items-center justify-center p-4">
      <Card className="w-full max-w-2xl">
        <CardHeader className="text-center">
          <div className="flex justify-center mb-4">
            <div className="bg-gradient-to-r from-blue-600 to-purple-600 p-3 rounded-lg">
              <Plane className="h-8 w-8 text-white" />
            </div>
          </div>
          <CardTitle className="text-2xl font-bold">Đăng ký TravelVN</CardTitle>
          <CardDescription>
            Tạo tài khoản để khám phá những chuyến du lịch tuyệt vời
          </CardDescription>
        </CardHeader>
        
        <CardContent className="space-y-4">
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Name Fields */}
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Họ *</label>
                <div className="relative">
                  <User className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    name="firstName"
                    placeholder="Nhập họ"
                    value={formData.firstName}
                    onChange={handleInputChange}
                    className="pl-10"
                    required
                  />
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Tên *</label>
                <Input
                  name="lastName"
                  placeholder="Nhập tên"
                  value={formData.lastName}
                  onChange={handleInputChange}
                  required
                />
              </div>
            </div>

            {/* Email */}
            <div className="space-y-2">
              <label className="text-sm font-medium">Email *</label>
              <div className="relative">
                <Mail className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                <Input
                  name="email"
                  type="email"
                  placeholder="Nhập địa chỉ email"
                  value={formData.email}
                  onChange={handleInputChange}
                  className="pl-10"
                  required
                />
              </div>
            </div>

            {/* Phone & Birth Date */}
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Số điện thoại *</label>
                <div className="relative">
                  <Phone className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    name="phone"
                    placeholder="Nhập số điện thoại"
                    value={formData.phone}
                    onChange={handleInputChange}
                    className="pl-10"
                    required
                  />
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Ngày sinh</label>
                <div className="relative">
                  <Calendar className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    name="birthDate"
                    type="date"
                    value={formData.birthDate}
                    onChange={handleInputChange}
                    className="pl-10"
                  />
                </div>
              </div>
            </div>

            {/* Gender */}
            <div className="space-y-2">
              <label className="text-sm font-medium">Giới tính</label>
              <Select value={formData.gender} onValueChange={(value) => handleSelectChange('gender', value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Chọn giới tính" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="male">Nam</SelectItem>
                  <SelectItem value="female">Nữ</SelectItem>
                  <SelectItem value="other">Khác</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {/* Password Fields */}
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium">Mật khẩu *</label>
                <div className="relative">
                  <Lock className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    name="password"
                    type={showPassword ? 'text' : 'password'}
                    placeholder="Nhập mật khẩu"
                    value={formData.password}
                    onChange={handleInputChange}
                    className="pl-10 pr-10"
                    required
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                  >
                    {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                  </button>
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium">Xác nhận mật khẩu *</label>
                <div className="relative">
                  <Lock className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                  <Input
                    name="confirmPassword"
                    type={showConfirmPassword ? 'text' : 'password'}
                    placeholder="Nhập lại mật khẩu"
                    value={formData.confirmPassword}
                    onChange={handleInputChange}
                    className="pl-10 pr-10"
                    required
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                    className="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                  >
                    {showConfirmPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                  </button>
                </div>
              </div>
            </div>

            {/* Terms and Conditions */}
            <div className="space-y-3">
              <div className="flex items-start space-x-2">
                <Checkbox
                  id="agreeTerms"
                  name="agreeTerms"
                  checked={formData.agreeTerms}
                  onCheckedChange={(checked) => 
                    setFormData(prev => ({ ...prev, agreeTerms: checked as boolean }))
                  }
                  className="mt-1"
                />
                <label htmlFor="agreeTerms" className="text-sm text-gray-600">
                  Tôi đồng ý với{' '}
                  <Link to="/terms" className="text-blue-600 hover:underline">
                    Điều khoản sử dụng
                  </Link>{' '}
                  và{' '}
                  <Link to="/privacy" className="text-blue-600 hover:underline">
                    Chính sách bảo mật
                  </Link>{' '}
                  của TravelVN *
                </label>
              </div>
              <div className="flex items-start space-x-2">
                <Checkbox
                  id="receivePromotions"
                  name="receivePromotions"
                  checked={formData.receivePromotions}
                  onCheckedChange={(checked) => 
                    setFormData(prev => ({ ...prev, receivePromotions: checked as boolean }))
                  }
                  className="mt-1"
                />
                <label htmlFor="receivePromotions" className="text-sm text-gray-600">
                  Tôi muốn nhận thông tin khuyến mãi và ưu đãi qua email
                </label>
              </div>
            </div>

            {/* Register Button */}
            <Button 
              type="submit" 
              className="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
              disabled={isLoading}
            >
              {isLoading ? 'Đang đăng ký...' : 'Đăng ký tài khoản'}
            </Button>
          </form>

          {/* Divider */}
          <div className="relative">
            <Separator />
            <span className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-sm text-gray-500">
              Hoặc
            </span>
          </div>

          {/* Social Register */}
          <div className="grid grid-cols-2 gap-2">
            <Button
              type="button"
              variant="outline"
              onClick={() => handleSocialRegister('Google')}
            >
              <Chrome className="h-4 w-4 mr-2" />
              Google
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={() => handleSocialRegister('Facebook')}
            >
              <Facebook className="h-4 w-4 mr-2" />
              Facebook
            </Button>
          </div>

          {/* Login Link */}
          <div className="text-center">
            <span className="text-sm text-gray-600">Đã có tài khoản? </span>
            <Link to="/login" className="text-sm text-blue-600 hover:underline font-medium">
              Đăng nhập ngay
            </Link>
          </div>

          {/* Back to Home */}
          <div className="text-center">
            <Link to="/" className="text-sm text-gray-500 hover:text-gray-700">
              ← Quay về trang chủ
            </Link>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}