@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
        <div class="stat-label">Total Users</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalOrders ?? 0 }}</div>
        <div class="stat-label">Total Orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalDeliveries ?? 0 }}</div>
        <div class="stat-label">Active Deliveries</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon revenue">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-value">{{ number_format($totalRevenue ?? 0, 2) }} {{ \App\Models\Setting::getCurrency() }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock"></i>
            Recent Activity
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                    M
                                </div>
                                <div>
                                    <div style="font-weight: 600;">Merchant User</div>
                                    <div style="font-size: 12px; color: #718096;">merchant@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Created new order</td>
                        <td>2 minutes ago</td>
                        <td><span class="badge badge-success">Completed</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                    A
                                </div>
                                <div>
                                    <div style="font-weight: 600;">Admin User</div>
                                    <div style="font-size: 12px; color: #718096;">admin@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Updated system settings</td>
                        <td>15 minutes ago</td>
                        <td><span class="badge badge-info">Updated</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 30px; height: 30px; font-size: 12px;">
                                    M
                                </div>
                                <div>
                                    <div style="font-weight: 600;">Merchant User</div>
                                    <div style="font-size: 12px; color: #718096;">merchant@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Requested delivery pickup</td>
                        <td>1 hour ago</td>
                        <td><span class="badge badge-warning">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt"></i>
            Quick Actions
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="/admin/users" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Add New User
            </a>
            <a href="/admin/orders" class="btn btn-success">
                <i class="fas fa-plus"></i>
                Create Order
            </a>
            <a href="/admin/deliveries" class="btn btn-primary">
                <i class="fas fa-truck"></i>
                Assign Delivery
            </a>
            <a href="/admin/reports" class="btn btn-success">
                <i class="fas fa-chart-line"></i>
                View Reports
            </a>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-server"></i>
            System Status
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Online</div>
                <div style="color: #718096; font-size: 14px;">API Status</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">99.9%</div>
                <div style="color: #718096; font-size: 14px;">Uptime</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Fast</div>
                <div style="color: #718096; font-size: 14px;">Response Time</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 24px; font-weight: 600; color: #10b981;">Secure</div>
                <div style="color: #718096; font-size: 14px;">SSL Status</div>
            </div>
        </div>
    </div>
</div>
@endsection
