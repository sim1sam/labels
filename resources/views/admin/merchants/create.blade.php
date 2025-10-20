@extends('layouts.admin')

@section('title', 'Add New Merchant')
@section('page-title', 'Add New Merchant')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-store"></i>
            Add New Merchant
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.merchants.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Merchants
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.merchants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; gap: 20px;">
                <!-- Basic Information -->
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; color: #2d3748;">Basic Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter merchant name" required>
                            @error('name')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter email address" required>
                            @error('email')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600;">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                               style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                               placeholder="Enter phone number">
                        @error('phone')
                            <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600;">Password *</label>
                            <input type="password" id="password" name="password" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter password" required>
                            @error('password')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" style="display: block; margin-bottom: 8px; font-weight: 600;">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Confirm password" required>
                            @error('password_confirmation')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600;">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                      placeholder="Enter shop address">{{ old('address') }}</textarea>
                            @error('address')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="logo" style="display: block; margin-bottom: 8px; font-weight: 600;">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   onchange="previewLogo(this)">
                            @error('logo')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                            <div id="logo-preview" style="margin-top: 10px; display: none;">
                                <img id="preview-img" style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid #e2e8f0;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Courier Assignment -->
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; color: #2d3748;">Assign Couriers</h4>
                    <p style="color: #718096; margin-bottom: 20px;">Select couriers and assign custom merchant IDs for each courier.</p>
                    
                    <div id="courier-selection">
                        @if(old('couriers'))
                            @foreach(old('couriers') as $index => $courierId)
                                <div class="courier-row" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; margin-bottom: 15px; align-items: end;">
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Courier</label>
                                        <select name="couriers[]" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                            <option value="">Select Courier</option>
                                            @foreach($couriers as $courier)
                                                <option value="{{ $courier->id }}" {{ $courierId == $courier->id ? 'selected' : '' }}>
                                                    {{ $courier->courier_name }} ({{ ucfirst($courier->vehicle_type) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant ID</label>
                                        <input type="text" name="merchant_custom_ids[]" value="{{ old('merchant_custom_ids.'.$index) }}" 
                                               style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                               placeholder="Custom ID" required>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 12px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="courier-row" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; margin-bottom: 15px; align-items: end;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Courier</label>
                                    <select name="couriers[]" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                        <option value="">Select Courier</option>
                                        @foreach($couriers as $courier)
                                            <option value="{{ $courier->id }}">{{ $courier->courier_name }} ({{ ucfirst($courier->vehicle_type) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant ID</label>
                                    <input type="text" name="merchant_custom_ids[]" 
                                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                           placeholder="Custom ID" required>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-courier" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        Add Another Courier
                    </button>
                    
                    @error('couriers')
                        <div style="color: #e53e3e; font-size: 14px; margin-top: 10px;">{{ $message }}</div>
                    @enderror
                    @error('merchant_custom_ids')
                        <div style="color: #e53e3e; font-size: 14px; margin-top: 10px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Merchant
                </button>
                <a href="{{ route('admin.merchants.index') }}" class="btn" style="background: #e2e8f0; color: #4a5568; text-decoration: none;">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courierSelection = document.getElementById('courier-selection');
    const addCourierBtn = document.getElementById('add-courier');
    
    // Add courier row
    addCourierBtn.addEventListener('click', function() {
        const courierRow = document.createElement('div');
        courierRow.className = 'courier-row';
        courierRow.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; margin-bottom: 15px; align-items: end;';
        
        courierRow.innerHTML = `
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Courier</label>
                <select name="couriers[]" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                    <option value="">Select Courier</option>
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->courier_name }} ({{ ucfirst($courier->vehicle_type) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant ID</label>
                <input type="text" name="merchant_custom_ids[]" 
                       style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                       placeholder="Custom ID" required>
            </div>
            <div>
                <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 12px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        courierSelection.appendChild(courierRow);
    });
    
    // Remove courier row
    courierSelection.addEventListener('click', function(e) {
        if (e.target.closest('.remove-courier')) {
            const courierRows = courierSelection.querySelectorAll('.courier-row');
            if (courierRows.length > 1) {
                e.target.closest('.courier-row').remove();
            } else {
                alert('At least one courier is required.');
            }
        }
    });
});

// Logo preview function
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('logo-preview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
