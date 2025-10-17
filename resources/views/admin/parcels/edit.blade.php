@extends('layouts.admin')

@section('title', 'Edit Parcel')
@section('page-title', 'Edit Parcel')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            Edit Parcel - {{ $parcel->parcel_id }}
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.parcels.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Parcels
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.parcels.update', $parcel) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; gap: 20px;">
                <!-- Basic Information -->
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; color: #2d3748;">Parcel Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="merchant_id" style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant *</label>
                            <select id="merchant_id" name="merchant_id" 
                                    style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                <option value="">Select Merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $parcel->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->shop_name }} ({{ $merchant->merchant_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('merchant_id')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="courier_id" style="display: block; margin-bottom: 8px; font-weight: 600;">Courier (Optional)</label>
                            <select id="courier_id" name="courier_id" 
                                    style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;">
                                <option value="">Select Courier (Optional)</option>
                                <!-- Couriers will be loaded dynamically based on selected merchant -->
                            </select>
                            @error('courier_id')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="customer_name" style="display: block; margin-bottom: 8px; font-weight: 600;">Customer Name *</label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $parcel->customer_name) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter customer name" required>
                            @error('customer_name')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="mobile_number" style="display: block; margin-bottom: 8px; font-weight: 600;">Mobile Number *</label>
                            <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $parcel->mobile_number) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter mobile number" required>
                            @error('mobile_number')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label for="delivery_address" style="display: block; margin-bottom: 8px; font-weight: 600;">Delivery Address *</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" 
                                  style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                  placeholder="Enter complete delivery address" required>{{ old('delivery_address', $parcel->delivery_address) }}</textarea>
                        @error('delivery_address')
                            <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="cod_amount" style="display: block; margin-bottom: 8px; font-weight: 600;">COD Amount *</label>
                            <input type="number" id="cod_amount" name="cod_amount" value="{{ old('cod_amount', $parcel->cod_amount) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="0.00" step="0.01" min="0" required>
                            @error('cod_amount')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600;">Status *</label>
                            <select id="status" name="status" 
                                    style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                <option value="pending" {{ old('status', $parcel->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="assigned" {{ old('status', $parcel->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="picked_up" {{ old('status', $parcel->status) == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="in_transit" {{ old('status', $parcel->status) == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="delivered" {{ old('status', $parcel->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="failed" {{ old('status', $parcel->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            @error('status')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label for="notes" style="display: block; margin-bottom: 8px; font-weight: 600;">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                  placeholder="Enter any additional notes">{{ old('notes', $parcel->notes) }}</textarea>
                        @error('notes')
                            <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Parcel
                </button>
                <a href="{{ route('admin.parcels.index') }}" class="btn" style="background: #e2e8f0; color: #4a5568; text-decoration: none;">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const merchantSelect = document.getElementById('merchant_id');
    const courierSelect = document.getElementById('courier_id');
    const selectedCourierId = '{{ old("courier_id", $parcel->courier_id) }}';
    
    // Store couriers data from server
    const merchantCouriers = @json($merchantCouriers);
    
    // Function to load couriers for a merchant
    function loadCouriers(merchantId) {
        // Clear courier options
        courierSelect.innerHTML = '<option value="">Select Courier (Optional)</option>';
        
        if (merchantId && merchantCouriers[merchantId]) {
            const couriers = merchantCouriers[merchantId];
            
            if (couriers && couriers.length > 0) {
                couriers.forEach(courier => {
                    const option = document.createElement('option');
                    option.value = courier.id;
                    option.textContent = courier.courier_name;
                    if (courier.id == selectedCourierId) {
                        option.selected = true;
                    }
                    courierSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No couriers assigned to this merchant';
                option.disabled = true;
                courierSelect.appendChild(option);
            }
        }
    }
    
    // Event listener for merchant selection change
    merchantSelect.addEventListener('change', function() {
        loadCouriers(this.value);
    });
    
    // Load couriers if merchant is pre-selected (for validation errors)
    if (merchantSelect.value) {
        loadCouriers(merchantSelect.value);
    }
});
</script>
@endsection
