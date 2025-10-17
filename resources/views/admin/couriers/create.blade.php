@extends('layouts.admin')

@section('title', 'Add New Courier')
@section('page-title', 'Add New Courier')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-motorcycle"></i>
            Add New Courier
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.couriers.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Couriers
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.couriers.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; gap: 20px;">
                <div>
                    <label for="courier_name" style="display: block; margin-bottom: 8px; font-weight: 600;">Courier Name *</label>
                    <input type="text" id="courier_name" name="courier_name" value="{{ old('courier_name') }}" 
                           style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 16px;"
                           placeholder="Enter courier name" required>
                    @error('courier_name')
                        <div style="color: #e53e3e; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Courier
                </button>
                <a href="{{ route('admin.couriers.index') }}" class="btn" style="background: #e2e8f0; color: #4a5568; text-decoration: none;">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
