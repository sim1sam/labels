<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Courier Management
    Route::resource('admin/couriers', App\Http\Controllers\CourierController::class)->names([
        'index' => 'admin.couriers.index',
        'create' => 'admin.couriers.create',
        'store' => 'admin.couriers.store',
        'show' => 'admin.couriers.show',
        'edit' => 'admin.couriers.edit',
        'update' => 'admin.couriers.update',
        'destroy' => 'admin.couriers.destroy'
    ]);
    
    // Merchant Management
    Route::resource('admin/merchants', App\Http\Controllers\MerchantController::class)->names([
        'index' => 'admin.merchants.index',
        'create' => 'admin.merchants.create',
        'store' => 'admin.merchants.store',
        'show' => 'admin.merchants.show',
        'edit' => 'admin.merchants.edit',
        'update' => 'admin.merchants.update',
        'destroy' => 'admin.merchants.destroy'
    ]);
    
    // Parcel Management
    Route::resource('admin/parcels', App\Http\Controllers\ParcelController::class)->names([
        'index' => 'admin.parcels.index',
        'create' => 'admin.parcels.create',
        'store' => 'admin.parcels.store',
        'show' => 'admin.parcels.show',
        'edit' => 'admin.parcels.edit',
        'update' => 'admin.parcels.update',
        'destroy' => 'admin.parcels.destroy'
    ]);
    
    // API route for getting couriers by merchant
    Route::get('admin/parcels/couriers-by-merchant', [App\Http\Controllers\ParcelController::class, 'getCouriersByMerchant'])->middleware('auth');
    
    // API route for getting customer data by mobile number
    Route::get('admin/customers/by-mobile/{mobile}', [App\Http\Controllers\CustomerController::class, 'getByMobile'])->middleware('auth');
    
    // Parcel label generation
    Route::get('admin/parcels/{parcel}/label', [App\Http\Controllers\ParcelController::class, 'generateLabel'])->name('admin.parcels.label');
    
    // Bulk parcel creation
    Route::get('admin/parcels/bulk/create', [App\Http\Controllers\ParcelController::class, 'bulkCreate'])->name('admin.parcels.bulk.create');
    Route::post('admin/parcels/bulk/store', [App\Http\Controllers\ParcelController::class, 'bulkStore'])->name('admin.parcels.bulk.store');
    Route::get('admin/parcels/bulk/format', [App\Http\Controllers\ParcelController::class, 'downloadFormat'])->name('admin.parcels.bulk.format');
    
    // Bulk label printing
    Route::get('admin/parcels/bulk/print', [App\Http\Controllers\ParcelController::class, 'bulkPrint'])->name('admin.parcels.bulk.print');
    Route::post('admin/parcels/bulk/print', [App\Http\Controllers\ParcelController::class, 'processBulkPrint'])->name('admin.parcels.bulk.print.process');
    
    // Bulk status update
    Route::post('admin/parcels/bulk/status', [App\Http\Controllers\ParcelController::class, 'bulkStatusUpdate'])->name('admin.parcels.bulk.status');
    
    // Reports
    Route::get('admin/reports/printed-parcels', [App\Http\Controllers\ReportController::class, 'printedParcels'])->name('admin.reports.printed-parcels');
    Route::get('admin/reports/printed-parcels/download', [App\Http\Controllers\ReportController::class, 'downloadPrintedParcels'])->name('admin.reports.printed-parcels.download');
    
    // Settings
    Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('admin.settings');
    Route::put('admin/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('admin.settings.update');
    
    // Customer Management
    Route::resource('admin/customers', App\Http\Controllers\CustomerController::class)->names([
        'index' => 'admin.customers.index',
        'create' => 'admin.customers.create',
        'store' => 'admin.customers.store',
        'show' => 'admin.customers.show',
        'edit' => 'admin.customers.edit',
        'update' => 'admin.customers.update',
        'destroy' => 'admin.customers.destroy'
    ]);
});

