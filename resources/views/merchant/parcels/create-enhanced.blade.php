@extends('layouts.merchant')

@section('title', 'Create New Parcel')
@section('page-title', 'Create New Parcel')

@push('styles')
<style>
    .courier-selection {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .courier-option {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .courier-option:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .courier-option.selected {
        border-color: #667eea;
        background: #f0f4ff;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .courier-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .courier-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .courier-details h4 {
        margin: 0 0 5px 0;
        color: #2d3748;
        font-size: 16px;
    }

    .courier-meta {
        display: flex;
        gap: 15px;
        font-size: 12px;
        color: #718096;
    }

    .courier-badges {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-api {
        background: #48bb78;
        color: white;
    }

    .badge-tracking {
        background: #4299e1;
        color: white;
    }

    .badge-primary {
        background: #fbbf24;
        color: #92400e;
    }

    .badge-rating {
        background: #e2e8f0;
        color: #4a5568;
    }

    .tracking-preview {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }

    .tracking-preview.show {
        display: block;
    }

    .tracking-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .tracking-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-in-transit {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-delivered {
        background: #d1fae5;
        color: #065f46;
    }

    .form-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-section h3 {
        margin: 0 0 20px 0;
        color: #2d3748;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section h3 i {
        color: #667eea;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group label .required {
        color: #e53e3e;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
        outline: none;
    }

    .form-control.is-invalid {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .invalid-feedback {
        color: #e53e3e;
        font-size: 12px;
        margin-top: 5px;
    }

    .loading-spinner {
        display: none;
        color: #4299e1;
        font-size: 12px;
        margin-top: 5px;
    }

    .loading-spinner.show {
        display: block;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #4a5568;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="form-section">
    <h3><i class="fas fa-shipping-fast"></i> Courier Selection</h3>
    
    <div class="courier-selection">
        <div class="form-group">
            <label>Select Courier <span class="required">*</span></label>
            <div id="courier-options">
                <div class="loading-spinner" id="courier-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading available couriers...
                </div>
            </div>
            <input type="hidden" name="courier_id" id="selected-courier-id" value="{{ old('courier_id') }}">
            @error('courier_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="tracking-preview" id="tracking-preview">
            <div class="tracking-info">
                <i class="fas fa-map-marker-alt"></i>
                <span>Live tracking will be available for this courier</span>
            </div>
            <div class="tracking-status status-pending" id="tracking-status">
                Ready for pickup
            </div>
        </div>
    </div>
</div>

<form action="{{ route('merchant.parcels.store') }}" method="POST" id="parcel-form">
    @csrf
    
    <div class="form-section">
        <h3><i class="fas fa-user"></i> Customer Information</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="mobile_number">
                    Mobile Number <span class="required">*</span>
                </label>
                <input type="text" 
                       id="mobile_number"
                       name="mobile_number" 
                       value="{{ old('mobile_number') }}"
                       required
                       onblur="fetchCustomerData()"
                       class="form-control @error('mobile_number') is-invalid @enderror">
                <div class="loading-spinner" id="customer-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading customer data...
                </div>
                @error('mobile_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="customer_name">
                    Customer Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="customer_name"
                       name="customer_name" 
                       value="{{ old('customer_name') }}"
                       required
                       class="form-control @error('customer_name') is-invalid @enderror">
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="delivery_address">
                Delivery Address <span class="required">*</span>
            </label>
            <textarea id="delivery_address"
                      name="delivery_address" 
                      required
                      rows="3"
                      class="form-control @error('delivery_address') is-invalid @enderror">{{ old('delivery_address') }}</textarea>
            @error('delivery_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-section">
        <h3><i class="fas fa-box"></i> Package Information</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="cod_amount">
                    COD Amount (BDT) <span class="required">*</span>
                </label>
                <input type="number" 
                       name="cod_amount" 
                       value="{{ old('cod_amount') }}"
                       required
                       min="0"
                       step="0.01"
                       class="form-control @error('cod_amount') is-invalid @enderror">
                @error('cod_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="weight">
                    Weight (kg)
                </label>
                <input type="number" 
                       name="weight" 
                       value="{{ old('weight') }}"
                       min="0"
                       step="0.1"
                       class="form-control @error('weight') is-invalid @enderror">
                @error('weight')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="description">
                Package Description
            </label>
            <textarea name="description" 
                      rows="2"
                      class="form-control @error('description') is-invalid @enderror"
                      placeholder="Describe the package contents...">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-section">
        <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
        
        <div class="form-group">
            <label for="notes">
                Notes (Optional)
            </label>
            <textarea name="notes" 
                      rows="2"
                      class="form-control @error('notes') is-invalid @enderror"
                      placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('merchant.parcels.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Cancel
        </a>
        <button type="submit" class="btn btn-primary" id="submit-btn">
            <i class="fas fa-save"></i>
            Create Parcel
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCourierOptions();
});

// Load available couriers
function loadCourierOptions() {
    const loadingDiv = document.getElementById('courier-loading');
    const optionsDiv = document.getElementById('courier-options');
    
    loadingDiv.classList.add('show');
    
    fetch('/api/courier-options?merchant_id={{ auth()->user()->merchant->id }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingDiv.classList.remove('show');
        
        if (data.success && data.data.length > 0) {
            renderCourierOptions(data.data);
        } else {
            optionsDiv.innerHTML = '<p style="color: #718096; text-align: center; padding: 20px;">No couriers available. Contact admin to assign couriers.</p>';
        }
    })
    .catch(error => {
        loadingDiv.classList.remove('show');
        console.error('Error loading couriers:', error);
        optionsDiv.innerHTML = '<p style="color: #e53e3e; text-align: center; padding: 20px;">Error loading couriers. Please try again.</p>';
    });
}

// Render courier options
function renderCourierOptions(couriers) {
    const optionsDiv = document.getElementById('courier-options');
    
    optionsDiv.innerHTML = couriers.map(courier => `
        <div class="courier-option" onclick="selectCourier(${courier.value}, this)">
            <input type="radio" name="courier_radio" value="${courier.value}">
            <div class="courier-info">
                <div class="courier-details">
                    <h4>${courier.label}</h4>
                    <div class="courier-meta">
                        <span><i class="fas fa-motorcycle"></i> ${courier.vehicle_type}</span>
                        <span><i class="fas fa-star"></i> ${courier.rating}/5</span>
                    </div>
                </div>
                <div class="courier-badges">
                    ${courier.has_api ? '<span class="badge badge-api">API</span>' : ''}
                    ${courier.has_tracking ? '<span class="badge badge-tracking">Tracking</span>' : ''}
                    ${courier.is_primary ? '<span class="badge badge-primary">Primary</span>' : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Select courier
function selectCourier(courierId, element) {
    // Remove previous selection
    document.querySelectorAll('.courier-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selection to clicked option
    element.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('selected-courier-id').value = courierId;
    
    // Show tracking preview if courier supports tracking
    const trackingPreview = document.getElementById('tracking-preview');
    const hasTracking = element.querySelector('.badge-tracking');
    
    if (hasTracking) {
        trackingPreview.classList.add('show');
    } else {
        trackingPreview.classList.remove('show');
    }
}

// Fetch customer data by mobile number
function fetchCustomerData() {
    const mobileNumber = document.getElementById('mobile_number').value.trim();
    const customerNameField = document.getElementById('customer_name');
    const deliveryAddressField = document.getElementById('delivery_address');
    const loadingDiv = document.getElementById('customer-loading');
    
    if (mobileNumber.length >= 10) {
        loadingDiv.classList.add('show');
        
        fetch(`/admin/customers/by-mobile/${encodeURIComponent(mobileNumber)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.classList.remove('show');
            
            if (data.success && data.customer) {
                customerNameField.value = data.customer.name;
                deliveryAddressField.value = data.customer.address;
                showMessage('Customer data loaded successfully!', 'success');
            }
        })
        .catch(error => {
            loadingDiv.classList.remove('show');
            console.error('Error fetching customer data:', error);
        });
    } else {
        loadingDiv.classList.remove('show');
    }
}

// Form validation
document.getElementById('parcel-form').addEventListener('submit', function(e) {
    const selectedCourier = document.getElementById('selected-courier-id').value;
    
    if (!selectedCourier) {
        e.preventDefault();
        showMessage('Please select a courier', 'error');
        return false;
    }
});

// Show messages
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

