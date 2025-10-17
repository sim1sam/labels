@extends('layouts.merchant')

@section('title', 'Edit Parcel')
@section('page-title', 'Edit Parcel')

@section('content')
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-edit"></i>
            Edit Parcel
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('merchant.parcels.show', $parcel) }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-eye"></i>
                View Parcel
            </a>
            <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Parcels
            </a>
        </div>
    </div>

    <form action="{{ route('merchant.parcels.update', $parcel) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Customer Name <span style="color: #e53e3e;">*</span>
                </label>
                <input type="text" 
                       name="customer_name" 
                       value="{{ old('customer_name', $parcel->customer_name) }}"
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                @error('customer_name')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Mobile Number <span style="color: #e53e3e;">*</span>
                </label>
                <input type="text" 
                       name="mobile_number" 
                       value="{{ old('mobile_number', $parcel->mobile_number) }}"
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                @error('mobile_number')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                Delivery Address <span style="color: #e53e3e;">*</span>
            </label>
            <textarea name="delivery_address" 
                      required
                      rows="3"
                      style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('delivery_address', $parcel->delivery_address) }}</textarea>
            @error('delivery_address')
                <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    COD Amount (BDT) <span style="color: #e53e3e;">*</span>
                </label>
                <input type="number" 
                       name="cod_amount" 
                       value="{{ old('cod_amount', $parcel->cod_amount) }}"
                       required
                       min="0"
                       step="0.01"
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                @error('cod_amount')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Assign Courier (Optional)
                </label>
                <select name="courier_id" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="">Select Courier</option>
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}" {{ old('courier_id', $parcel->courier_id) == $courier->id ? 'selected' : '' }}>
                            {{ $courier->courier_name }}
                        </option>
                    @endforeach
                </select>
                @error('courier_id')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('merchant.parcels.show', $parcel) }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="merchant-btn merchant-btn-success">
                <i class="fas fa-save"></i>
                Update Parcel
            </button>
        </div>
    </form>
</div>

<!-- Current Parcel Info -->
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-info-circle"></i>
            Current Parcel Information
        </h3>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-weight: 600; color: #2d3748; margin-bottom: 5px;">Parcel ID</div>
            <div style="color: #4a5568;">{{ $parcel->parcel_id }}</div>
        </div>
        <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-weight: 600; color: #2d3748; margin-bottom: 5px;">Status</div>
            <div style="color: #4a5568;">{{ $parcel->getStatusDisplayText() }}</div>
        </div>
        <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-weight: 600; color: #2d3748; margin-bottom: 5px;">Created</div>
            <div style="color: #4a5568;">{{ $parcel->created_at->format('M d, Y') }}</div>
        </div>
        @if($parcel->printed_at)
            <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="font-weight: 600; color: #2d3748; margin-bottom: 5px;">Printed</div>
                <div style="color: #4a5568;">{{ $parcel->printed_at->format('M d, Y') }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
