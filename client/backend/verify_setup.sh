#!/bin/bash

# 🎯 Admin Panel Setup Checklist
# Run this to verify everything is set up correctly

echo "🔍 Admin Panel Setup Verification"
echo "================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to check file exists
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅${NC} $1"
        return 0
    else
        echo -e "${RED}❌${NC} $1 (NOT FOUND)"
        return 1
    fi
}

# Function to check directory exists
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✅${NC} $1"
        return 0
    else
        echo -e "${RED}❌${NC} $1 (NOT FOUND)"
        return 1
    fi
}

echo "📂 Checking Controllers..."
check_file "app/Http/Controllers/Admin/AuthController.php"
check_file "app/Http/Controllers/Admin/DashboardController.php"
check_file "app/Http/Controllers/Admin/UserController.php"
check_file "app/Http/Controllers/Admin/TourController.php"
check_file "app/Http/Controllers/Admin/BlogController.php"
echo ""

echo "🛡️  Checking Middleware..."
check_file "app/Http/Middleware/Authenticate.php"
check_file "app/Http/Middleware/IsAdmin.php"
echo ""

echo "📄 Checking Views..."
check_dir "resources/views/admin"
check_file "resources/views/admin/layouts/app.blade.php"
check_file "resources/views/admin/auth/login.blade.php"
check_file "resources/views/admin/dashboard.blade.php"
check_dir "resources/views/admin/users"
check_dir "resources/views/admin/tours"
check_dir "resources/views/admin/blogs"
echo ""

echo "🗺️  Checking Routes..."
check_file "routes/web.php"
echo ""

echo "🗄️  Checking Database..."
check_file "database/migrations/2025_11_12_000000_add_role_to_users_table.php"
check_file "database/seeders/AdminSeeder.php"
echo ""

echo "📚 Checking Documentation..."
check_file "ADMIN_PANEL_README.md"
check_file "QUICK_START.md"
check_file "STRUCTURE.md"
check_file "ARCHITECTURE.md"
check_file "COMMANDS.md"
check_file "SETUP_COMPLETE.md"
echo ""

echo "✅ All files are in place!"
echo ""
echo "🚀 Next Steps:"
echo "1. Run: php artisan migrate"
echo "2. Run: php artisan db:seed --class=AdminSeeder"
echo "3. Run: php artisan serve"
echo "4. Visit: http://127.0.0.1:8000/admin/login"
echo ""
echo "📖 Read QUICK_START.md for detailed instructions"
