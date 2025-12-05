import { useState, useEffect } from "react";
import { useAuth } from "@/hooks/useAuth";
import { toast } from "@/hooks/use-toast";
import { updateUserProfile, enableTwoFactor, confirmTwoFactor, disableTwoFactor } from "@/api/auth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Textarea } from "@/components/ui/textarea";
import { Mail, Phone, Camera, Edit, Save, X, Loader2, Shield } from "lucide-react";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import Header from "@/components/Header";

export default function Profile() {
  const { user, setUser, loading: authLoading } = useAuth();
  const [isEditing, setIsEditing] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
  
  // 2FA states
  const [show2FADialog, setShow2FADialog] = useState(false);
  const [show2FAConfirmDialog, setShow2FAConfirmDialog] = useState(false);
  const [show2FADisableDialog, setShow2FADisableDialog] = useState(false);
  const [otpCode, setOtpCode] = useState('');
  const [disablePassword, setDisablePassword] = useState('');
  const [loading2FA, setLoading2FA] = useState(false);
  
  const [formData, setFormData] = useState({
    name: user?.name || "",
    phone: user?.phone || "",
    address: user?.address || "",
    bio: user?.bio || "",
  });

  // Debug: Log user avatar when it changes
  useEffect(() => {
    console.log("🖼️ User avatar:", user?.avatar);
  }, [user?.avatar]);

  // Cập nhật form khi user thay đổi (sau login)
  useEffect(() => {
    if (user) {
      setFormData({
        name: user.name || "",
        phone: user.phone || "",
        address: user.address || "",
        bio: user.bio || "",
      });
      setAvatarPreview(null);
    }
  }, [user]);

  const handleInputChange = (field: string, value: string) =>
    setFormData((prev) => ({ ...prev, [field]: value }));

  const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        const base64String = reader.result as string;
        setAvatarPreview(base64String);
        setFormData((prev) => ({ ...prev, avatar: base64String }));
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSave = async () => {
    try {
      setIsSaving(true);
      const updated = await updateUserProfile(formData) as { user?: typeof user };
      if (updated?.user) setUser(updated.user);
      else setUser({ ...user, ...formData });
      setIsEditing(false);
      setAvatarPreview(null); // Xóa preview để hiển thị avatar từ server
      toast({ title: "Cập nhật thành công!", description: "Thông tin đã được lưu." });
    } catch (err: any) {
      console.error('Update error:', err);
      toast({
        title: "Lỗi cập nhật",
        description: err?.response?.data?.message || "Không thể lưu thông tin, vui lòng thử lại.",
        variant: "destructive",
      });
    } finally {
      setIsSaving(false);
    }
  };

  const handleCancel = () => {
    setFormData({
      name: user?.name || "",
      phone: user?.phone || "",
      address: user?.address || "",
      bio: user?.bio || "",
    });
    setAvatarPreview(null);
    setIsEditing(false);
  };

  // 2FA Methods
  const handleEnable2FA = async () => {
    setLoading2FA(true);
    try {
      await enableTwoFactor();
      setShow2FADialog(false);
      setShow2FAConfirmDialog(true);
      toast({ title: 'OTP đã gửi', description: 'Kiểm tra email của bạn' });
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Không thể bật 2FA',
        variant: 'destructive',
      });
    } finally {
      setLoading2FA(false);
    }
  };

  const handleConfirm2FA = async () => {
    if (otpCode.length !== 6) {
      toast({ title: 'Lỗi', description: 'Vui lòng nhập đủ 6 chữ số', variant: 'destructive' });
      return;
    }
    setLoading2FA(true);
    try {
      const res = await confirmTwoFactor({ code: otpCode });
      if ((res as any).data?.user) {
        setUser((res as any).data.user);
      }
      setShow2FAConfirmDialog(false);
      setOtpCode('');
      toast({ title: 'Thành công', description: '2FA đã được kích hoạt' });
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Xác thực thất bại',
        variant: 'destructive',
      });
    } finally {
      setLoading2FA(false);
    }
  };

  const handleDisable2FA = async () => {
    if (!disablePassword) {
      toast({ title: 'Lỗi', description: 'Vui lòng nhập mật khẩu', variant: 'destructive' });
      return;
    }
    setLoading2FA(true);
    try {
      const res = await disableTwoFactor({ password: disablePassword });
      if ((res as any).data?.user) {
        setUser((res as any).data.user);
      }
      setShow2FADisableDialog(false);
      setDisablePassword('');
      toast({ title: 'Thành công', description: '2FA đã bị vô hiệu hóa' });
    } catch (error: any) {
      toast({
        title: 'Lỗi',
        description: error.response?.data?.message || 'Không thể vô hiệu hóa 2FA',
        variant: 'destructive',
      });
    } finally {
      setLoading2FA(false);
    }
  };

  const getInitials = (name: string) =>
    name
      ? name
          .split(" ")
          .map((w) => w[0])
          .join("")
          .toUpperCase()
          .slice(0, 2)
      : "U";

  if (authLoading || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin mx-auto mb-2" />
          <p>Đang tải thông tin người dùng...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />

      <div className="container mx-auto px-4 py-8">
        <div className="max-w-4xl mx-auto">
          <div className="mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Thông tin cá nhân</h1>
            <p className="text-gray-600">Quản lý thông tin tài khoản và cài đặt của bạn</p>
          </div>

          <Tabs defaultValue="profile" className="space-y-6">
            <TabsList className="grid w-full grid-cols-3">
              <TabsTrigger value="profile">Hồ sơ</TabsTrigger>
              <TabsTrigger value="security">Bảo mật</TabsTrigger>
              <TabsTrigger value="preferences">Tùy chọn</TabsTrigger>
            </TabsList>

            {/* Tab Hồ sơ */}
            <TabsContent value="profile" className="space-y-6">
              <Card>
                <CardHeader>
                  <div className="flex items-center justify-between">
                    <CardTitle>Thông tin cá nhân</CardTitle>
                    {!isEditing ? (
                      <Button variant="outline" size="sm" onClick={() => setIsEditing(true)}>
                        <Edit className="h-4 w-4 mr-2" /> Chỉnh sửa
                      </Button>
                    ) : (
                      <div className="flex space-x-2">
                        <Button variant="outline" size="sm" onClick={handleCancel} disabled={isSaving}>
                          <X className="h-4 w-4 mr-2" /> Hủy
                        </Button>
                        <Button size="sm" onClick={handleSave} disabled={isSaving}>
                          {isSaving ? (
                            <>
                              <Loader2 className="h-4 w-4 mr-2 animate-spin" /> Đang lưu...
                            </>
                          ) : (
                            <>
                              <Save className="h-4 w-4 mr-2" /> Lưu
                            </>
                          )}
                        </Button>
                      </div>
                    )}
                  </div>
                </CardHeader>

                <CardContent className="space-y-6">
                  {/* Avatar */}
                  <div className="flex items-center space-x-6">
                    <div className="relative">
                      <Avatar className="h-20 w-20">
                        <AvatarImage 
                          src={avatarPreview || user?.avatar || ""} 
                          alt="User avatar"
                        />
                        <AvatarFallback className="bg-gradient-to-r from-blue-600 to-purple-600 text-white text-lg font-bold">
                          {getInitials(user?.name || "U")}
                        </AvatarFallback>
                      </Avatar>
                      {isEditing && (
                        <label htmlFor="avatar-upload">
                          <Button
                            size="sm"
                            className="absolute -bottom-2 -right-2 rounded-full w-8 h-8 p-0 cursor-pointer"
                            asChild
                          >
                            <span>
                              <Camera className="h-4 w-4" />
                            </span>
                          </Button>
                        </label>
                      )}
                      <input
                        id="avatar-upload"
                        type="file"
                        accept="image/*"
                        onChange={handleAvatarChange}
                        className="hidden"
                      />
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold">{formData.name}</h3>
                      <p className="text-gray-600">{formData.phone}</p>
                      <Badge variant="secondary" className="mt-2">
                        Thành viên từ{" "}
                        {user.created_at
                          ? new Date(user.created_at).toLocaleDateString("vi-VN")
                          : "N/A"}
                      </Badge>
                    </div>
                  </div>

                  <Separator />

                  {/* Thông tin chi tiết */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <Label htmlFor="name">Họ và tên</Label>
                      <Input
                        id="name"
                        value={formData.name}
                        onChange={(e) => handleInputChange("name", e.target.value)}
                        disabled={!isEditing}
                      />
                    </div>

                    <div>
                      <Label htmlFor="phone">Số điện thoại</Label>
                      <Input
                        id="phone"
                        type="tel"
                        value={formData.phone}
                        onChange={(e) => handleInputChange("phone", e.target.value)}
                        disabled={!isEditing}
                      />
                    </div>

                    <div className="md:col-span-2">
                      <Label htmlFor="address">Địa chỉ</Label>
                      <Input
                        id="address"
                        value={formData.address}
                        onChange={(e) => handleInputChange("address", e.target.value)}
                        disabled={!isEditing}
                      />
                    </div>

                    <div className="md:col-span-2">
                      <Label htmlFor="bio">Giới thiệu bản thân</Label>
                      <Textarea
                        id="bio"
                        placeholder="Chia sẻ về bản thân bạn..."
                        value={formData.bio}
                        onChange={(e) => handleInputChange("bio", e.target.value)}
                        disabled={!isEditing}
                        rows={4}
                      />
                    </div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Tab Bảo mật */}
            <TabsContent value="security">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Shield className="h-5 w-5" />
                    Bảo mật tài khoản
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                  {/* 2FA Section */}
                  <div className="space-y-4">
                    <div className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                      <div>
                        <h3 className="font-semibold">Xác thực 2 lớp (2FA)</h3>
                        <p className="text-sm text-gray-600 mt-1">
                          Bảo vệ tài khoản của bạn bằng mã OTP qua email
                        </p>
                      </div>
                      <Badge variant={user?.two_factor_enabled ? 'default' : 'secondary'}>
                        {user?.two_factor_enabled ? 'Đã bật' : 'Chưa bật'}
                      </Badge>
                    </div>

                    {user?.two_factor_enabled ? (
                      <Button
                        variant="destructive"
                        onClick={() => setShow2FADisableDialog(true)}
                        disabled={loading2FA}
                      >
                        Tắt 2FA
                      </Button>
                    ) : (
                      <Button
                        onClick={() => setShow2FADialog(true)}
                        disabled={loading2FA}
                      >
                        Kích hoạt 2FA
                      </Button>
                    )}
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Tab Tùy chọn */}
            <TabsContent value="preferences">
              <Card>
                <CardHeader>
                  <CardTitle>Tùy chọn cá nhân</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-gray-600">
                    Cấu hình ngôn ngữ, thông báo, múi giờ sẽ được thêm sau.
                  </p>
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>

      {/* 2FA Enable Dialog */}
      <Dialog open={show2FADialog} onOpenChange={setShow2FADialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Kích hoạt 2FA</DialogTitle>
            <DialogDescription>
              Bạn sắp nhận được mã OTP qua email. Bạn có chắc chắn muốn tiếp tục không?
            </DialogDescription>
          </DialogHeader>
          <div className="flex gap-3">
            <Button variant="outline" onClick={() => setShow2FADialog(false)}>
              Hủy
            </Button>
            <Button onClick={handleEnable2FA} disabled={loading2FA}>
              {loading2FA ? 'Đang xử lý...' : 'Xác nhận'}
            </Button>
          </div>
        </DialogContent>
      </Dialog>

      {/* 2FA Confirm Dialog */}
      <Dialog open={show2FAConfirmDialog} onOpenChange={setShow2FAConfirmDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Xác thực OTP</DialogTitle>
            <DialogDescription>
              Nhập mã OTP 6 chữ số được gửi đến email của bạn
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <Input
              placeholder="000000"
              maxLength={6}
              value={otpCode}
              onChange={(e) => setOtpCode(e.target.value.replace(/\D/g, ''))}
              inputMode="numeric"
              className="text-center text-2xl tracking-widest"
            />
            <div className="flex gap-3">
              <Button
                variant="outline"
                onClick={() => {
                  setShow2FAConfirmDialog(false);
                  setOtpCode('');
                }}
              >
                Hủy
              </Button>
              <Button onClick={handleConfirm2FA} disabled={loading2FA || otpCode.length !== 6}>
                {loading2FA ? 'Đang xử lý...' : 'Xác thực'}
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>

      {/* 2FA Disable Dialog */}
      <Dialog open={show2FADisableDialog} onOpenChange={setShow2FADisableDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Tắt 2FA</DialogTitle>
            <DialogDescription>
              Nhập mật khẩu để xác nhận việc tắt 2FA
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <Input
              type="password"
              placeholder="Mật khẩu"
              value={disablePassword}
              onChange={(e) => setDisablePassword(e.target.value)}
            />
            <div className="flex gap-3">
              <Button
                variant="outline"
                onClick={() => {
                  setShow2FADisableDialog(false);
                  setDisablePassword('');
                }}
              >
                Hủy
              </Button>
              <Button
                variant="destructive"
                onClick={handleDisable2FA}
                disabled={loading2FA || !disablePassword}
              >
                {loading2FA ? 'Đang xử lý...' : 'Tắt 2FA'}
              </Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
