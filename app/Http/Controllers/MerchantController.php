<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with('user', 'couriers')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.merchants', compact('merchants'));
    }

    public function create()
    {
        $couriers = Courier::where('status', 'active')->get();
        return view('admin.merchants.create', compact('couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:merchants,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
            'couriers' => 'required|array|min:1',
            'couriers.*' => 'exists:couriers,id',
            'merchant_custom_ids' => 'required|array|min:1',
            'merchant_custom_ids.*' => 'required|string|max:255',
            'merchant_api_keys' => 'nullable|array',
            'merchant_api_keys.*' => 'nullable|string|max:255',
            'merchant_api_secrets' => 'nullable|array',
            'merchant_api_secrets.*' => 'nullable|string|max:255',
            'courier_status' => 'nullable|array',
            'courier_status.*' => 'nullable|in:active,inactive',
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'nullable|integer',
        ]);

        // Handle logo upload - pathwise storage (not storage link)
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            
            // Ensure directory exists
            $uploadDir = public_path('uploads/merchants');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move file directly to public/uploads directory (pathwise)
            $logo->move($uploadDir, $logoName);
            
            // Store relative path (without leading slash)
            $logoPath = 'uploads/merchants/' . $logoName;
        }

        // Create new user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => User::TYPE_MERCHANT,
            'email_verified_at' => now(),
        ]);

        // Create merchant
        $merchant = Merchant::create([
            'merchant_id' => Merchant::generateMerchantId(),
            'shop_name' => $request->name, // Use the same name for shop_name
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
            'user_id' => $user->id,
        ]);

        // Attach couriers with custom merchant IDs and API credentials
        $courierData = [];
        foreach ($request->couriers as $index => $courierId) {
            $courierData[$courierId] = [
                'merchant_custom_id' => $request->merchant_custom_ids[$index],
                'status' => $request->courier_status[$index] ?? 'active',
                'merchant_api_key' => $request->merchant_api_keys[$index] ?? null,
                'merchant_api_secret' => $request->merchant_api_secrets[$index] ?? null,
                'is_primary' => in_array($index, $request->is_primary ?? []),
            ];
        }

        $merchant->couriers()->attach($courierData);

        return redirect()->route('admin.merchants.index')
                        ->with('success', 'Merchant and user account created successfully.');
    }

    public function show(Merchant $merchant)
    {
        $merchant->load('user', 'couriers');
        return view('admin.merchants.show', compact('merchant'));
    }

    public function edit(Merchant $merchant)
    {
        $couriers = Courier::where('status', 'active')->get();
        $merchant->load('couriers', 'user');
        return view('admin.merchants.edit', compact('merchant', 'couriers'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants,email,' . $merchant->id . '|unique:users,email,' . $merchant->user_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'couriers' => 'required|array|min:1',
            'couriers.*' => 'exists:couriers,id',
            'merchant_custom_ids' => 'required|array|min:1',
            'merchant_custom_ids.*' => 'required|string|max:255',
            'merchant_api_keys' => 'nullable|array',
            'merchant_api_keys.*' => 'nullable|string|max:255',
            'merchant_api_secrets' => 'nullable|array',
            'merchant_api_secrets.*' => 'nullable|string|max:255',
            'courier_status' => 'nullable|array',
            'courier_status.*' => 'nullable|in:active,inactive',
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'nullable|integer',
        ]);

        // Handle logo upload - pathwise storage (not storage link)
        $logoPath = $merchant->logo; // Keep existing logo if no new one uploaded
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($merchant->logo) {
                $oldLogoPath = public_path($merchant->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            
            // Ensure directory exists
            $uploadDir = public_path('uploads/merchants');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move file directly to public/uploads directory (pathwise)
            $logo->move($uploadDir, $logoName);
            
            // Store relative path (without leading slash)
            $logoPath = 'uploads/merchants/' . $logoName;
        }

        // Update user account
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $merchant->user->update($userData);

        // Update merchant
        $merchant->update([
            'shop_name' => $request->name, // Use the same name for shop_name
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'logo' => $logoPath,
        ]);

        // Update courier relationships
        $courierData = [];
        foreach ($request->couriers as $index => $courierId) {
            $courierData[$courierId] = [
                'merchant_custom_id' => $request->merchant_custom_ids[$index],
                'status' => $request->courier_status[$index] ?? 'active',
                'merchant_api_key' => $request->merchant_api_keys[$index] ?? null,
                'merchant_api_secret' => $request->merchant_api_secrets[$index] ?? null,
                'is_primary' => in_array($index, $request->is_primary ?? []),
            ];
        }

        $merchant->couriers()->sync($courierData);

        return redirect()->route('admin.merchants.index')
                        ->with('success', 'Merchant updated successfully.');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();

        return redirect()->route('admin.merchants.index')
                        ->with('success', 'Merchant deleted successfully.');
    }
}
