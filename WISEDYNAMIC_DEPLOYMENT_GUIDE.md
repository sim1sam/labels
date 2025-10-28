# 🚀 Deployment Guide for wisedynamic.in/labels/

## 📋 **Domain Configuration: wisedynamic.in/labels/**

### **1. Server Setup for Subdirectory Deployment**

#### **Directory Structure:**
```
/var/www/html/wisedynamic.in/
├── labels/                    # Your Laravel project
│   ├── public/               # Document root for labels subdirectory
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── index.html                 # Main website (if exists)
```

### **2. Web Server Configuration**

#### **Apache Virtual Host (.htaccess in labels/public/):**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle subdirectory routing
    RewriteBase /labels/

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### **Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name wisedynamic.in www.wisedynamic.in;
    root /var/www/html/wisedynamic.in;
    index index.html index.php;

    # Main website
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Labels subdirectory
    location /labels {
        alias /var/www/html/wisedynamic.in/labels/public;
        try_files $uri $uri/ @labels;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    location @labels {
        rewrite /labels/(.*)$ /labels/index.php?/$1 last;
    }
}
```

### **3. Environment Configuration**

#### **Create .env file for wisedynamic.in:**
```env
# ===========================================
# WISEDYNAMIC.IN PRODUCTION CONFIGURATION
# ===========================================

# Application Settings
APP_NAME="Labels Courier Management"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://wisedynamic.in/labels

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wisedynamic_labels_db
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Cache & Session
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@wisedynamic.in
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@wisedynamic.in"
MAIL_FROM_NAME="${APP_NAME}"

# ===========================================
# STEADFAST API CONFIGURATION
# ===========================================

# Steadfast API Settings
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
STEADFAST_TIMEOUT=30

# Courier API Settings
COURIER_API_ENABLED=true
COURIER_MOCK_IN_LOCAL=false

# ===========================================
# SECURITY SETTINGS
# ===========================================

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Force HTTPS
FORCE_HTTPS=true

# ===========================================
# SUBDIRECTORY SETTINGS
# ===========================================

# Subdirectory path
SUBDIRECTORY_PATH=/labels
ASSET_URL=https://wisedynamic.in/labels
```

### **4. Laravel Configuration Updates**

#### **Update config/app.php for subdirectory:**
```php
// Add this to config/app.php
'asset_url' => env('ASSET_URL', null),
'url' => env('APP_URL', 'https://wisedynamic.in/labels'),
```

#### **Update public/index.php for subdirectory:**
```php
// Add this at the top of public/index.php
$subdirectory = '/labels';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

// Remove subdirectory from REQUEST_URI
if (strpos($requestUri, $subdirectory) === 0) {
    $_SERVER['REQUEST_URI'] = substr($requestUri, strlen($subdirectory));
}

// Update SCRIPT_NAME
$_SERVER['SCRIPT_NAME'] = $subdirectory . '/index.php';
```

### **5. Deployment Steps**

#### **Step 1: Upload Files**
```bash
# Upload to server
scp -r * user@wisedynamic.in:/var/www/html/wisedynamic.in/labels/
```

#### **Step 2: Set Permissions**
```bash
# SSH into server
ssh user@wisedynamic.in

# Navigate to project
cd /var/www/html/wisedynamic.in/labels

# Set permissions
chmod -R 755 storage bootstrap/cache public
chown -R www-data:www-data storage bootstrap/cache public
```

#### **Step 3: Install Dependencies**
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
npm run build
```

#### **Step 4: Database Setup**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE wisedynamic_labels_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force
```

#### **Step 5: Generate Application Key**
```bash
# Generate application key
php artisan key:generate
```

#### **Step 6: Optimize Application**
```bash
# Clear and optimize caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **6. Testing the Deployment**

#### **Test URLs:**
- **Main Application:** https://wisedynamic.in/labels/
- **Admin Login:** https://wisedynamic.in/labels/admin/login
- **Merchant Login:** https://wisedynamic.in/labels/merchant/login
- **API Test:** https://wisedynamic.in/labels/api/test-connection

#### **Test Steadfast API:**
```bash
# SSH into server
ssh user@wisedynamic.in
cd /var/www/html/wisedynamic.in/labels

# Test API connection
php artisan tinker
>>> $service = new App\Services\SteadfastApiService();
>>> $result = $service->testConnection();
>>> dd($result);
```

### **7. SSL Certificate Setup**

#### **Using Let's Encrypt:**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d wisedynamic.in -d www.wisedynamic.in
```

### **8. Troubleshooting**

#### **Common Issues:**

1. **404 Errors:**
   - Check .htaccess file in public directory
   - Verify Apache mod_rewrite is enabled
   - Check RewriteBase is set to /labels/

2. **Asset Loading Issues:**
   - Check ASSET_URL in .env
   - Verify public directory permissions
   - Clear view cache: `php artisan view:clear`

3. **Database Connection:**
   - Verify database credentials in .env
   - Check database exists and is accessible
   - Test connection: `php artisan tinker`

4. **Steadfast API Issues:**
   - Check API credentials in database
   - Verify STEADFAST_API_ENABLED=true
   - Check logs: `tail -f storage/logs/laravel.log`

### **9. Monitoring & Maintenance**

#### **Log Monitoring:**
```bash
# View application logs
tail -f /var/www/html/wisedynamic.in/labels/storage/logs/laravel.log

# View web server logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

#### **Performance Monitoring:**
```bash
# Check disk usage
df -h

# Check memory usage
free -h

# Check PHP processes
ps aux | grep php
```

### **10. Backup Strategy**

#### **Database Backup:**
```bash
# Create backup
mysqldump -u username -p wisedynamic_labels_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore backup
mysql -u username -p wisedynamic_labels_db < backup_file.sql
```

#### **File Backup:**
```bash
# Create file backup
tar -czf labels_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/html/wisedynamic.in/labels/
```

## 🎯 **Quick Deployment Commands**

```bash
# Complete deployment for wisedynamic.in
cd /var/www/html/wisedynamic.in/labels
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
chmod -R 755 storage bootstrap/cache public
chown -R www-data:www-data storage bootstrap/cache public
```

## ✅ **Deployment Checklist for wisedynamic.in**

- [ ] Files uploaded to `/var/www/html/wisedynamic.in/labels/`
- [ ] `.env` file configured with correct domain
- [ ] Database created and migrated
- [ ] Steadfast API configured and tested
- [ ] Web server configured for subdirectory
- [ ] SSL certificate installed
- [ ] Permissions set correctly
- [ ] Application optimized
- [ ] All functionality tested
- [ ] Monitoring configured

---

**🎉 Your Labels Courier Management System is ready for wisedynamic.in/labels/!**
