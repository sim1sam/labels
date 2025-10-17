<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantDashboardController extends Controller
{

    public function dashboard()
    {
        $merchant = auth()->user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('login')->with('error', 'Merchant account not found.');
        }

        // Get statistics
        $totalParcels = Parcel::where('merchant_id', $merchant->id)->count();
        $pendingParcels = Parcel::where('merchant_id', $merchant->id)->where('status', 'pending')->count();
        $deliveredParcels = Parcel::where('merchant_id', $merchant->id)->where('status', 'delivered')->count();
        $printedParcels = Parcel::where('merchant_id', $merchant->id)->whereNotNull('printed_at')->count();

        // Get recent parcels
        $recentParcels = Parcel::where('merchant_id', $merchant->id)
                              ->with(['courier'])
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get();

        return view('merchant.dashboard', compact(
            'totalParcels',
            'pendingParcels', 
            'deliveredParcels',
            'printedParcels',
            'recentParcels'
        ));
    }
}