@extends('layouts.admin')

@section('title', 'Orders Management')
@section('page-title', 'Orders Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-box"></i>
            All Orders
        </h3>
        <div style="margin-left: auto;">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create Order
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 35px; height: 35px; font-size: 14px;">
                                    {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $order->customer_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $order->customer_email }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i>
                                    Pending
                                </span>
                            @elseif($order->status === 'in_transit')
                                <span class="badge badge-info">
                                    <i class="fas fa-truck"></i>
                                    In Transit
                                </span>
                            @elseif($order->status === 'delivered')
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                    Delivered
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i>
                                    Cancelled
                                </span>
                            @endif
                        </td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
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

<!-- Order Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value">{{ count($orders) }}</div>
        <div class="stat-label">Total Orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($orders)->where('status', 'pending')->count() }}</div>
        <div class="stat-label">Pending</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($orders)->where('status', 'in_transit')->count() }}</div>
        <div class="stat-label">In Transit</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <div class="stat-value">{{ collect($orders)->where('status', 'delivered')->count() }}</div>
        <div class="stat-label">Delivered</div>
    </div>
</div>
@endsection
