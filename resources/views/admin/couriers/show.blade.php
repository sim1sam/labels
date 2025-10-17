@extends('layouts.admin')

@section('title', 'Courier Details')
@section('page-title', 'Courier Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-motorcycle"></i>
            {{ $courier->courier_name }}
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.couriers.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Couriers
            </a>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; gap: 30px;">
            <!-- Courier Information -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i>
                    Courier Information
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Courier ID</label>
                        <div style="background: #e6fffa; color: #234e52; padding: 8px 12px; border-radius: 6px; font-weight: 600;">
                            #{{ $courier->id }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Status</label>
                        <div>
                            @if($courier->status == 'active')
                                <span style="background: #c6f6d5; color: #22543d; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @elseif($courier->status == 'busy')
                                <span style="background: #fef5e7; color: #744210; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-clock"></i> Busy
                                </span>
                            @else
                                <span style="background: #fed7d7; color: #742a2a; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-pause-circle"></i> Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Courier Name</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748; font-weight: 600;">
                            {{ $courier->courier_name }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Vehicle Type</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                {{ ucfirst($courier->vehicle_type) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Phone Number</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $courier->phone ?? 'Not provided' }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Email Address</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            {{ $courier->email ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Rating</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-star" style="color: #fbbf24;"></i>
                                <span style="font-weight: 600;">{{ $courier->rating }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Total Deliveries</label>
                        <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                            <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                {{ $courier->total_deliveries }} deliveries
                            </span>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #4a5568;">Created Date</label>
                    <div style="padding: 8px 12px; background: #f7fafc; border-radius: 6px; color: #2d3748;">
                        {{ $courier->created_at->format('M d, Y \a\t h:i A') }}
                    </div>
                </div>
            </div>
            
            <!-- Assigned Merchants -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h4 style="margin-bottom: 15px; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-store"></i>
                    Assigned Merchants ({{ $courier->merchants->count() }})
                </h4>
                
                @if($courier->merchants->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                            <thead style="background: #f7fafc;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant ID</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Assigned Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courier->merchants as $merchant)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px; color: #2d3748; font-weight: 600;">{{ $merchant->shop_name }}</td>
                                        <td style="padding: 12px; color: #4a5568;">
                                            <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                {{ $merchant->merchant_id }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px;">
                                            @if($merchant->pivot->status == 'active')
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
                                            {{ $merchant->pivot->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: #718096;">
                        <i class="fas fa-store" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e0;"></i>
                        <h3 style="margin-bottom: 8px; color: #4a5568;">No Merchants Assigned</h3>
                        <p style="margin-bottom: 20px;">This courier doesn't have any merchants assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Edit Courier
            </a>
            <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this courier?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete Courier
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

