<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get dashboard statistics
        $totalCouriers = \App\Models\Courier::count();
        $totalMerchants = \App\Models\Merchant::count();
        $totalParcels = \App\Models\Parcel::count();
        
        // Calculate total revenue from parcels
        $totalRevenue = \App\Models\Parcel::sum('cod_amount');
        
        // Get recent parcels
        $recentParcels = \App\Models\Parcel::with(['merchant', 'courier'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalCouriers',
            'totalMerchants',
            'totalParcels',
            'totalRevenue',
            'recentParcels'
        ));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function orders()
    {
        // Mock orders data for demo
        $orders = [
            (object)[
                'id' => 1,
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'status' => 'pending',
                'total' => 25.99,
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'id' => 2,
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'status' => 'delivered',
                'total' => 45.50,
                'created_at' => now()->subHours(5)
            ],
            (object)[
                'id' => 3,
                'customer_name' => 'Bob Johnson',
                'customer_email' => 'bob@example.com',
                'status' => 'in_transit',
                'total' => 78.25,
                'created_at' => now()->subHours(8)
            ]
        ];

        return view('admin.orders', compact('orders'));
    }

    public function deliveries()
    {
        // Mock deliveries data for demo
        $deliveries = [
            (object)[
                'id' => 1,
                'order_id' => 1,
                'courier_name' => 'Mike Wilson',
                'status' => 'assigned',
                'pickup_address' => '123 Main St, City',
                'delivery_address' => '456 Oak Ave, City',
                'estimated_time' => '2:30 PM'
            ],
            (object)[
                'id' => 2,
                'order_id' => 2,
                'courier_name' => 'Sarah Davis',
                'status' => 'in_transit',
                'pickup_address' => '789 Pine St, City',
                'delivery_address' => '321 Elm St, City',
                'estimated_time' => '3:15 PM'
            ]
        ];

        return view('admin.deliveries', compact('deliveries'));
    }

    public function couriers()
    {
        // Mock couriers data for demo
        $couriers = [
            (object)[
                'id' => 1,
                'name' => 'Mike Wilson',
                'email' => 'mike@courier.com',
                'phone' => '+1-555-0123',
                'status' => 'active',
                'vehicle_type' => 'Motorcycle',
                'rating' => 4.8,
                'total_deliveries' => 156
            ],
            (object)[
                'id' => 2,
                'name' => 'Sarah Davis',
                'email' => 'sarah@courier.com',
                'phone' => '+1-555-0124',
                'status' => 'active',
                'vehicle_type' => 'Van',
                'rating' => 4.9,
                'total_deliveries' => 203
            ]
        ];

        return view('admin.couriers', compact('couriers'));
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
