@extends('layouts.merchant')

@section('title', 'Merchant Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Message -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h2 class="merchant-card-title">
            <i class="fas fa-home"></i>
            Welcome, {{ auth()->user()->name }}!
        </h2>
    </div>
    <p style="color: #718096; font-size: 16px; line-height: 1.6;">
        Manage your parcels, track deliveries, and view reports from your merchant dashboard.
    </p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="merchant-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $totalParcels }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total Parcels</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $pendingParcels }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Pending</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $deliveredParcels }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Delivered</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $printedParcels }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Printed Labels</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-print"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-bolt"></i>
            Quick Actions
        </h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <a href="{{ route('merchant.parcels.create') }}" class="merchant-btn merchant-btn-primary">
            <i class="fas fa-plus"></i>
            Create New Parcel
        </a>
        <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-success">
            <i class="fas fa-list"></i>
            View All Parcels
        </a>
        <a href="{{ route('merchant.reports.printed-parcels') }}" class="merchant-btn merchant-btn-warning">
            <i class="fas fa-chart-bar"></i>
            View Reports
        </a>
    </div>
</div>

<!-- Recent Parcels -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-history"></i>
            Recent Parcels
        </h3>
        <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
            <i class="fas fa-eye"></i>
            View All
        </a>
    </div>
    
    @if($recentParcels->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4a5568;">Parcel ID</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4a5568;">Customer</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4a5568;">Amount</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4a5568;">Status</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #4a5568;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentParcels as $parcel)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px;">
                                <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ $parcel->parcel_id }}
                                </span>
                            </td>
                            <td style="padding: 12px; color: #2d3748;">
                                <div style="font-weight: 600;">{{ $parcel->customer_name }}</div>
                                <div style="font-size: 12px; color: #718096;">{{ $parcel->mobile_number }}</div>
                            </td>
                            <td style="padding: 12px; color: #2d3748; font-weight: 600;">
                                {{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}
                            </td>
                            <td style="padding: 12px;">
                                @php
                                    $badgeColors = [
                                        'pending' => '#fef5e7',
                                        'assigned' => '#e6f3ff',
                                        'picked_up' => '#e6f3ff',
                                        'in_transit' => '#f7fafc',
                                        'delivered' => '#c6f6d5',
                                        'failed' => '#fed7d7'
                                    ];
                                    $textColors = [
                                        'pending' => '#744210',
                                        'assigned' => '#234e52',
                                        'picked_up' => '#1a365d',
                                        'in_transit' => '#4a5568',
                                        'delivered' => '#22543d',
                                        'failed' => '#742a2a'
                                    ];
                                @endphp
                                <span style="background: {{ $badgeColors[$parcel->status] ?? '#f7fafc' }}; color: {{ $textColors[$parcel->status] ?? '#4a5568' }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ $parcel->getStatusDisplayText() }}
                                </span>
                            </td>
                            <td style="padding: 12px; color: #718096; font-size: 14px;">
                                {{ $parcel->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #718096;">
            <i class="fas fa-box" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <h3 style="margin: 0 0 8px 0; color: #4a5568;">No Parcels Yet</h3>
            <p style="margin: 0 0 20px 0;">Create your first parcel to get started.</p>
            <a href="{{ route('merchant.parcels.create') }}" class="merchant-btn merchant-btn-primary">
                <i class="fas fa-plus"></i>
                Create First Parcel
            </a>
        </div>
    @endif
</div>
@endsection
