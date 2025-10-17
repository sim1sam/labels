@extends('layouts.admin')

@section('title', 'Bulk Label Printing')

@section('content')
<div class="admin-content">
    <div class="admin-header">
        <h1>Bulk Label Printing</h1>
        <p>Print multiple parcel labels by date range and status</p>
    </div>

    <div class="admin-body">
        @if(session('error'))
            <div style="background: #fed7d7; color: #742a2a; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Print Selection Form -->
        <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="color: #2d3748; margin-bottom: 20px;">
                <i class="fas fa-print" style="color: #ed8936; margin-right: 8px;"></i>
                Select Parcels to Print
            </h3>
            
            <form action="{{ route('admin.parcels.bulk.print.process') }}" method="POST">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <!-- Date Range -->
                    <div>
                        <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                            Start Date
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               required
                               style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        @error('start_date')
                            <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                            End Date
                        </label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ old('end_date', date('Y-m-d')) }}"
                               required
                               style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        @error('end_date')
                            <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status Filter -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                        Status Filter (Optional)
                    </label>
                    <select name="status" 
                            style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="picked_up" {{ old('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div style="display: flex; gap: 15px; align-items: center;">
                    <button type="submit" 
                            style="background: #ed8936; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center;">
                        <i class="fas fa-search" style="margin-right: 8px;"></i>
                        Find Parcels
                    </button>
                    
                    <a href="{{ route('admin.parcels.index') }}" 
                       style="background: #e2e8f0; color: #4a5568; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                        Back to Parcels
                    </a>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div style="background: #f7fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0;">
            <h3 style="color: #2d3748; margin-bottom: 15px;">
                <i class="fas fa-info-circle" style="color: #4299e1; margin-right: 8px;"></i>
                How Bulk Printing Works
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Step 1: Select Criteria</h4>
                    <ul style="color: #718096; margin: 0; padding-left: 20px;">
                        <li>Choose start and end dates</li>
                        <li>Optionally filter by status</li>
                        <li>Click "Find Parcels" to preview</li>
                    </ul>
                </div>
                <div>
                    <h4 style="color: #4a5568; margin-bottom: 10px;">Step 2: Print All Labels</h4>
                    <ul style="color: #718096; margin: 0; padding-left: 20px;">
                        <li>Review the list of parcels</li>
                        <li>Click "Print All Labels"</li>
                        <li>All labels will print automatically</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
