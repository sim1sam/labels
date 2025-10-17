@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<!-- Report Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon revenue">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-value">$12,450</div>
        <div class="stat-label">Monthly Revenue</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orders">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-value">156</div>
        <div class="stat-label">Orders This Month</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon deliveries">
                <i class="fas fa-truck"></i>
            </div>
        </div>
        <div class="stat-value">89</div>
        <div class="stat-label">Completed Deliveries</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">23</div>
        <div class="stat-label">New Users</div>
    </div>
</div>

<!-- Charts Section -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i>
            Delivery Performance
        </h3>
    </div>
    <div class="card-body">
        <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 8px;">
            <div style="text-align: center; color: #718096;">
                <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 15px;"></i>
                <div>Chart visualization would go here</div>
                <div style="font-size: 14px; margin-top: 5px;">Integration with Chart.js or similar library</div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Report -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-dollar-sign"></i>
            Revenue Report
        </h3>
    </div>
    <div class="card-body">
        <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 8px;">
            <div style="text-align: center; color: #718096;">
                <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 15px;"></i>
                <div>Revenue chart would go here</div>
                <div style="font-size: 14px; margin-top: 5px;">Monthly revenue trends and analytics</div>
            </div>
        </div>
    </div>
</div>

<!-- Export Options -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-download"></i>
            Export Reports
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <button class="btn btn-primary">
                <i class="fas fa-file-pdf"></i>
                Export PDF
            </button>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
            <button class="btn btn-info">
                <i class="fas fa-file-csv"></i>
                Export CSV
            </button>
            <button class="btn btn-warning">
                <i class="fas fa-envelope"></i>
                Email Report
            </button>
        </div>
    </div>
</div>
@endsection
