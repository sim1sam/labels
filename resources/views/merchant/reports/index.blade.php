@extends('layouts.merchant')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<!-- Summary Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Total Parcels -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $stats['total_parcels'] }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total Parcels</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <!-- Delivered Parcels -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $stats['delivered_parcels'] }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Delivered</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Pending Parcels -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $stats['pending_parcels'] }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Pending</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Failed Parcels -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 8px;">{{ $stats['failed_parcels'] }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Failed</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <!-- Total COD Amount -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 24px; font-weight: bold; margin-bottom: 8px;">{{ number_format($stats['total_cod_amount'], 0) }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Total COD ({{ \App\Models\Setting::getCurrency() }})</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <!-- Delivered COD Amount -->
    <div class="merchant-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 24px; font-weight: bold; margin-bottom: 8px;">{{ number_format($stats['delivered_cod_amount'], 0) }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Delivered COD ({{ \App\Models\Setting::getCurrency() }})</div>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>
</div>

<!-- Time-based Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
    <div class="merchant-card" style="text-align: center; padding: 20px;">
        <div style="font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 8px;">{{ $timeStats['today_parcels'] }}</div>
        <div style="font-size: 14px; color: #4a5568;">Today</div>
    </div>
    <div class="merchant-card" style="text-align: center; padding: 20px;">
        <div style="font-size: 24px; font-weight: bold; color: #43e97b; margin-bottom: 8px;">{{ $timeStats['this_week_parcels'] }}</div>
        <div style="font-size: 14px; color: #4a5568;">This Week</div>
    </div>
    <div class="merchant-card" style="text-align: center; padding: 20px;">
        <div style="font-size: 24px; font-weight: bold; color: #f093fb; margin-bottom: 8px;">{{ $timeStats['this_month_parcels'] }}</div>
        <div style="font-size: 14px; color: #4a5568;">This Month</div>
    </div>
</div>

<!-- Report Filters -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-filter"></i>
            Report Filters
        </h3>
    </div>
    
    <form method="GET" action="{{ route('merchant.reports.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Report Type</label>
            <select name="report_type" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                <option value="all_parcels" {{ $reportType == 'all_parcels' ? 'selected' : '' }}>All Parcels</option>
                <option value="printed_parcels" {{ $reportType == 'printed_parcels' ? 'selected' : '' }}>Printed Parcels</option>
                <option value="delivered_parcels" {{ $reportType == 'delivered_parcels' ? 'selected' : '' }}>Delivered Parcels</option>
                <option value="pending_parcels" {{ $reportType == 'pending_parcels' ? 'selected' : '' }}>Pending Parcels</option>
                <option value="failed_parcels" {{ $reportType == 'failed_parcels' ? 'selected' : '' }}>Failed Parcels</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Start Date</label>
            <input type="date" 
                   name="start_date" 
                   value="{{ $startDate }}"
                   style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">End Date</label>
            <input type="date" 
                   name="end_date" 
                   value="{{ $endDate }}"
                   style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
        </div>

        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Status</label>
            <select name="status" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                <option value="">All Statuses</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="assigned" {{ $status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="picked_up" {{ $status == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="in_transit" {{ $status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="merchant-btn merchant-btn-primary">
                <i class="fas fa-search"></i>
                Filter
            </button>
            
            <a href="{{ route('merchant.reports.index') }}" class="merchant-btn merchant-btn-secondary">
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
            <i class="fas fa-chart-bar"></i>
            @switch($reportType)
                @case('printed_parcels')
                    Printed Parcels Report
                    @break
                @case('delivered_parcels')
                    Delivered Parcels Report
                    @break
                @case('pending_parcels')
                    Pending Parcels Report
                    @break
                @case('failed_parcels')
                    Failed Parcels Report
                    @break
                @default
                    All Parcels Report
            @endswitch
            ({{ $parcels->total() }} found)
        </h3>
        
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('merchant.reports.download', array_merge(request()->query(), ['report_type' => $reportType])) }}" 
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
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Created</th>
                        @if($reportType == 'printed_parcels')
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748;">Printed</th>
                        @endif
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748;">Actions</th>
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
                                <div style="font-size: 12px; color: #718096;">{{ Str::limit($parcel->delivery_address, 30) }}</div>
                            </td>
                            <td style="padding: 15px; color: #2d3748;">
                                {{ $parcel->courier->courier_name ?? 'Not Assigned' }}
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
                                <div>{{ $parcel->created_at->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: #a0aec0;">{{ $parcel->created_at->format('h:i A') }}</div>
                            </td>
                            @if($reportType == 'printed_parcels')
                                <td style="padding: 15px; color: #718096; font-size: 14px;">
                                    @if($parcel->printed_at)
                                        <div>{{ $parcel->printed_at->format('M d, Y') }}</div>
                                        <div style="font-size: 12px; color: #a0aec0;">{{ $parcel->printed_at->format('h:i A') }}</div>
                                    @else
                                        <span style="color: #a0aec0;">Not Printed</span>
                                    @endif
                                </td>
                            @endif
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                    @if($parcel->courier && $parcel->courier->hasApiIntegration())
                                        <button onclick="showLiveTracking({{ $parcel->id }})" 
                                                style="background: #38a169; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center;">
                                            <i class="fas fa-shipping-fast" style="margin-right: 4px;"></i>
                                            Track
                                        </button>
                                    @endif
                                    <a href="{{ route('merchant.parcels.show', $parcel) }}" 
                                       style="background: #4299e1; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                        <i class="fas fa-eye" style="margin-right: 4px;"></i>
                                        View
                                    </a>
                                </div>
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
            <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <h3 style="margin: 0 0 8px 0; color: #4a5568;">No Data Found</h3>
            <p style="margin: 0;">No parcels match your current filters.</p>
        </div>
    @endif
</div>

<!-- Live Tracking Modal (same as in parcels index) -->
<div id="trackingModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 800px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 600;">
                <i class="fas fa-shipping-fast" style="margin-right: 10px;"></i>
                Live Tracking Status
            </h3>
            <button onclick="closeTrackingModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="trackingContent" style="padding: 30px;">
            <!-- Loading State -->
            <div id="trackingLoading" style="text-align: center; padding: 40px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 20px; color: #666; font-size: 16px;">Loading tracking information...</p>
            </div>
            
            <!-- Error State -->
            <div id="trackingError" style="display: none; text-align: center; padding: 40px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #e53e3e; margin-bottom: 20px;"></i>
                <h3 style="color: #e53e3e; margin-bottom: 10px;">Tracking Unavailable</h3>
                <p id="trackingErrorMessage" style="color: #666; margin-bottom: 20px;"></p>
                <button onclick="retryTracking()" style="background: #667eea; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-redo" style="margin-right: 8px;"></i>
                    Try Again
                </button>
            </div>
            
            <!-- Success State -->
            <div id="trackingSuccess" style="display: none;">
                <!-- Current Status -->
                <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #38a169;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <i class="fas fa-map-marker-alt" style="color: #38a169; margin-right: 10px; font-size: 18px;"></i>
                        <h4 style="margin: 0; color: #2d3748; font-size: 18px;">Current Status</h4>
                    </div>
                    <div id="currentStatus" style="font-size: 16px; color: #4a5568;"></div>
                </div>
                
                <!-- Tracking Details -->
                <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 15px 0; color: #2d3748; font-size: 16px; display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="color: #4299e1; margin-right: 8px;"></i>
                        Tracking Details
                    </h4>
                    <div id="trackingDetails" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 14px;"></div>
                </div>
                
                <!-- Tracking History -->
                <div style="background: #f7fafc; padding: 20px; border-radius: 8px;">
                    <h4 style="margin: 0 0 15px 0; color: #2d3748; font-size: 16px; display: flex; align-items: center;">
                        <i class="fas fa-history" style="color: #ed8936; margin-right: 8px;"></i>
                        Tracking History
                    </h4>
                    <div id="trackingHistory" style="space-y: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.tracking-history-item {
    display: flex; 
    align-items: center; 
    padding: 15px; 
    background: white; 
    border-radius: 8px; 
    margin-bottom: 10px; 
    border-left: 4px solid #e2e8f0;
    transition: all 0.3s ease;
}

.tracking-history-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateX(5px);
}

.tracking-history-item.delivered {
    border-left-color: #38a169;
}

.tracking-history-item.in_transit {
    border-left-color: #4299e1;
}

.tracking-history-item.picked_up {
    border-left-color: #ed8936;
}

.tracking-history-item.pending {
    border-left-color: #a0aec0;
}

.tracking-status-icon {
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin-right: 15px; 
    font-size: 16px; 
    color: white;
}

.tracking-status-icon.delivered {
    background: #38a169;
}

.tracking-status-icon.in_transit {
    background: #4299e1;
}

.tracking-status-icon.picked_up {
    background: #ed8936;
}

.tracking-status-icon.pending {
    background: #a0aec0;
}

.tracking-history-content {
    flex: 1;
}

.tracking-history-status {
    font-weight: 600; 
    color: #2d3748; 
    margin-bottom: 4px;
}

.tracking-history-location {
    color: #4a5568; 
    font-size: 14px; 
    margin-bottom: 2px;
}

.tracking-history-date {
    color: #718096; 
    font-size: 12px;
}

.tracking-history-notes {
    color: #4a5568; 
    font-size: 13px; 
    font-style: italic; 
    margin-top: 4px;
}
</style>

<script>
let currentParcelId = null;

function showLiveTracking(parcelId) {
    currentParcelId = parcelId;
    document.getElementById('trackingModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Reset modal state
    document.getElementById('trackingLoading').style.display = 'block';
    document.getElementById('trackingError').style.display = 'none';
    document.getElementById('trackingSuccess').style.display = 'none';
    
    // Fetch tracking data
    fetchTrackingData(parcelId);
}

function closeTrackingModal() {
    document.getElementById('trackingModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentParcelId = null;
}

function retryTracking() {
    if (currentParcelId) {
        fetchTrackingData(currentParcelId);
    }
}

function fetchTrackingData(parcelId) {
    // Show loading state
    document.getElementById('trackingLoading').style.display = 'block';
    document.getElementById('trackingError').style.display = 'none';
    document.getElementById('trackingSuccess').style.display = 'none';
    
    fetch(`/api/live-tracking/${parcelId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned HTML instead of JSON. Check if the API endpoint exists.');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('trackingLoading').style.display = 'none';
        
        if (data.success) {
            displayTrackingData(data.data);
        } else {
            showTrackingError(data.message || 'Failed to get tracking information');
        }
    })
    .catch(error => {
        document.getElementById('trackingLoading').style.display = 'none';
        console.error('Tracking error:', error);
        
        if (error.message.includes('HTML instead of JSON')) {
            showTrackingError('API endpoint not found. Please check the route configuration.');
        } else {
            showTrackingError('Failed to fetch tracking information. Please try again.');
        }
    });
}

function displayTrackingData(data) {
    document.getElementById('trackingSuccess').style.display = 'block';
    
    // Display current status
    const statusColors = {
        'delivered': '#38a169',
        'in_transit': '#4299e1', 
        'picked_up': '#ed8936',
        'pending': '#a0aec0'
    };
    
    const statusText = data.status_text || data.status || 'Unknown';
    const statusColor = statusColors[data.status] || '#a0aec0';
    
    document.getElementById('currentStatus').innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 10px;">
            <div style="width: 12px; height: 12px; background: ${statusColor}; border-radius: 50%; margin-right: 10px;"></div>
            <span style="font-weight: 600; color: ${statusColor}; font-size: 18px;">${statusText}</span>
        </div>
        ${data.current_location ? `<div style="color: #4a5568;"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>${data.current_location}</div>` : ''}
    `;
    
    // Display tracking details
    const details = [];
    if (data.tracking_code) details.push({label: 'Tracking Code', value: data.tracking_code});
    if (data.delivery_date) details.push({label: 'Delivery Date', value: new Date(data.delivery_date).toLocaleString()});
    if (data.delivery_attempts) details.push({label: 'Delivery Attempts', value: data.delivery_attempts});
    if (data.delivery_notes) details.push({label: 'Delivery Notes', value: data.delivery_notes});
    
    const detailsHtml = details.map(detail => `
        <div style="background: white; padding: 10px; border-radius: 6px;">
            <div style="font-weight: 600; color: #2d3748; margin-bottom: 4px;">${detail.label}</div>
            <div style="color: #4a5568;">${detail.value}</div>
        </div>
    `).join('');
    
    document.getElementById('trackingDetails').innerHTML = detailsHtml;
    
    // Display tracking history
    if (data.tracking_history && data.tracking_history.length > 0) {
        const historyHtml = data.tracking_history.map(item => `
            <div class="tracking-history-item ${item.status}">
                <div class="tracking-status-icon ${item.status}">
                    <i class="fas fa-${getStatusIcon(item.status)}"></i>
                </div>
                <div class="tracking-history-content">
                    <div class="tracking-history-status">${item.status_text || item.status}</div>
                    <div class="tracking-history-location">
                        <i class="fas fa-map-marker-alt" style="margin-right: 6px;"></i>${item.location || 'Unknown Location'}
                    </div>
                    <div class="tracking-history-date">
                        <i class="fas fa-clock" style="margin-right: 6px;"></i>${new Date(item.date).toLocaleString()}
                    </div>
                    ${item.notes ? `<div class="tracking-history-notes">${item.notes}</div>` : ''}
                </div>
            </div>
        `).join('');
        
        document.getElementById('trackingHistory').innerHTML = historyHtml;
    } else {
        document.getElementById('trackingHistory').innerHTML = `
            <div style="text-align: center; padding: 20px; color: #718096;">
                <i class="fas fa-history" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>No tracking history available</p>
            </div>
        `;
    }
}

function showTrackingError(message) {
    document.getElementById('trackingError').style.display = 'block';
    document.getElementById('trackingErrorMessage').textContent = message;
}

function getStatusIcon(status) {
    const icons = {
        'delivered': 'check-circle',
        'in_transit': 'truck',
        'picked_up': 'hand-holding',
        'pending': 'clock'
    };
    return icons[status] || 'circle';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('trackingModal');
    if (event.target === modal) {
        closeTrackingModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeTrackingModal();
    }
});
</script>
@endsection
