# 🚀 Production Deployment Checklist

## 📋 **Pre-Deployment Setup**

### **1. Server Requirements**
- [ ] **PHP 8.1+** installed and configured
- [ ] **MySQL 5.7+** or **MariaDB 10.3+** installed
- [ ] **Apache/Nginx** web server configured
- [ ] **Composer** installed globally
- [ ] **Node.js & NPM** installed (for asset compilation)
- [ ] **SSL Certificate** configured (Let's Encrypt recommended)

### **2. Domain & DNS**
- [ ] **Domain name** registered and pointing to server IP
- [ ] **DNS records** configured:
  ```
  Type: A, Name: @, Value: YOUR_SERVER_IP
  Type: A, Name: www, Value: YOUR_SERVER_IP
  ```
- [ ] **DNS propagation** verified (can take 24-48 hours)

## 🔧 **Environment Configuration**

### **3. Create Production .env File**
```bash
# Copy the template
cp production.env.template .env

# Edit with your actual values
nano .env
```

### **4. Required .env Variables**
- [ ] **APP_KEY** - Generate with `php artisan key:generate`
- [ ] **APP_URL** - Your actual domain (https://yourdomain.com)
- [ ] **DB_* variables** - Your production database credentials
- [ ] **MAIL_* variables** - Your SMTP email settings
- [ ] **STEADFAST_* variables** - Steadfast API configuration

### **5. Steadfast API Configuration**
```env
# Steadfast API Settings (REQUIRED)
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
STEADFAST_TIMEOUT=30
```

## 📦 **Deployment Steps**

### **6. Upload Files**
- [ ] Upload all project files to server
- [ ] Ensure proper file ownership and permissions
- [ ] Set document root to `/public` directory

### **7. Install Dependencies**
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install

# Build assets
npm run build
```

### **8. Database Setup**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE labels_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force
```

### **9. Set Permissions**
```bash
# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
chown -R www-data:www-data public/uploads
```

### **10. Generate Application Key**
```bash
# Generate unique application key
php artisan key:generate
```

## ⚡ **Optimization & Caching**

### **11. Clear and Optimize Caches**
```bash
# Clear all caches
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

### **12. Production Optimizations**
```bash
# Optimize Composer autoloader
composer dump-autoload --optimize

# Clear compiled views
php artisan view:clear

# Optimize for production
php artisan optimize
```

## 🔒 **Security Configuration**

### **13. Web Server Security**
- [ ] **HTTPS** enforced (redirect HTTP to HTTPS)
- [ ] **Security headers** configured
- [ ] **File upload limits** set appropriately
- [ ] **Directory browsing** disabled
- [ ] **Hidden files** protected (.env, .git, etc.)

### **14. Laravel Security**
- [ ] **APP_DEBUG=false** in production
- [ ] **APP_ENV=production** set
- [ ] **Session security** configured
- [ ] **CSRF protection** enabled
- [ ] **SQL injection** protection active

## 🧪 **Testing & Verification**

### **15. Functionality Tests**
- [ ] **Homepage** loads correctly
- [ ] **Admin login** works
- [ ] **Merchant login** works
- [ ] **Parcel creation** works
- [ ] **Label printing** works
- [ ] **Steadfast API** connection works

### **16. Steadfast API Tests**
```bash
# Test API connection
php artisan tinker
>>> $service = new App\Services\SteadfastApiService();
>>> $result = $service->testConnection();
>>> dd($result);
```

### **17. Database Tests**
- [ ] **All tables** created successfully
- [ ] **Sample data** seeded
- [ ] **Relationships** working
- [ ] **Foreign keys** properly set

## 📊 **Monitoring & Maintenance**

### **18. Logging Configuration**
- [ ] **Error logging** enabled
- [ ] **Log rotation** configured
- [ ] **Log levels** set appropriately
- [ ] **Log files** accessible

### **19. Backup Strategy**
- [ ] **Database backup** automated
- [ ] **File backup** configured
- [ ] **Backup retention** policy set
- [ ] **Restore procedure** documented

### **20. Performance Monitoring**
- [ ] **Server resources** monitored
- [ ] **Database performance** tracked
- [ ] **API response times** logged
- [ ] **Error rates** monitored

## 🚨 **Critical Production Settings**

### **21. Steadfast API Production Settings**
```env
# CRITICAL: These must be set correctly
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
```

### **22. Database Production Settings**
```env
# CRITICAL: Use MySQL in production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=labels_db
DB_USERNAME=your_production_username
DB_PASSWORD=your_secure_password
```

### **23. Security Production Settings**
```env
# CRITICAL: Security settings
APP_DEBUG=false
APP_ENV=production
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

## 🔍 **Post-Deployment Verification**

### **24. Final Checks**
- [ ] **Website** accessible via domain name
- [ ] **SSL certificate** working
- [ ] **All pages** load without errors
- [ ] **Forms** submit successfully
- [ ] **File uploads** work
- [ ] **Email** sending works
- [ ] **Steadfast API** responding

### **25. Performance Checks**
- [ ] **Page load times** acceptable (< 3 seconds)
- [ ] **Database queries** optimized
- [ ] **Caching** working effectively
- [ ] **Memory usage** within limits

## 📞 **Support & Maintenance**

### **26. Documentation**
- [ ] **Deployment guide** updated
- [ ] **API documentation** current
- [ ] **Troubleshooting guide** created
- [ ] **Backup procedures** documented

### **27. Monitoring Setup**
- [ ] **Uptime monitoring** configured
- [ ] **Error tracking** enabled
- [ ] **Performance monitoring** active
- [ ] **Alert notifications** set up

## ✅ **Deployment Complete Checklist**

- [ ] All files uploaded and configured
- [ ] Database created and migrated
- [ ] Environment variables set correctly
- [ ] Steadfast API configured and tested
- [ ] Security settings applied
- [ ] Caching optimized
- [ ] All functionality tested
- [ ] Performance verified
- [ ] Monitoring configured
- [ ] Documentation updated

## 🎯 **Quick Commands for Production**

```bash
# Complete production setup
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/uploads
```

## ⚠️ **Important Notes**

1. **Never use SQLite in production** - Always use MySQL/MariaDB
2. **Always use HTTPS** - Never deploy without SSL
3. **Keep APP_DEBUG=false** - Never enable debug in production
4. **Test Steadfast API** - Verify connection before going live
5. **Monitor logs** - Check for errors regularly
6. **Backup regularly** - Database and files
7. **Update dependencies** - Keep packages current
8. **Monitor performance** - Watch server resources

---

**🎉 Your Labels Courier Management System is ready for production!**
