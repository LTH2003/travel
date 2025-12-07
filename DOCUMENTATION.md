# 📚 TRAVEL APP - DOCUMENTATION TOÀN DIỆN

## 📋 Mục Lục
1. [Tổng Quan Dự Án](#tổng-quan-dự-án)
2. [Kiến Trúc Hệ Thống](#kiến-trúc-hệ-thống)
3. [Backend Documentation](#backend-documentation)
4. [Frontend Documentation](#frontend-documentation)
5. [Database Schema](#database-schema)
6. [API Documentation](#api-documentation)
7. [Hướng Dẫn Setup](#hướng-dẫn-setup)
8. [Các Tính Năng](#các-tính-năng)

---

## 🎯 Tổng Quan Dự Án

### Giới Thiệu
**Travel App** là một ứng dụng web toàn diện để đặt tour du lịch, khách sạn và quản lý booking. Ứng dụng được xây dựng với:

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: React 18 + TypeScript + Vite + Tailwind CSS
- **Database**: MySQL
- **API**: REST API (Laravel Sanctum)
- **Thanh Toán**: QR Code Payment
- **Xác Thực**: 2FA (Two-Factor Authentication) via OTP

### Người Dùng Chính
1. **Admin** - Quản lý tour, khách sạn, người dùng, booking
2. **Customers** - Tìm kiếm, đặt tour, quản lý yêu thích
3. **Guests** - Xem công khai tour, blog

### Tính Năng Chính
- ✅ Quản lý Tour Du Lịch (CRUD)
- ✅ Quản lý Khách Sạn & Phòng
- ✅ Hệ Thống Booking
- ✅ Giỏ Hàng & Thanh Toán QR
- ✅ 2FA với OTP
- ✅ Yêu Thích Tour
- ✅ Lịch Sử Mua Hàng
- ✅ Blog & Tin Tức
- ✅ Đề Xuất Tour (Recommendation)
- ✅ Admin Panel
- ✅ Quản Lý Liên Hệ

---

## 🏗️ Kiến Trúc Hệ Thống

### Sơ Đồ Kiến Trúc
```
┌─────────────────────────────────────────────────────┐
│                   FRONTEND (React)                  │
│  Pages, Components, Hooks, API Calls                │
└──────────────────┬──────────────────────────────────┘
                   │ HTTP/REST
                   ↓
┌─────────────────────────────────────────────────────┐
│              BACKEND (Laravel API)                  │
│  Controllers, Services, Models, Middleware          │
└──────────────────┬──────────────────────────────────┘
                   │ SQL
                   ↓
┌─────────────────────────────────────────────────────┐
│            DATABASE (MySQL)                         │
│  Users, Tours, Hotels, Orders, Bookings, etc.       │
└─────────────────────────────────────────────────────┘
```

### Stack Teknologi

#### Backend
```
Laravel 12
├── Sanctum (API Authentication)
├── Eloquent ORM
├── Migrations
├── Seeders
└── QR Code Generator (endroid/qr-code)
```

#### Frontend
```
React 18 + TypeScript
├── React Router (Navigation)
├── React Query (Data Fetching)
├── Tailwind CSS (Styling)
├── Shadcn UI (Components)
├── Framer Motion (Animations)
└── Axios (HTTP Client)
```

#### Database
```
MySQL
├── Users
├── Tours
├── Hotels
├── Rooms
├── Orders
├── Payments
├── Bookings
├── Favorites
├── Contacts
└── OTP Codes
```

---

## 📂 Backend Documentation

### Cấu Trúc Folder Backend

```
client/backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          (Admin Panel Controllers)
│   │   │   └── Api/            (API Endpoints)
│   │   ├── Middleware/         (Auth, Admin check)
│   │   ├── Requests/           (Form Validation)
│   │   ├── Resources/          (JSON Responses)
│   │   └── Kernel.php
│   ├── Models/                 (Database Models)
│   ├── Services/               (Business Logic)
│   ├── Mail/                   (Email Templates)
│   ├── Observers/              (Model Observers)
│   └── Providers/              (Service Providers)
├── config/                     (Configuration Files)
├── database/
│   ├── migrations/             (Database Schema)
│   ├── seeders/                (Seed Data)
│   └── factories/              (Factory Templates)
├── routes/
│   ├── api.php                 (API Routes)
│   └── web.php                 (Web Routes - Admin)
├── resources/
│   ├── views/                  (Blade Templates)
│   ├── css/
│   └── js/
├── storage/                    (File Storage)
├── tests/                      (Unit & Feature Tests)
└── vendor/                     (Dependencies)
```

### Backend Controllers

#### Admin Controllers (`app/Http/Controllers/Admin/`)
- **AuthController** - Login/Logout cho Admin
- **DashboardController** - Thống kê & Dashboard
- **UserController** - Quản lý Người Dùng (CRUD)
- **TourController** - Quản lý Tour (CRUD)
- **BlogController** - Quản lý Blog (CRUD)
- **HotelController** - Quản lý Khách Sạn (CRUD)
- **RoomController** - Quản lý Phòng (CRUD)
- **BookingController** - Xem & Cập Nhật Booking
- **ContactController** - Quản lý Liên Hệ

#### API Controllers (`app/Http/Controllers/Api/`)
- **AuthController** - Register, Login, 2FA (OTP)
- **TourController** - Lấy Tour, Chi Tiết Tour
- **HotelController** - Lấy Khách Sạn
- **RoomController** - Lấy Phòng
- **CartController** - Quản lý Giỏ Hàng
- **BookingController** - Tạo Booking, Lấy Chi Tiết
- **PaymentController** - Xử Lý Thanh Toán QR
- **FavoriteController** - Thêm/Xóa Yêu Thích
- **BlogController** - Lấy Blog
- **UserController** - Lấy Thông Tin User
- **RecommendationController** - Gợi Ý Tour
- **ContactController** - Submit Liên Hệ
- **BookingManagementController** - Quản Lý Booking Detail

### Database Models

| Model | Mô Tả | Quan Hệ |
|-------|-------|---------|
| **User** | Người dùng | has_many: Orders, Favorites, Contacts |
| **Tour** | Tour du lịch | has_many: Favorites |
| **Hotel** | Khách sạn | has_many: Rooms, Favorites |
| **Room** | Phòng khách sạn | belongs_to: Hotel |
| **Order** | Đơn hàng | belongs_to: User, has_many: Payments, BookingDetails |
| **Payment** | Thanh toán | belongs_to: Order |
| **BookingDetail** | Chi tiết booking | belongs_to: Order |
| **Favorite** | Yêu thích | belongs_to: User |
| **Blog** | Bài viết blog | - |
| **Contact** | Liên hệ | belongs_to: User |
| **OtpCode** | Mã OTP | belongs_to: User |

### Middleware

```php
// authenticate (Kiểm tra đã login)
// admin (Kiểm tra role = admin)
// api (Default API middleware)
```

### Authentication Flow

```
1. User POST /api/register
   ↓
2. User POST /api/login
   ↓
3. Backend tạo OTP Code & gửi email
   ↓
4. User POST /api/auth/verify-otp
   ↓
5. Verify OTP → Tạo API Token (Sanctum)
   ↓
6. Frontend lưu token vào localStorage
   ↓
7. Các request sau gủi Authorization: Bearer TOKEN
```

### Routes API

#### Public Routes (không cần auth)
```
GET    /api/blog                    - Danh sách blog
GET    /api/blog/{id}               - Chi tiết blog
GET    /api/tours                   - Danh sách tour
GET    /api/tours/{id}              - Chi tiết tour
POST   /api/register                - Đăng ký
POST   /api/login                   - Đăng nhập
POST   /api/auth/verify-otp         - Xác minh OTP
POST   /api/auth/resend-otp         - Gửi lại OTP
POST   /api/contacts                - Gửi liên hệ
```

#### Protected Routes (cần auth token)
```
POST   /api/logout                  - Đăng xuất
GET    /api/profile                 - Thông tin cá nhân
PUT    /api/profile                 - Cập nhật hồ sơ
GET    /api/favorites               - Danh sách yêu thích
POST   /api/favorites               - Thêm yêu thích
DELETE /api/favorites/{id}          - Xóa yêu thích
GET    /api/cart                    - Lấy giỏ hàng
POST   /api/cart                    - Thêm vào giỏ
DELETE /api/cart/{id}               - Xóa khỏi giỏ
POST   /api/bookings                - Tạo booking
GET    /api/bookings                - Lịch sử booking
GET    /api/bookings/{id}           - Chi tiết booking
POST   /api/payments                - Tạo thanh toán
GET    /api/payments/{id}           - Chi tiết thanh toán
GET    /api/recommendations         - Tour gợi ý
```

#### Admin Routes (cần auth + role=admin)
```
GET    /admin/dashboard             - Dashboard
GET    /admin/users                 - Danh sách người dùng
POST   /admin/users                 - Tạo người dùng
PUT    /admin/users/{id}            - Cập nhật người dùng
DELETE /admin/users/{id}            - Xóa người dùng

GET    /admin/tours                 - Danh sách tour
POST   /admin/tours                 - Tạo tour
PUT    /admin/tours/{id}            - Cập nhật tour
DELETE /admin/tours/{id}            - Xóa tour

GET    /admin/blogs                 - Danh sách blog
POST   /admin/blogs                 - Tạo blog
PUT    /admin/blogs/{id}            - Cập nhật blog
DELETE /admin/blogs/{id}            - Xóa blog

GET    /admin/hotels                - Danh sách khách sạn
POST   /admin/hotels                - Tạo khách sạn
PUT    /admin/hotels/{id}           - Cập nhật khách sạn
DELETE /admin/hotels/{id}           - Xóa khách sạn

GET    /admin/bookings              - Danh sách booking
GET    /admin/bookings/{id}         - Chi tiết booking
PUT    /admin/bookings/{id}/status  - Cập nhật trạng thái
```

### Key Features Backend

#### 1. Authentication & Authorization
- Laravel Sanctum cho API token
- 2FA via Email OTP
- Role-based access control (User, Admin)
- Middleware protection

#### 2. Payment System
- QR Code generation (endroid/qr-code)
- Payment tracking
- Order status management
- Email notifications

#### 3. Booking System
- Tour/Hotel booking
- Room availability
- Booking status tracking
- Notification system

#### 4. Email System
- OTP sending (BookingConfirmationMail, OtpCodeMail)
- Gmail SMTP configuration
- Queued emails

---

## 🎨 Frontend Documentation

### Cấu Trúc Folder Frontend

```
client/frontend/
├── src/
│   ├── pages/              (Page Components)
│   ├── components/         (Reusable Components)
│   ├── hooks/              (Custom Hooks)
│   ├── api/                (API Calls)
│   ├── lib/                (Utilities)
│   ├── App.tsx             (Main App)
│   └── main.tsx            (Entry Point)
├── public/                 (Static Assets)
├── package.json
├── vite.config.ts
├── tailwind.config.ts
└── tsconfig.json
```

### Pages (Routes)

```
/                    → Index.tsx              (Trang chủ)
/tours               → Tours.tsx              (Danh sách tour)
/tours/:id           → TourDetail.tsx         (Chi tiết tour)
/hotels              → Hotels.tsx             (Danh sách khách sạn)
/hotels/:id          → HotelDetail.tsx        (Chi tiết khách sạn)
/blog                → Blog.tsx               (Danh sách blog)
/blog/:slug          → BlogDetail.tsx         (Chi tiết blog)
/contact             → Contact.tsx            (Liên hệ)
/login               → Login.tsx              (Đăng nhập)
/register            → Register.tsx           (Đăng ký)
/verify-otp          → VerifyOtp.tsx          (Xác minh OTP)
/cart                → Cart.tsx               (Giỏ hàng)
/checkout            → Checkout.tsx           (Thanh toán)
/payment-qr/:id      → PaymentQR.tsx          (QR Thanh toán)
/booking-success/:id → BookingSuccess.tsx     (Thành công)
/favorites           → Favorites.tsx          (Yêu thích)
/recommendations     → Recommendations.tsx   (Gợi ý)
/purchase-history    → PurchaseHistory.tsx   (Lịch sử mua)
/profile             → Profile.tsx            (Hồ sơ)
*                    → NotFound.tsx           (404)
```

### Components

#### UI Components (`components/ui/`)
- Accordion, AlertDialog, Avatar, Badge, Button
- Card, Checkbox, Dialog, Dropdown, Form
- Input, Label, Progress, Select, Tabs
- Toast, Tooltip, Slider, Switch, Textarea
- Popover, Skeleton, etc. (Shadcn UI)

#### Custom Components
| Component | Mô Tả |
|-----------|-------|
| **Header** | Navigation bar với menu, search, user |
| **QRScanner** | QR code scanner cho thanh toán |
| **SearchForm** | Tìm kiếm tour/khách sạn |
| **TourCard** | Thẻ hiển thị tour |

### Hooks

#### Custom Hooks
- `useAuth()` - Quản lý authentication
- `useCart()` - Quản lý giỏ hàng
- `useFavorites()` - Quản lý yêu thích
- `useBooking()` - Quản lý booking
- `useProfile()` - Lấy hồ sơ user

### API Integration

#### API Client Setup
```typescript
// api/client.ts
import axios from 'axios';

const API_BASE = 'http://127.0.0.1:8000/api';

const client = axios.create({
  baseURL: API_BASE,
});

// Add token to requests
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default client;
```

#### API Calls
```typescript
// Auth
POST   /api/register        - Đăng ký
POST   /api/login           - Đăng nhập
POST   /api/logout          - Đăng xuất
POST   /api/verify-otp      - Xác minh OTP

// Tours
GET    /api/tours           - Danh sách tour
GET    /api/tours/:id       - Chi tiết tour

// Bookings
POST   /api/bookings        - Tạo booking
GET    /api/bookings        - Lịch sử booking

// Favorites
GET    /api/favorites       - Danh sách yêu thích
POST   /api/favorites       - Thêm yêu thích
DELETE /api/favorites/:id   - Xóa yêu thích

// Payments
POST   /api/payments        - Tạo thanh toán
GET    /api/payments/:id    - Chi tiết thanh toán
```

### State Management

#### Local Storage
```javascript
// Auth
localStorage.getItem('auth_token')
localStorage.getItem('user')

// Cart
localStorage.getItem('cart')

// Theme
localStorage.getItem('theme')
```

#### React Query
```typescript
// Fetching data
const { data, isLoading, error } = useQuery({
  queryKey: ['tours'],
  queryFn: () => getTours(),
});

// Mutations
const { mutate } = useMutation({
  mutationFn: (data) => createBooking(data),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['bookings'] });
  },
});
```

### Styling

#### Tailwind CSS
- Utility-first CSS framework
- Configured in `tailwind.config.ts`
- Custom colors, fonts, spacing

#### CSS Modules
- Global styles in `index.css`
- Component-specific in `App.css`

---

## 💾 Database Schema

### Tables

#### users
```sql
id (PK)
name (VARCHAR)
email (VARCHAR, UNIQUE)
password (VARCHAR)
role (ENUM: user, admin)
phone (VARCHAR)
address (TEXT)
bio (TEXT)
avatar (VARCHAR)
two_factor_enabled (BOOLEAN)
two_factor_verified (BOOLEAN)
created_at
updated_at
```

#### tours
```sql
id (PK)
name (VARCHAR)
slug (VARCHAR, UNIQUE)
destination (VARCHAR)
description (LONGTEXT)
price (DECIMAL)
duration (INT)
image (VARCHAR)
rating (FLOAT)
highlights (JSON)
includes (JSON)
itinerary (JSON)
departure (JSON)
created_at
updated_at
```

#### hotels
```sql
id (PK)
name (VARCHAR)
description (LONGTEXT)
address (VARCHAR)
city (VARCHAR)
rating (FLOAT)
image (VARCHAR)
amenities (JSON)
created_at
updated_at
```

#### rooms
```sql
id (PK)
hotel_id (FK)
name (VARCHAR)
type (VARCHAR)
price (DECIMAL)
capacity (INT)
amenities (JSON)
image (VARCHAR)
available_count (INT)
created_at
updated_at
```

#### orders
```sql
id (PK)
user_id (FK)
order_code (VARCHAR, UNIQUE)
total_amount (DECIMAL)
status (ENUM: pending, completed, cancelled)
payment_method (VARCHAR)
items (JSON)
notes (TEXT)
completed_at (DATETIME)
email_sent_at (DATETIME)
created_at
updated_at
```

#### payments
```sql
id (PK)
order_id (FK)
amount (DECIMAL)
payment_method (VARCHAR)
reference_code (VARCHAR)
qr_code (TEXT)
status (ENUM: pending, completed, failed)
created_at
updated_at
```

#### booking_details
```sql
id (PK)
order_id (FK)
tour_id (FK, nullable)
hotel_id (FK, nullable)
room_id (FK, nullable)
check_in (DATE)
check_out (DATE)
guests (INT)
notes (TEXT)
created_at
updated_at
```

#### favorites
```sql
id (PK)
user_id (FK)
favoritable_id (INT)
favoritable_type (VARCHAR)
created_at
updated_at
```

#### blogs
```sql
id (PK)
title (VARCHAR)
slug (VARCHAR, UNIQUE)
content (LONGTEXT)
author (VARCHAR)
image (VARCHAR)
published_at (DATETIME)
created_at
updated_at
```

#### contacts
```sql
id (PK)
user_id (FK, nullable)
name (VARCHAR)
email (VARCHAR)
message (LONGTEXT)
status (ENUM: new, read, replied)
created_at
updated_at
```

#### otp_codes
```sql
id (PK)
user_id (FK)
code (VARCHAR)
email (VARCHAR)
expires_at (DATETIME)
verified_at (DATETIME)
created_at
```

---

## 🔌 API Documentation

### Authentication

#### 1. Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response:
{
  "message": "Registration successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### 2. Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "message": "OTP sent to email",
  "user_id": 1,
  "temp_token": "xxx"
}
```

#### 3. Verify OTP
```http
POST /api/auth/verify-otp
Content-Type: application/json

{
  "user_id": 1,
  "code": "123456"
}

Response:
{
  "message": "OTP verified",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

### Tours

#### Get Tours
```http
GET /api/tours
Query: ?page=1&per_page=10

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Tour Hà Nội - Sapa",
      "destination": "Sapa",
      "price": 5000000,
      "rating": 4.5,
      "image": "url"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 10,
    "current_page": 1
  }
}
```

#### Get Tour Detail
```http
GET /api/tours/:id

Response:
{
  "id": 1,
  "name": "Tour Hà Nội - Sapa",
  "destination": "Sapa",
  "description": "...",
  "price": 5000000,
  "duration": 3,
  "rating": 4.5,
  "highlights": ["..."],
  "includes": ["..."],
  "itinerary": [{...}],
  "departure": [{...}]
}
```

### Bookings

#### Create Booking
```http
POST /api/bookings
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "items": [
    {
      "type": "tour",
      "id": 1,
      "guests": 2,
      "date": "2024-12-25"
    }
  ],
  "notes": "Special requests"
}

Response:
{
  "order_id": 123,
  "total_amount": 10000000,
  "status": "pending",
  "next_step": "payment"
}
```

#### Get Booking History
```http
GET /api/bookings
Authorization: Bearer TOKEN

Response:
{
  "data": [
    {
      "id": 1,
      "order_code": "ORD-001",
      "total_amount": 10000000,
      "status": "completed",
      "items": [{...}]
    }
  ]
}
```

### Payments

#### Create Payment
```http
POST /api/payments
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "order_id": 123,
  "amount": 10000000,
  "payment_method": "qr_code"
}

Response:
{
  "id": 1,
  "qr_code": "data:image/png;base64,...",
  "amount": 10000000,
  "reference_code": "REF-123456"
}
```

### Favorites

#### Add to Favorites
```http
POST /api/favorites
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "favoritable_type": "tour",
  "favoritable_id": 1
}

Response:
{
  "message": "Added to favorites",
  "id": 1
}
```

#### Get Favorites
```http
GET /api/favorites
Authorization: Bearer TOKEN

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Tour Hà Nội",
      "type": "tour",
      "image": "url"
    }
  ]
}
```

---

## 🚀 Hướng Dẫn Setup

### Yêu Cầu Hệ Thống
- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- Composer
- npm hoặc pnpm

### Backend Setup

#### Bước 1: Clone & Install
```bash
cd client/backend
composer install
```

#### Bước 2: Cấu Hình Environment
```bash
cp .env.example .env
php artisan key:generate
```

Cập nhật `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
```

#### Bước 3: Database
```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

#### Bước 4: Khởi Động
```bash
php artisan serve
# Server: http://127.0.0.1:8000
```

### Frontend Setup

#### Bước 1: Install Dependencies
```bash
cd client/frontend
pnpm install
# hoặc npm install
```

#### Bước 2: Khởi Động Dev Server
```bash
pnpm dev
# hoặc npm run dev
# Server: http://localhost:5173
```

#### Bước 3: Build Production
```bash
pnpm build
pnpm preview
```

### Admin Login
```
URL: http://127.0.0.1:8000/admin/login
Email: admin@example.com
Password: password
```

---

## ✨ Các Tính Năng

### 1. Quản Lý Tour
- ✅ CRUD Tour
- ✅ Upload hình ảnh
- ✅ Quản lý lịch trình
- ✅ Đánh giá & Rating
- ✅ Phân trang

### 2. Quản Lý Khách Sạn & Phòng
- ✅ CRUD Khách Sạn
- ✅ CRUD Phòng
- ✅ Quản lý sức chứa phòng
- ✅ Tiện nghi (Amenities)

### 3. Hệ Thống Booking
- ✅ Tạo booking
- ✅ Xem chi tiết booking
- ✅ Cập nhật trạng thái
- ✅ Thông báo email
- ✅ Lịch sử booking

### 4. Thanh Toán QR
- ✅ Tạo QR code thanh toán
- ✅ Theo dõi thanh toán
- ✅ Cập nhật trạng thái
- ✅ Email xác nhận

### 5. 2FA (OTP)
- ✅ Gửi OTP qua email
- ✅ Xác minh OTP
- ✅ Gửi lại OTP
- ✅ Hết hạn OTP

### 6. Yêu Thích
- ✅ Thêm tour/khách sạn yêu thích
- ✅ Xóa khỏi yêu thích
- ✅ Danh sách yêu thích

### 7. Blog & Tin Tức
- ✅ CRUD Blog
- ✅ Hiển thị danh sách
- ✅ Chi tiết bài viết

### 8. Đề Xuất Tour
- ✅ Gợi ý dựa trên yêu thích
- ✅ Gợi ý dựa trên lịch sử xem
- ✅ Gợi ý phổ biến

### 9. Giỏ Hàng
- ✅ Thêm tour vào giỏ
- ✅ Xóa khỏi giỏ
- ✅ Lưu giỏ hàng (localStorage)
- ✅ Tính tổng tiền

### 10. Admin Panel
- ✅ Dashboard thống kê
- ✅ Quản lý người dùng
- ✅ Quản lý tour
- ✅ Quản lý blog
- ✅ Quản lý khách sạn
- ✅ Quản lý booking
- ✅ Quản lý liên hệ

---

## 🔒 Security Features

### Authentication & Authorization
- Laravel Sanctum tokens
- 2FA via OTP
- Password hashing (bcrypt)
- CORS configuration
- Rate limiting

### Data Protection
- SQL Injection prevention
- XSS protection
- CSRF tokens
- Input validation
- Output escaping

---

## 📞 Contact & Support

### Admin Email Configuration
```env
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Travel App
```

### Getting Gmail App Password
1. Go to https://myaccount.google.com
2. Security → App passwords
3. Select Mail & Windows
4. Generate app password
5. Copy & paste to `.env`

---

## 📋 Danh Sách File Quan Trọng

### Backend
- `routes/api.php` - API Routes
- `routes/web.php` - Web Routes (Admin)
- `app/Http/Controllers/Api/*` - API Controllers
- `app/Http/Controllers/Admin/*` - Admin Controllers
- `app/Models/*` - Database Models
- `database/migrations/*` - Database Schema
- `database/seeders/*` - Sample Data
- `.env` - Configuration

### Frontend
- `src/App.tsx` - Main Component
- `src/main.tsx` - Entry Point
- `src/pages/*` - Page Components
- `src/components/*` - Reusable Components
- `src/api/*` - API Calls
- `src/hooks/*` - Custom Hooks
- `vite.config.ts` - Vite Configuration
- `tailwind.config.ts` - Tailwind Configuration

---

## 🎓 Học Thêm

### Backend
- [Laravel Documentation](https://laravel.com/docs)
- [Sanctum API Tokens](https://laravel.com/docs/sanctum)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

### Frontend
- [React Documentation](https://react.dev)
- [React Router](https://reactrouter.com)
- [React Query](https://tanstack.com/query)
- [Tailwind CSS](https://tailwindcss.com)
- [Shadcn UI](https://ui.shadcn.com)

### Database
- [MySQL Documentation](https://dev.mysql.com/doc)

---

## 📝 Ghi Chú

### Lưu Ý Quan Trọng
1. **Email Configuration**: Phải cấu hình Gmail app password để gửi OTP
2. **Database**: Chạy migration trước khi khởi động
3. **CORS**: Frontend URL phải được thêm vào CORS config
4. **Token**: Lưu token trong localStorage, gửi qua Authorization header
5. **2FA**: OTP hết hạn sau 10 phút

### Troubleshooting
- Nếu migration lỗi: Kiểm tra database connection
- Nếu email không gửi: Kiểm tra MAIL_* config
- Nếu login lỗi: Kiểm tra OTP code
- Nếu API lỗi 401: Kiểm tra token validity

---

**Last Updated**: December 7, 2025
**Version**: 1.0
**Status**: Complete Documentation

---
