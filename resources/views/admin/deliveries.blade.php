@extends('layouts.admin')

@section('title', 'Deliveries Management')
@section('page-title', 'Deliveries Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-truck"></i>
            Active Deliveries
        </h3>
        <div style="margin-left: auto;">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Assign Delivery
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Order ID</th>
                        <th>Courier</th>
                        <th>Status</th>
                        <th>Pickup Address</th>
                        <th>Delivery Address</th>
                        <th>ETA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveries as $delivery)
                    <tr>
                        <td>#{{ $delivery->id }}</td>
                        <td>#{{ $delivery->order_id }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 35px; height: 35px; font-size: 14px;">
                                    {{ strtoupper(substr($delivery->courier_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $delivery->courier_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($delivery->status === 'assigned')
                                <span class="badge badge-info">
                                    <i class="fas fa-user-check"></i>
                                    Assigned
                                </span>
                            @elseif($delivery->status === 'in_transit')
                                <span class="badge badge-warning">
                                    <i class="fas fa-truck"></i>
                                    In Transit
                                </span>
                            @elseif($delivery->status === 'delivered')
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                    Delivered
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i>
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $delivery->pickup_address }}
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $delivery->delivery_address }}
                            </div>
                        </td>
                        <td>{{ $delivery->estimated_time }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delivery Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">{{ count($deliveries) }}</div>
        <div class="stat-label">Active Deliveries</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($deliveries)->where('status', 'assigned')->count() }}</div>
        <div class="stat-label">Assigned</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($deliveries)->where('status', 'in_transit')->count() }}</div>
        <div class="stat-label">In Transit</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($deliveries)->where('status', 'delivered')->count() }}</div>
        <div class="stat-label">Completed</div>
    </div>
</div>
@endsection
