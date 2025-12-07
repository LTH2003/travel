# Hướng dẫn hoàn thành hệ thống Tour Manager & Hotel Manager

## ✅ Các bước đã hoàn thành

### 1. **Thêm 2 Role mới: tour_manager & hotel_manager**
   - Cập nhật `app/Http/Controllers/Admin/UserController.php` để cho phép tạo/cập nhật users với 2 role mới
   - Cập nhật `resources/views/admin/users/create.blade.php` - Thêm options cho tour_manager và hotel_manager
   - Cập nhật `resources/views/admin/users/edit.blade.php` - Thêm options cho tour_manager và hotel_manager

### 2. **Tạo Controllers cho Manager Roles**
   - `app/Http/Controllers/Admin/TourManagerController.php` - CRUD operations for tours
   - `app/Http/Controllers/Admin/HotelManagerController.php` - CRUD operations for hotels
   - `app/Http/Controllers/Admin/ManagerAuthController.php` - Login handling for both manager roles

### 3. **Tạo Middleware cho Authorization**
   - `app/Http/Middleware/IsTourManager.php` - Kiểm tra role='tour_manager'
   - `app/Http/Middleware/IsHotelManager.php` - Kiểm tra role='hotel_manager'
   - Cập nhật `bootstrap/app.php` - Đăng ký middleware aliases

### 4. **Cập nhật Routes**
   - Thêm login routes cho `/tour-manager/login` và `/hotel-manager/login`
   - Thêm protected routes cho `/tour-manager/*` (yêu cầu auth + tour_manager role)
   - Thêm protected routes cho `/hotel-manager/*` (yêu cầu auth + hotel_manager role)

### 5. **Tạo Views cho Tour Manager**
   - `resources/views/admin/tour-manager/dashboard.blade.php` - Dashboard với stats
   - `resources/views/admin/tour-manager/tours/index.blade.php` - Danh sách tours
   - `resources/views/admin/tour-manager/tours/create.blade.php` - Form tạo tour
   - `resources/views/admin/tour-manager/tours/edit.blade.php` - Form sửa tour
   - `resources/views/admin/tour-manager/tours/show.blade.php` - Chi tiết tour

### 6. **Tạo Views cho Hotel Manager**
   - `resources/views/admin/hotel-manager/dashboard.blade.php` - Dashboard với stats
   - `resources/views/admin/hotel-manager/hotels/index.blade.php` - Danh sách khách sạn
   - `resources/views/admin/hotel-manager/hotels/create.blade.php` - Form tạo khách sạn
   - `resources/views/admin/hotel-manager/hotels/edit.blade.php` - Form sửa khách sạn
   - `resources/views/admin/hotel-manager/hotels/show.blade.php` - Chi tiết khách sạn

### 7. **Tạo Login Views**
   - `resources/views/admin/auth/tour-manager-login.blade.php` - Login page cho tour_manager
   - `resources/views/admin/auth/hotel-manager-login.blade.php` - Login page cho hotel_manager

### 8. **Tạo Guest Layout**
   - `resources/views/admin/layouts/guest.blade.php` - Base layout cho login pages

---

## 🚀 Cách sử dụng

### **Tạo Tour Manager User**
1. Đăng nhập với tài khoản admin
2. Vào Quản Lý Người Dùng → Tạo Người Dùng Mới
3. Chọn Role: `Tour Manager`
4. Nhập email và password

### **Đăng nhập Tour Manager**
1. Truy cập: `http://localhost:8000/tour-manager/login`
2. Nhập email và password của tour_manager
3. Sẽ redirect đến `/tour-manager/dashboard`

### **Đăng nhập Hotel Manager**
1. Truy cập: `http://localhost:8000/hotel-manager/login`
2. Nhập email và password của hotel_manager
3. Sẽ redirect đến `/hotel-manager/dashboard`

---

## 📊 Dashboard Features

### **Tour Manager Dashboard**
- Tổng số tours
- Tổng views
- Đánh giá trung bình
- Danh sách 10 tour gần đây
- Buttons: Xem, Sửa, Xóa tour

### **Hotel Manager Dashboard**
- Tổng số khách sạn
- Tổng số phòng
- Đánh giá trung bình
- Phòng còn trống (placeholder)
- Danh sách 10 khách sạn gần đây
- Buttons: Xem, Sửa, Xóa khách sạn

---

## 🔗 Routes Summary

