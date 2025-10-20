# 🚀 Steadfast API Setup Guide

## 📋 **Merchant Information:**
- **Merchant:** kolkata2 dhaka
- **API Key:** w21i6x8sjwygmg6rz2on4omniflrd5rb
- **Secret Key:** g84orvx9hiywjtm7wy3w4h5e
- **Base URL:** https://portal.packzy.com/api/v1

## 🔧 **Setup Steps:**

### **Step 1: Create Steadfast Courier in Admin Panel**

#### **Courier Details:**
```
Courier Name: Steadfast Courier
Phone: (Add contact number)
Email: (Add contact email)
Vehicle Type: van
Status: active
Rating: 4.5
Total Deliveries: 0

API Configuration:
- API Endpoint: https://portal.packzy.com/api/v1
- API Key: w21i6x8sjwygmg6rz2on4omniflrd5rb
- API Secret: g84orvx9hiywjtm7wy3w4h5e
- Has Tracking: Yes
- Tracking URL Template: https://portal.packzy.com/track/{tracking_number}
```

### **Step 2: Assign Steadfast to Merchant**

#### **Merchant-Courier Assignment:**
```
Merchant: kolkata2 dhaka
Courier: Steadfast Courier
Merchant Custom ID: STEADFAST001 (or any custom ID)
Status: active
Is Primary: Yes (if this is the main courier)

Merchant API Credentials (Optional):
- Merchant API Key: w21i6x8sjwygmg6rz2on4omniflrd5rb
- Merchant API Secret: g84orvx9hiywjtm7wy3w4h5e
```

## 🛠️ **Implementation Commands:**

### **Option 1: Using Tinker (Quick Setup)**
```bash
php artisan tinker
```

```php
// Create Steadfast Courier
$courier = App\Models\Courier::create([
    'courier_name' => 'Steadfast Courier',
    'phone' => '01700000000',
    'email' => 'info@steadfast.com',
    'vehicle_type' => 'van',
    'status' => 'active',
    'rating' => 4.5,
    'total_deliveries' => 0,
    'api_endpoint' => 'https://portal.packzy.com/api/v1',
    'api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
    'api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
    'has_tracking' => true,
    'tracking_url_template' => 'https://portal.packzy.com/track/{tracking_number}'
]);

// Find merchant (adjust the condition based on your merchant data)
$merchant = App\Models\Merchant::where('shop_name', 'like', '%kolkata2%')
    ->orWhere('shop_name', 'like', '%dhaka%')
    ->first();

if ($merchant) {
    // Attach courier to merchant
    $merchant->couriers()->attach($courier->id, [
        'merchant_custom_id' => 'STEADFAST001',
        'status' => 'active',
        'merchant_api_key' => 'w21i6x8sjwygmg6rz2on4omniflrd5rb',
        'merchant_api_secret' => 'g84orvx9hiywjtm7wy3w4h5e',
        'is_primary' => true
    ]);
    
    echo "Steadfast courier assigned to merchant: " . $merchant->shop_name;
} else {
    echo "Merchant not found. Please check merchant name.";
}

exit
```

### **Option 2: Using Database Seeder**
```bash
php artisan make:seeder SteadfastCourierSeeder
```

## 📊 **Steadfast API Integration Features:**

### **1. Order Creation:**
- **Endpoint:** `/create_order`
- **Method:** POST
- **Features:** Single order creation with all required fields

### **2. Bulk Order Creation:**
- **Endpoint:** `/create_order/bulk-order`
- **Method:** POST
- **Features:** Up to 500 orders at once

### **3. Delivery Status Tracking:**
- **By Consignment ID:** `/status_by_cid/{id}`
- **By Invoice ID:** `/status_by_invoice/{invoice}`
- **By Tracking Code:** `/status_by_trackingcode/{trackingCode}`

### **4. Balance Checking:**
- **Endpoint:** `/get_balance`
- **Method:** GET

### **5. Return Requests:**
- **Create Return:** `/create_return_request`
- **Get Returns:** `/get_return_requests`
- **Single Return:** `/get_return_request/{id}`

## 🔄 **Status Mapping:**

### **Steadfast Status → System Status:**
```
pending → pending
in_review → pending
delivered → delivered
delivered_approval_pending → delivered
partial_delivered → in_transit
partial_delivered_approval_pending → in_transit
cancelled → failed
cancelled_approval_pending → failed
hold → pending
unknown → pending
```

## 🎯 **Testing the Setup:**

### **1. Test API Connection:**
```bash
# Test the API connection
curl -X GET "https://portal.packzy.com/api/v1/get_balance" \
  -H "Api-Key: w21i6x8sjwygmg6rz2on4omniflrd5rb" \
  -H "Secret-Key: g84orvx9hiywjtm7wy3w4h5e" \
  -H "Content-Type: application/json"
```

### **2. Test Order Creation:**
```bash
curl -X POST "https://portal.packzy.com/api/v1/create_order" \
  -H "Api-Key: w21i6x8sjwygmg6rz2on4omniflrd5rb" \
  -H "Secret-Key: g84orvx9hiywjtm7wy3w4h5e" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice": "TEST123",
    "recipient_name": "John Doe",
    "recipient_phone": "01234567890",
    "recipient_address": "House 44, Road 2/A, Dhanmondi, Dhaka 1209",
    "cod_amount": 1000,
    "note": "Test order"
  }'
```

## 🚀 **Next Steps:**

1. **Run the setup commands** to create the courier and assign to merchant
2. **Test the API connection** using the test endpoint
3. **Create a test parcel** using the enhanced parcel creation form
4. **Verify tracking** works correctly
5. **Set up automated status updates** (optional)

## ⚠️ **Important Notes:**

- **API Keys are sensitive** - store securely
- **Test with small amounts** first
- **Monitor API rate limits**
- **Keep backup of credentials**
- **Test all endpoints** before production use

This setup will enable the merchant "kolkata2 dhaka" to use Steadfast Courier with full API integration and live tracking capabilities!

