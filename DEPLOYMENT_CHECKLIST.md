# 🚀 Server Deployment Checklist

## 📋 **Required Changes for Production Server**

### 1. **Environment Configuration (.env)**
```env
# Change these for production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (update with your server database)
DB_CONNECTION=mysql
DB_HOST=your_server_db_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Courier API Settings
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.steadfast.com.bd/api/v1
STEADFAST_TIMEOUT=30

# Mail Configuration (if using email features)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
```

### 2. **File Permissions**
```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 3. **Laravel Commands to Run**
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (if needed)
php artisan db:seed --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

### 4. **Web Server Configuration**

#### **Apache (.htaccess)**
Ensure your `.htaccess` file in the `public` directory contains:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

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

#### **Nginx Configuration**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/project/public;

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

### 5. **SSL Certificate (Recommended)**
```bash
# Install SSL certificate (Let's Encrypt)
sudo certbot --apache -d yourdomain.com
# or
sudo certbot --nginx -d yourdomain.com
```

### 6. **Database Setup**
```sql
-- Create database
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'your_username'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON your_database_name.* TO 'your_username'@'localhost';
FLUSH PRIVILEGES;
```

### 7. **Upload Files**
```bash
# Upload all files except:
# - .env (create new one on server)
# - storage/logs/* (let Laravel create these)
# - vendor/ (run composer install on server)
# - node_modules/ (if using npm)

# Files to upload:
# - app/
# - config/
# - database/
# - public/
# - resources/
# - routes/
# - composer.json
# - composer.lock
# - artisan
# - package.json (if using npm)
```

### 8. **Post-Deployment Testing**
```bash
# Test API connection
php artisan steadfast:test

# Test merchant setup
php artisan merchants:list

# Check logs
tail -f storage/logs/laravel.log
```

### 9. **Security Considerations**
- ✅ **Change default admin password**
- ✅ **Use strong database passwords**
- ✅ **Enable SSL/HTTPS**
- ✅ **Set proper file permissions**
- ✅ **Keep Laravel updated**
- ✅ **Regular backups**

### 10. **Monitoring Setup**
```bash
# Set up log rotation
sudo nano /etc/logrotate.d/laravel

# Add:
/path/to/your/project/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

## 🎯 **Key Differences from Localhost**

| Feature | Localhost | Production |
|---------|-----------|------------|
| API Calls | Mock (LOCAL-xxx) | Real Steadfast API |
| Debug Mode | Enabled | Disabled |
| Error Display | Detailed | Generic |
| Caching | Disabled | Enabled |
| Logging | Verbose | Optimized |

## ✅ **Verification Checklist**

- [ ] Website loads without errors
- [ ] Admin login works
- [ ] Merchant login works
- [ ] Parcel creation works
- [ ] Steadfast API integration works
- [ ] Tracking numbers are real (not LOCAL-xxx)
- [ ] SSL certificate installed
- [ ] Database migrations completed
- [ ] File permissions set correctly
- [ ] Logs are being written

## 🚨 **Common Issues & Solutions**

### **Issue: 500 Internal Server Error**
```bash
# Check file permissions
chmod -R 755 storage/ bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/

# Check Laravel logs
tail -f storage/logs/laravel.log
```

### **Issue: Database Connection Error**
- Verify database credentials in `.env`
- Ensure database server is running
- Check database user permissions

### **Issue: Steadfast API Not Working**
- Verify API credentials are correct
- Check if `STEADFAST_MOCK_IN_LOCAL=false`
- Test API connection: `php artisan steadfast:test`

### **Issue: File Upload Not Working**
- Check `storage/` directory permissions
- Ensure `php artisan storage:link` was run
- Verify upload directory exists

---

**After deployment, your Steadfast integration will work with real API calls and create actual parcels in your Steadfast dashboard!** 🎉

