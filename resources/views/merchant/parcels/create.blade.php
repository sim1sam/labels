@extends('layouts.merchant')

@section('title', 'Create New Parcel')
@section('page-title', 'Create New Parcel')

@section('content')
<div class="merchant-card">
    <div class="merchant-card-header">
        <h3 class="merchant-card-title">
            <i class="fas fa-plus"></i>
            Create New Parcel
        </h3>
        <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Parcels
        </a>
    </div>

    <form action="{{ route('merchant.parcels.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Mobile Number <span style="color: #e53e3e;">*</span>
                </label>
                <input type="text" 
                       id="mobile_number"
                       name="mobile_number" 
                       value="{{ old('mobile_number') }}"
                       required
                       onblur="fetchCustomerData()"
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                <div id="customer-loading" style="display: none; color: #4299e1; font-size: 12px; margin-top: 5px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading customer data...
                </div>
                @error('mobile_number')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Customer Name <span style="color: #e53e3e;">*</span>
                </label>
                <input type="text" 
                       id="customer_name"
                       name="customer_name" 
                       value="{{ old('customer_name') }}"
                       required
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                @error('customer_name')
                    <p style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                Delivery Address <span style="color: #e53e3e;">*</span>
            </label>
            <textarea id="delivery_address"
                      name="delivery_address" 
                      required
                      rows="3"
                      style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('delivery_address') }}</textarea>
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
                       value="{{ old('cod_amount') }}"
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
                        <option value="{{ $courier->id }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>
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
            <a href="{{ route('merchant.parcels.index') }}" class="merchant-btn merchant-btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="merchant-btn merchant-btn-success">
                <i class="fas fa-save"></i>
                Create Parcel
            </button>
        </div>
    </form>
</div>

<!-- Available Couriers Info -->
@if($couriers->count() > 0)
    <div class="merchant-card">
        <div class="merchant-card-header">
            <h3 class="merchant-card-title">
                <i class="fas fa-truck"></i>
                Available Couriers
            </h3>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            @foreach($couriers as $courier)
                <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 600; color: #2d3748; margin-bottom: 5px;">{{ $courier->courier_name }}</div>
                    <div style="font-size: 12px; color: #718096;">Available for pickup</div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="merchant-card">
        <div style="text-align: center; padding: 20px; color: #718096;">
            <i class="fas fa-truck" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
            <h3 style="margin: 0 0 8px 0; color: #4a5568;">No Couriers Assigned</h3>
            <p style="margin: 0;">Contact admin to assign couriers to your account.</p>
        </div>
    </div>
@endif

<script>
// Function to fetch customer data by mobile number
function fetchCustomerData() {
    const mobileNumber = document.getElementById('mobile_number').value.trim();
    const customerNameField = document.getElementById('customer_name');
    const deliveryAddressField = document.getElementById('delivery_address');
    const loadingDiv = document.getElementById('customer-loading');
    
    // Only fetch if mobile number is not empty and has at least 10 digits
    if (mobileNumber.length >= 10) {
        loadingDiv.style.display = 'block';
        
        fetch(`/admin/customers/by-mobile/${encodeURIComponent(mobileNumber)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';
            
            if (data.success && data.customer) {
                // Auto-fill customer name and address
                customerNameField.value = data.customer.name;
                deliveryAddressField.value = data.customer.address;
                
                // Show success message
                showMessage('Customer data loaded successfully!', 'success');
            } else {
                // Clear fields if customer not found
                if (customerNameField.value === '') {
                    customerNameField.value = '';
                }
                if (deliveryAddressField.value === '') {
                    deliveryAddressField.value = '';
                }
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            console.error('Error fetching customer data:', error);
        });
    } else {
        loadingDiv.style.display = 'none';
    }
}

// Function to show messages
function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        ${type === 'success' ? 'background: #48bb78;' : 'background: #f56565;'}
    `;
    messageDiv.textContent = message;
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}
</script>
@endsection