// Merchant only routes
Route::middleware(['auth', 'role:merchant'])->group(function () {
    // Dashboard
    Route::get('/merchant/dashboard', [App\Http\Controllers\MerchantDashboardController::class, 'dashboard'])->name('merchant.dashboard');
    
    // Bulk parcel upload (must be before resource routes to avoid conflicts)
    Route::get('merchant/parcels/bulk-create', [App\Http\Controllers\MerchantParcelController::class, 'bulkCreate'])->name('merchant.parcels.bulk-create');
    Route::post('merchant/parcels/bulk-store', [App\Http\Controllers\MerchantParcelController::class, 'bulkStore'])->name('merchant.parcels.bulk-store');
    Route::get('merchant/parcels/download-format', [App\Http\Controllers\MerchantParcelController::class, 'downloadFormat'])->name('merchant.parcels.download-format');
    
    // Parcels
    Route::resource('merchant/parcels', App\Http\Controllers\MerchantParcelController::class)->names([
        'index' => 'merchant.parcels.index',
        'create' => 'merchant.parcels.create',
        'store' => 'merchant.parcels.store',
        'show' => 'merchant.parcels.show',
        'edit' => 'merchant.parcels.edit',
        'update' => 'merchant.parcels.update',
        'destroy' => 'merchant.parcels.destroy',
    ]);
    
    // Reports
    Route::get('merchant/reports', [App\Http\Controllers\ReportController::class, 'merchantReports'])->name('merchant.reports.index');
    Route::get('merchant/reports/download', [App\Http\Controllers\ReportController::class, 'downloadMerchantReports'])->name('merchant.reports.download');
    Route::get('merchant/reports/printed-parcels', [App\Http\Controllers\ReportController::class, 'merchantPrintedParcels'])->name('merchant.reports.printed-parcels');
    Route::get('merchant/reports/printed-parcels/download', [App\Http\Controllers\ReportController::class, 'downloadMerchantPrintedParcels'])->name('merchant.reports.printed-parcels.download');
});

