@extends('layouts.admin')

@section('title', 'Parcel Details')
@section('page-title', 'Parcel Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            {{ $parcel->parcel_id }}
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.parcels.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Parcels
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; gap: 30px;">
            <!-- Parcel Information -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i>
                    Parcel Information
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Parcel ID</label>
                        <div style="background: #e6fffa; color: #234e52; padding: 8px 12px; border-radius: 6px; font-weight: 600;">
                            {{ $parcel->parcel_id }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Status</label>
                        <div>
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
                            <span style="background: {{ $badgeColors[$parcel->status] ?? '#f7fafc' }}; color: {{ $textColors[$parcel->status] ?? '#4a5568' }}; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                {{ $parcel->getStatusDisplayText() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Customer Name</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748; font-weight: 600;">
                            {{ $parcel->customer_name }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Mobile Number</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $parcel->mobile_number }}
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Delivery Address</label>
                    <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                        {{ $parcel->delivery_address }}
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">COD Amount</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748; font-weight: 600; font-size: 18px;">
                            ${{ number_format($parcel->cod_amount, 2) }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Created Date</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $parcel->created_at->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>
                </div>
                
                @if($parcel->notes)
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Notes</label>
                    <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                        {{ $parcel->notes }}
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Merchant and Courier Information -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-users"></i>
                    Assignment Information
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Merchant</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            <div style="font-weight: 600;">{{ $parcel->merchant->shop_name }}</div>
                            <div style="font-size: 14px; color: #718096;">{{ $parcel->merchant->merchant_id }}</div>
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Assigned Courier</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            @if($parcel->courier)
                                <div style="font-weight: 600;">{{ $parcel->courier->courier_name }}</div>
                                <div style="font-size: 14px; color: #718096;">{{ ucfirst($parcel->courier->vehicle_type) }}</div>
                            @else
                                <span style="color: #a0aec0;">Not assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($parcel->pickup_date || $parcel->delivery_date)
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    @if($parcel->pickup_date)
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Pickup Date</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $parcel->pickup_date->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>
                    @endif
                    
                    @if($parcel->delivery_date)
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Delivery Date</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $parcel->delivery_date->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('admin.parcels.edit', $parcel) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Parcel
            </a>
            <form action="{{ route('admin.parcels.destroy', $parcel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this parcel?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete Parcel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

