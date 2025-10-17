@extends('layouts.merchant')

@section('title', 'View Parcel')
@section('page-title', 'View Parcel')

@section('content')
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-eye"></i>
            Parcel Details
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('merchant.parcels.edit', $parcel) }}" class="merchant-btn merchant-btn-warning">
                <i class="fas fa-edit"></i>
                Edit Parcel
            </a>
            <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Parcels
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Parcel Information -->
        <div>
            <h4 style="color: #2d3748; margin-bottom: 15px; font-weight: 600;">Parcel Information</h4>
            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Parcel ID</label>
                    <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 14px; font-weight: 600;">
                        {{ $parcel->parcel_id }}
                    </span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Status</label>
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
                    <span style="background: {{ $badgeColors[$parcel->status] ?? '#f7fafc' }}; color: {{ $textColors[$parcel->status] ?? '#4a5568' }}; padding: 4px 8px; border-radius: 4px; font-size: 14px; font-weight: 600;">
                        {{ $parcel->getStatusDisplayText() }}
                    </span>
                    @if($parcel->isPrinted())
                        <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-left: 8px;">
                            <i class="fas fa-print"></i> Printed
                        </span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">COD Amount</label>
                    <div style="font-size: 18px; font-weight: 700; color: #2d3748;">
                        {{ number_format($parcel->cod_amount, 0) }} BDT
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Assigned Courier</label>
                    <div style="color: #2d3748;">
                        {{ $parcel->courier->courier_name ?? 'Not Assigned' }}
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Created Date</label>
                    <div style="color: #2d3748;">
                        {{ $parcel->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>

                @if($parcel->printed_at)
                    <div>
                        <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Printed Date</label>
                        <div style="color: #2d3748;">
                            {{ $parcel->printed_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        <div>
            <h4 style="color: #2d3748; margin-bottom: 15px; font-weight: 600;">Customer Information</h4>
            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Customer Name</label>
                    <div style="color: #2d3748; font-size: 16px; font-weight: 600;">
                        {{ $parcel->customer_name }}
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Mobile Number</label>
                    <div style="color: #2d3748; font-size: 16px;">
                        {{ $parcel->mobile_number }}
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 5px;">Delivery Address</label>
                    <div style="color: #2d3748; line-height: 1.6;">
                        {{ $parcel->delivery_address }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
