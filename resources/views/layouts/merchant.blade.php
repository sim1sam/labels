<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Merchant Panel') - {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #2d3748;
            line-height: 1.6;
        }

        .merchant-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .merchant-sidebar {
            width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .merchant-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .merchant-sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .merchant-sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }

        .merchant-nav {
            padding: 20px 0;
        }

        .merchant-nav-item {
            margin-bottom: 5px;
        }

        .merchant-nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .merchant-nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #ffd700;
        }

        .merchant-nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #ffd700;
        }

        .merchant-nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        /* Main Content */
        .merchant-main {
            flex: 1;
            margin-left: 260px;
            background: #f8fafc;
        }

        .merchant-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .merchant-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .merchant-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .merchant-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .merchant-user-details h3 {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }

        .merchant-user-details p {
            font-size: 12px;
            color: #718096;
        }

        .merchant-logout {
            background: #e53e3e;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .merchant-logout:hover {
            background: #c53030;
        }

        .merchant-content {
            padding: 30px;
        }

        /* Cards */
        .merchant-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .merchant-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .merchant-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
        }

        .merchant-card-title i {
            margin-right: 10px;
            color: #4299e1;
        }

        /* Buttons */
        .merchant-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .merchant-btn-primary {
            background: #4299e1;
            color: white;
        }

        .merchant-btn-primary:hover {
            background: #3182ce;
        }

        .merchant-btn-success {
            background: #38a169;
            color: white;
        }

        .merchant-btn-success:hover {
            background: #2f855a;
        }

        .merchant-btn-warning {
            background: #ed8936;
            color: white;
        }

        .merchant-btn-warning:hover {
            background: #dd6b20;
        }

        .merchant-btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .merchant-btn-secondary:hover {
            background: #cbd5e0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .merchant-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .merchant-sidebar.open {
                transform: translateX(0);
            }

            .merchant-main {
                margin-left: 0;
            }

            .merchant-header {
                padding: 15px 20px;
            }

            .merchant-content {
                padding: 20px;
            }
        }

        /* Alerts */
        .merchant-alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .merchant-alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .merchant-alert-warning {
            background: #fef5e7;
            color: #744210;
            border: 1px solid #f6e05e;
        }

        .merchant-alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="merchant-container">
        <!-- Sidebar -->
        <div class="merchant-sidebar">
            <div class="merchant-sidebar-header">
                <h2>Merchant Panel</h2>
                <p>{{ auth()->user()->name }}</p>
            </div>
            
            <nav class="merchant-nav">
                <div class="merchant-nav-item">
                    <a href="{{ route('merchant.dashboard') }}" class="merchant-nav-link {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="merchant-nav-item">
                    <a href="{{ route('merchant.parcels.index') }}" class="merchant-nav-link {{ request()->routeIs('merchant.parcels.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>My Parcels</span>
                    </a>
                </div>
                <div class="merchant-nav-item">
                    <a href="{{ route('merchant.parcels.create') }}" class="merchant-nav-link {{ request()->routeIs('merchant.parcels.create') ? 'active' : '' }}">
                        <i class="fas fa-plus"></i>
                        <span>Create Parcel</span>
                    </a>
                </div>
                <div class="merchant-nav-item">
                    <a href="{{ route('merchant.reports.index') }}" class="merchant-nav-link {{ request()->routeIs('merchant.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                <div class="merchant-nav-item">
                    <a href="{{ route('profile.show') }}" class="merchant-nav-link {{ request()->is('profile*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="merchant-main">
            <!-- Header -->
            <div class="merchant-header">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="merchant-user-info">
                    <div class="merchant-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="merchant-user-details">
                        <h3>{{ auth()->user()->name }}</h3>
                        <p>Merchant Account</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="merchant-logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="merchant-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.merchant-sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
