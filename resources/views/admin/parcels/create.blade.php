@extends('layouts.admin')

@section('title', 'Add New Parcel')
@section('page-title', 'Add New Parcel')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            Add New Parcel
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.parcels.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Parcels
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.parcels.store') }}" method="POST">
            @csrf
            
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
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
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
                            <label for="mobile_number" style="display: block; margin-bottom: 8px; font-weight: 600;">Mobile Number *</label>
                            <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter mobile number" required onblur="fetchCustomerData()">
                            <div id="customer-loading" style="display: none; color: #4299e1; font-size: 12px; margin-top: 5px;">
                                <i class="fas fa-spinner fa-spin"></i> Loading customer data...
                            </div>
                            @error('mobile_number')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="customer_name" style="display: block; margin-bottom: 8px; font-weight: 600;">Customer Name *</label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter customer name" required>
                            @error('customer_name')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label for="delivery_address" style="display: block; margin-bottom: 8px; font-weight: 600;">Delivery Address *</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" 
                                  style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                  placeholder="Enter complete delivery address" required>{{ old('delivery_address') }}</textarea>
                        @error('delivery_address')
                            <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="cod_amount" style="display: block; margin-bottom: 8px; font-weight: 600;">COD Amount *</label>
                            <input type="number" id="cod_amount" name="cod_amount" value="{{ old('cod_amount') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="0.00" step="0.01" min="0" required>
                            @error('cod_amount')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="notes" style="display: block; margin-bottom: 8px; font-weight: 600;">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" 
                                      style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                      placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Parcel
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
