@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
@if(session('success'))
    <div style="background: #c6f6d5; color: #22543d; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Header Actions -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="margin: 0; color: #2d3748; font-size: 28px; font-weight: 700;">Customer Management</h1>
        <p style="margin: 5px 0 0 0; color: #718096; font-size: 16px;">Manage customer information and view parcel history</p>
    </div>
    <div style="display: flex; gap: 15px;">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add New Customer
        </a>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Customer Name</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Mobile Number</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Address</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Total Parcels</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Created</th>
                            <th style="padding: 15px; text-align: center; font-weight: 600; color: #2d3748; border-bottom: 1px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td style="padding: 15px; color: #2d3748;">
                                <div style="font-weight: 600;">{{ $customer->customer_name }}</div>
                            </td>
                            <td style="padding: 15px; color: #2d3748;">
                                <div style="font-weight: 500;">{{ $customer->mobile_number }}</div>
                            </td>
                            <td style="padding: 15px; color: #2d3748;">
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $customer->address }}">
                                    {{ $customer->address }}
                                </div>
                            </td>
                            <td style="padding: 15px; color: #2d3748; text-align: center;">
                                <span style="background: #e6f3ff; color: #2c5282; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ $customer->parcels->count() }}
                                </span>
                            </td>
                            <td style="padding: 15px; color: #718096; font-size: 14px;">
                                {{ $customer->created_at->format('M d, Y') }}
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
                {{ $customers->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 40px; color: #718096;">
                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e0;"></i>
                <h3 style="margin: 0 0 10px 0; color: #4a5568;">No Customers Found</h3>
                <p style="margin: 0 0 20px 0;">Start by adding your first customer or create a parcel to automatically add customers.</p>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add First Customer
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
