<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')
                        ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        $customer->load('parcels.merchant', 'parcels.courier');
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('admin.customers.index')
                        ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
                        ->with('success', 'Customer deleted successfully!');
    }

    public function getByMobile($mobile)
    {
        $customer = Customer::where('mobile_number', $mobile)->first();
        
        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'name' => $customer->customer_name,
                    'address' => $customer->address,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }
}