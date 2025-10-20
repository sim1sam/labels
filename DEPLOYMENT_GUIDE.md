# 🚀 Server Deployment Guide for westside.in

## DNS Configuration Issues - Solutions

### 1. **DNS Settings (Most Important)**
Configure these DNS records with your domain registrar:

```
Type: A
Name: @ (or leave blank)
Value: YOUR_SERVER_IP_ADDRESS
TTL: 3600

Type: A  
Name: www
Value: YOUR_SERVER_IP_ADDRESS
TTL: 3600
```

### 2. **Server Requirements**
- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Composer
- Node.js & NPM (for asset compilation)

### 3. **Laravel Configuration**

#### Create `.env` file on server:
```env
APP_NAME="Labels Courier Management"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://westside.in

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=labels_db
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@westside.in"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. **Deployment Steps**

#### Upload Files:
```bash
# Upload all files to your server's web directory
# Example: /var/www/html/westside.in/
```

#### Install Dependencies:
```bash
cd /path/to/your/project
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### Generate Application Key:
```bash
php artisan key:generate
```

#### Database Setup:
```bash
# Create database and import
php artisan migrate --force
php artisan db:seed --force
```

#### Set Permissions:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

#### Clear Caches:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. **Web Server Configuration**

#### Apache Virtual Host:
```apache
<VirtualHost *:80>
    ServerName westside.in
    ServerAlias www.westside.in
    DocumentRoot /var/www/html/westside.in/public
    
    <Directory /var/www/html/westside.in/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/westside_error.log
    CustomLog ${APACHE_LOG_DIR}/westside_access.log combined
</VirtualHost>
```

#### Nginx Configuration:
```nginx
server {
    listen 80;
    server_name westside.in www.westside.in;
    root /var/www/html/westside.in/public;
    
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

### 6. **SSL Certificate (Recommended)**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d westside.in -d www.westside.in
```

### 7. **Troubleshooting**

#### Check DNS Propagation:
```bash
# Test DNS resolution
nslookup westside.in
dig westside.in
```

#### Test Server Response:
```bash
# Test if server responds
curl -I http://YOUR_SERVER_IP
curl -I https://westside.in
```

#### Check Laravel Logs:
```bash
tail -f storage/logs/laravel.log
```

#### Common Issues:
1. **DNS not propagated** - Wait 24-48 hours for DNS changes
2. **Firewall blocking** - Ensure ports 80/443 are open
3. **Web server not running** - Restart Apache/Nginx
4. **File permissions** - Check storage and cache permissions
5. **Database connection** - Verify database credentials

### 8. **Quick Fix Commands**
```bash
# Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan optimize
```

## ⚠️ Important Notes:
- Replace `YOUR_SERVER_IP_ADDRESS` with your actual server IP
- Update database credentials in `.env`
- Ensure your domain registrar's DNS settings point to your server
- DNS propagation can take 24-48 hours
- Test with your server IP first: `http://YOUR_SERVER_IP`

