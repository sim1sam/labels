@extends('layouts.merchant')

@section('title', 'My Printed Parcels Report')
@section('page-title', 'Printed Parcels Report')

@section('content')
<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="merchant-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $totalPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total Printed</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-print"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $todayPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Today</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $thisWeekPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">This Week</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
    </div>

    <div class="merchant-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $thisMonthPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">This Month</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-filter"></i>
            Filter Options
        </h3>
    </div>
    
    <form method="GET" action="{{ route('merchant.reports.printed-parcels') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Start Date</label>
            <input type="date" 
                   name="start_date" 
                   value="{{ request('start_date') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">End Date</label>
            <input type="date" 
                   name="end_date" 
                   value="{{ request('end_date') }}"
                   style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Status</label>
            <select name="status" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="merchant-btn merchant-btn-primary">
                <i class="fas fa-search"></i>
                Filter
            </button>
            
            <a href="{{ route('merchant.reports.printed-parcels') }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-times"></i>
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Results and Actions -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-print"></i>
            My Printed Parcels ({{ $parcels->total() }} found)
        </h3>
        
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('merchant.reports.printed-parcels.download', request()->query()) }}" 
               class="merchant-btn merchant-btn-success">
                <i class="fas fa-download"></i>
                Download CSV
            </a>
        </div>
    </div>

    @if($parcels->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0; background: #f7fafc;">
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Parcel ID</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Customer</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Courier</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">COD Amount</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Status</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Printed Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcels as $parcel)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px;">
                                <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ $parcel->parcel_id }}
                                </span>
                            </td>
                            <td style="padding: 15px; color: #2d3748;">
                                <div style="font-weight: 600;">{{ $parcel->customer_name }}</div>
                                <div style="font-size: 12px; color: #718096;">{{ $parcel->mobile_number }}</div>
                            </td>
                            <td style="padding: 15px; color: #2d3748;">
                                {{ $parcel->courier->courier_name ?? 'N/A' }}
                            </td>
                            <td style="padding: 15px; color: #2d3748; font-weight: 600;">
                                {{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}
                            </td>
                            <td style="padding: 15px;">
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
                            <td style="padding: 15px; color: #718096; font-size: 14px;">
                                <div>{{ $parcel->printed_at->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: #a0aec0;">{{ $parcel->printed_at->format('h:i A') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $parcels->appends(request()->query())->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #718096;">
            <i class="fas fa-print" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <h3 style="margin: 0 0 8px 0; color: #4a5568;">No Printed Parcels Found</h3>
            <p style="margin: 0;">No printed parcels match your current filters.</p>
        </div>
    @endif
</div>
@endsection
