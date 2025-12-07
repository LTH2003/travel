# 🧪 QUICK TEST: Contact Form

## ✅ Xác Minh Hệ Thống

### 1️⃣ Kiểm Tra Backend

```bash
# Terminal 1: Backend
cd c:\Users\Admin\travel-app\client\backend
php artisan serve
# Output: Server running at http://127.0.0.1:8000
```

### 2️⃣ Kiểm Tra Frontend

```bash
# Terminal 2: Frontend
cd c:\Users\Admin\travel-app\client\frontend
pnpm dev
# hoặc: npm run dev
# Output: Local: http://localhost:5173
```

### 3️⃣ Test Contact Form

**Cách 1: Via Frontend UI**
1. Mở: http://localhost:5173/contact
2. Điền form:
   - Họ và tên: `Test Admin`
   - Số điện thoại: `0123456789`
   - Email: `admin@test.com`
   - Chủ đề: `Test message`
   - Nội dung: `This is a test message for contact form`
3. Ấn **Gửi Tin Nhắn**
4. Nên thấy toast: ✅ "Gửi tin nhắn thành công!"

**Cách 2: Via Postman**
```
POST http://127.0.0.1:8000/api/contacts
Content-Type: application/json

{
  "name": "Test User",
  "email": "test@test.com",
  "phone": "0987654321",
  "subject": "Test Subject",
  "message": "This is a test message that is definitely long enough"
}
```

### 4️⃣ Kiểm Tra Admin Panel

**Từ Web:**
1. Mở: http://127.0.0.1:8000/admin/login
2. Đăng nhập:
   - Email: `admin@example.com`
   - Password: `password`
3. Vào **Liên hệ** (hoặc menu Contacts)
4. URL: http://127.0.0.1:8000/admin/contacts
5. Bạn sẽ thấy:
   - Danh sách tin nhắn
   - Stats (Tổng, Chưa đọc, Đã đọc, Đã trả lời)
   - Click vào message để xem chi tiết & reply

---

## 📊 Expected Flow

```
Frontend Form Submission
        ↓
API: POST /api/contacts
        ↓
Backend: Validate & Save to DB
        ↓
Response: { status: true, message: "..." }
        ↓
Frontend: Show success toast
        ↓
Admin Panel: GET /admin/contacts
        ↓
Display in table with stats
```

---

## 🔍 Debug Commands

```bash
# 1. Kiểm tra số lượng contacts
php artisan tinker
>>> App\Models\Contact::count()

# 2. Lấy tất cả contacts
>>> App\Models\Contact::all()

# 3. Xóa tất cả contacts (DEV ONLY)
>>> App\Models\Contact::truncate()

# 4. Exit tinker
>>> exit
```

---

## ✨ Status

- ✅ Database: Sẵn sàng (migration chạy thành công)
- ✅ Backend API: Hoạt động (routes được đăng ký)
- ✅ Admin Panel: Hoạt động (views được tạo)
- ✅ Frontend Form: Hoạt động (component được viết)
- ✅ Model & Controller: Cập nhật (thêm fields response)

---

**Tất cả đã sẵn sàng để test!** 🎉
