<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Courier;
use Illuminate\Http\Request;

class MerchantParcelController extends Controller
{

    public function index(Request $request)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        $query = Parcel::where('merchant_id', $merchant->id)
                      ->with(['courier']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parcels = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('merchant.parcels.index', compact('parcels'));
    }

    public function create()
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        // Get couriers assigned to this merchant
        $couriers = $merchant->couriers()->where('merchant_courier.status', 'active')->get();

        return view('merchant.parcels.create', compact('couriers'));
    }

    public function store(Request $request)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'cod_amount' => 'required|numeric|min:0',
            'courier_id' => 'nullable|exists:couriers,id',
        ]);

        // Verify courier is assigned to this merchant
        if ($request->courier_id) {
            $isAssigned = $merchant->couriers()->where('courier_id', $request->courier_id)->exists();
            if (!$isAssigned) {
                return back()->withErrors(['courier_id' => 'Selected courier is not assigned to your account.']);
            }
        }

        // Automatically create or update customer
        \App\Models\Customer::findOrCreate(
            $request->customer_name,
            $request->mobile_number,
            $request->delivery_address
        );

        // Create parcel
        $parcel = Parcel::create([
            'parcel_id' => Parcel::generateParcelId(),
            'merchant_id' => $merchant->id,
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'delivery_address' => $request->delivery_address,
            'cod_amount' => $request->cod_amount,
            'courier_id' => $request->courier_id,
            'status' => 'pending',
            'created_by' => 'merchant',
            'tracking_number' => Parcel::generateTrackingNumber(),
        ]);

        // If courier is selected and has API integration, create order with courier
        if ($request->courier_id) {
            $courier = \App\Models\Courier::find($request->courier_id);
            
            if ($courier && $courier->hasApiIntegration()) {
                try {
                    // Get API credentials - first try merchant-specific, then courier defaults
                    $merchantCourier = $merchant->couriers()->where('couriers.id', $courier->id)->first();
                    $apiKey = $merchantCourier ? ($merchantCourier->pivot->merchant_api_key ?: $courier->api_key) : $courier->api_key;
                    $apiSecret = $merchantCourier ? ($merchantCourier->pivot->merchant_api_secret ?: $courier->api_secret) : $courier->api_secret;
                    
                    if (!empty($apiKey) && !empty($apiSecret)) {
                        // Log API credentials being used
                        \Log::info('Using API credentials for parcel creation', [
                            'merchant_id' => $merchant->id,
                            'courier_id' => $courier->id,
                            'api_key_source' => $merchantCourier && $merchantCourier->pivot->merchant_api_key ? 'merchant' : 'courier',
                            'api_key' => substr($apiKey, 0, 10) . '...',
                            'parcel_id' => $parcel->parcel_id
                        ]);
                        
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
                            
                            return redirect()->route('merchant.parcels.index')
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
                            return redirect()->route('merchant.parcels.index')
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
                        
                        return redirect()->route('merchant.parcels.index')
                            ->with('warning', 'Parcel created successfully, but Steadfast API credentials are not configured. Please configure API credentials in courier settings.');
                    }
                } catch (\Exception $e) {
                    \Log::error('Steadfast API error for parcel: ' . $parcel->parcel_id, [
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('merchant.parcels.index')
                        ->with('warning', 'Parcel created successfully, but courier API is temporarily unavailable.');
                }
            }
        }

        return redirect()->route('merchant.parcels.index')
                        ->with('success', 'Parcel created successfully!');
    }

    public function show(Parcel $parcel)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant || $parcel->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized access to this parcel.');
        }

        $parcel->load(['courier']);

        return view('merchant.parcels.show', compact('parcel'));
    }

    public function edit(Parcel $parcel)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant || $parcel->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized access to this parcel.');
        }

        // Get couriers assigned to this merchant
        $couriers = $merchant->couriers()->where('merchant_courier.status', 'active')->get();

        return view('merchant.parcels.edit', compact('parcel', 'couriers'));
    }

    public function update(Request $request, Parcel $parcel)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant || $parcel->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized access to this parcel.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'cod_amount' => 'required|numeric|min:0',
            'courier_id' => 'nullable|exists:couriers,id',
        ]);

        // Verify courier is assigned to this merchant
        if ($request->courier_id) {
            $isAssigned = $merchant->couriers()->where('courier_id', $request->courier_id)->exists();
            if (!$isAssigned) {
                return back()->withErrors(['courier_id' => 'Selected courier is not assigned to your account.']);
            }
        }

        $parcel->update([
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'delivery_address' => $request->delivery_address,
            'cod_amount' => $request->cod_amount,
            'courier_id' => $request->courier_id,
        ]);

        return redirect()->route('merchant.parcels.index')
                        ->with('success', 'Parcel updated successfully!');
    }

    public function destroy(Parcel $parcel)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant || $parcel->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized access to this parcel.');
        }

        $parcel->delete();

        return redirect()->route('merchant.parcels.index')
                        ->with('success', 'Parcel deleted successfully!');
    }

    public function bulkCreate()
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        // Get couriers assigned to this merchant
        $couriers = $merchant->couriers()->where('merchant_courier.status', 'active')->get();

        return view('merchant.parcels.bulk-create', compact('couriers'));
    }

    public function downloadFormat()
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        // Get couriers assigned to this merchant
        $couriers = $merchant->couriers()->where('merchant_courier.status', 'active')->get();

        $filename = 'merchant_parcel_upload_format.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($couriers) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'customer_name',
                'mobile_number', 
                'delivery_address',
                'cod_amount',
                'courier_name'
            ]);

            // Sample data
            fputcsv($file, [
                'John Doe',
                '+1234567890',
                '123 Main Street, City, State',
                '100',
                $couriers->first() ? $couriers->first()->courier_name : 'Sample Courier'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkStore(Request $request)
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $csvData = [];
        
        if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        if (empty($csvData)) {
            return back()->withErrors(['csv_file' => 'CSV file is empty or invalid.']);
        }

        // Remove header row
        array_shift($csvData);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $steadfastSuccessCount = 0;
        $steadfastErrorCount = 0;
        $steadfastErrors = [];

        foreach ($csvData as $index => $row) {
            try {
                // Validate required fields
                if (count($row) < 5) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient data";
                    $errorCount++;
                    continue;
                }

                $customerName = trim($row[0]);
                $mobileNumber = trim($row[1]);
                $deliveryAddress = trim($row[2]);
                $codAmount = trim($row[3]);
                $courierName = trim($row[4]) ?: null;

                // Validate courier if provided
                $courierId = null;
                if ($courierName) {
                    $courier = Courier::where('courier_name', $courierName)->first();
                    if (!$courier) {
                        $errors[] = "Row " . ($index + 2) . ": Courier '{$courierName}' not found";
                        $errorCount++;
                        continue;
                    }
                    
                    // Verify courier is assigned to this merchant
                    $isAssigned = $merchant->couriers()->where('courier_id', $courier->id)->exists();
                    if (!$isAssigned) {
                        $errors[] = "Row " . ($index + 2) . ": Courier '{$courierName}' is not assigned to your account";
                        $errorCount++;
                        continue;
                    }
                    
                    $courierId = $courier->id;
                }

                // Validate COD amount
                if (!is_numeric($codAmount) || $codAmount < 0) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid COD amount '{$codAmount}'";
                    $errorCount++;
                    continue;
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
                    'status' => 'pending',
                    'created_by' => 'merchant',
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
                                \Log::info('Bulk upload: Attempting Steadfast upload for parcel', [
                                    'parcel_id' => $parcel->parcel_id,
                                    'courier_id' => $courier->id,
                                    'merchant_id' => $merchant->id,
                                    'api_key_source' => $merchantCourier && $merchantCourier->pivot->merchant_api_key ? 'merchant' : 'courier'
                                ]);
                                
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
                                    
                                    $steadfastSuccessCount++;
                                    \Log::info('Bulk upload: Steadfast upload successful', [
                                        'parcel_id' => $parcel->parcel_id,
                                        'tracking_code' => $result['data']['tracking_code']
                                    ]);
                                } else {
                                    // Log error but don't fail the parcel creation
                                    $steadfastErrorCount++;
                                    $steadfastErrors[] = "Parcel {$parcel->parcel_id}: " . $result['message'];
                                    \Log::warning('Bulk upload: Failed to create Steadfast order for parcel: ' . $parcel->parcel_id, [
                                        'error' => $result['message'],
                                        'parcel_data' => [
                                            'customer_name' => $parcel->customer_name,
                                            'mobile_number' => $parcel->mobile_number
                                        ]
                                    ]);
                                }
                            } else {
                                \Log::warning('Bulk upload: Steadfast API credentials missing', [
                                    'parcel_id' => $parcel->parcel_id,
                                    'courier_id' => $courier->id,
                                    'has_api_key' => !empty($apiKey),
                                    'has_api_secret' => !empty($apiSecret)
                                ]);
                            }
                        } catch (\Exception $e) {
                            // Log error but don't fail the parcel creation
                            $steadfastErrorCount++;
                            $steadfastErrors[] = "Parcel {$parcel->parcel_id}: " . $e->getMessage();
                            \Log::error('Bulk upload: Steadfast API error for parcel: ' . $parcel->parcel_id, [
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        \Log::info('Bulk upload: Courier has no API integration', [
                            'parcel_id' => $parcel->parcel_id,
                            'courier_id' => $courierId,
                            'has_api_integration' => $courier ? $courier->hasApiIntegration() : false
                        ]);
                    }
                }

                $successCount++;

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                $errorCount++;
            }
        }

        $message = "Successfully created {$successCount} parcels from CSV file!";
        if ($errorCount > 0) {
            $message .= " {$errorCount} errors occurred.";
        }
        
        // Add Steadfast upload summary
        if ($steadfastSuccessCount > 0 || $steadfastErrorCount > 0) {
            $message .= " | Steadfast: {$steadfastSuccessCount} uploaded";
            if ($steadfastErrorCount > 0) {
                $message .= ", {$steadfastErrorCount} failed";
                if (!empty($steadfastErrors)) {
                    $message .= " (" . implode('; ', array_slice($steadfastErrors, 0, 3));
                    if (count($steadfastErrors) > 3) {
                        $message .= " and " . (count($steadfastErrors) - 3) . " more";
                    }
                    $message .= ")";
                }
            }
        }

        if ($errorCount > 0) {
            return back()->withErrors(['csv_file' => implode('; ', $errors)])
                        ->with('success', $message);
        }

        return redirect()->route('merchant.parcels.index')
                        ->with('success', $message);
    }
}