@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="margin: 0; color: #2d3748; font-size: 28px; font-weight: 700;">Edit Customer</h1>
        <p style="margin: 5px 0 0 0; color: #718096; font-size: 16px;">Update customer information</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info">
            <i class="fas fa-eye"></i>
            View Details
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Customers
        </a>
    </div>
</div>

<!-- Customer Form -->
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                        Customer Name <span style="color: #e53e3e;">*</span>
                    </label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" required
                           style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;"
                           placeholder="Enter customer full name">
                    @error('customer_name')
                        <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                        Mobile Number <span style="color: #e53e3e;">*</span>
                    </label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $customer->mobile_number) }}" required
                           style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;"
                           placeholder="Enter mobile number">
                    @error('mobile_number')
                        <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Address <span style="color: #e53e3e;">*</span>
                </label>
                <textarea name="address" rows="3" required
                          style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; resize: vertical;"
                          placeholder="Enter complete delivery address">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                    <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>


            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Customer
                </button>
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
