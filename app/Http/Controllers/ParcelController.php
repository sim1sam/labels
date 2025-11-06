<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\Courier;

class ParcelController extends Controller
{
    public function index(Request $request)
    {
        $query = Parcel::with('merchant', 'courier');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $parcels = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.parcels', compact('parcels'));
    }

    public function create()
    {
        $merchants = Merchant::where('status', 'active')->get();
        $couriers = Courier::where('status', 'active')->get();
        
        // Get couriers for each merchant
        $merchantCouriers = [];
        foreach ($merchants as $merchant) {
            $merchantCouriers[$merchant->id] = $merchant->couriers()->where('merchant_courier.status', 'active')->get();
        }
        
        return view('admin.parcels.create', compact('merchants', 'couriers', 'merchantCouriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'cod_amount' => 'required|numeric|min:0',
            'courier_id' => 'nullable|exists:couriers,id',
            'notes' => 'nullable|string',
        ]);

        // Automatically create or update customer
        \App\Models\Customer::findOrCreate(
            $request->customer_name,
            $request->mobile_number,
            $request->delivery_address
        );

        $parcel = Parcel::create([
            'parcel_id' => Parcel::generateParcelId(),
            'merchant_id' => $request->merchant_id,
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'delivery_address' => $request->delivery_address,
            'cod_amount' => $request->cod_amount,
            'courier_id' => $request->courier_id,
            'notes' => $request->notes,
            'status' => $request->courier_id ? 'assigned' : 'pending',
            'created_by' => 'admin',
            'tracking_number' => Parcel::generateTrackingNumber(),
        ]);

        // If courier is selected and has API integration, create order with courier
        if ($request->courier_id) {
            $courier = Courier::find($request->courier_id);
            $merchant = Merchant::find($request->merchant_id);
            
            if ($courier && $courier->hasApiIntegration() && $merchant) {
                try {
                    // Get API credentials - first try merchant-specific, then courier defaults
                    $merchantCourier = $merchant->couriers()->where('couriers.id', $courier->id)->first();
                    $apiKey = $merchantCourier ? ($merchantCourier->pivot->merchant_api_key ?: $courier->api_key) : $courier->api_key;
                    $apiSecret = $merchantCourier ? ($merchantCourier->pivot->merchant_api_secret ?: $courier->api_secret) : $courier->api_secret;
                    
                    if (!empty($apiKey) && !empty($apiSecret)) {
                        // Create Steadfast API service instance
                        $steadfastService = new \App\Services\SteadfastApiService(
                            $apiKey,
                            $apiSecret
                        );
                        
                        // Load merchant relationship for API call
                        $parcel->load('merchant');
                        
                        // Create order with Steadfast
                        $result = $steadfastService->createOrder($parcel);
                        
                        if ($result['success']) {
                            // Update parcel with Steadfast tracking info
                            $parcel->update([
                                'courier_tracking_number' => $result['data']['tracking_code'],
                                'status' => $result['data']['status']
                            ]);
                            
                            return redirect()->route('admin.parcels.index')
                                ->with('success', 'Parcel created successfully and order placed with ' . $courier->courier_name . '! Tracking: ' . $result['data']['tracking_code']);
                        } else {
                            // Log error but don't fail the parcel creation
                            \Log::warning('Failed to create Steadfast order for parcel: ' . $parcel->parcel_id, [
                                'error' => $result['message'],
                                'parcel_data' => [
                                    'customer_name' => $parcel->customer_name,
                                    'mobile_number' => $parcel->mobile_number,
                                    'delivery_address' => $parcel->delivery_address
                                ]
                            ]);
                            
                            // Show actual error message from Steadfast API
                            $errorMsg = $result['message'] ?? 'Unknown error occurred';
                            return redirect()->route('admin.parcels.index')
                                ->with('warning', 'Parcel created successfully, but failed to create order with ' . $courier->courier_name . '. Error: ' . $errorMsg);
                        }
                    } else {
                        // API credentials are missing
                        \Log::warning('Steadfast API credentials missing for parcel: ' . $parcel->parcel_id, [
                            'merchant_id' => $merchant->id,
                            'courier_id' => $courier->id,
                            'has_api_key' => !empty($apiKey),
                            'has_api_secret' => !empty($apiSecret)
                        ]);
                        
                        return redirect()->route('admin.parcels.index')
                            ->with('warning', 'Parcel created successfully, but Steadfast API credentials are not configured. Please configure API credentials in courier settings.');
                    }
                } catch (\Exception $e) {
                    \Log::error('Steadfast API error for parcel: ' . $parcel->parcel_id, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return redirect()->route('admin.parcels.index')
                        ->with('warning', 'Parcel created successfully, but courier API error: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.parcels.index')
                        ->with('success', 'Parcel created successfully.');
    }

    public function show(Parcel $parcel)
    {
        $parcel->load('merchant', 'courier');
        return view('admin.parcels.show', compact('parcel'));
    }

    public function edit(Parcel $parcel)
    {
        $merchants = Merchant::where('status', 'active')->get();
        $couriers = Courier::where('status', 'active')->get();
        $parcel->load('merchant', 'courier');
        
        // Get couriers for each merchant
        $merchantCouriers = [];
        foreach ($merchants as $merchant) {
            $merchantCouriers[$merchant->id] = $merchant->couriers()->where('merchant_courier.status', 'active')->get();
        }
        
        return view('admin.parcels.edit', compact('parcel', 'merchants', 'couriers', 'merchantCouriers'));
    }

    public function update(Request $request, Parcel $parcel)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'cod_amount' => 'required|numeric|min:0',
            'courier_id' => 'nullable|exists:couriers,id',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,failed',
        ]);

        $parcel->update([
            'merchant_id' => $request->merchant_id,
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'delivery_address' => $request->delivery_address,
            'cod_amount' => $request->cod_amount,
            'courier_id' => $request->courier_id,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.parcels.index')
                        ->with('success', 'Parcel updated successfully.');
    }

    public function destroy(Parcel $parcel)
    {
        $parcel->delete();

        return redirect()->route('admin.parcels.index')
                        ->with('success', 'Parcel deleted successfully.');
    }

    // API endpoint to get couriers for a specific merchant
    public function getCouriersByMerchant(Request $request)
    {
        try {
            $merchantId = $request->merchant_id;
            
            if (!$merchantId) {
                return response()->json(['error' => 'Merchant ID is required'], 400);
            }
            
            $merchant = Merchant::find($merchantId);
            if (!$merchant) {
                return response()->json(['error' => 'Merchant not found'], 404);
            }
            
            // Get couriers assigned to this merchant
            $couriers = $merchant->couriers()->where('merchant_courier.status', 'active')->get();
            
            return response()->json($couriers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Generate parcel label
    public function generateLabel(Parcel $parcel)
    {
        $parcel->load('merchant', 'courier');
        
        // Get the courier's merchant custom ID if assigned
        $courierMerchantId = null;
        if ($parcel->courier) {
            $pivot = $parcel->merchant->couriers()->where('courier_id', $parcel->courier->id)->first();
            if ($pivot) {
                $courierMerchantId = $pivot->pivot->merchant_custom_id;
            }
        }
        
        // Mark parcel as printed
        $parcel->markAsPrinted();
        
        return view('admin.parcels.label', compact('parcel', 'courierMerchantId'));
    }

    // Bulk parcel creation
    public function bulkCreate()
    {
        $merchants = Merchant::with('couriers')->get();
        $couriers = Courier::all();
        
        // Preload merchant-courier data
        $merchantCouriers = [];
        foreach ($merchants as $merchant) {
            $merchantCouriers[$merchant->id] = $merchant->couriers()->where('merchant_courier.status', 'active')->get();
        }
        
        return view('admin.parcels.bulk-create', compact('merchants', 'couriers', 'merchantCouriers'));
    }

    // Store bulk parcels from CSV
    public function bulkStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        $header = array_shift($csvData); // Remove header row

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($csvData as $index => $row) {
            try {
                // Validate required fields
                if (count($row) < 6) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient data";
                    $errorCount++;
                    continue;
                }

                $merchantName = trim($row[0]);
                $customerName = trim($row[1]);
                $mobileNumber = trim($row[2]);
                $deliveryAddress = trim($row[3]);
                $codAmount = trim($row[4]);
                $courierName = trim($row[5]) ?: null;

                // Validate merchant exists by name
                $merchant = Merchant::where('shop_name', $merchantName)->first();
                if (!$merchant) {
                    $errors[] = "Row " . ($index + 2) . ": Merchant '{$merchantName}' not found";
                    $errorCount++;
                    continue;
                }

                // Validate courier if provided
                $courierId = null;
                if ($courierName) {
                    $courier = Courier::where('courier_name', $courierName)->first();
                    if (!$courier) {
                        $errors[] = "Row " . ($index + 2) . ": Courier '{$courierName}' not found";
                        $errorCount++;
                        continue;
                    }
                    $courierId = $courier->id;
                }

                // Automatically create or update customer
                \App\Models\Customer::findOrCreate(
                    $customerName,
                    $mobileNumber,
                    $deliveryAddress
                );

                // Create parcel
                $parcel = Parcel::create([
                    'parcel_id' => Parcel::generateParcelId(),
                    'merchant_id' => $merchant->id,
                    'customer_name' => $customerName,
                    'mobile_number' => $mobileNumber,
                    'delivery_address' => $deliveryAddress,
                    'cod_amount' => $codAmount,
                    'courier_id' => $courierId,
                    'status' => $courierId ? 'assigned' : 'pending',
                    'created_by' => 'admin',
                    'tracking_number' => Parcel::generateTrackingNumber(),
                ]);

                // If courier is selected and has API integration, create order with courier
                if ($courierId) {
                    $courier = Courier::find($courierId);
                    
                    if ($courier && $courier->hasApiIntegration()) {
                        try {
                            // Get API credentials - first try merchant-specific, then courier defaults
                            $merchantCourier = $merchant->couriers()->where('couriers.id', $courier->id)->first();
                            $apiKey = $merchantCourier ? ($merchantCourier->pivot->merchant_api_key ?: $courier->api_key) : $courier->api_key;
                            $apiSecret = $merchantCourier ? ($merchantCourier->pivot->merchant_api_secret ?: $courier->api_secret) : $courier->api_secret;
                            
                            if (!empty($apiKey) && !empty($apiSecret)) {
                                // Create Steadfast API service instance
                                $steadfastService = new \App\Services\SteadfastApiService(
                                    $apiKey,
                                    $apiSecret
                                );
                                
                                // Load merchant relationship for API call
                                $parcel->load('merchant');
                                
                                // Create order with Steadfast
                                $result = $steadfastService->createOrder($parcel);
                                
                                if ($result['success']) {
                                    // Update parcel with Steadfast tracking info
                                    $parcel->update([
                                        'courier_tracking_number' => $result['data']['tracking_code'],
                                        'status' => $result['data']['status']
                                    ]);
                                } else {
                                    // Log error but don't fail the parcel creation
                                    \Log::warning('Failed to create Steadfast order for parcel: ' . $parcel->parcel_id, [
                                        'error' => $result['message']
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            // Log error but don't fail the parcel creation
                            \Log::error('Steadfast API error for parcel: ' . $parcel->parcel_id, [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }
                }

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                $errorCount++;
            }
        }

        $message = "Bulk upload completed. Success: {$successCount}, Errors: {$errorCount}";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " and " . (count($errors) - 5) . " more...";
            }
        }

        return redirect()->route('admin.parcels.index')
                        ->with($errorCount > 0 ? 'warning' : 'success', $message);
    }

    // Download CSV format
    public function downloadFormat()
    {
        $filename = 'parcel_bulk_upload_format.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add header row
            fputcsv($file, [
                'merchant_name',
                'customer_name', 
                'mobile_number',
                'delivery_address',
                'cod_amount',
                'courier_name'
            ]);

            // Add sample data row
            $sampleMerchant = Merchant::first();
            $sampleCourier = Courier::first();
            fputcsv($file, [
                $sampleMerchant ? $sampleMerchant->shop_name : 'Sample Merchant',
                'John Doe',
                '+1234567890',
                '123 Main St, City, Country',
                '100',
                $sampleCourier ? $sampleCourier->courier_name : 'Sample Courier'
            ]);

            // Add empty rows for user input
            for ($i = 0; $i < 5; $i++) {
                fputcsv($file, ['', '', '', '', '', '']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Bulk label printing
    public function bulkPrint()
    {
        return view('admin.parcels.bulk-print');
    }

    // Process bulk label printing
    public function processBulkPrint(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,assigned,picked_up,in_transit,delivered,failed',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $status = $request->status;

        // Build query
        $query = Parcel::with('merchant', 'courier')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($status) {
            $query->where('status', $status);
        }

        $parcels = $query->get();

        if ($parcels->isEmpty()) {
            return redirect()->route('admin.parcels.bulk.print')
                           ->with('error', 'No parcels found for the selected date range and criteria.');
        }

        // Mark all parcels as printed
        $parcels->each(function($parcel) {
            $parcel->markAsPrinted();
        });

        return view('admin.parcels.bulk-labels', compact('parcels', 'startDate', 'endDate', 'status'));
    }

    // Bulk status update
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'parcel_ids' => 'required|array',
            'parcel_ids.*' => 'exists:parcels,id',
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,failed',
        ]);

        $parcelIds = $request->parcel_ids;
        $newStatus = $request->status;

        $updatedCount = Parcel::whereIn('id', $parcelIds)->update(['status' => $newStatus]);

        $statusText = ucfirst(str_replace('_', ' ', $newStatus));
        
        return redirect()->route('admin.parcels.index')
                        ->with('success', "Successfully updated {$updatedCount} parcels to {$statusText} status.");
    }
}
