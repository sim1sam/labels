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

        Parcel::create([
            'parcel_id' => Parcel::generateParcelId(),
            'merchant_id' => $merchant->id,
            'customer_name' => $request->customer_name,
            'mobile_number' => $request->mobile_number,
            'delivery_address' => $request->delivery_address,
            'cod_amount' => $request->cod_amount,
            'courier_id' => $request->courier_id,
            'status' => 'pending',
            'created_by' => 'merchant',
        ]);

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
}