# 🛠️ Useful Commands - Admin Panel

## 🚀 Setup & Installation

```bash
# Vào thư mục backend
cd client/backend

# Cài đặt dependencies
composer install

# Tạo .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Chạy migration
php artisan migrate

# Seed database
php artisan db:seed --class=AdminSeeder

# Khởi động server
php artisan serve
```

---

## 📋 Database Commands

### Migration
```bash
# Chạy migration
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Refresh (xóa & tạo lại)
php artisan migrate:refresh

# Refresh + seed
php artisan migrate:refresh --seed

# Kiểm tra status
php artisan migrate:status

# Chạy migration cụ thể
php artisan migrate --path=database/migrations/2025_11_12_000000_add_role_to_users_table.php
```

### Seeding
```bash
# Seed specific seeder
php artisan db:seed --class=AdminSeeder

# Seed tất cả
php artisan db:seed

# Seed lại mà không rollback
php artisan db:seed --class=AdminSeeder
```

---

## 🔐 User Management (Tinker)

```bash
# Vào interactive shell
php artisan tinker

# Xem tất cả users
User::all()

# Tìm user theo email
User::where('email', 'admin@example.com')->first()

# Tạo user mới
User::create([
    'name' => 'New User',
    'email' => 'newuser@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
])

# Cập nhật user
$user = User::find(1);
$user->update(['role' => 'admin']);

# Đổi password
$user = User::find(1);
$user->password = Hash::make('newpassword');
$user->save();

# Xóa user
User::find(1)->delete();

# Xem access tokens
$user->tokens;

# Xóa tất cả tokens
$user->tokens()->delete();
```

---

## 📊 Model Queries

```bash
# Vào tinker
php artisan tinker

# USER
User::count()                    # Tổng users
User::where('role', 'admin')->get()
User::latest()->limit(5)->get()
User::where('email_verified_at', '!=', null)->count()

# TOUR
Tour::count()                    # Tổng tours
Tour::where('price', '>', 1000000)->get()
Tour::orderBy('price', 'desc')->get()
Tour::where('rating', '>=', 4)->get()

# BLOG
Blog::count()                    # Tổng blogs
Blog::where('published_at', '!=', null)->get()
Blog::where('category', 'Travel')->get()
Blog::orderBy('views', 'desc')->get()
Blog::where('published_at', '<', now())->get()
```

---

## 🧹 Cache & Config

```bash
# Clear config cache
php artisan config:clear

# Clear app cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear all cache
php artisan optimize:clear
```

---

## 🐛 Debugging & Logs

```bash
# Xem logs
tail -f storage/logs/laravel.log

# Xóa logs
echo "" > storage/logs/laravel.log

# Tìm error trong logs
grep -i error storage/logs/laravel.log

# Tìm specific message
grep "some text" storage/logs/laravel.log
```

---

## 🧪 Testing

```bash
# Chạy test
php artisan test

# Chạy test cụ thể
php artisan test tests/Feature/Admin

# Test với output chi tiết
php artisan test --verbose

# Test và generate coverage
php artisan test --coverage
```

---

## 📦 Dependency Management

```bash
# Update composer
composer update

# Cài package
composer require package/name

# Remove package
composer remove package/name

# Dump autoload
composer dump-autoload

# Validate composer.json
composer validate

# Check security vulnerabilities
composer audit
```

---

## 🌐 Route Management

```bash
# Danh sách tất cả routes
php artisan route:list

# Routes trong group cụ thể
php artisan route:list --path=admin

# Routes theo method
php artisan route:list --method=POST

# Export routes
php artisan route:list -v
```

---

## 📝 File Management

```bash
# Xóa storage link
rm storage/app/public

# Tạo lại storage link
php artisan storage:link

# Publish vendor files
php artisan vendor:publish

# Vendor publish cụ thể
php artisan vendor:publish --provider="Package\ServiceProvider"
```

---

## 🔧 Development Commands

```bash
# Watch & rebuild CSS/JS
npm run dev

# Build for production
npm run build

# Development server
npm run dev

# Serve frontend
npm run dev -- --host
```

---

## ⚙️ Server Management

```bash
# Start Laravel development server
php artisan serve

# Serve tại port cụ thể
php artisan serve --port=8001

# Serve tại host cụ thể
php artisan serve --host=0.0.0.0

# Serve tại host:port
php artisan serve --host=0.0.0.0 --port=8000

# Background process
php artisan serve &

# Queue listener
php artisan queue:listen

# Background worker
php artisan queue:work
```

---

## 🔑 Key Generation

```bash
# Generate app key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Generate API keys
php artisan tinker
> User::find(1)->createToken('token-name')->plainTextToken;
```

---

## 📂 Make Commands (Generate Files)

```bash
# Generate migration
php artisan make:migration create_table_name --create=table_name

# Generate model
php artisan make:model ModelName

# Generate controller
php artisan make:controller ControllerName

# Generate middleware
php artisan make:middleware MiddlewareName

# Generate request (form validation)
php artisan make:request StoreUserRequest

# Generate seeder
php artisan make:seeder UserSeeder

# Generate factory
php artisan make:factory UserFactory --model=User
```

---

## 🚨 Error Handling

```bash
# Debug mode
APP_DEBUG=true

# Error log
storage/logs/laravel.log

# Check config
php artisan config:show database.default

# Test database connection
php artisan db:show

# Show tables
php artisan db:table users
```

---

## 🏃 Performance

```bash
# Optimize app
php artisan optimize

# Clear optimization
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Route caching
php artisan route:cache

# View caching
php artisan view:cache

# Event caching
php artisan event:cache
```

---

## 📚 Useful Aliases

Thêm vào `.bashrc` hoặc `.zshrc`:

```bash
alias art="php artisan"
alias tinker="php artisan tinker"
alias migrate="php artisan migrate"
alias seed="php artisan db:seed"
alias serve="php artisan serve"
alias cc="php artisan config:clear"
```

Sử dụng:
```bash
art migrate
art seed --class=AdminSeeder
art serve
```

---

## 🔍 Frequently Used

```bash
# 90% của commands bạn cần:
php artisan migrate              # Migration
php artisan db:seed              # Seed
php artisan serve                # Start server
php artisan tinker               # Interactive shell
php artisan route:list           # View routes
php artisan config:clear         # Clear cache
php artisan cache:clear          # Clear cache
php artisan test                 # Run tests
php artisan optimize:clear       # Clear optimization
```

---

**💡 Tip**: Tạo một script file `run.sh` để automation
```bash
#!/bin/bash
php artisan migrate:refresh --seed
php artisan optimize:clear
php artisan serve
```

Chạy với: `bash run.sh`
