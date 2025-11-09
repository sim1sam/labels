# Fix: Courier ID 7 Has No API Integration

## Problem
Logs show: `"has_api_integration":false` for `courier_id:7`

## Diagnosis

The `hasApiIntegration()` method returns `false` for courier ID 7. This means:

1. **If courier name contains "steadfast"**: Only `api_key` is required
2. **If courier name doesn't contain "steadfast"**: Both `api_endpoint` AND `api_key` are required

## Quick Fix - Check Courier in Database

Run this on your server:

```bash
php artisan tinker
```

```php
$courier = \App\Models\Courier::find(7);
if ($courier) {
    echo "Courier ID: " . $courier->id . "\n";
    echo "Courier Name: " . $courier->courier_name . "\n";
    echo "Is Steadfast: " . (stripos($courier->courier_name, 'steadfast') !== false ? 'YES' : 'NO') . "\n";
    echo "API Key: " . ($courier->api_key ? 'SET (' . substr($courier->api_key, 0, 10) . '...)' : 'NOT SET') . "\n";
    echo "API Secret: " . ($courier->api_secret ? 'SET' : 'NOT SET') . "\n";
    echo "API Endpoint: " . ($courier->api_endpoint ?: 'NOT SET') . "\n";
    echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES' : 'NO') . "\n";
} else {
    echo "Courier ID 7 NOT FOUND!\n";
}
```

## Solutions

### Solution 1: If Courier Name Doesn't Contain "Steadfast"

Update the courier name to include "Steadfast":

```php
$courier = \App\Models\Courier::find(7);
$courier->update([
    'courier_name' => 'Steadfast Courier' // Make sure it contains "steadfast"
]);
```

### Solution 2: If API Key is Missing

Update the courier with API credentials:

```php
$courier = \App\Models\Courier::find(7);
$courier->update([
    'api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
    'api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
    'api_endpoint' => 'https://portal.packzy.com/api/v1' // Optional for Steadfast
]);
```

### Solution 3: If Courier Name is Correct but Still Not Working

The name check is case-insensitive, but verify:

```php
$courier = \App\Models\Courier::find(7);
$name = strtolower($courier->courier_name);
echo "Lowercase name: " . $name . "\n";
echo "Contains 'steadfast': " . (strpos($name, 'steadfast') !== false ? 'YES' : 'NO') . "\n";
```

## Complete Fix Script

Run this to check and fix everything:

```php
php artisan tinker
```

```php
$courier = \App\Models\Courier::find(7);

if (!$courier) {
    echo "ERROR: Courier ID 7 not found!\n";
    exit;
}

echo "=== Current Status ===\n";
echo "ID: {$courier->id}\n";
echo "Name: {$courier->courier_name}\n";
echo "API Key: " . ($courier->api_key ? 'SET' : 'NOT SET') . "\n";
echo "API Secret: " . ($courier->api_secret ? 'SET' : 'NOT SET') . "\n";
echo "API Endpoint: " . ($courier->api_endpoint ?: 'NOT SET') . "\n";
echo "Has API Integration: " . ($courier->hasApiIntegration() ? 'YES' : 'NO') . "\n\n";

// Fix if needed
$needsUpdate = false;
$updates = [];

// Ensure name contains "steadfast"
if (stripos($courier->courier_name, 'steadfast') === false) {
    $updates['courier_name'] = 'Steadfast Courier';
    $needsUpdate = true;
    echo "⚠️  Name doesn't contain 'steadfast', will update...\n";
}

// Set API key if missing
if (empty($courier->api_key)) {
    $updates['api_key'] = 'w21i6x8sjwygmg6rz2on4omniflrd5rb';
    $needsUpdate = true;
    echo "⚠️  API key missing, will set...\n";
}

// Set API secret if missing
if (empty($courier->api_secret)) {
    $updates['api_secret'] = 'g84orvx9hiywjtm7wy3w4h5e';
    $needsUpdate = true;
    echo "⚠️  API secret missing, will set...\n";
}

if ($needsUpdate) {
    $courier->update($updates);
    echo "\n✅ Courier updated!\n";
    echo "Has API Integration: " . ($courier->fresh()->hasApiIntegration() ? 'YES ✓' : 'NO ✗') . "\n";
} else {
    echo "✅ Courier is already configured correctly!\n";
}
```

## After Fixing

1. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Test by creating a new parcel or bulk upload

3. Check logs - you should now see:
```
Bulk upload: Attempting Steadfast upload for parcel
```
instead of:
```
Bulk upload: Courier has no API integration
```

