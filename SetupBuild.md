# 📋 Setup Project TravelVN từ GitHub

## 🚀 Bước 1: Clone Project

```bash
git clone https://github.com/LTH2003/travel-web.git
cd travel-web
```

---

## 📦 Bước 2: Setup Backend (Laravel)

### 2.1 Di chuyển vào thư mục backend
```bash
cd client/backend
```

### 2.2 Cài đặt dependencies PHP
```bash
composer install
```

### 2.3 Tạo file .env
```bash
cp .env.example .env
```

### 2.4 Generate App Key
```bash
php artisan key:generate
```

### 2.5 Cấu hình Database
Mở file `.env` và cập nhật:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel
DB_USERNAME=root
DB_PASSWORD=
```

### 2.6 Cấu hình Email (Gmail SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=TravelVN
```

> **Lưu ý**: Lấy app password từ Google Account > Security > App passwords

### 2.7 Chạy Migration
```bash
php artisan migrate
```

### 2.8 Seed Database (tạo dữ liệu test)
```bash
php artisan db:seed --class=AdminSeeder
```

### 2.9 Khởi động Laravel Server
```bash
php artisan serve
```

Server chạy ở: `http://127.0.0.1:8000`

---

## 🎨 Bước 3: Setup Frontend (React + Vite)

### 3.1 Di chuyển vào thư mục frontend
```bash
cd ../frontend
# hoặc từ backend: cd ../frontend
```

### 3.2 Cài đặt dependencies Node
```bash
npm install
# hoặc nếu dùng pnpm
pnpm install
```

### 3.3 Tạo file .env (nếu cần)
```bash
# Thường không cần, nhưng nếu có thể tạo:
cp .env.example .env
```

### 3.4 Khởi động Dev Server
```bash
npm run dev
```

Frontend chạy ở: `http://localhost:5173`

---

## 🔧 Bước 4: Các Lệnh Quan Trọng

### 4.1 Backend Commands (Laravel)

#### Database
```bash
# Chạy migration
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Refresh (xóa & tạo lại)
php artisan migrate:refresh

# Refresh + seed data
php artisan migrate:refresh --seed

# Kiểm tra status migration
php artisan migrate:status

# Chạy migration cụ thể
php artisan migrate --path=database/migrations/2025_11_29_000000_add_performance_indexes.php
```

#### Seeding
```bash
# Seed database
php artisan db:seed

# Seed class cụ thể
php artisan db:seed --class=AdminSeeder

# Seed sau khi refresh
php artisan migrate:refresh --seed
```

#### Cache
```bash
# Xóa cache
php artisan cache:clear

# Xóa semall cache
php artisan optimize:clear
```

#### Testing
```bash
# Chạy unit tests
php artisan test

# Chạy tests cụ thể
php artisan test --filter=LoginTest
```

#### Development Server
```bash
# Khởi động dev server
php artisan serve

# Khởi động trên host & port cụ thể
php artisan serve --host=0.0.0.0 --port=8000
```

### 4.2 Frontend Commands (React + Vite)

#### Development
```bash
# Khởi động dev server
npm run dev

# Khởi động trên port cụ thể
npm run dev -- --port 5173
```

#### Production Build
```bash
# Build production (tối ưu hóa)
npm run build

# Preview build trước khi deploy
npm run preview
```

#### Code Quality
```bash
# Lint code (ESLint)
npm run lint

# Fix linting issues (nếu cấu hình)
npm run lint -- --fix

# Format code (Prettier)
npm run format

# Format check
npm run format:check
```

### 4.3 Build & Deploy

#### Full Build Process
```bash
# ===== Backend =====
cd client/backend

# Xóa cache
php artisan optimize:clear

# Cài đặt dependencies
composer install

# Chạy migrations
php artisan migrate

# Seed database (nếu cần)
php artisan db:seed --class=AdminSeeder

# ===== Frontend =====
cd ../frontend

# Xóa node_modules & reinstall (nếu gặp lỗi)
rm -rf node_modules package-lock.json
npm install

# Build production
npm run build

# Kết quả: tạo thư mục 'dist'
```

#### Start Development Environment
```bash
# Terminal 1: Backend (tại client/backend)
php artisan serve

# Terminal 2: Frontend (tại client/frontend)
npm run dev
```

#### Start Production Environment
```bash
# Backend
php artisan serve --host=0.0.0.0 --port=8000

# Frontend (sau khi build)
# Copy thư mục 'dist' vào server hoặc:
npm run preview
```

---

## ✅ Kiểm Tra Setup

### Backend
```bash
# Kiểm tra kết nối database
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
```

### Frontend
Truy cập: `http://localhost:5173`

Kiểm tra Console (F12) không có error

---

## 🔐 Tài Khoản Test

Sau khi seed database, bạn có thể dùng:

```
Email: admin@example.com
Password: 123456 (hoặc check trong seeder)
```

---

## 📝 Lưu Ý Quan Trọng

1. **MySQL phải chạy** trước khi khởi động backend
2. **Node.js phải v16+** để chạy frontend
3. **Gmail app password**: Cần bật 2-Step Verification trong Google Account
4. **Port 8000 & 5173** phải available

---

## 🚨 Troubleshooting

### Lỗi "Connection refused"
```bash
# Kiểm tra MySQL chạy chưa
# Windows: Chạy XAMPP MySQL hoặc MySQL Service
# Mac/Linux: brew services start mysql
```

### Lỗi "No such file or directory: artisan"
```bash
# Đảm bảo ở trong thư mục backend
cd client/backend
```

### Lỗi Node modules
```bash
# Xóa & cài lại
rm -rf node_modules package-lock.json
npm install
```

### Lỗi CORS
Kiểm tra backend `.env`:
```env
APP_URL=http://localhost:8000
```

---

## 📚 Thêm Thông Tin

- Laravel Docs: https://laravel.com/docs
- React Docs: https://react.dev
- Vite Docs: https://vitejs.dev

---

**Last Updated**: November 30, 2025
