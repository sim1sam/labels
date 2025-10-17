@extends('layouts.admin')

@section('title', 'Merchants Management')
@section('page-title', 'Merchants Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-store"></i>
            Merchants Management
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New Merchant
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div style="background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($merchants->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <thead style="background: #f7fafc;">
                        <tr>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Merchant ID</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Shop Name</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">User</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Email</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Couriers</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Created</th>
                            <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($merchants as $merchant)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px; color: #4a5568;">
                                    <span style="background: #e6fffa; color: #234e52; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        {{ $merchant->merchant_id }}
                                    </span>
                                </td>
                                <td style="padding: 15px; color: #2d3748; font-weight: 600;">{{ $merchant->shop_name }}</td>
                                <td style="padding: 15px; color: #4a5568;">{{ $merchant->user->name }}</td>
                                <td style="padding: 15px; color: #4a5568;">{{ $merchant->email }}</td>
                                <td style="padding: 15px; color: #4a5568;">
                                    <span style="background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                        {{ $merchant->couriers->count() }} courier{{ $merchant->couriers->count() != 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td style="padding: 15px;">
                                    @if($merchant->status == 'active')
                                        <span style="background: #c6f6d5; color: #22543d; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    @elseif($merchant->status == 'inactive')
                                        <span style="background: #fed7d7; color: #742a2a; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-pause-circle"></i> Inactive
                                        </span>
                                    @else
                                        <span style="background: #fef5e7; color: #744210; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-exclamation-triangle"></i> Suspended
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 15px; color: #718096; font-size: 14px;">
                                    {{ $merchant->created_at->format('M d, Y') }}
                                </td>
                                <td style="padding: 15px; text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.merchants.show', $merchant) }}" class="btn btn-sm" style="background: #4299e1; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-sm" style="background: #ed8936; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.merchants.destroy', $merchant) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this merchant?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm" style="background: #e53e3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $merchants->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #718096;">
                <i class="fas fa-store" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e0;"></i>
                <h3 style="margin-bottom: 8px; color: #4a5568;">No Merchants Found</h3>
                <p style="margin-bottom: 20px;">Get started by creating your first merchant.</p>
                <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New Merchant
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
