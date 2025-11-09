# 🔧 Steadfast API Not Working on Production Server - Fix Guide

## Problem
Steadfast API works on local but NOT on production server (wisedynamic.in)

## Common Causes & Solutions

### 1. ✅ Environment Configuration (.env file)

**Check your `.env` file on the server:**

```env
# MUST BE SET TO production (not local)
APP_ENV=production

# Steadfast API Configuration
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
STEADFAST_TIMEOUT=30

# If you get SSL errors, set this to false
STEADFAST_VERIFY_SSL=false
```

**⚠️ IMPORTANT:** 
- If `APP_ENV=local` on your server, the API will be mocked/disabled
- Make sure `STEADFAST_MOCK_IN_LOCAL=false` 
- If SSL certificate errors occur, set `STEADFAST_VERIFY_SSL=false`

### 2. ✅ SSL Certificate Issues (Most Common)

Many production servers have SSL certificate issues. **Try this first:**

```env
STEADFAST_VERIFY_SSL=false
```

Then clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. ✅ Network/Firewall Issues

The server might be blocking outbound HTTPS connections. Check:

```bash
# Test if server can reach Steadfast API
curl -v https://portal.packzy.com/api/v1/get_balance
```

If this fails, contact your hosting provider to allow outbound HTTPS to `portal.packzy.com`

### 4. ✅ Missing cURL SSL Certificates

Some servers don't have proper SSL certificates installed. Check:

```bash
php -r "var_dump(openssl_get_cert_locations());"
```

If certificates are missing, install them or disable SSL verification (see #2)

### 5. ✅ Database Configuration

Check if Steadfast courier has API credentials:

```bash
php artisan tinker
```

```php
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
echo "API Key: " . ($courier->api_key ? 'SET' : 'NOT SET') . "\n";
echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES' : 'NO') . "\n";
```

### 6. ✅ Check Logs for Exact Error

After creating a parcel, check logs:

```bash
tail -f storage/logs/laravel.log | grep -i "steadfast\|parcel"
```

Look for:
- `Steadfast createOrder called` - Shows environment detection
- `SSL verification` - Shows SSL settings
- `Steadfast API Request` - Shows if request is being made
- `Steadfast API Response` - Shows API response
- `Connection Error` - Network issues
- `API is disabled` - Configuration issue

## Quick Diagnostic Script

Run this on your server to check everything:

```bash
php artisan tinker
```

```php
// Check environment
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "Environment: " . app()->environment() . "\n";

// Check Steadfast config
echo "STEADFAST_API_ENABLED: " . (config('courier.steadfast.enabled') ? 'true' : 'false') . "\n";
echo "STEADFAST_MOCK_IN_LOCAL: " . (config('courier.steadfast.mock_in_local') ? 'true' : 'false') . "\n";
echo "STEADFAST_BASE_URL: " . config('courier.steadfast.base_url') . "\n";
echo "STEADFAST_VERIFY_SSL: " . (config('courier.steadfast.verify_ssl') ? 'true' : 'false') . "\n";

// Check courier
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
if ($courier) {
    echo "Courier Found: YES\n";
    echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES' : 'NO') . "\n";
} else {
    echo "Courier Found: NO\n";
}
```

## Step-by-Step Fix

### Step 1: Update .env file
```bash
cd /path/to/your/project
nano .env
```

Make sure these are set:
```env
APP_ENV=production
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_VERIFY_SSL=false
```

### Step 2: Clear all caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
```

### Step 3: Test API connection
```bash
php artisan tinker
```

```php
$service = new \App\Services\SteadfastApiService('YOUR_API_KEY', 'YOUR_API_SECRET');
$result = $service->testConnection();
print_r($result);
```

### Step 4: Create test parcel
Create a parcel and check logs:
```bash
tail -f storage/logs/laravel.log
```

## Expected Log Output (Success)

When working correctly, you should see:
```
[INFO] Steadfast createOrder called
[INFO] SSL verification disabled for Steadfast API request
[INFO] Steadfast API Request
[INFO] Steadfast API Response (status: 200)
```

## If Still Not Working

1. **Check server PHP version** - Should be PHP 7.4 or higher
2. **Check cURL extension** - `php -m | grep curl`
3. **Check allow_url_fopen** - `php -i | grep allow_url_fopen`
4. **Contact hosting provider** - May need to whitelist `portal.packzy.com`
5. **Check firewall rules** - Outbound HTTPS might be blocked

## Contact Information

If issues persist, provide:
- Server PHP version: `php -v`
- Laravel version: `php artisan --version`
- Log file excerpt from `storage/logs/laravel.log`
- Output from diagnostic script above

