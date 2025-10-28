#!/bin/bash

# ===========================================
# Labels Courier Management - Deployment Script
# ===========================================

echo "🚀 Starting deployment process..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_status "Laravel project directory confirmed"

# Step 1: Install/Update Dependencies
echo ""
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
if [ $? -eq 0 ]; then
    print_status "Composer dependencies installed"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Step 2: Install Node.js dependencies and build assets
echo ""
echo "🎨 Building frontend assets..."
npm install
if [ $? -eq 0 ]; then
    print_status "NPM dependencies installed"
else
    print_warning "NPM install failed, continuing..."
fi

npm run build
if [ $? -eq 0 ]; then
    print_status "Frontend assets built"
else
    print_warning "Asset build failed, continuing..."
fi

# Step 3: Generate Application Key (if not exists)
echo ""
echo "🔑 Checking application key..."
if [ -z "$(grep 'APP_KEY=' .env 2>/dev/null | grep -v 'APP_KEY=$')" ]; then
    php artisan key:generate
    print_status "Application key generated"
else
    print_status "Application key already exists"
fi

# Step 4: Run Database Migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_status "Database migrations completed"
else
    print_error "Database migrations failed"
    exit 1
fi

# Step 5: Seed Database (if needed)
echo ""
echo "🌱 Seeding database..."
php artisan db:seed --force
if [ $? -eq 0 ]; then
    print_status "Database seeded successfully"
else
    print_warning "Database seeding failed, continuing..."
fi

# Step 6: Set Permissions
echo ""
echo "🔐 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
if [ -d "public/uploads" ]; then
    chmod -R 755 public/uploads
fi

# Try to set ownership (may require sudo)
if command -v chown &> /dev/null; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || print_warning "Could not set file ownership (may need sudo)"
    if [ -d "public/uploads" ]; then
        chown -R www-data:www-data public/uploads 2>/dev/null || print_warning "Could not set uploads ownership (may need sudo)"
    fi
fi

print_status "File permissions set"

# Step 7: Clear and Optimize Caches
echo ""
echo "⚡ Optimizing application..."

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

if [ $? -eq 0 ]; then
    print_status "Application optimized"
else
    print_warning "Optimization had some issues, continuing..."
fi

# Step 8: Test Steadfast API Connection
echo ""
echo "🔗 Testing Steadfast API connection..."
php artisan tinker --execute="
\$service = new App\Services\SteadfastApiService();
\$result = \$service->testConnection();
if (\$result['success']) {
    echo 'Steadfast API: Connected successfully';
} else {
    echo 'Steadfast API: Connection failed - ' . \$result['message'];
}
" 2>/dev/null || print_warning "Could not test Steadfast API connection"

# Step 9: Final Checks
echo ""
echo "🔍 Running final checks..."

# Check if .env file exists
if [ -f ".env" ]; then
    print_status ".env file exists"
else
    print_error ".env file not found! Please create it from production.env.template"
fi

# Check if storage is writable
if [ -w "storage" ]; then
    print_status "Storage directory is writable"
else
    print_error "Storage directory is not writable"
fi

# Check if cache directory is writable
if [ -w "bootstrap/cache" ]; then
    print_status "Cache directory is writable"
else
    print_error "Cache directory is not writable"
fi

echo ""
echo "🎉 Deployment completed!"
echo ""
echo "📋 Next steps:"
echo "1. Verify your .env file has correct production settings"
echo "2. Test the website functionality"
echo "3. Check Steadfast API connection"
echo "4. Monitor application logs"
echo ""
echo "🔧 Useful commands:"
echo "- View logs: tail -f storage/logs/laravel.log"
echo "- Clear cache: php artisan cache:clear"
echo "- Test API: php artisan tinker"
echo ""
echo "📚 Documentation:"
echo "- Deployment Guide: DEPLOYMENT_GUIDE.md"
echo "- Checklist: DEPLOYMENT_CHECKLIST_PRODUCTION.md"
echo "- Steadfast Setup: STEADFAST_SETUP_COMPLETE.md"
