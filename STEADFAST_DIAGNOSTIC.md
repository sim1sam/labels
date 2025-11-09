# Steadfast API Diagnostic Guide

## Problem
Parcels are created successfully but not being sent to Steadfast API on live server.

## Possible Causes

### 1. Courier Database Configuration Missing
The Steadfast courier in the database might be missing required fields:
- `api_endpoint` - Should be: `https://portal.packzy.com/api/v1`
- `api_key` - Should be set
- `api_secret` - Should be set

### 2. Merchant-Courier Relationship Missing
The merchant might not be properly assigned to Steadfast courier, or API credentials might be missing in the pivot table.

### 3. Environment Configuration
Check your `.env` file on the server:
```env
STEADFAST_API_ENABLED=true
STEADFAST_MOCK_IN_LOCAL=false
STEADFAST_BASE_URL=https://portal.packzy.com/api/v1
STEADFAST_VERIFY_SSL=true
```

## Diagnostic Steps

### Step 1: Check Courier Database Record

Run this SQL query or use Tinker:

```php
php artisan tinker
```

```php
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
if ($courier) {
    echo "Courier ID: " . $courier->id . "\n";
    echo "Courier Name: " . $courier->courier_name . "\n";
    echo "API Endpoint: " . ($courier->api_endpoint ?: 'NOT SET') . "\n";
    echo "API Key: " . ($courier->api_key ? 'SET' : 'NOT SET') . "\n";
    echo "API Secret: " . ($courier->api_secret ? 'SET' : 'NOT SET') . "\n";
    echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES' : 'NO') . "\n";
} else {
    echo "Steadfast courier not found!\n";
}
```

### Step 2: Check Merchant-Courier Assignment

```php
$merchant = \App\Models\Merchant::find(MERCHANT_ID); // Replace with actual merchant ID
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();

if ($merchant && $courier) {
    $merchantCourier = $merchant->couriers()->where('couriers.id', $courier->id)->first();
    if ($merchantCourier) {
        echo "Merchant-Courier relationship exists\n";
        echo "Merchant API Key: " . ($merchantCourier->pivot->merchant_api_key ? 'SET' : 'NOT SET') . "\n";
        echo "Merchant API Secret: " . ($merchantCourier->pivot->merchant_api_secret ? 'SET' : 'NOT SET') . "\n";
    } else {
        echo "Merchant-Courier relationship NOT FOUND!\n";
    }
}
```

### Step 3: Check Laravel Logs

After creating a parcel, check the logs:

```bash
tail -f storage/logs/laravel.log | grep -i "parcel\|steadfast"
```

Look for these log entries:
- `Parcel creation - Checking API integration`
- `Parcel created but API integration not called`
- `Parcel creation - Calling Steadfast API`
- `Steadfast API credentials missing`

### Step 4: Fix Missing Configuration

If the courier is missing configuration, update it:

```php
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
if ($courier) {
    $courier->update([
        'api_endpoint' => 'https://portal.packzy.com/api/v1',
        'api_key' => 'YOUR_API_KEY_HERE',
        'api_secret' => 'YOUR_API_SECRET_HERE',
    ]);
    echo "Courier updated successfully!\n";
}
```

### Step 5: Assign Merchant to Courier (if not assigned)

```php
$merchant = \App\Models\Merchant::find(MERCHANT_ID);
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();

if ($merchant && $courier) {
    $merchant->couriers()->syncWithoutDetaching([
        $courier->id => [
            'merchant_custom_id' => 'STEADFAST001',
            'status' => 'active',
            'merchant_api_key' => 'YOUR_API_KEY_HERE',
            'merchant_api_secret' => 'YOUR_API_SECRET_HERE',
            'is_primary' => true
        ]
    ]);
    echo "Merchant assigned to Steadfast courier!\n";
}
```

## Quick Fix Command

Run this command to check and fix common issues:

```bash
php artisan tinker
```

Then run:

```php
// Find Steadfast courier
$courier = \App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->orWhere('courier_name', 'like', '%steadfast%')->first();

if (!$courier) {
    echo "ERROR: Steadfast courier not found in database!\n";
    echo "Please create it first using the admin panel or seeder.\n";
    exit;
}

echo "Found courier: {$courier->courier_name} (ID: {$courier->id})\n";
echo "API Endpoint: " . ($courier->api_endpoint ?: 'NOT SET') . "\n";
echo "API Key: " . ($courier->api_key ? 'SET (' . substr($courier->api_key, 0, 10) . '...)' : 'NOT SET') . "\n";
echo "API Secret: " . ($courier->api_secret ? 'SET' : 'NOT SET') . "\n";
echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES ✓' : 'NO ✗') . "\n";

if (!$courier->hasApiIntegration()) {
    echo "\n⚠️  WARNING: Courier does not have API integration configured!\n";
    echo "Please update the courier with API credentials.\n";
}
```

## After Fixing

1. Clear Laravel cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Try creating a parcel again

3. Check logs to see if API is being called:
```bash
tail -f storage/logs/laravel.log
```