```
# Tour Manager Routes
GET    /tour-manager/login                     → ManagerAuthController@showTourManagerLogin
POST   /tour-manager/login                     → ManagerAuthController@loginTourManager
GET    /tour-manager/dashboard                 → TourManagerController@dashboard
GET    /tour-manager/tours                     → TourManagerController@index
GET    /tour-manager/tours/create              → TourManagerController@create
POST   /tour-manager/tours                     → TourManagerController@store
GET    /tour-manager/tours/{tour}              → TourManagerController@show
GET    /tour-manager/tours/{tour}/edit         → TourManagerController@edit
PUT    /tour-manager/tours/{tour}              → TourManagerController@update
DELETE /tour-manager/tours/{tour}              → TourManagerController@destroy
POST   /tour-manager/logout                    → ManagerAuthController@logout

# Hotel Manager Routes
GET    /hotel-manager/login                    → ManagerAuthController@showHotelManagerLogin
POST   /hotel-manager/login                    → ManagerAuthController@loginHotelManager
GET    /hotel-manager/dashboard                → HotelManagerController@dashboard
GET    /hotel-manager/hotels                   → HotelManagerController@index
GET    /hotel-manager/hotels/create            → HotelManagerController@create
POST   /hotel-manager/hotels                   → HotelManagerController@store
GET    /hotel-manager/hotels/{hotel}           → HotelManagerController@show
GET    /hotel-manager/hotels/{hotel}/edit      → HotelManagerController@edit
PUT    /hotel-manager/hotels/{hotel}           → HotelManagerController@update
DELETE /hotel-manager/hotels/{hotel}           → HotelManagerController@destroy
POST   /hotel-manager/logout                   → ManagerAuthController@logout
```

---

## 📝 Validation Rules

### **Tour Manager User Creation**
```php
'name' => 'required|string|max:255',
'email' => 'required|string|email|max:255|unique:users',
'password' => 'required|string|min:6|confirmed',
'role' => 'required|in:user,admin,tour_manager,hotel_manager',
```

### **Tour CRUD**
```php
'name' => 'required|string|max:255',
'destination' => 'required|string|max:255',
'duration' => 'required|integer|min:1',
'price' => 'required|numeric|min:0',
'description' => 'required|string',
'rating' => 'nullable|numeric|min:0|max:5',
'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
```

### **Hotel CRUD**
```php
'name' => 'required|string|max:255',
'address' => 'required|string|max:255',
'city' => 'required|string|max:100',
'description' => 'required|string',
'amenities' => 'nullable|string',
'rating' => 'nullable|numeric|min:0|max:5',
'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
```

---

## 🔧 Testing Checklist

- [ ] Tạo tour_manager user từ admin panel
- [ ] Tạo hotel_manager user từ admin panel
- [ ] Đăng nhập với tour_manager tài khoản
- [ ] Xem tour manager dashboard
- [ ] Tạo tour mới
- [ ] Sửa tour hiện tại
- [ ] Xem chi tiết tour
- [ ] Xóa tour
- [ ] Đăng xuất tour manager
- [ ] Đăng nhập với hotel_manager tài khoản
- [ ] Xem hotel manager dashboard
- [ ] Tạo khách sạn mới
- [ ] Sửa khách sạn hiện tại
- [ ] Xem chi tiết khách sạn
- [ ] Xóa khách sạn
- [ ] Đăng xuất hotel manager

---

## ⚠️ Lưu ý

1. **Middleware Protection**: Routes được bảo vệ bằng middleware `auth` + role-specific middleware
2. **Role Validation**: User phải có đúng role để truy cập
3. **Logout Handling**: Cả hai manager roles dùng chung `ManagerAuthController@logout`
4. **Image Storage**: Images được lưu trong `storage/app/public` (nếu có upload)

---

## 📚 File được tạo/sửa

### Controllers (2 files mới)
- `/app/Http/Controllers/Admin/ManagerAuthController.php`
- `/app/Http/Controllers/Admin/TourManagerController.php`
- `/app/Http/Controllers/Admin/HotelManagerController.php`

### Middleware (2 files mới)
- `/app/Http/Middleware/IsTourManager.php`
- `/app/Http/Middleware/IsHotelManager.php`

### Views (11 files mới)
- Dashboard (2): tour-manager, hotel-manager
- Tours CRUD (4): index, create, edit, show
- Hotels CRUD (4): index, create, edit, show
- Login (2): tour-manager-login, hotel-manager-login

### Configuration (1 file)
- `/bootstrap/app.php` - Middleware registration

### Routes (1 file)
- `/routes/web.php` - New routes for managers

---

Hệ thống Tour Manager & Hotel Manager đã sẵn sàng để sử dụng! 🎉
