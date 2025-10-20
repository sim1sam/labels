@extends('layouts.admin')

@section('title', 'Edit Merchant')
@section('page-title', 'Edit Merchant')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-store"></i>
            Edit Merchant
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.merchants.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Merchants
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.merchants.update', $merchant) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div style="display: grid; gap: 20px;">
                <!-- Basic Information -->
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; color: #2d3748;">Basic Information</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">Merchant Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $merchant->user->name) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter merchant name" required>
                            @error('name')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $merchant->email) }}" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter email address" required>
                            @error('email')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600;">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $merchant->phone) }}" 
                               style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                               placeholder="Enter phone number">
                        @error('phone')
                            <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600;">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Enter new password">
                            @error('password')
                                <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" style="display: block; margin-bottom: 8px; font-weight: 600;">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                   placeholder="Confirm new password">
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
                                      placeholder="Enter shop address">{{ old('address', $merchant->address) }}</textarea>
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
                            
                            @if($merchant->logo)
                                <div style="margin-top: 10px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Current Logo:</label>
                                    <img src="/{{ $merchant->logo }}" style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid #e2e8f0;">
                                </div>
                            @endif
                            
                            <div id="logo-preview" style="margin-top: 10px; display: none;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">New Logo Preview:</label>
                                <img id="preview-img" style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid #e2e8f0;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Courier Assignment -->
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 15px; color: #2d3748;">🚚 Assign Couriers with API Integration</h4>
                    <p style="color: #718096; margin-bottom: 20px;">Select couriers and configure API credentials for live tracking and order management.</p>
                    
                    <div id="courier-selection">
                        @php
                            $selectedCouriers = $merchant->couriers->pluck('id')->toArray();
                        @endphp
                        
                        @if(old('couriers'))
                            @foreach(old('couriers') as $index => $courierId)
                                <div class="courier-assignment-card" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: #f8fafc;">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                        <h5 style="color: #2d3748; margin: 0;">Courier Assignment #{{ $index + 1 }}</h5>
                                        <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 8px 12px;">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Select Courier</label>
                                            <select name="couriers[]" class="courier-select" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                                <option value="">Select Courier</option>
                                                @foreach($couriers as $courier)
                                                    <option value="{{ $courier->id }}" {{ $courierId == $courier->id ? 'selected' : '' }} 
                                                            data-has-api="{{ $courier->hasApiIntegration() ? 'true' : 'false' }}"
                                                            data-has-tracking="{{ $courier->supportsTracking() ? 'true' : 'false' }}">
                                                        {{ $courier->courier_name }} 
                                                        @if($courier->hasApiIntegration()) 
                                                            <span style="color: #38a169;">✓ API</span>
                                                        @endif
                                                        @if($courier->supportsTracking()) 
                                                            <span style="color: #3182ce;">✓ Tracking</span>
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Merchant Custom ID</label>
                                            <input type="text" name="merchant_custom_ids[]" value="{{ old('merchant_custom_ids.'.$index) }}" 
                                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                                   placeholder="e.g., MERCHANT001" required>
                                        </div>
                                    </div>
                                    
                                    <!-- API Credentials Section -->
                                    <div class="api-credentials" style="background: #edf2f7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                        <h6 style="color: #2d3748; margin-bottom: 10px;">🔑 API Credentials (Optional - for custom merchant API)</h6>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div>
                                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Key</label>
                                                <input type="text" name="merchant_api_keys[]" value="{{ old('merchant_api_keys.'.$index) }}" 
                                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                       placeholder="Leave empty to use courier's default API">
                                            </div>
                                            <div>
                                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Secret</label>
                                                <input type="password" name="merchant_api_secrets[]" value="{{ old('merchant_api_secrets.'.$index) }}" 
                                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                       placeholder="Leave empty to use courier's default API">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status and Primary -->
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Status</label>
                                            <select name="courier_status[]" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                                                <option value="active" {{ old('courier_status.'.$index, 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('courier_status.'.$index) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <div style="display: flex; align-items: center; margin-top: 25px;">
                                            <input type="checkbox" name="is_primary[]" value="{{ $index }}" 
                                                   {{ old('is_primary.'.$index) ? 'checked' : '' }}
                                                   style="margin-right: 8px; transform: scale(1.2);">
                                            <label style="font-weight: 600; color: #4a5568; margin: 0;">Set as Primary Courier</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @if($merchant->couriers->count() > 0)
                                @foreach($merchant->couriers as $index => $courier)
                                    <div class="courier-assignment-card" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: #f8fafc;">
                                        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                            <h5 style="color: #2d3748; margin: 0;">Courier Assignment #{{ $index + 1 }}</h5>
                                            <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 8px 12px;">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                            <div>
                                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Select Courier</label>
                                                <select name="couriers[]" class="courier-select" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                                    <option value="">Select Courier</option>
                                                    @foreach($couriers as $c)
                                                        <option value="{{ $c->id }}" {{ $courier->id == $c->id ? 'selected' : '' }} 
                                                                data-has-api="{{ $c->hasApiIntegration() ? 'true' : 'false' }}"
                                                                data-has-tracking="{{ $c->supportsTracking() ? 'true' : 'false' }}">
                                                            {{ $c->courier_name }} 
                                                            @if($c->hasApiIntegration()) 
                                                                <span style="color: #38a169;">✓ API</span>
                                                            @endif
                                                            @if($c->supportsTracking()) 
                                                                <span style="color: #3182ce;">✓ Tracking</span>
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Merchant Custom ID</label>
                                                <input type="text" name="merchant_custom_ids[]" value="{{ $courier->pivot->merchant_custom_id }}" 
                                                       style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                                       placeholder="e.g., MERCHANT001" required>
                                            </div>
                                        </div>
                                        
                                        <!-- API Credentials Section -->
                                        <div class="api-credentials" style="background: #edf2f7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                            <h6 style="color: #2d3748; margin-bottom: 10px;">🔑 API Credentials (Optional - for custom merchant API)</h6>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                                <div>
                                                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Key</label>
                                                    <input type="text" name="merchant_api_keys[]" value="{{ $courier->pivot->merchant_api_key }}" 
                                                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                           placeholder="Leave empty to use courier's default API">
                                                </div>
                                                <div>
                                                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Secret</label>
                                                    <input type="password" name="merchant_api_secrets[]" value="{{ $courier->pivot->merchant_api_secret }}" 
                                                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                           placeholder="Leave empty to use courier's default API">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Status and Primary -->
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div>
                                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Status</label>
                                                <select name="courier_status[]" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                                                    <option value="active" {{ $courier->pivot->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $courier->pivot->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div style="display: flex; align-items: center; margin-top: 25px;">
                                                <input type="checkbox" name="is_primary[]" value="{{ $index }}" 
                                                       {{ $courier->pivot->is_primary ? 'checked' : '' }}
                                                       style="margin-right: 8px; transform: scale(1.2);">
                                                <label style="font-weight: 600; color: #4a5568; margin: 0;">Set as Primary Courier</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="courier-assignment-card" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: #f8fafc;">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                                        <h5 style="color: #2d3748; margin: 0;">Courier Assignment #1</h5>
                                        <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 8px 12px;">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Select Courier</label>
                                            <select name="couriers[]" class="courier-select" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                                                <option value="">Select Courier</option>
                                                @foreach($couriers as $courier)
                                                    <option value="{{ $courier->id }}" 
                                                            data-has-api="{{ $courier->hasApiIntegration() ? 'true' : 'false' }}"
                                                            data-has-tracking="{{ $courier->supportsTracking() ? 'true' : 'false' }}">
                                                        {{ $courier->courier_name }} 
                                                        @if($courier->hasApiIntegration()) 
                                                            <span style="color: #38a169;">✓ API</span>
                                                        @endif
                                                        @if($courier->supportsTracking()) 
                                                            <span style="color: #3182ce;">✓ Tracking</span>
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Merchant Custom ID</label>
                                            <input type="text" name="merchant_custom_ids[]" 
                                                   style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                                                   placeholder="e.g., MERCHANT001" required>
                                        </div>
                                    </div>
                                    
                                    <!-- API Credentials Section -->
                                    <div class="api-credentials" style="background: #edf2f7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                        <h6 style="color: #2d3748; margin-bottom: 10px;">🔑 API Credentials (Optional - for custom merchant API)</h6>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div>
                                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Key</label>
                                                <input type="text" name="merchant_api_keys[]" 
                                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                       placeholder="Leave empty to use courier's default API">
                                            </div>
                                            <div>
                                                <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Secret</label>
                                                <input type="password" name="merchant_api_secrets[]" 
                                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                                                       placeholder="Leave empty to use courier's default API">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Status and Primary -->
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                        <div>
                                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Status</label>
                                            <select name="courier_status[]" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div style="display: flex; align-items: center; margin-top: 25px;">
                                            <input type="checkbox" name="is_primary[]" value="0" checked
                                                   style="margin-right: 8px; transform: scale(1.2);">
                                            <label style="font-weight: 600; color: #4a5568; margin: 0;">Set as Primary Courier</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                    Update Merchant
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
    
    let courierCount = document.querySelectorAll('.courier-assignment-card').length;
    
    // Add courier assignment card
    addCourierBtn.addEventListener('click', function() {
        courierCount++;
        const courierCard = document.createElement('div');
        courierCard.className = 'courier-assignment-card';
        courierCard.style.cssText = 'border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: #f8fafc;';
        
        courierCard.innerHTML = `
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                <h5 style="color: #2d3748; margin: 0;">Courier Assignment #${courierCount}</h5>
                <button type="button" class="btn btn-danger btn-sm remove-courier" style="padding: 8px 12px;">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Select Courier</label>
                    <select name="couriers[]" class="courier-select" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;" required>
                        <option value="">Select Courier</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" 
                                    data-has-api="{{ $courier->hasApiIntegration() ? 'true' : 'false' }}"
                                    data-has-tracking="{{ $courier->supportsTracking() ? 'true' : 'false' }}">
                                {{ $courier->courier_name }} 
                                @if($courier->hasApiIntegration()) 
                                    <span style="color: #38a169;">✓ API</span>
                                @endif
                                @if($courier->supportsTracking()) 
                                    <span style="color: #3182ce;">✓ Tracking</span>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Merchant Custom ID</label>
                    <input type="text" name="merchant_custom_ids[]" 
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                           placeholder="e.g., MERCHANT001" required>
                </div>
            </div>
            
            <!-- API Credentials Section -->
            <div class="api-credentials" style="background: #edf2f7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <h6 style="color: #2d3748; margin-bottom: 10px;">🔑 API Credentials (Optional - for custom merchant API)</h6>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Key</label>
                        <input type="text" name="merchant_api_keys[]" 
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                               placeholder="Leave empty to use courier's default API">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #4a5568;">API Secret</label>
                        <input type="password" name="merchant_api_secrets[]" 
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                               placeholder="Leave empty to use courier's default API">
                    </div>
                </div>
            </div>
            
            <!-- Status and Primary -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568;">Status</label>
                    <select name="courier_status[]" style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div style="display: flex; align-items: center; margin-top: 25px;">
                    <input type="checkbox" name="is_primary[]" value="${courierCount - 1}" 
                           style="margin-right: 8px; transform: scale(1.2);">
                    <label style="font-weight: 600; color: #4a5568; margin: 0;">Set as Primary Courier</label>
                </div>
            </div>
        `;
        
        courierSelection.appendChild(courierCard);
        
        // Add event listener for courier selection change
        const courierSelect = courierCard.querySelector('.courier-select');
        courierSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const hasApi = selectedOption.getAttribute('data-has-api') === 'true';
            const apiSection = courierCard.querySelector('.api-credentials');
            
            if (hasApi) {
                apiSection.style.display = 'block';
            } else {
                apiSection.style.display = 'none';
            }
        });
    });
    
    // Remove courier assignment card
    courierSelection.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-courier') || e.target.closest('.remove-courier')) {
            const courierCard = e.target.closest('.courier-assignment-card');
            if (courierCard) {
                courierCard.remove();
                updateCourierNumbering();
            }
        }
    });
    
    // Update courier numbering
    function updateCourierNumbering() {
        const cards = document.querySelectorAll('.courier-assignment-card');
        cards.forEach((card, index) => {
            const title = card.querySelector('h5');
            if (title) {
                title.textContent = `Courier Assignment #${index + 1}`;
            }
        });
    }
    
    // Add event listeners to existing courier selects
    document.querySelectorAll('.courier-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const hasApi = selectedOption.getAttribute('data-has-api') === 'true';
            
            const courierCard = this.closest('.courier-assignment-card');
            const apiSection = courierCard.querySelector('.api-credentials');
            
            if (hasApi) {
                apiSection.style.display = 'block';
            } else {
                apiSection.style.display = 'none';
            }
        });
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
