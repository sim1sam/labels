@extends('layouts.merchant')

@section('title', 'My Parcels')
@section('page-title', 'My Parcels')

@section('content')
@if(session('success'))
    <div class="merchant-alert merchant-alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="merchant-alert merchant-alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Filters -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-filter"></i>
            Filter Parcels
        </h3>
    </div>
    
    <form method="GET" action="{{ route('merchant.parcels.index') }}" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">Status</label>
            <select name="status" style="padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
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
            
            <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-times"></i>
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Parcels Table -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-box"></i>
            My Parcels ({{ $parcels->total() }} found)
        </h3>
        <a href="{{ route('merchant.parcels.create') }}" class="merchant-btn merchant-btn-success">
            <i class="fas fa-plus"></i>
            Create New Parcel
        </a>
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
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
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
                                    <a href="{{ route('merchant.parcels.show', $parcel) }}" 
                                       style="background: #4299e1; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                        <i class="fas fa-eye" style="margin-right: 4px;"></i>
                                        View
                                    </a>
                                    <a href="{{ route('merchant.parcels.edit', $parcel) }}" 
                                       style="background: #ed8936; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center;">
                                        <i class="fas fa-edit" style="margin-right: 4px;"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('merchant.parcels.destroy', $parcel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this parcel?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #e53e3e; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center;">
                                            <i class="fas fa-trash" style="margin-right: 4px;"></i>
                                            Delete
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
            {{ $parcels->appends(request()->query())->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #718096;">
            <i class="fas fa-box" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
            <h3 style="margin: 0 0 8px 0; color: #4a5568;">No Parcels Found</h3>
            <p style="margin: 0 0 20px 0;">Create your first parcel to get started.</p>
            <a href="{{ route('merchant.parcels.create') }}" class="merchant-btn merchant-btn-primary">
                <i class="fas fa-plus"></i>
                Create First Parcel
            </a>
        </div>
    @endif
</div>
@endsection
