@extends('layouts.admin')

@section('title', 'Couriers Management')
@section('page-title', 'Couriers Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-motorcycle"></i>
            All Couriers
        </h3>
        <div style="margin-left: auto;">
            <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New Courier
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Courier Name</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($couriers as $courier)
                    <tr>
                        <td>{{ $courier->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 35px; height: 35px; font-size: 14px;">
                                    {{ strtoupper(substr($courier->courier_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $courier->courier_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($courier->status === 'active')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i>
                                    Active
                                </span>
                            @elseif($courier->status === 'busy')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i>
                                    Busy
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <span style="color: #666; font-size: 14px;">
                                {{ $courier->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.couriers.show', $courier) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this courier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
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
    </div>
</div>

<!-- Courier Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon couriers">
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>
        <div class="stat-value">{{ count($couriers) }}</div>
        <div class="stat-label">Total Couriers</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon couriers">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($couriers)->where('status', 'active')->count() }}</div>
        <div class="stat-label">Active</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon couriers">
                <i class="fas fa-motorcycle"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($couriers)->where('vehicle_type', 'Motorcycle')->count() }}</div>
        <div class="stat-label">Motorcycles</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon couriers">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($couriers)->where('vehicle_type', 'Van')->count() }}</div>
        <div class="stat-label">Vans</div>
    </div>
</div>
@endsection
