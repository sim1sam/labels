@extends('layouts.admin')

@section('title', 'Merchant Details')
@section('page-title', 'Merchant Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-store"></i>
            {{ $merchant->shop_name }}
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.merchants.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Merchants
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; gap: 30px;">
            <!-- Merchant Information -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i>
                    Merchant Information
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Merchant ID</label>
                        <div style="background: #e6fffa; color: #234e52; padding: 8px 12px; border-radius: 6px; font-weight: 600;">
                            {{ $merchant->merchant_id }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Status</label>
                        <div>
                            @if($merchant->status == 'active')
                                <span style="background: #c6f6d5; color: #22543d; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @elseif($merchant->status == 'inactive')
                                <span style="background: #fed7d7; color: #742a2a; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-pause-circle"></i> Inactive
                                </span>
                            @else
                                <span style="background: #fef5e7; color: #744210; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-exclamation-triangle"></i> Suspended
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Logo</label>
                        <div>
                            @if($merchant->logo)
                                <img src="/{{ $merchant->logo }}" alt="Merchant Logo" style="max-width: 80px; max-height: 80px; border-radius: 8px; border: 2px solid #e2e8f0;">
                            @else
                                <div style="width: 80px; height: 80px; background: #f7fafc; border: 2px dashed #cbd5e0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #a0aec0;">
                                    <i class="fas fa-image" style="font-size: 24px;"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Merchant Name</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $merchant->user->name }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Email Address</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $merchant->email }}
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Phone Number</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $merchant->phone ?? 'Not provided' }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Created Date</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $merchant->created_at->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>
                </div>
                
                @if($merchant->address)
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Address</label>
                    <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                        {{ $merchant->address }}
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Assigned Couriers -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-motorcycle"></i>
                    Assigned Couriers ({{ $merchant->couriers->count() }})
                </h4>
                
                @if($merchant->couriers->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                            <thead style="background: #f7fafc;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Courier Name</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant ID</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Vehicle Type</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Assigned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($merchant->couriers as $courier)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px; color: #2d3748; font-weight: 600;">{{ $courier->courier_name }}</td>
                                        <td style="padding: 12px; color: #4a5568;">
                                            <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ $courier->pivot->merchant_custom_id }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px; color: #4a5568;">
                                            <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ ucfirst($courier->vehicle_type) }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px;">
                                            @if($courier->pivot->status == 'active')
                                                <span style="background: #c6f6d5; color: #22543d; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            @else
                                                <span style="background: #fed7d7; color: #742a2a; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                    <i class="fas fa-pause-circle"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px; color: #718096; font-size: 14px;">
                                            {{ $courier->pivot->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: #718096;">
                        <i class="fas fa-motorcycle" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e0;"></i>
                        <h3 style="margin-bottom: 8px; color: #4a5568;">No Couriers Assigned</h3>
                        <p style="margin-bottom: 20px;">This merchant doesn't have any couriers assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Merchant
            </a>
            <form action="{{ route('admin.merchants.destroy', $merchant) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this merchant? This will also delete the associated user account.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete Merchant
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
