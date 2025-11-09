# Server Deployment Fixes Applied

## Issues Fixed

### 1. Image Upload Path Issues ✅
**Problem**: Image uploads were not working correctly on servers, especially when the project is in a subdirectory.

**Solution Applied**:
- Updated `MerchantController.php` and `ProfileController.php` to use Laravel's Storage facade for better server compatibility
- Added automatic directory creation with proper permissions (0755)
- Updated `Merchant.php` model to use `asset()` helper instead of `url()` for better subdirectory support
- Images are now stored in both `public/uploads/merchants/` and Laravel's storage system for backward compatibility

**Files Modified**:
- `app/Http/Controllers/MerchantController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Models/Merchant.php`

### 2. Steadfast API Issues ✅
**Problem**: Steadfast API was not working correctly on production servers.

**Solution Applied**:
- Improved SSL verification handling for production environments
- Added better error logging and debugging information
- Added `Accept: application/json` header for better API compatibility
- Added configuration option `STEADFAST_VERIFY_SSL` in environment file
- Enhanced logging to help diagnose API connection issues

**Files Modified**:
- `app/Services/SteadfastApiService.php`
- `production.env.template`

## Server Deployment Steps

### 1. Environment Configuration

Update your `.env` file on the server with these settings:

```env
# Application URL (IMPORTANT: Update with your actual server URL)
APP_URL=https://yourdomain.com/labels

# Steadfast API Configuration
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
STEADFAST_TIMEOUT=30

# If you encounter SSL certificate errors, set this to false
STEADFAST_VERIFY_SSL=true
```

### 2. File Permissions

Run these commands on your server to ensure proper permissions:

```bash
# Set permissions for storage and uploads directories
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/uploads/

# Create uploads directory if it doesn't exist
mkdir -p public/uploads/merchants
chmod -R 755 public/uploads/

# Set ownership (adjust www-data to your web server user if different)
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
chown -R www-data:www-data public/uploads/
```

### 3. Laravel Commands

Run these commands after uploading files:

```bash
# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link (if using Laravel storage)
php artisan storage:link
```

### 4. Testing Image Uploads

1. Go to Merchant Profile or Admin > Merchants
2. Try uploading a logo/image
3. Check that the image appears correctly
4. Verify the image is saved in `public/uploads/merchants/` directory

### 5. Testing Steadfast API

1. Check your `.env` file has correct `STEADFAST_BASE_URL`
2. Verify API credentials are set correctly in the database
3. Try creating a shipment/parcel
4. Check logs in `storage/logs/laravel.log` for any API errors

### 6. Troubleshooting

#### Image Upload Not Working
- Check file permissions: `ls -la public/uploads/merchants/`
- Verify directory exists: `ls -la public/uploads/`
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Verify `APP_URL` in `.env` matches your server URL

#### Steadfast API Not Working
- Check API credentials in database (merchant_courier table)
- Verify `STEADFAST_BASE_URL` is correct
- Check if SSL verification is causing issues - try setting `STEADFAST_VERIFY_SSL=false`
- Review logs: `tail -f storage/logs/laravel.log | grep Steadfast`
- Test API connection using the test connection feature in admin panel

#### SSL Certificate Errors
If you see SSL certificate errors, add this to your `.env`:
```env
STEADFAST_VERIFY_SSL=false
```

**Note**: Disabling SSL verification is less secure but may be necessary on some servers with certificate issues.

## Additional Notes

- Images are stored in `public/uploads/merchants/` for direct web access
- The `asset()` helper automatically handles subdirectory paths
- API requests now include better error handling and logging
- All changes are backward compatible with existing data

## Support

If issues persist:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify all environment variables are set correctly
3. Ensure file permissions are correct
4. Test API connection using the admin panel test feature

