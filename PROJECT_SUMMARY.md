# 🌍 Travel App Project - Complete Overview

**Ngày tạo:** 14/12/2025  
**Dự án:** TravelVN - Ứng Dụng Đặt Tour Du Lịch & Khách Sạn  
**Loại:** Full-Stack Web Application (Laravel + React)

---

## 📋 Table of Contents

1. [Tổng Quan Dự Án](#tổng-quan-dự-án)
2. [Kiến Trúc Hệ Thống](#kiến-trúc-hệ-thống)
3. [Backend (Laravel)](#backend-laravel)
4. [Frontend (React + TypeScript)](#frontend-react--typescript)
5. [Database & Models](#database--models)
6. [API Routes & Controllers](#api-routes--controllers)
7. [Admin Panel](#admin-panel)
8. [Tính Năng Chính](#tính-năng-chính)
9. [Setup & Installation](#setup--installation)

---

## 🎯 Tổng Quan Dự Án

### Định Nghĩa
**Travel App** là một nền tảng e-commerce được xây dựng để cho phép khách hàng:
- 🏝️ Tìm kiếm và đặt tours du lịch
- 🏨 Tìm kiếm và đặt phòng khách sạn
- 💳 Thanh toán an toàn qua QR code/ngân hàng
- 📝 Đánh giá và bình luận về tours/khách sạn
- 📌 Lưu yêu thích, quản lý giỏ hàng

### Độc Giả & Người Dùng

| Nhóm | Mô Tả |
|------|-------|
| **Khách Hàng** | Người dùng bình thường, đăng ký để đặt tour và khách sạn |
| **Admin** | Quản lý toàn bộ hệ thống (tours, khách sạn, users, payments) |
| **Tour Manager** | Quản lý tours (tạo, chỉnh sửa, xóa) |
| **Hotel Manager** | Quản lý khách sạn và phòng |

### Công Nghệ Stack

#### Backend
- **Framework:** Laravel 12.0
- **Language:** PHP 8.2+
- **Authentication:** Laravel Sanctum (API tokens)
- **ORM:** Eloquent
- **Database:** SQLite (dev) / MySQL (prod)
- **Additional Libraries:**
  - `barryvdh/laravel-dompdf` - PDF generation
  - `endroid/qr-code` - QR code generation
  - `laravel/tinker` - REPL console

#### Frontend
- **Framework:** React 19.1.1
- **Language:** TypeScript
- **Build Tool:** Vite
- **UI Framework:** Shadcn/UI (Radix UI components)
- **Styling:** Tailwind CSS
- **HTTP Client:** Axios
- **State Management:** React Query (TanStack Query)
- **Routing:** React Router
- **Form:** React Hook Form
- **Notifications:** Sonner Toast

#### Styling & UI
- **CSS Framework:** Tailwind CSS
- **Icons:** Lucide React, Bootstrap Icons
- **Components:** Radix UI, Shadcn/UI
- **Animations:** Framer Motion

---

## 🏗️ Kiến Trúc Hệ Thống

### Cấu Trúc Dự Án

```
travel-app/
├── client/
│   ├── backend/                (Laravel API)
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/
│   │   │   │   │   ├── Admin/           (Admin dashboard controllers)
│   │   │   │   │   └── Api/             (API controllers)
│   │   │   │   ├── Kernel.php
│   │   │   │   ├── Middleware/
│   │   │   │   └── Requests/
│   │   │   ├── Models/
│   │   │   ├── Services/
│   │   │   ├── Mail/
│   │   │   ├── Observers/
│   │   │   └── Providers/
│   │   ├── database/
│   │   │   ├── migrations/
│   │   │   ├── seeders/
│   │   │   └── factories/
│   │   ├── routes/
│   │   │   ├── api.php           (API routes)
│   │   │   └── web.php           (Admin routes)
│   │   ├── resources/
│   │   │   └── views/            (Blade templates for admin)
│   │   ├── config/
│   │   ├── public/
│   │   └── storage/
│   │
│   └── frontend/               (React + TypeScript)
│       ├── src/
│       │   ├── pages/          (Page components)
│       │   ├── components/     (Reusable components)
│       │   ├── api/            (API integration)
│       │   ├── hooks/          (Custom React hooks)
│       │   ├── lib/            (Utilities)
│       │   ├── App.tsx
│       │   └── main.tsx
│       ├── vite.config.ts
│       ├── tailwind.config.ts
│       └── package.json
│
├── SetupBuild.md
└── USE_CASES.md
```

### Kiến Trúc Layers

```
┌────────────────────────────┐
│   Frontend (React + TS)    │
│  - Pages, Components       │
│  - React Query, Axios      │
└────────────┬───────────────┘
             │ HTTP (REST API)
┌────────────▼───────────────┐
│   API Routing Layer        │
│   - routes/api.php         │
└────────────┬───────────────┘
             │ Middleware
┌────────────▼───────────────┐
│   Controller Layer         │
│   - Api Controllers        │
└────────────┬───────────────┘
             │ Business Logic
┌────────────▼───────────────┐
│   Service Layer            │
│   - Business logic         │
│   - OTP Service, etc       │
└────────────┬───────────────┘
             │ Data Access
┌────────────▼───────────────┐
│   Model Layer (Eloquent)   │
│   - Tour, User, Hotel...   │
└────────────┬───────────────┘
             │ ORM
┌────────────▼───────────────┐
│   Database Layer           │
│   - MySQL/SQLite           │
└────────────────────────────┘
```

---

## 🖥️ Backend (Laravel)

### Cấu Trúc Backend

#### Controllers
Backend sử dụng 2 loại controllers:

**1. API Controllers** (`app/Http/Controllers/Api/`)
- `AuthController` - Đăng ký, đăng nhập, 2FA (OTP)
- `TourController` - Danh sách, chi tiết tours
- `HotelController` - Danh sách, chi tiết khách sạn
- `RoomController` - Quản lý phòng
- `CartController` - Giỏ hàng
- `FavoriteController` - Danh sách yêu thích
- `PaymentController` - Tạo đơn hàng, thanh toán
- `BookingController` / `BookingManagementController` - Quản lý booking
- `TourReviewController` - Đánh giá tours
- `BlogController` - Blog posts
- `BlogCommentController` - Bình luận blog
- `ContactController` - Form liên hệ
- `RecommendationController` - Gợi ý products
- `UserController` - Profile user

**2. Admin Controllers** (`app/Http/Controllers/Admin/`)
- `AuthController` - Login/Logout admin
- `DashboardController` - Trang chủ admin
- `UserController` - Quản lý users (CRUD)
- `TourController` - Quản lý tours (CRUD)
- `BlogController` - Quản lý blogs (CRUD)
- `BlogCommentController` - Duyệt bình luận
- `HotelController` - Quản lý khách sạn (CRUD)
- `RoomController` - Quản lý phòng (CRUD)
- `BookingController` - Xem booking, quản lý status
- `PaymentController` - Xem payments, confirm/reject
- `TourReviewController` - Duyệt, từ chối review
- `ContactController` - Quản lý contact forms

#### Models

| Model | Mô Tả | Mối Quan Hệ |
|-------|-------|-------------|
| **User** | Người dùng | hasMany(Order, Cart, Favorite, TourReview, Contact) |
| **Tour** | Tours du lịch | belongsTo(User-creator), hasMany(TourReview, BookingDetail) |
| **Hotel** | Khách sạn | belongsTo(User-creator), hasMany(Room, Favorite) |
| **Room** | Phòng khách sạn | belongsTo(Hotel), hasMany(BookingDetail) |
| **Order** | Đơn hàng | belongsTo(User), hasMany(BookingDetail, Payment, PurchaseHistory) |
| **BookingDetail** | Chi tiết booking (tour/room) | belongsTo(Order), morphTo(Tour/Room) |
| **Payment** | Thanh toán | belongsTo(Order) |
| **Cart** | Giỏ hàng | belongsTo(User) |
| **Favorite** | Yêu thích | belongsTo(User), morphTo(Tour/Hotel) |
| **TourReview** | Đánh giá tour | belongsTo(Tour, User) |
| **Blog** | Blog posts | hasMany(BlogComment) |
| **BlogComment** | Bình luận blog | belongsTo(Blog, User) |
| **Contact** | Form liên hệ | belongsTo(User) |
| **OtpCode** | Mã OTP 2FA | belongsTo(User) |

#### Routes

**API Routes** (`routes/api.php`)
```
PUBLIC ROUTES (không cần token):
- GET  /api/blog
- GET  /api/blog/{id}
- POST /api/blog/{id}/increment-view
- GET  /api/tours
- GET  /api/tours/{id}
- GET  /api/tours/{tourId}/reviews
- POST /api/register
- POST /api/login
- POST /api/auth/verify-otp
- POST /api/auth/resend-otp

PROTECTED ROUTES (cần auth:sanctum):
- Auth:
  - POST /api/logout
  - GET  /api/me
  - GET  /api/profile
  - PUT  /api/profile
  - POST /api/auth/enable-2fa
  - POST /api/auth/confirm-2fa
  - POST /api/auth/disable-2fa

- Cart:
  - GET  /api/cart
  - POST /api/cart
  - DELETE /api/cart

- Favorites:
  - GET  /api/favorites
  - POST /api/favorites
  - DELETE /api/favorites
  - POST /api/favorites/check

- Reviews & Comments:
  - POST /api/tours/{tourId}/reviews
  - PUT  /api/reviews/{reviewId}
  - DELETE /api/reviews/{reviewId}
  - POST /api/blog-comments/{blogId}
  - PUT  /api/blog-comments/{commentId}
  - DELETE /api/blog-comments/{commentId}

- Payments & Orders:
  - POST /api/orders (tạo đơn hàng)
  - GET  /api/orders (danh sách đơn)
  - GET  /api/orders/{orderId}
  - PUT  /api/orders/{orderId}
  - POST /api/verify-payment (verify thanh toán)
  - POST /api/check-payment-status

- Bookings:
  - GET  /api/bookings
  - POST /api/bookings/checkin (check in)

- Recommendations:
  - GET /api/recommendations

- Contact:
  - POST /api/contacts
```

**Web Routes** (`routes/web.php`)
```
PUBLIC ROUTES:
- GET  / → Login page
- GET  /admin/login
- POST /admin/login

PROTECTED ROUTES (middleware: auth, admin_or_manager):
- Admin:
  - GET /admin/dashboard
  - GET /admin/users/* (admin only)
  - GET /admin/tours/*
  - GET /admin/blogs/* (admin only)
  - GET /admin/hotels/*
  - GET /admin/hotels.rooms/*
  - GET /admin/bookings/* (admin only)
  - GET /admin/payments/* (admin only)
  - GET /admin/tour-reviews/* (admin only)
  - GET /admin/contacts/* (admin only)
```

---

## 💻 Frontend (React + TypeScript)

### Cấu Trúc Frontend

#### Pages
```
src/pages/
├── Index.tsx              - Trang chủ
├── Tours.tsx              - Danh sách tours
├── TourDetail.tsx         - Chi tiết tour
├── Hotels.tsx             - Danh sách khách sạn
├── HotelDetail.tsx        - Chi tiết khách sạn
├── Blog.tsx               - Blog posts
├── BlogDetail.tsx         - Chi tiết bài blog
├── Cart.tsx               - Giỏ hàng
├── Checkout.tsx           - Thanh toán
├── PaymentQR.tsx          - QR code thanh toán
├── Favorites.tsx          - Danh sách yêu thích
├── Recommendations.tsx    - Gợi ý sản phẩm
├── Contact.tsx            - Form liên hệ
├── Login.tsx              - Đăng nhập
├── Register.tsx           - Đăng ký
├── VerifyOtp.tsx          - Xác nhận OTP 2FA
├── Profile.tsx            - Hồ sơ người dùng
├── PurchaseHistory.tsx    - Lịch sử mua hàng
├── BookingSuccess.tsx     - Trang xác nhận booking
└── NotFound.tsx           - 404 page
```

#### Components
```
src/components/
├── Header.tsx             - Navigation header
├── Footer.tsx             - Footer
├── TourCard.tsx           - Component card tour
├── SearchForm.tsx         - Form tìm kiếm
├── TourReviews.tsx        - Section đánh giá
├── BlogComments.tsx       - Section bình luận
├── QRScanner.tsx          - QR code scanner
└── ui/                    - Shadcn/UI components
    ├── button.tsx
    ├── card.tsx
    ├── input.tsx
    ├── dialog.tsx
    ├── toast.tsx
    └── ... (other UI components)
```

#### Hooks & API
```
src/hooks/
- Custom React hooks for:
  - useAuth() - Authentication logic
  - useFetch() - Data fetching
  - useForm() - Form handling
  - etc.

src/api/
- API integration with backend
- Axios instance configuration
- Request interceptors for auth token
```

#### Styling
- **Tailwind CSS** - Utility-first CSS framework
- **Custom CSS** - `src/index.css`, `src/App.css`
- **Component Styles** - Shadcn/UI with custom theme

#### Configuration
- **vite.config.ts** - Vite configuration with React SWC
- **tsconfig.json** - TypeScript configuration
- **tailwind.config.ts** - Tailwind theme customization
- **package.json** - Dependencies & scripts

---

## 📊 Database & Models

### Database Schema

#### Users Table
```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  role ENUM('user', 'admin', 'tour_manager', 'hotel_manager'),
  phone VARCHAR(20),
  address TEXT,
  bio TEXT,
  avatar LONGTEXT,
  two_factor_enabled BOOLEAN DEFAULT false,
  two_factor_verified BOOLEAN DEFAULT false,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Tours Table
```sql
CREATE TABLE tours (
  id BIGINT PRIMARY KEY,
  title VARCHAR(255),
  destination VARCHAR(255),
  description LONGTEXT,
  price DECIMAL(10,2),
  original_price DECIMAL(10,2),
  duration INT,
  image LONGTEXT,
  rating DECIMAL(3,2),
  review_count INT,
  category VARCHAR(255),
  max_guests INT,
  highlights JSON,
  includes JSON,
  itinerary JSON,
  departure JSON,
  created_by BIGINT REFERENCES users(id),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Orders (Booking) Table
```sql
CREATE TABLE orders (
  id BIGINT PRIMARY KEY,
  user_id BIGINT REFERENCES users(id),
  order_code VARCHAR(255) UNIQUE,
  total_amount DECIMAL(10,2),
  status ENUM('pending', 'completed', 'cancelled'),
  payment_method VARCHAR(50),
  items JSON,
  notes TEXT,
  qr_code LONGTEXT,
  completed_at TIMESTAMP,
  checked_in_at TIMESTAMP,
  email_sent_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Booking Details Table
```sql
CREATE TABLE booking_details (
  id BIGINT PRIMARY KEY,
  order_id BIGINT REFERENCES orders(id),
  bookable_type VARCHAR(255),
  bookable_id BIGINT,
  quantity INT,
  price DECIMAL(10,2),
  booking_info JSON,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Hotels & Rooms Tables
```sql
CREATE TABLE hotels (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255),
  location VARCHAR(255),
  address VARCHAR(255),
  city VARCHAR(255),
  description LONGTEXT,
  rating DECIMAL(3,2),
  price DECIMAL(10,2),
  original_price DECIMAL(10,2),
  image LONGTEXT,
  images JSON,
  amenities JSON,
  check_in VARCHAR(10),
  check_out VARCHAR(10),
  cancellation TEXT,
  children TEXT,
  rooms_count INT,
  created_by BIGINT REFERENCES users(id),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE rooms (
  id BIGINT PRIMARY KEY,
  hotel_id BIGINT REFERENCES hotels(id),
  name VARCHAR(255),
  capacity INT,
  price DECIMAL(10,2),
  original_price DECIMAL(10,2),
  description LONGTEXT,
  images JSON,
  amenities JSON,
  available BOOLEAN,
  size VARCHAR(50),
  beds VARCHAR(50),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Payments Table
```sql
CREATE TABLE payments (
  id BIGINT PRIMARY KEY,
  order_id BIGINT REFERENCES orders(id),
  transaction_id VARCHAR(255),
  status ENUM('pending', 'success', 'failed'),
  amount DECIMAL(10,2),
  payment_method VARCHAR(50),
  request_id VARCHAR(255),
  response_data JSON,
  error_message TEXT,
  paid_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Other Tables
- **carts** - Giỏ hàng users
- **favorites** - Danh sách yêu thích (polymorphic)
- **tour_reviews** - Đánh giá tours
- **blogs** - Blog posts
- **blog_comments** - Bình luận blogs
- **contacts** - Form liên hệ
- **otp_codes** - 2FA OTP codes
- **purchase_history** - Lịch sử mua hàng

---

## 🔐 API Routes & Controllers

### Authentication Flow

```
1. User Registration (POST /api/register)
   ├─ Validate email unique
   ├─ Hash password
   └─ Create user + token

2. User Login (POST /api/login)
   ├─ Find user by email
   ├─ Verify password
   ├─ Check 2FA enabled?
   │  ├─ YES: Send OTP → Return requires_otp
   │  └─ NO: Return token
   └─ Create auth token

3. 2FA OTP Verification (POST /api/auth/verify-otp)
   ├─ Validate OTP code
   ├─ Check if expired
   └─ Return token if valid

4. Logout (POST /api/logout)
   └─ Delete all tokens
```

### Payment Flow

```
1. Add to Cart (POST /api/cart)
   ├─ Validate product exists
   ├─ Check quantity available
   └─ Add to cart

2. Create Order (POST /api/orders)
   ├─ Get cart items
   ├─ Calculate total
   ├─ Create Order record
   ├─ Create BookingDetails
   ├─ Generate QR code
   └─ Return order_id

3. Create Payment (POST /api/verify-payment)
   ├─ Validate order exists
   ├─ Create Payment record
   ├─ Call payment gateway
   │  └─ Mock: Always return success
   └─ Return payment status

4. Check Payment Status (POST /api/check-payment-status)
   ├─ Get latest payment
   ├─ Return status
   └─ If success: Mark order as completed
```

### Review & Comment Flow

```
1. Post Tour Review (POST /api/tours/{tourId}/reviews)
   ├─ Validate user has booked tour
   ├─ Create TourReview (is_approved = false by default)
   ├─ Update tour rating
   └─ Return review

2. Admin Approve Review (POST /admin/tour-reviews/{id}/approve)
   ├─ Set is_approved = true
   ├─ Update tour rating
   └─ Return success

3. Post Blog Comment (POST /api/blog-comments/{blogId})
   ├─ Create BlogComment (is_approved = false)
   └─ Return comment

4. Admin Approve Comment (POST /admin/blog-comments/approve-bulk)
   ├─ Batch update is_approved = true
   └─ Return success
```

---

## 👨‍💼 Admin Panel

### Admin Dashboard Routes

```
/admin/login                    - Login page (public)
/admin/dashboard                - Dashboard (protected)
/admin/users                    - Users management (admin only)
/admin/tours                    - Tours management (admin + tour_manager)
/admin/hotels                   - Hotels management (admin + hotel_manager)
/admin/hotels/:id/rooms         - Rooms management (admin + hotel_manager)
/admin/blogs                    - Blogs management (admin only)
/admin/blog-comments            - Comments moderation (admin only)
/admin/bookings                 - Bookings management (admin only)
/admin/payments                 - Payments management (admin only)
/admin/payments/statistics      - Payment statistics (admin only)
/admin/tour-reviews             - Tour reviews moderation (admin only)
/admin/contacts                 - Contact forms (admin only)
```

### Admin Features

| Feature | Mô Tả |
|---------|-------|
| **User Management** | Tạo, sửa, xóa users; gán roles |
| **Tour Management** | CRUD tours; quản lý itinerary, highlights, departure |
| **Hotel Management** | CRUD hotels; quản lý rooms; set amenities, prices |
| **Booking Management** | Xem bookings; update status; export PDF; refund |
| **Payment Management** | Xem payments; confirm/reject; statistics; export PDF |
| **Review Moderation** | Approve/reject tour reviews; bulk actions |
| **Comment Moderation** | Approve/reject blog comments; bulk actions |
| **Contact Management** | Reply to contact forms; send cancellation emails |
| **Blog Management** | CRUD blog posts; manage categories |

### Admin Middleware

- `auth` - User phải đăng nhập
- `admin` - User phải có role 'admin'
- `admin_or_manager` - User có role 'admin' hoặc 'tour_manager'/'hotel_manager'

---

## 🌟 Tính Năng Chính

### 1. Authentication & Security
- ✅ Đăng ký/Đăng nhập với email
- ✅ Two-Factor Authentication (2FA) với OTP
- ✅ JWT tokens (Sanctum)
- ✅ Password hashing (bcrypt)
- ✅ CSRF protection

### 2. Tours Management
- ✅ Danh sách tours với filtering
- ✅ Chi tiết tour (itinerary, highlights, includes, departure points)
- ✅ Tours có rating/review từ khách hàng
- ✅ Tìm kiếm nâng cao (giá, vị trí, rating)
- ✅ Thêm/xóa yêu thích

### 3. Hotels & Rooms
- ✅ Danh sách khách sạn với filtering
- ✅ Chi tiết khách sạn (amenities, check-in/check-out)
- ✅ Quản lý phòng (capacity, price, images)
- ✅ Available rooms check
- ✅ Yêu thích khách sạn

### 4. Shopping Cart & Checkout
- ✅ Giỏ hàng lưu trữ (tours + rooms)
- ✅ Tính toán tổng tiền tự động
- ✅ Lưu thông tin booking (dates, guests, special requests)
- ✅ Ước tính giá khi thanh toán

### 5. Payment System
- ✅ Tạo đơn hàng/booking
- ✅ Thanh toán online
- ✅ QR code thanh toán (JPAY, ngân hàng)
- ✅ Mock payment mode (APP_PAYMENT_MOCK)
- ✅ Payment status tracking
- ✅ Email confirmation với QR code

### 6. Booking Management
- ✅ Xem lịch sử bookings
- ✅ Hủy booking (có phí)
- ✅ Check-in bookings
- ✅ Download QR code & hóa đơn
- ✅ Billing details & order receipt

### 7. Reviews & Ratings
- ✅ Khách hàng đánh giá tours (1-5 sao)
- ✅ Admin duyệt reviews trước publish
- ✅ Cập nhật rating tour tự động
- ✅ Bình luận bài blog (moderated)

### 8. Blog & Content
- ✅ Blog posts about travel
- ✅ Blog comments (moderated)
- ✅ View count tracking
- ✅ Categories

### 9. User Profile
- ✅ Chỉnh sửa thông tin cá nhân
- ✅ Avatar upload
- ✅ Thay đổi mật khẩu
- ✅ Enable/disable 2FA
- ✅ Quản lý bookings

### 10. Recommendations
- ✅ Gợi ý tours/hotels dựa trên lịch sử
- ✅ Trending products
- ✅ Similar products (dựa trên category/location)

### 11. Admin Dashboard
- ✅ Overview statistics
- ✅ Recent bookings
- ✅ Revenue charts
- ✅ User management
- ✅ Content management
- ✅ Payment verification

### 12. Communication
- ✅ Contact form từ users
- ✅ Email notifications
- ✅ Admin reply to contacts
- ✅ Booking confirmation emails
- ✅ OTP via email

---

## 🚀 Setup & Installation

### Prerequisites
- **PHP** 8.2+
- **Node.js** 18+ (với pnpm 8.10+)
- **MySQL** 5.7+ hoặc **SQLite**
- **Composer** (PHP package manager)

### Backend Setup

```bash
cd client/backend

# 1. Cài dependencies PHP
composer install

# 2. Copy .env file
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Cấu hình database trong .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=travel
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Cấu hình email (Gmail SMTP)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=your-email@gmail.com
# MAIL_PASSWORD=your-app-password
# MAIL_FROM_ADDRESS=your-email@gmail.com

# 6. Chạy migrations
php artisan migrate

# 7. Seed database (tạo admin user)
php artisan db:seed --class=AdminSeeder

# 8. Khởi động server
php artisan serve
# Server: http://127.0.0.1:8000
```

### Frontend Setup

```bash
cd client/frontend

# 1. Cài dependencies Node
npm install
# hoặc
pnpm install

# 2. Tạo .env file (nếu cần)
cp .env.example .env

# 3. Khởi động dev server
npm run dev
# hoặc
pnpm dev
# Server: http://localhost:5173
```

### Environment Variables

**.env (Backend)**
```env
APP_NAME=TravelVN
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

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

# Mock payment (development)
APP_PAYMENT_MOCK=true
```

### Default Admin Account
- **Email:** `admin@example.com`
- **Password:** `password`

(Tạo bởi `AdminSeeder`)

---

## 📚 Project Documentation

### Key Files
- [SetupBuild.md](./SetupBuild.md) - Installation guide
- [USE_CASES.md](./USE_CASES.md) - Detailed use cases & features
- [client/backend/ARCHITECTURE.md](./client/backend/ARCHITECTURE.md) - Backend architecture

### API Documentation
API endpoints documented in:
- `routes/api.php` - Route definitions
- `app/Http/Controllers/Api/` - Controller docs

### Database
- Schema: `database/migrations/`
- Seeders: `database/seeders/`
- Factories: `database/factories/`

---

## 🛠️ Development Commands

### Backend
```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate
php artisan migrate:rollback

# Seed database
php artisan db:seed
php artisan db:seed --class=AdminSeeder

# Artisan tinker (REPL)
php artisan tinker

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Frontend
```bash
# Start dev server
npm run dev
pnpm dev

# Build for production
npm run build
pnpm build

# Preview production build
npm run preview
pnpm preview

# Lint code
npm run lint
pnpm lint
```

---

## 📝 Project Status

| Component | Status |
|-----------|--------|
| Backend API | ✅ Complete |
| Frontend UI | ✅ Complete |
| Admin Panel | ✅ Complete |
| Authentication | ✅ Complete (with 2FA) |
| Payment System | ✅ Complete (Mock mode) |
| Booking System | ✅ Complete |
| Review System | ✅ Complete |
| Database | ✅ Complete |

---

## 🎓 Architecture Highlights

### Best Practices Used
- ✅ MVC Architecture
- ✅ RESTful API design
- ✅ Middleware-based authentication
- ✅ Polymorphic relationships (Favorites)
- ✅ Service layer pattern (OtpService)
- ✅ Component-based UI
- ✅ Separation of concerns
- ✅ Environment-based configuration

### Scalability Features
- ✅ Database migrations for versioning
- ✅ API token-based auth (stateless)
- ✅ JSON storage for complex data
- ✅ Lazy loading with React Query
- ✅ Modular component structure

---

## 📞 Support & Contact

For issues or questions about the project:
1. Check `USE_CASES.md` for detailed feature documentation
2. Review `ARCHITECTURE.md` for system design
3. Check API controllers for implementation details

---

**Last Updated:** 14/12/2025  
**Version:** 1.0.0
