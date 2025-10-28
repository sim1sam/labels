#!/bin/bash

# ===========================================
# Labels Courier Management - wisedynamic.in Deployment
# ===========================================

echo "🚀 Starting deployment to wisedynamic.in/labels/..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_status "Laravel project directory confirmed"

# Step 1: Prepare for subdirectory deployment
echo ""
print_info "Preparing for wisedynamic.in/labels/ deployment..."

# Update .htaccess for subdirectory
if [ -f "public/.htaccess.subdirectory" ]; then
    cp public/.htaccess.subdirectory public/.htaccess
    print_status "Updated .htaccess for subdirectory deployment"
else
    print_warning ".htaccess.subdirectory not found, using default"
fi

# Step 2: Install/Update Dependencies
echo ""
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
if [ $? -eq 0 ]; then
    print_status "Composer dependencies installed"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Step 3: Install Node.js dependencies and build assets
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

# Step 4: Generate Application Key (if not exists)
echo ""
echo "🔑 Checking application key..."
if [ -z "$(grep 'APP_KEY=' .env 2>/dev/null | grep -v 'APP_KEY=$')" ]; then
    php artisan key:generate
    print_status "Application key generated"
else
    print_status "Application key already exists"
fi

# Step 5: Update environment for wisedynamic.in
echo ""
print_info "Updating environment for wisedynamic.in..."

# Check if .env exists
if [ ! -f ".env" ]; then
    if [ -f "production.env.template" ]; then
        cp production.env.template .env
        print_status "Created .env from template"
    else
        print_error ".env file not found and no template available"
        exit 1
    fi
fi

# Update APP_URL for wisedynamic.in
sed -i 's|APP_URL=.*|APP_URL=https://wisedynamic.in/labels|g' .env
print_status "Updated APP_URL for wisedynamic.in"

# Step 6: Run Database Migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_status "Database migrations completed"
else
    print_error "Database migrations failed"
    exit 1
fi

# Step 7: Seed Database (if needed)
echo ""
echo "🌱 Seeding database..."
php artisan db:seed --force
if [ $? -eq 0 ]; then
    print_status "Database seeded successfully"
else
    print_warning "Database seeding failed, continuing..."
fi

# Step 8: Set Permissions
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

# Step 9: Clear and Optimize Caches
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

# Step 10: Test Steadfast API Connection
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

# Step 11: Final Checks
echo ""
echo "🔍 Running final checks..."

# Check if .env file exists
if [ -f ".env" ]; then
    print_status ".env file exists"
    
    # Check if APP_URL is set correctly
    if grep -q "wisedynamic.in/labels" .env; then
        print_status "APP_URL configured for wisedynamic.in"
    else
        print_warning "APP_URL may not be set correctly"
    fi
else
    print_error ".env file not found!"
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

# Check if .htaccess is configured for subdirectory
if grep -q "RewriteBase /labels/" public/.htaccess; then
    print_status ".htaccess configured for subdirectory"
else
    print_warning ".htaccess may not be configured for subdirectory"
fi

echo ""
echo "🎉 Deployment to wisedynamic.in/labels/ completed!"
echo ""
echo "📋 Next steps:"
echo "1. Upload files to: /var/www/html/wisedynamic.in/labels/"
echo "2. Configure web server for subdirectory routing"
echo "3. Set up SSL certificate for wisedynamic.in"
echo "4. Test the application at: https://wisedynamic.in/labels/"
echo "5. Verify Steadfast API connection"
echo ""
echo "🔧 Useful commands:"
echo "- View logs: tail -f storage/logs/laravel.log"
echo "- Clear cache: php artisan cache:clear"
echo "- Test API: php artisan tinker"
echo ""
echo "📚 Documentation:"
echo "- Deployment Guide: WISEDYNAMIC_DEPLOYMENT_GUIDE.md"
echo "- Checklist: DEPLOYMENT_CHECKLIST_PRODUCTION.md"
echo "- Steadfast Setup: STEADFAST_SETUP_COMPLETE.md"
echo ""
echo "🌐 Your application will be available at:"
echo "   https://wisedynamic.in/labels/"
echo "   https://wisedynamic.in/labels/admin/login"
echo "   https://wisedynamic.in/labels/merchant/login"
