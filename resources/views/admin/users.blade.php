@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            All Users
        </h3>
        <div style="margin-left: auto;">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add New User
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 35px; height: 35px; font-size: 14px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->user_type === 'admin')
                                <span class="badge badge-danger">
                                    <i class="fas fa-user-shield"></i>
                                    Admin
                                </span>
                            @else
                                <span class="badge badge-info">
                                    <i class="fas fa-store"></i>
                                    Merchant
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i>
                                    Verified
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-sm btn-primary">
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
        
        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $users->total() }}</div>
        <div class="stat-label">Total Users</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
        <div class="stat-value">{{ $users->where('user_type', 'admin')->count() }}</div>
        <div class="stat-label">Admins</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-store"></i>
            </div>
        </div>
        <div class="stat-value">{{ $users->where('user_type', 'merchant')->count() }}</div>
        <div class="stat-label">Merchants</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $users->where('email_verified_at', '!=', null)->count() }}</div>
        <div class="stat-label">Verified</div>
    </div>
</div>
@endsection