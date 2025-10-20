# 🔧 Server Configuration Fixes

## Current Issues Identified:

### 1. **Domain Mismatch**
- Environment: `wisedynamic.in/labels`
- Expected: `westside.in`
- **Fix:** Update APP_URL to match your actual domain

### 2. **Subdirectory Deployment Issues**
- Current: `http://wisedynamic.in/labels`
- **Problem:** Laravel in subdirectory requires special configuration

## 🚀 **Immediate Fixes Required:**

### **Option 1: Fix Subdirectory Configuration**

Update your `.env` file:
```env
APP_NAME=Labels
APP_ENV=production
APP_KEY=base64:LBKL5RXpCgTM+TFV9An1IimmWFcivIbsuciD28ovj98=
APP_DEBUG=false
APP_URL=http://wisedynamic.in/labels

# Keep all other settings the same...
```

**Web Server Configuration for Subdirectory:**
```apache
# Apache .htaccess in /labels/ directory
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Angular and other front-end routes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### **Option 2: Move to Root Domain (Recommended)**

Update your `.env` file:
```env
APP_NAME=Labels
APP_ENV=production
APP_KEY=base64:LBKL5RXpCgTM+TFV9An1IimmWFcivIbsuciD28ovj98=
APP_DEBUG=false
APP_URL=http://wisedynamic.in

# Keep all other settings the same...
```

**Move Laravel files to root directory:**
- Move all Laravel files from `/labels/` to root `/`
- Update document root to point to `/public` folder

## 🔧 **Required Server Commands:**

### **1. Clear All Caches:**
```bash
cd /path/to/your/laravel/project
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### **2. Set Proper Permissions:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### **3. Database Migration:**
```bash
php artisan migrate --force
php artisan db:seed --force
```

## 🌐 **DNS Configuration:**

### **For wisedynamic.in:**
```
Type: A
Name: @
Value: YOUR_SERVER_IP
TTL: 3600

Type: A
Name: www
Value: YOUR_SERVER_IP
TTL: 3600
```

### **For westside.in (if different):**
```
Type: A
Name: @
Value: YOUR_SERVER_IP
TTL: 3600

Type: A
Name: www
Value: YOUR_SERVER_IP
TTL: 3600
```

## 🔍 **Troubleshooting Steps:**

### **1. Test Current Setup:**
```bash
# Test if server responds
curl -I http://wisedynamic.in/labels
curl -I http://YOUR_SERVER_IP/labels
```

### **2. Check Laravel Logs:**
```bash
tail -f storage/logs/laravel.log
```

### **3. Verify Database Connection:**
```bash
php artisan tinker
# Then run: DB::connection()->getPdo();
```

### **4. Check File Permissions:**
```bash
ls -la storage/
ls -la bootstrap/cache/
```

## ⚠️ **Common Issues & Solutions:**

### **Issue 1: 404 Not Found**
- **Cause:** Web server not configured for Laravel
- **Fix:** Update document root to `/public` folder

### **Issue 2: 500 Internal Server Error**
- **Cause:** File permissions or .env issues
- **Fix:** Set proper permissions and check .env syntax

### **Issue 3: Database Connection Error**
- **Cause:** Wrong database credentials
- **Fix:** Verify database exists and credentials are correct

### **Issue 4: CSS/JS Not Loading**
- **Cause:** Asset paths not configured for subdirectory
- **Fix:** Run `php artisan asset:publish` or move to root

## 🎯 **Recommended Action Plan:**

1. **Choose deployment method** (subdirectory vs root)
2. **Update APP_URL** in .env file
3. **Configure web server** properly
4. **Set file permissions**
5. **Clear all caches**
6. **Test the application**
7. **Configure DNS** if using different domain

## 📞 **Quick Test Commands:**

```bash
# Test Laravel installation
php artisan --version

# Test database connection
php artisan migrate:status

# Test web server
curl -I http://wisedynamic.in/labels

# Check error logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
```

