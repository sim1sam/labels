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
@endsection