// Public debug routes (no authentication required)
Route::get('/debug/steadfast', function () {
    try {
        $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
        
        if (!$courier) {
            return response()->json([
                'error' => 'Steadfast Courier not found in database',
                'message' => 'Please create Steadfast courier first'
            ], 404);
        }
        
        $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
        $result = $apiService->testConnection();
        
        return response()->json([
            'courier_info' => [
                'id' => $courier->id,
                'name' => $courier->courier_name,
                'api_endpoint' => $courier->api_endpoint,
                'has_api_key' => !empty($courier->api_key),
                'has_api_secret' => !empty($courier->api_secret),
                'status' => $courier->status
            ],
            'api_test' => $result,
            'environment' => [
                'steadfast_enabled' => config('courier.steadfast.enabled'),
                'mock_in_local' => config('courier.steadfast.mock_in_local'),
                'base_url' => config('courier.steadfast.base_url'),
                'timeout' => config('courier.steadfast.timeout')
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Exception occurred',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Debug page for Steadfast API
Route::get('/debug/steadfast-page', function () {
    return view('debug.steadfast');
});

// Test parcel creation page
Route::get('/test-parcel', function () {
    $action = request('action');
    $results = [];
    
    if ($action === 'config') {
        // Check Steadfast configuration
        try {
            $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
            
            if (!$courier) {
                $results['error'] = 'Steadfast Courier not found in database';
            } else {
                $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
                $apiResult = $apiService->testConnection();
                
                $results = [
                    'courier_info' => [
                        'id' => $courier->id,
                        'name' => $courier->courier_name,
                        'api_endpoint' => $courier->api_endpoint,
                        'has_api_key' => !empty($courier->api_key),
                        'has_api_secret' => !empty($courier->api_secret),
                        'status' => $courier->status
                    ],
                    'api_test' => $apiResult,
                    'environment' => [
                        'steadfast_enabled' => config('courier.steadfast.enabled'),
                        'mock_in_local' => config('courier.steadfast.mock_in_local'),
                        'base_url' => config('courier.steadfast.base_url'),
                        'timeout' => config('courier.steadfast.timeout')
                    ]
                ];
            }
        } catch (Exception $e) {
            $results['error'] = 'Exception occurred: ' . $e->getMessage();
        }
    } elseif ($action === 'run') {
        // Run test parcel creation
        try {
            $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
            
            if (!$courier) {
                $results['error'] = 'Steadfast Courier not found';
            } else {
                $merchant = App\Models\Merchant::first();
                if (!$merchant) {
                    $results['error'] = 'No merchant found';
                } else {
                    // Create a test parcel
                    $testParcel = new App\Models\Parcel([
                        'parcel_id' => 'TEST-' . time(),
                        'customer_name' => 'Test Customer',
                        'mobile_number' => '01700000000',
                        'delivery_address' => 'Test Address, Dhaka 1205',
                        'cod_amount' => 100,
                        'merchant_id' => $merchant->id,
                        'courier_id' => $courier->id,
                        'status' => 'pending'
                    ]);
                    
                    $testParcel->save();
                    
                    // Test API service
                    $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
                    $apiResult = $apiService->createOrder($testParcel);
                    
                    $results = [
                        'test_parcel' => [
                            'id' => $testParcel->id,
                            'parcel_id' => $testParcel->parcel_id,
                            'customer_name' => $testParcel->customer_name,
                            'mobile_number' => $testParcel->mobile_number,
                            'delivery_address' => $testParcel->delivery_address,
                            'cod_amount' => $testParcel->cod_amount,
                            'merchant_id' => $testParcel->merchant_id,
                            'courier_id' => $testParcel->courier_id
                        ],
                        'api_result' => $apiResult,
                        'message' => 'Test parcel creation completed'
                    ];
                }
            }
        } catch (Exception $e) {
            $results['error'] = 'Test failed: ' . $e->getMessage();
        }
    }
    
    return view('debug.test-parcel', compact('action', 'results'));
});

// Test parcel creation API endpoint
Route::get('/api/test-parcel', function () {
    try {
        $courier = App\Models\Courier::where('courier_name', 'like', '%Steadfast%')->first();
        
        if (!$courier) {
            return response()->json(['error' => 'Steadfast Courier not found'], 404);
        }
        
        // Get first merchant
        $merchant = App\Models\Merchant::first();
        if (!$merchant) {
            return response()->json(['error' => 'No merchant found'], 404);
        }
        
        // Create a test parcel
        $testParcel = new App\Models\Parcel([
            'parcel_id' => 'TEST-' . time(),
            'customer_name' => 'Test Customer',
            'mobile_number' => '01700000000',
            'delivery_address' => 'Test Address, Dhaka 1205',
            'cod_amount' => 100,
            'merchant_id' => $merchant->id,
            'courier_id' => $courier->id,
            'status' => 'pending'
        ]);
        
        $testParcel->save();
        
        // Test API service
        $apiService = new App\Services\SteadfastApiService($courier->api_key, $courier->api_secret);
        $result = $apiService->createOrder($testParcel);
        
        return response()->json([
            'test_parcel' => [
                'id' => $testParcel->id,
                'parcel_id' => $testParcel->parcel_id,
                'customer_name' => $testParcel->customer_name,
                'mobile_number' => $testParcel->mobile_number,
                'delivery_address' => $testParcel->delivery_address,
                'cod_amount' => $testParcel->cod_amount,
                'merchant_id' => $testParcel->merchant_id,
                'courier_id' => $testParcel->courier_id
            ],
            'api_result' => $result,
            'message' => 'Test parcel creation completed'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Common authenticated routes
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    
    // Admin profile update
    Route::middleware(['role:admin'])->group(function () {
        Route::put('/profile/admin', [App\Http\Controllers\ProfileController::class, 'updateAdmin'])->name('profile.update.admin');
    });
    
    // Merchant profile update
    Route::middleware(['role:merchant'])->group(function () {
        Route::put('/profile/merchant', [App\Http\Controllers\ProfileController::class, 'updateMerchant'])->name('profile.update.merchant');
    });
    
    // Courier API routes
    Route::prefix('api')->group(function () {
        Route::get('/courier-options', [App\Http\Controllers\CourierApiController::class, 'getCourierOptions']);
        Route::get('/available-couriers', [App\Http\Controllers\CourierApiController::class, 'getAvailableCouriers']);
        Route::get('/tracking/{parcel}', [App\Http\Controllers\CourierApiController::class, 'getTracking']);
        Route::get('/live-tracking/{parcel}', [App\Http\Controllers\CourierApiController::class, 'getLiveTracking']);
        Route::post('/shipment/{parcel}', [App\Http\Controllers\CourierApiController::class, 'createShipment']);
        Route::get('/test-connection/{courier}', [App\Http\Controllers\CourierApiController::class, 'testConnection']);
    });
});

// Public API routes for tracking (no authentication required)
Route::prefix('api')->group(function () {
    Route::get('/live-tracking/{parcel}', [App\Http\Controllers\CourierApiController::class, 'getLiveTracking']);
    Route::get('/tracking/{parcel}', [App\Http\Controllers\CourierApiController::class, 'getTracking']);
});
