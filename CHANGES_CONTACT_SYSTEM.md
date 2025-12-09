# 📧 Hệ Thống Gửi Tin Nhắn - Tổng Hợp Thay Đổi

## 🔄 Các Thay Đổi Thực Hiện

### 1. Backend (Laravel)

#### Migration
- **File**: `database/migrations/2025_12_09_add_user_id_to_contacts_table.php`
- **Thay đổi**: Thêm cột `user_id` vào bảng `contacts` với foreign key tới bảng `users`
- **Status**: ✅ Đã chạy migration thành công

#### Contact Model (`app/Models/Contact.php`)
- Thêm relationship `user()` - liên kết với User model
- Thêm relationship `respondedByUser()` - liên kết admin phản hồi
- Cập nhật `$fillable` để bao gồm `user_id`
- Thêm cast `responded_at` thành datetime

#### API Controller (`app/Http/Controllers/Api/ContactController.php`)
- **`store()` method**: 
  - Yêu cầu user phải đăng nhập (check auth)
  - Chỉ nhận 2 tham số: `subject` và `message`
  - Tự động lấy thông tin user từ authenticated user: `name`, `email`, `phone`
  - Trả về lỗi 401 nếu user chưa đăng nhập
  
#### Admin Controller (`app/Http/Controllers/Admin/ContactController.php`)
- `index()`: Thêm eager loading của `user` relationship
- `show()`: Thêm eager loading của `user` và `respondedByUser` relationships

### 2. Frontend (React)

#### Contact Page (`src/pages/Contact.tsx`)
- Import `useAuth` hook để kiểm tra trạng thái đăng nhập
- Import `useNavigate` để redirect đến login page
- **Form chỉ hiển thị 2 trường**:
  1. Chủ đề (subject)
  2. Nội dung (message)
- **Nếu user chưa đăng nhập**: Hiển thị thông báo và nút "Đăng nhập"
- **Nếu user đã đăng nhập**: Hiển thị form với thông tin user (tên, email, số điện thoại) trong blue info box
- Gửi form data chỉ gồm `{ subject, message }`, API sẽ tự động điền user info

### 3. Admin Panel

#### Blade Templates

**`resources/views/admin/contacts/index.blade.php`**:
- Cập nhật header table từ "Tên" → "Tên người gửi"
- Thêm hiển thị User ID khi hover email

**`resources/views/admin/contacts/show.blade.php`**:
- Thêm link "Xem hồ sơ người dùng" nếu contact từ user đã đăng nhập
- Link dẫn tới trang profile của user: `route('admin.users.show', $contact->user->id)`

---

## 📝 API Endpoints

### Public Endpoint (Cần Auth)
```
POST /api/contacts
Content-Type: application/json
Authorization: Bearer {token}

{
  "subject": "Chủ đề tin nhắn",
  "message": "Nội dung tin nhắn..."
}
```

**Response (Success)**:
```json
{
  "status": true,
  "message": "Tin nhắn đã được gửi thành công!",
  "data": {
    "id": 1,
    "user_id": 5,
    "name": "Lê Hữu Yên",
    "email": "yen@example.com",
    "phone": "0889421997",
    "subject": "...",
    "message": "...",
    "status": "new",
    "created_at": "2025-12-09T10:30:00Z",
    ...
  }
}
```

**Response (Not Authenticated)**:
```json
{
  "status": false,
  "message": "Vui lòng đăng nhập để gửi tin nhắn"
}
```

### Admin Endpoints
```
GET    /api/admin/contacts                  - Danh sách tin nhắn
GET    /api/admin/contacts/{id}             - Chi tiết 1 tin nhắn
PUT    /api/admin/contacts/{id}             - Cập nhật trạng thái/phản hồi
DELETE /api/admin/contacts/{id}             - Xóa tin nhắn
```

---

## 🧪 Hướng Dẫn Test

### 1. Test Frontend Form
1. Truy cập `/contact` khi **CHƯA đăng nhập**:
   - ✅ Sẽ hiển thị thông báo "Vui lòng đăng nhập"
   - ✅ Nút "Đăng Nhập" sẽ redirect tới `/login`

2. Đăng nhập tài khoản bất kỳ, sau đó truy cập `/contact`:
   - ✅ Form sẽ hiển thị 2 trường: Chủ đề & Nội dung
   - ✅ Blue box sẽ hiển thị: Tên, Email, Số điện thoại của user đang đăng nhập
   - ✅ Nhập chủ đề & nội dung, click "Gửi Tin Nhắn"
   - ✅ Thông báo "Gửi tin nhắn thành công!"
   - ✅ Form reset

### 2. Test Admin Panel
1. Đăng nhập admin panel (`/admin/login`)
2. Vào mục "Quản lý Tin nhắn Liên hệ" (hoặc `/admin/contacts`)
3. Xem danh sách tin nhắn:
   - ✅ Hiển thị tên, email, số điện thoại của người gửi
   - ✅ User ID sẽ hiển thị dưới email
4. Click vào 1 tin nhắn để xem chi tiết:
   - ✅ Hiển thị nút "Xem hồ sơ người dùng" nếu tin nhắn từ user có account
   - ✅ Admin có thể phản hồi & cập nhật trạng thái

### 3. Test Database
```bash
# Kiểm tra cột user_id đã được thêm
sqlite3 database.sqlite ".schema contacts"

# Query tin nhắn cùng user info
SELECT c.id, c.name, c.email, u.id as user_id, u.name as user_name 
FROM contacts c
LEFT JOIN users u ON c.user_id = u.id
ORDER BY c.created_at DESC;
```

---

## 🔒 Security Notes

✅ **Authentication**: Form cần token JWT để gửi tin nhắn
✅ **Authorization**: Chỉ admin có thể xem danh sách & chi tiết tin nhắn
✅ **Data Validation**: `subject` & `message` được validate bằng Laravel validation rules
✅ **SQL Injection**: Sử dụng Eloquent ORM, safe khỏi SQL injection
✅ **User Info**: Tự động lấy từ authenticated user, không thể spoofing

---

## 📦 Files Modified

**Backend**:
- ✅ `database/migrations/2025_12_09_add_user_id_to_contacts_table.php` (NEW)
- ✅ `app/Models/Contact.php`
- ✅ `app/Http/Controllers/Api/ContactController.php`
- ✅ `app/Http/Controllers/Admin/ContactController.php`
- ✅ `resources/views/admin/contacts/index.blade.php`
- ✅ `resources/views/admin/contacts/show.blade.php`

**Frontend**:
- ✅ `src/pages/Contact.tsx`

---

## 🚀 Next Steps (Optional)

1. **Email Notification**: Gửi email tới admin khi có tin nhắn mới
2. **Email Reply**: Admin phản hồi qua email tới user
3. **Real-time Notification**: WebSocket để notify admin khi có tin nhắn mới
4. **Attachment Support**: Cho phép upload file trong tin nhắn
5. **Auto-Response**: Auto reply template khi user gửi tin nhắn

---

**Hoàn tất ngày**: 09/12/2025
**Status**: ✅ COMPLETE
