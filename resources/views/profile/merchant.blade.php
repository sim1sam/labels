@extends('layouts.merchant')

@section('title', 'Merchant Profile')

@push('styles')
<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .profile-header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .profile-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .profile-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        overflow: hidden;
        border: none;
    }

    .profile-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 25px 30px;
        border-bottom: 1px solid #e2e8f0;
    }

    .profile-card-header h3 {
        margin: 0;
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-card-header i {
        color: #667eea;
        font-size: 1.3rem;
    }

    .profile-card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-group label i {
        color: #667eea;
        width: 16px;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        width: 100%;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
        outline: none;
    }

    .form-control[readonly] {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }

    .form-control.is-invalid {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .invalid-feedback {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .section-divider {
        border: none;
        height: 2px;
        background: linear-gradient(90deg, #e2e8f0 0%, #667eea 50%, #e2e8f0 100%);
        margin: 30px 0;
        border-radius: 1px;
    }

    .section-title {
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #667eea;
    }

    .section-description {
        color: #718096;
        font-size: 0.95rem;
        margin-bottom: 25px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 8px;
        border-left: 4px solid #667eea;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
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

    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .row {
        display: flex;
        gap: 20px;
        margin-bottom: 0;
    }

    .col-md-6 {
        flex: 1;
    }

    .col-md-4 {
        flex: 1;
    }

    .logo-upload {
        background: #f8fafc;
        border: 2px dashed #cbd5e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .logo-upload:hover {
        border-color: #667eea;
        background: #f7fafc;
    }

    .current-logo {
        margin-bottom: 15px;
    }

    .current-logo img {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .current-logo p {
        margin-top: 10px;
        color: #718096;
        font-size: 0.9rem;
    }

    .form-control-file {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        background: white;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-control-file:hover {
        border-color: #667eea;
    }

    .form-text {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 8px;
        display: block;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    @media (max-width: 768px) {
        .profile-container {
            padding: 15px;
        }
        
        .profile-header {
            padding: 20px;
        }
        
        .profile-header h1 {
            font-size: 2rem;
        }
        
        .profile-card-body {
            padding: 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-store"></i> Merchant Profile</h1>
            <p>Manage your account settings and business information</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update.merchant') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-card">
                <div class="profile-card-header">
                    <h3><i class="fas fa-user"></i> Personal Information</h3>
                </div>
                <div class="profile-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_type">
                            <i class="fas fa-user-tag"></i> User Type
                        </label>
                        <input type="text" 
                               class="form-control" 
                               value="{{ ucfirst($user->user_type) }}" 
                               readonly>
                    </div>

                    <hr class="section-divider">
                    
                    <div class="section-title">
                        <i class="fas fa-lock"></i> Change Password
                    </div>
                    <div class="section-description">
                        Leave password fields empty if you don't want to change your password.
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="current_password">
                                    <i class="fas fa-key"></i> Current Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="password">
                                    <i class="fas fa-lock"></i> New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="password_confirmation">
                                    <i class="fas fa-lock"></i> Confirm New Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <h3><i class="fas fa-store"></i> Business Information</h3>
                </div>
                <div class="profile-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="shop_name">
                                    <i class="fas fa-store"></i> Shop Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('shop_name') is-invalid @enderror" 
                                       id="shop_name" 
                                       name="shop_name" 
                                       value="{{ old('shop_name', $merchant->shop_name ?? '') }}" 
                                       required>
                                @error('shop_name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $merchant->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3">{{ old('address', $merchant->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="logo">
                            <i class="fas fa-image"></i> Shop Logo
                        </label>
                        <div class="logo-upload">
                            @if($merchant && $merchant->logo)
                                <div class="current-logo">
                                    <img src="{{ asset($merchant->logo) }}" 
                                         alt="Current Logo" 
                                         style="max-width: 150px; max-height: 150px;">
                                    <p>Current logo</p>
                                </div>
                            @endif
                            <input type="file" 
                                   class="form-control-file @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*">
                            <small class="form-text">
                                Upload a logo for your shop (JPEG, PNG, JPG, GIF - Max 2MB)
                            </small>
                            @error('logo')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="merchant_id">
                                    <i class="fas fa-id-card"></i> Merchant ID
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $merchant->merchant_id ?? '' }}" 
                                       readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">
                                    <i class="fas fa-toggle-on"></i> Status
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ ucfirst($merchant->status ?? '') }}" 
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <a href="{{ route('merchant.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
