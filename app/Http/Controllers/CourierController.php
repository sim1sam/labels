<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.couriers', compact('couriers'));
    }

    public function create()
    {
        return view('admin.couriers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'courier_name' => 'required|string|max:255',
        ]);

        Courier::create([
            'courier_name' => $request->courier_name,
            'vehicle_type' => 'motorcycle', // Default value
            'status' => 'active', // Default value
        ]);

        return redirect()->route('admin.couriers.index')
                        ->with('success', 'Courier created successfully.');
    }

    public function show(Courier $courier)
    {
        return view('admin.couriers.show', compact('courier'));
    }

    public function edit(Courier $courier)
    {
        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, Courier $courier)
    {
        $request->validate([
            'courier_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vehicle_type' => 'required|in:motorcycle,van,bike',
            'status' => 'required|in:active,inactive,busy',
        ]);

        $courier->update($request->all());

        return redirect()->route('admin.couriers.index')
                        ->with('success', 'Courier updated successfully.');
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();

        return redirect()->route('admin.couriers.index')
                        ->with('success', 'Courier deleted successfully.');
    }
}
