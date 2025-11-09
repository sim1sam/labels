<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Merchant;

class ProfileController extends Controller
{
    /**
     * Show the profile form
     */
    public function show()
    {
        $user = Auth::user();
        
        if ($user->isMerchant()) {
            $merchant = $user->merchant;
            return view('profile.merchant', compact('user', 'merchant'));
        }
        
        return view('profile.admin', compact('user'));
    }

    /**
     * Update admin profile (name, email, password)
     */
    public function updateAdmin(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update merchant profile (all fields)
     */
    public function updateMerchant(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchant;
        
        $request->validate([
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
            
            // Merchant fields
            'shop_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        // Handle logo upload - pathwise storage (not storage link)
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
            
            // Create directory if it doesn't exist
            $uploadDir = public_path('uploads/merchants');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Move file directly to public/uploads directory (pathwise)
            $logo->move($uploadDir, $logoName);
            
            // Store relative path (without leading slash)
            $merchant->logo = 'uploads/merchants/' . $logoName;
        }

        // Update merchant data
        $merchant->shop_name = $request->shop_name;
        $merchant->phone = $request->phone;
        $merchant->address = $request->address;
        $merchant->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
