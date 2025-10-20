@extends('layouts.admin')

@section('title', 'Parcels Management')
@section('page-title', 'Parcels Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            Parcels Management
        </h3>
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <a href="{{ route('admin.parcels.bulk.print') }}" class="btn btn-warning">
                <i class="fas fa-print"></i>
                Bulk Print
            </a>
            <a href="{{ route('admin.parcels.bulk.create') }}" class="btn btn-success">
                <i class="fas fa-upload"></i>
                Bulk Upload
            </a>
            <a href="{{ route('admin.parcels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New Parcel
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div style="background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div style="background: #fef5e7; color: #744210; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('warning') }}
            </div>
        @endif

        <!-- Status Filter -->
        <div style="margin-bottom: 20px; padding: 15px; background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
            <form method="GET" action="{{ route('admin.parcels.index') }}" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div>
                    <label for="status_filter" style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Filter by Status:</label>
                    <select id="status_filter" name="status" style="padding: 8px 12px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
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
                    <button type="submit" class="btn" style="background: #4299e1; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
                @if(request('status'))
                <div>
                    <a href="{{ route('admin.parcels.index') }}" class="btn" style="background: #e2e8f0; color: #4a5568; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 14px;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Bulk Actions -->
        <div id="bulk-actions" style="display: none; margin-bottom: 20px; padding: 15px; background: #e6f3ff; border-radius: 8px; border: 1px solid #bee3f8;">
            <form id="bulk-status-form" action="{{ route('admin.parcels.bulk.status') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                    <div>
                        <span id="selected-count" style="font-weight: 600; color: #2d3748;">0 parcels selected</span>
                    </div>
                    <div>
                        <select name="status" required style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="assigned">Assigned</option>
                            <option value="picked_up">Picked Up</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" style="background: #4299e1; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-sync-alt"></i> Update Status
                        </button>
                    </div>
                    <div>
                        <button type="button" onclick="clearSelection()" style="background: #e2e8f0; color: #4a5568; padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-times"></i> Clear Selection
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Summary -->
        <div style="margin-bottom: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            @php
                $statusCounts = [
                    'pending' => \App\Models\Parcel::where('status', 'pending')->count(),
                    'assigned' => \App\Models\Parcel::where('status', 'assigned')->count(),
                    'picked_up' => \App\Models\Parcel::where('status', 'picked_up')->count(),
                    'in_transit' => \App\Models\Parcel::where('status', 'in_transit')->count(),
                    'delivered' => \App\Models\Parcel::where('status', 'delivered')->count(),
                    'failed' => \App\Models\Parcel::where('status', 'failed')->count(),
                ];
                $totalParcels = array_sum($statusCounts);
            @endphp
            
            <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #2d3748;">{{ $totalParcels }}</div>
                <div style="font-size: 14px; color: #718096;">Total Parcels</div>
            </div>
            
            @foreach($statusCounts as $status => $count)
                @php
                    $colors = [
                        'pending' => ['bg' => '#fef5e7', 'text' => '#744210'],
                        'assigned' => ['bg' => '#e6fffa', 'text' => '#234e52'],
                        'picked_up' => ['bg' => '#e6f3ff', 'text' => '#1a365d'],
                        'in_transit' => ['bg' => '#f7fafc', 'text' => '#4a5568'],
                        'delivered' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                        'failed' => ['bg' => '#fed7d7', 'text' => '#742a2a']
                    ];
                    $color = $colors[$status] ?? ['bg' => '#f7fafc', 'text' => '#4a5568'];
                @endphp
                <div style="background: {{ $color['bg'] }}; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <div style="font-size: 24px; font-weight: 600; color: {{ $color['text'] }};">{{ $count }}</div>
                    <div style="font-size: 14px; color: {{ $color['text'] }};">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                </div>
            @endforeach
        </div>

        @if($parcels->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <thead style="background: #f7fafc;">
                        <tr>
                            <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0; width: 50px;">
                                <input type="checkbox" id="select-all" onchange="toggleAllSelection()" style="transform: scale(1.2);">
                            </th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Parcel ID</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Customer</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Courier</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">COD Amount</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Created</th>
                            <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parcels as $parcel)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px; text-align: center;">
                                    <input type="checkbox" class="parcel-checkbox" value="{{ $parcel->id }}" onchange="updateSelection()" style="transform: scale(1.2);">
                                </td>
                                <td style="padding: 15px; color: #4a5568;">
                                    <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        {{ $parcel->parcel_id }}
                                    </span>
                                </td>
                                <td style="padding: 15px; color: #2d3748;">
                                    <div>
                                        <div style="font-weight: 600;">{{ $parcel->customer_name }}</div>
                                        <div style="font-size: 14px; color: #718096;">{{ $parcel->mobile_number }}</div>
                                    </div>
                                </td>
                                <td style="padding: 15px; color: #4a5568;">{{ $parcel->merchant->shop_name }}</td>
                                <td style="padding: 15px; color: #4a5568;">
                                    @if($parcel->courier)
                                        {{ $parcel->courier->courier_name }}
                                    @else
                                        <span style="color: #a0aec0;">Not assigned</span>
                                    @endif
                                </td>
                                <td style="padding: 15px; color: #2d3748; font-weight: 600;">
                                    {{ number_format($parcel->cod_amount, 0) }} {{ \App\Models\Setting::getCurrency() }}
                                </td>
                                <td style="padding: 15px;">
                                    @php
                                        $badgeColors = [
                                            'pending' => '#fef5e7',
                                            'assigned' => '#e6fffa',
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
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <span style="background: {{ $badgeColors[$parcel->status] ?? '#f7fafc' }}; color: {{ $textColors[$parcel->status] ?? '#4a5568' }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            {{ $parcel->getStatusDisplayText() }}
                                        </span>
                                        @if($parcel->isCreatedByMerchant())
                                            <span style="background: #fef5e7; color: #744210; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-store"></i> Merchant Created
                                            </span>
                                        @endif
                                        @if($parcel->isPrinted())
                                            <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-print"></i> Printed
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 15px; color: #718096; font-size: 14px;">
                                    {{ $parcel->created_at->format('M d, Y') }}
                                </td>
                                <td style="padding: 15px; text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                        @if($parcel->courier && $parcel->courier->hasApiIntegration())
                                            <button onclick="showLiveTracking({{ $parcel->id }})" 
                                                    style="background: #38a169; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center;">
                                                <i class="fas fa-shipping-fast" style="margin-right: 4px;"></i>
                                                Live Track
                                            </button>
                                        @endif
                                        
                                        <!-- Quick Status Change -->
                                        <form action="{{ route('admin.parcels.update', $parcel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to change the status?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="merchant_id" value="{{ $parcel->merchant_id }}">
                                            <input type="hidden" name="customer_name" value="{{ $parcel->customer_name }}">
                                            <input type="hidden" name="mobile_number" value="{{ $parcel->mobile_number }}">
                                            <input type="hidden" name="delivery_address" value="{{ $parcel->delivery_address }}">
                                            <input type="hidden" name="cod_amount" value="{{ $parcel->cod_amount }}">
                                            <input type="hidden" name="courier_id" value="{{ $parcel->courier_id }}">
                                            <input type="hidden" name="notes" value="{{ $parcel->notes }}">
                                            <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 12px; background: white;">
                                                <option value="pending" {{ $parcel->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="assigned" {{ $parcel->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                                <option value="picked_up" {{ $parcel->status == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                                <option value="in_transit" {{ $parcel->status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                                <option value="delivered" {{ $parcel->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="failed" {{ $parcel->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                            </select>
                                        </form>
                                        
                                        <!-- Action Buttons -->
                                        <a href="{{ route('admin.parcels.label', $parcel) }}" target="_blank" class="btn btn-sm" style="background: #38a169; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px;" title="Print Label">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('admin.parcels.show', $parcel) }}" class="btn btn-sm" style="background: #4299e1; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.parcels.edit', $parcel) }}" class="btn btn-sm" style="background: #ed8936; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.parcels.destroy', $parcel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this parcel?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm" style="background: #e53e3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $parcels->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #718096;">
                <i class="fas fa-box" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e0;"></i>
                <h3 style="margin-bottom: 8px; color: #4a5568;">No Parcels Found</h3>
                <p style="margin-bottom: 20px;">Get started by creating your first parcel.</p>
                <a href="{{ route('admin.parcels.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New Parcel
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function toggleAllSelection() {
    const selectAllCheckbox = document.getElementById('select-all');
    const parcelCheckboxes = document.querySelectorAll('.parcel-checkbox');
    
    parcelCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelection();
}

function updateSelection() {
    const parcelCheckboxes = document.querySelectorAll('.parcel-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-status-form');
    
    const checkedBoxes = document.querySelectorAll('.parcel-checkbox:checked');
    const count = checkedBoxes.length;
    
    // Update selected count
    selectedCount.textContent = count + ' parcel' + (count !== 1 ? 's' : '') + ' selected';
    
    // Show/hide bulk actions
    if (count > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
    
    // Update select all checkbox state
    if (count === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (count === parcelCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
    
    // Update form with selected parcel IDs
    const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Remove existing hidden inputs
    const existingInputs = bulkForm.querySelectorAll('input[name="parcel_ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    // Add new hidden inputs
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'parcel_ids[]';
        input.value = id;
        bulkForm.appendChild(input);
    });
}

function clearSelection() {
    const parcelCheckboxes = document.querySelectorAll('.parcel-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    
    parcelCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateSelection();
}

// Form submission confirmation
document.getElementById('bulk-status-form').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.parcel-checkbox:checked');
    const status = this.querySelector('select[name="status"]').value;
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one parcel.');
        return;
    }
    
    if (!status) {
        e.preventDefault();
        alert('Please select a status.');
        return;
    }
    
    const statusText = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    if (!confirm(`Are you sure you want to update ${checkedBoxes.length} parcel(s) to "${statusText}" status?`)) {
        e.preventDefault();
    }
});
</script>

<!-- Live Tracking Modal -->
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
