@extends('layouts.admin')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="margin: 0; color: #2d3748; font-size: 28px; font-weight: 700;">Customer Details</h1>
        <p style="margin: 5px 0 0 0; color: #718096; font-size: 16px;">View customer information and parcel history</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i>
            Edit Customer
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Customers
        </a>
    </div>
</div>

<!-- Customer Information -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user"></i>
            Customer Information
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Customer Name</label>
                    <div style="font-size: 16px; color: #2d3748;">{{ $customer->customer_name }}</div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Mobile Number</label>
                    <div style="font-size: 16px; color: #2d3748;">{{ $customer->mobile_number }}</div>
                </div>
                
            </div>
            
            <div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Address</label>
                    <div style="font-size: 16px; color: #2d3748; line-height: 1.5;">{{ $customer->address }}</div>
                </div>
                
            </div>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #2d3748;">{{ $customer->parcels->count() }}</div>
                    <div style="color: #718096; font-size: 14px;">Total Parcels</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #2d3748;">{{ $customer->created_at->format('M d, Y') }}</div>
                    <div style="color: #718096; font-size: 14px;">Customer Since</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #2d3748;">{{ $customer->parcels->where('status', 'delivered')->count() }}</div>
                    <div style="color: #718096; font-size: 14px;">Delivered</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #2d3748;">{{ $customer->parcels->where('status', 'pending')->count() }}</div>
                    <div style="color: #718096; font-size: 14px;">Pending</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Parcel History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            Parcel History
        </h3>
    </div>
    <div class="card-body">
        @if($customer->parcels->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Parcel ID</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Courier</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Amount</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Created</th>
                            <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->parcels as $parcel)
                        <tr>
                            <td style="padding: 15px; color: #2d3748; font-weight: 600;">{{ $parcel->parcel_id }}</td>
                            <td style="padding: 15px; color: #2d3748;">{{ $parcel->merchant->shop_name ?? 'N/A' }}</td>
                            <td style="padding: 15px; color: #2d3748;">{{ $parcel->courier->courier_name ?? 'Not assigned' }}</td>
                            <td style="padding: 15px; color: #2d3748; font-weight: 600;">{{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}</td>
                            <td style="padding: 15px;">
                                @php
                                    $badgeColors = [
                                        'pending' => '#fef5e7',
                                        'assigned' => '#e6f3ff',
                                        'picked_up' => '#f0fff4',
                                        'in_transit' => '#e6fffa',
                                        'delivered' => '#f0fff4',
                                        'failed' => '#fed7d7'
                                    ];
                                    $textColors = [
                                        'pending' => '#744210',
                                        'assigned' => '#2c5282',
                                        'picked_up' => '#22543d',
                                        'in_transit' => '#234e52',
                                        'delivered' => '#22543d',
                                        'failed' => '#742a2a'
                                    ];
                                @endphp
                                <span style="background: {{ $badgeColors[$parcel->status] ?? '#f7fafc' }}; color: {{ $textColors[$parcel->status] ?? '#4a5568' }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ $parcel->getStatusDisplayText() }}
                                </span>
                            </td>
                            <td style="padding: 15px; color: #718096; font-size: 14px;">{{ $parcel->created_at->format('M d, Y') }}</td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="{{ route('admin.parcels.show', $parcel) }}" class="btn btn-sm btn-info" title="View Parcel">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #718096;">
                <i class="fas fa-box" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e0;"></i>
                <h3 style="margin: 0 0 10px 0; color: #4a5568;">No Parcels Found</h3>
                <p style="margin: 0;">This customer hasn't created any parcels yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
