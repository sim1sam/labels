@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon couriers">
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalCouriers ?? 0 }}</div>
        <div class="stat-label">Total Couriers</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon merchants">
                <i class="fas fa-store"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalMerchants ?? 0 }}</div>
        <div class="stat-label">Total Merchants</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon parcels">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalParcels ?? 0 }}</div>
        <div class="stat-label">Total Parcels</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon revenue">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalRevenue ?? 0, 2) }} {{ \App\Models\Setting::getCurrency() }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<!-- Recent Parcels -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock"></i>
            Recent Parcels
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Parcel ID</th>
                        <th>Customer</th>
                        <th>Merchant</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentParcels ?? [] as $parcel)
                    <tr>
                        <td style="font-weight: 600; color: #2d3748;">{{ $parcel->parcel_id }}</td>
                        <td>
                            <div>
                                <div style="font-weight: 600;">{{ $parcel->customer_name }}</div>
                                <div style="font-size: 12px; color: #718096;">{{ $parcel->mobile_number }}</div>
                            </div>
                        </td>
                        <td>{{ $parcel->merchant->shop_name ?? 'N/A' }}</td>
                        <td style="font-weight: 600; color: #2d3748;">{{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}</td>
                        <td>
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
                        <td style="color: #718096; font-size: 14px;">{{ $parcel->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #718096; padding: 20px;">
                            No recent parcels found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Quick Actions
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary">
                <i class="fas fa-motorcycle"></i>
                Add New Courier
            </a>
            <a href="{{ route('admin.merchants.create') }}" class="btn btn-success">
                <i class="fas fa-store"></i>
                Add New Merchant
            </a>
            <a href="{{ route('admin.parcels.create') }}" class="btn btn-primary">
                <i class="fas fa-box"></i>
                Create Parcel
            </a>
            <a href="{{ route('admin.reports.printed-parcels') }}" class="btn btn-success">
                <i class="fas fa-chart-line"></i>
                View Reports
            </a>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-server"></i>
            System Status
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Online</div>
                <div style="color: #718096; font-size: 14px;">API Status</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">99.9%</div>
                <div style="color: #718096; font-size: 14px;">Uptime</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Fast</div>
                <div style="color: #718096; font-size: 14px;">Response Time</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Secure</div>
                <div style="color: #718096; font-size: 14px;">SSL Status</div>
            </div>
        </div>
    </div>
</div>
@endsection
