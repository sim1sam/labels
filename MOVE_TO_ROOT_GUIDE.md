# 🚀 Move Laravel from Subdirectory to Root

## Current Setup:
- **Location:** `/labels/` subdirectory
- **URL:** `http://wisedynamic.in/labels`
- **Target:** Root directory
- **New URL:** `http://wisedynamic.in`

## 📋 **Step-by-Step Migration:**

### **1. Backup Current Setup**
```bash
# Create backup
cp -r /path/to/current/labels /path/to/backup/labels_backup
```

### **2. Move Laravel Files to Root**
```bash
# Move all Laravel files from /labels/ to root
mv /path/to/labels/* /path/to/root/
mv /path/to/labels/.* /path/to/root/ 2>/dev/null || true

# Remove empty labels directory
rmdir /path/to/labels
```

### **3. Update .env Configuration**
```env
APP_NAME=Labels
APP_ENV=production
APP_KEY=base64:LBKL5RXpCgTM+TFV9An1IimmWFcivIbsuciD28ovj98=
APP_DEBUG=false
APP_URL=http://wisedynamic.in

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u301769681_labels
DB_USERNAME=u301769681_labels
DB_PASSWORD=Dyn@m!c2019

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

### **4. Update Web Server Configuration**

#### **Apache Configuration:**
```apache
<VirtualHost *:80>
    ServerName wisedynamic.in
    ServerAlias www.wisedynamic.in
    DocumentRoot /path/to/root/public
    
    <Directory /path/to/root/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/wisedynamic_error.log
    CustomLog ${APACHE_LOG_DIR}/wisedynamic_access.log combined
</VirtualHost>
```

#### **Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name wisedynamic.in www.wisedynamic.in;
    root /path/to/root/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### **5. Set Proper Permissions**
```bash
# Set ownership
chown -R www-data:www-data /path/to/root

# Set permissions
chmod -R 755 /path/to/root
chmod -R 775 /path/to/root/storage
chmod -R 775 /path/to/root/bootstrap/cache
```

### **6. Clear and Optimize Laravel**
```bash
cd /path/to/root

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate optimized autoloader
composer install --optimize-autoloader --no-dev

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **7. Test the Application**
```bash
# Test Laravel
php artisan --version

# Test database connection
php artisan migrate:status

# Test web access
curl -I http://wisedynamic.in
```

### **8. Update DNS (if needed)**
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

## 🔧 **Troubleshooting:**

### **Common Issues:**

#### **1. 404 Not Found**
- **Cause:** Document root not pointing to `/public`
- **Fix:** Update web server config to point to `/public` folder

#### **2. 500 Internal Server Error**
- **Cause:** File permissions or .env issues
- **Fix:** Check permissions and .env syntax

#### **3. CSS/JS Not Loading**
- **Cause:** Asset paths not updated
- **Fix:** Run `php artisan optimize` and clear browser cache

#### **4. Database Connection Error**
- **Cause:** .env not updated or database not accessible
- **Fix:** Verify database credentials and connection

### **Quick Fix Commands:**
```bash
# Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx

# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
```

## ✅ **Verification Checklist:**

- [ ] Files moved to root directory
- [ ] .env updated with correct APP_URL
- [ ] Web server configured to point to `/public`
- [ ] File permissions set correctly
- [ ] Laravel caches cleared and optimized
- [ ] Database connection working
- [ ] Website accessible at `http://wisedynamic.in`
- [ ] All functionality working (login, profile, etc.)

## 🎯 **Expected Result:**
- **Old URL:** `http://wisedynamic.in/labels`
- **New URL:** `http://wisedynamic.in`
- **Better Performance:** No subdirectory overhead
- **Cleaner URLs:** No `/labels/` in paths
- **Easier Maintenance:** Standard Laravel deployment

