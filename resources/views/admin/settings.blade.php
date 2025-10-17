@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
@if(session('success'))
    <div style="background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Currency Settings -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-coins"></i>
            Currency Settings
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px;">
                    Default Currency
                </label>
                <select name="currency" required style="width: 100%; max-width: 300px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    @foreach($availableCurrencies as $code => $name)
                        <option value="{{ $code }}" {{ $currentCurrency === $code ? 'selected' : '' }}>
                            {{ $code }} - {{ $name }}
                        </option>
                    @endforeach
                </select>
                <p style="color: #718096; font-size: 14px; margin-top: 8px;">
                    This currency will be used for all amount fields throughout the application.
                </p>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Current Settings Info -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i>
            Current Settings
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        {{ $currentCurrency }}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #2d3748;">Current Currency</div>
                        <div style="color: #718096; font-size: 14px;">{{ $availableCurrencies[$currentCurrency] }}</div>
                    </div>
                </div>
            </div>

            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #2d3748;">Available Currencies</div>
                        <div style="color: #718096; font-size: 14px;">{{ count($availableCurrencies) }} currencies supported</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Currency Examples -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-eye"></i>
            Currency Preview
        </h3>
    </div>
    <div class="card-body">
        <p style="color: #718096; margin-bottom: 15px;">
            Here's how amounts will be displayed with the current currency:
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="background: #e6fffa; padding: 15px; border-radius: 8px; border: 1px solid #b2f5ea;">
                <div style="font-weight: 600; color: #234e52; margin-bottom: 5px;">Parcel COD Amount</div>
                <div style="font-size: 18px; font-weight: 700; color: #234e52;">
                    {{ number_format(100, 0) }} {{ $currentCurrency }}
                </div>
            </div>
            
            <div style="background: #e6fffa; padding: 15px; border-radius: 8px; border: 1px solid #b2f5ea;">
                <div style="font-weight: 600; color: #234e52; margin-bottom: 5px;">Label Bill Amount</div>
                <div style="font-size: 18px; font-weight: 700; color: #234e52;">
                    {{ number_format(250, 0) }} {{ $currentCurrency }}
                </div>
            </div>
            
            <div style="background: #e6fffa; padding: 15px; border-radius: 8px; border: 1px solid #b2f5ea;">
                <div style="font-weight: 600; color: #234e52; margin-bottom: 5px;">Reports Amount</div>
                <div style="font-size: 18px; font-weight: 700; color: #234e52;">
                    {{ number_format(1500, 0) }} {{ $currentCurrency }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection