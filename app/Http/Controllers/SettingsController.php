<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $currentCurrency = Setting::getCurrency();
        $availableCurrencies = Setting::getAvailableCurrencies();
        
        return view('admin.settings', compact('currentCurrency', 'availableCurrencies'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|in:' . implode(',', array_keys(Setting::getAvailableCurrencies())),
        ]);

        Setting::setCurrency($request->currency);

        return redirect()->route('admin.settings')
                        ->with('success', 'Settings updated successfully!');
    }
}