@extends('layouts.admin')

@section('title', 'Printed Parcels Report')

@section('content')
<div class="admin-content">
    <div class="admin-header">
        <h1>Printed Parcels Report</h1>
        <p>View and download reports of all printed parcel labels</p>
    </div>

    <div class="admin-body">
        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $totalPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total Printed</div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $todayPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Today</div>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $thisWeekPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">This Week</div>
            </div>
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $thisMonthPrinted }}</div>
                <div style="font-size: 14px; opacity: 0.9;">This Month</div>
            </div>
        </div>

        <!-- Filters -->
        <div style="background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="color: #2d3748; margin-bottom: 20px; display: flex; align-items: center;">
                <i class="fas fa-filter" style="color: #4299e1; margin-right: 10px;"></i>
                Filter Options
            </h3>
            
            <form method="GET" action="{{ route('admin.reports.printed-parcels') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
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

                <div>
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Merchant</label>
                    <select name="merchant_id" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                        <option value="">All Merchants</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                {{ $merchant->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="background: #4299e1; color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center;">
                        <i class="fas fa-search" style="margin-right: 8px;"></i>
                        Filter
                    </button>
                    
                    <a href="{{ route('admin.reports.printed-parcels') }}" style="background: #e2e8f0; color: #4a5568; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
                        <i class="fas fa-times" style="margin-right: 8px;"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Results and Actions -->
        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h3 style="color: #2d3748; margin: 0;">
                    <i class="fas fa-print" style="color: #ed8936; margin-right: 8px;"></i>
                    Printed Parcels ({{ $parcels->total() }} found)
                </h3>
                
                <div style="display: flex; gap: 10px; margin-left: auto;">
                    <a href="{{ route('admin.reports.printed-parcels.download', request()->query()) }}" 
                       style="background: #38a169; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center;">
                        <i class="fas fa-download" style="margin-right: 8px;"></i>
                        Download CSV
                    </a>
                </div>
            </div>

            @if($parcels->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                        <thead style="background: #f7fafc;">
                            <tr>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Parcel ID</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Customer</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Courier</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">COD Amount</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                                <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Printed Date</th>
                                <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parcels as $parcel)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 15px; color: #4a5568;">
                                        <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            {{ $parcel->parcel_id }}
                                        </span>
                                    </td>
                                    <td style="padding: 15px; color: #2d3748;">
                                        <div style="font-weight: 600;">{{ $parcel->customer_name }}</div>
                                        <div style="font-size: 12px; color: #718096;">{{ $parcel->mobile_number }}</div>
                                    </td>
                                    <td style="padding: 15px; color: #2d3748;">
                                        {{ $parcel->merchant->shop_name ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 15px; color: #2d3748;">
                                        {{ $parcel->courier->courier_name ?? 'N/A' }}
                                    </td>
                            <td style="padding: 15px; color: #2d3748; font-weight: 600;">
                                {{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}
                            </td>
                                    <td style="padding: 15px; color: #2d3748;">
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
                                    <td style="padding: 15px; text-align: center;">
                                        <a href="{{ route('admin.parcels.label', $parcel) }}" 
                                           style="background: #ed8936; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                            <i class="fas fa-print" style="margin-right: 4px;"></i>
                                            View Label
                                        </a>
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
    </div>
</div>
@endsection
