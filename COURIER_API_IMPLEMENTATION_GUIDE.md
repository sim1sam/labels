# 🚀 Courier API Integration & Live Tracking Implementation

## ✅ **What's Been Implemented:**

### **1. Database Enhancements**
- **Courier Table:** Added API fields (endpoint, key, secret, config, tracking support)
- **Merchant-Courier Pivot:** Added merchant-specific API credentials and custom IDs
- **Parcel Table:** Added tracking fields (tracking_number, courier_tracking_number, tracking_history)

### **2. Models & Services**
- **Enhanced Courier Model:** API integration methods, tracking URL generation
- **Enhanced Parcel Model:** Tracking history management, status updates
- **CourierApiService:** Complete API integration service for tracking and shipments

### **3. Controllers & Routes**
- **CourierApiController:** API endpoints for tracking, courier selection, live updates
- **API Routes:** RESTful endpoints for all courier operations

### **4. Enhanced UI**
- **Modern Parcel Creation Form:** Courier selection with live tracking preview
- **Real-time Updates:** AJAX-powered courier loading and tracking

## 🎯 **Key Features Implemented:**

### **Courier Selection with API Integration:**
```php
// When creating a parcel, merchants can:
1. Select from available couriers
2. See which couriers have API integration
3. See which couriers support live tracking
4. View courier ratings and vehicle types
5. Set custom merchant IDs for each courier
```

### **Live Tracking System:**
```php
// Real-time tracking features:
1. Automatic tracking number generation
2. Live status updates from courier APIs
3. Tracking history storage
4. Status change notifications
5. Tracking URL generation
```

### **API Integration:**
```php
// Courier API capabilities:
1. Test API connections
2. Create shipments via API
3. Retrieve tracking information
4. Update parcel status automatically
5. Handle API errors gracefully
```

## 🔧 **How to Use:**

### **1. Configure Couriers with API:**

#### **Admin Panel - Add Courier API:**
```php
// In courier management, add:
- API Endpoint: https://api.courier.com/v1
- API Key: your_api_key_here
- API Secret: your_api_secret_here
- Has Tracking: Yes/No
- Tracking URL Template: https://track.courier.com/{tracking_number}
```

#### **Merchant Panel - Set Custom Credentials:**
```php
// When assigning couriers to merchants:
- Merchant Custom ID: MERCH001
- Merchant API Key: merchant_specific_key (optional)
- Merchant API Secret: merchant_specific_secret (optional)
- Is Primary: Yes/No
```

### **2. Create Parcels with Courier Selection:**

#### **Enhanced Parcel Creation:**
1. **Select Courier:** Choose from available couriers with API support
2. **Live Tracking Preview:** See if courier supports tracking
3. **Automatic Tracking:** System generates tracking numbers
4. **API Integration:** Parcel automatically synced with courier

### **3. Live Tracking Features:**

#### **Real-time Updates:**
```javascript
// AJAX endpoints for live tracking:
GET /api/live-tracking/{parcel}     // Get latest tracking info
GET /api/tracking/{parcel}          // Get full tracking history
POST /api/shipment/{parcel}         // Create shipment with courier
```

#### **Tracking Display:**
- **Status Updates:** Pending → In Transit → Delivered
- **Location Tracking:** Real-time location updates
- **History Log:** Complete tracking timeline
- **Notifications:** Status change alerts

## 📋 **Implementation Steps:**

### **Step 1: Update Courier Management**
```php
// Add API fields to courier creation/edit forms:
- API Endpoint
- API Key & Secret
- Tracking Support Toggle
- Tracking URL Template
```

### **Step 2: Update Merchant-Courier Assignment**
```php
// When assigning couriers to merchants:
- Custom Merchant ID
- Merchant-specific API credentials
- Primary courier designation
```

### **Step 3: Use Enhanced Parcel Creation**
```php
// Replace existing parcel creation with:
- Enhanced form with courier selection
- Live tracking preview
- API integration
```

### **Step 4: Implement Live Tracking Display**
```php
// Add to parcel management:
- Live tracking status
- Tracking history timeline
- Real-time updates
```

## 🔌 **API Integration Examples:**

### **Courier API Response Format:**
```json
{
  "success": true,
  "data": {
    "tracking_number": "TRK123456",
    "status": "in_transit",
    "location": "Dhaka, Bangladesh",
    "timestamp": "2025-10-18T10:30:00Z",
    "tracking_history": [
      {
        "status": "pending",
        "location": "Warehouse",
        "timestamp": "2025-10-18T09:00:00Z",
        "description": "Package received"
      },
      {
        "status": "in_transit",
        "location": "Dhaka, Bangladesh",
        "timestamp": "2025-10-18T10:30:00Z",
        "description": "Out for delivery"
      }
    ]
  }
}
```

### **Shipment Creation:**
```json
{
  "merchant_id": 1,
  "tracking_number": "TRK123456",
  "sender": {
    "name": "John Doe",
    "phone": "+880123456789",
    "address": "123 Main St, Dhaka"
  },
  "receiver": {
    "name": "Jane Smith",
    "phone": "+880987654321",
    "address": "456 Oak Ave, Chittagong"
  },
  "package": {
    "weight": 2.5,
    "description": "Electronics",
    "value": 5000.00
  }
}
```

## 🎨 **UI Features:**

### **Courier Selection Interface:**
- **Visual Cards:** Each courier displayed as a selectable card
- **Badges:** API support, tracking capability, primary status
- **Ratings:** Star ratings for courier performance
- **Vehicle Types:** Motorcycle, van, bike indicators

### **Live Tracking Preview:**
- **Status Indicators:** Color-coded status badges
- **Real-time Updates:** Automatic status refresh
- **Tracking Timeline:** Visual tracking history
- **Location Updates:** Current location display

## 🔧 **Configuration Examples:**

### **Popular Courier APIs:**

#### **Pathao API:**
```php
API Endpoint: https://api-hermes.pathao.com/api/v1
Tracking URL: https://pathao.com/track/{tracking_number}
```

#### **RedX API:**
```php
API Endpoint: https://api.redx.com.bd/v1
Tracking URL: https://redx.com.bd/track/{tracking_number}
```

#### **eCourier API:**
```php
API Endpoint: https://backoffice.ecourier.com.bd/api
Tracking URL: https://ecourier.com.bd/track/{tracking_number}
```

## 🚀 **Next Steps:**

1. **Test API Connections:** Use the test connection feature
2. **Configure Real Couriers:** Add actual courier API credentials
3. **Update Parcel Forms:** Use the enhanced creation form
4. **Implement Live Tracking:** Add tracking display to parcel views
5. **Monitor Performance:** Track API response times and success rates

## ⚠️ **Important Notes:**

- **API Keys:** Store securely, never expose in frontend
- **Rate Limiting:** Implement proper rate limiting for API calls
- **Error Handling:** Graceful fallbacks when APIs are unavailable
- **Data Privacy:** Ensure tracking data is properly secured
- **Backup Systems:** Have manual tracking as fallback

This implementation provides a complete courier API integration system with live tracking capabilities!

