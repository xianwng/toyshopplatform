<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Admin Dashboard</title>

    <!-- Stylesheets -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- Tailwind / base app styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> <!-- Your custom overrides -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet"> <!-- Google Fonts -->
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <!-- Brand/logo -->
                <a class="sidebar-brand" href="#">
                    <span class="align-middle">MENU</span>
                </a>

                <!-- Sidebar navigation -->
                <ul class="sidebar-nav">
                    <!-- Dashboard -->
                    <li class="sidebar-item {{ request()->routeIs('admin.home') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.home') }}">
                            <i class="align-middle" data-feather="home"></i> 
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <!-- Product Management -->
                    <li class="sidebar-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.products.index') }}">
                            <i class="align-middle" data-feather="package"></i>
                            <span class="align-middle">Product Management</span>
                        </a>
                    </li>

                    <!-- Auction Management -->
                    <li class="sidebar-item {{ request()->routeIs('admin.auctions.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.auctions.index') }}">
                            <i class="align-middle" data-feather="tag"></i>
                            <span class="align-middle">Auction Management</span>
                        </a>
                    </li>

                    <!-- Trading Management -->
                    <li class="sidebar-item {{ request()->routeIs('admin.trading.*') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.trading.management') }}">
                            <i class="align-middle" data-feather="repeat"></i>
                            <span class="align-middle">Trading Management</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content area -->
        <div class="main">
            <!-- Navbar -->
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <!-- User profile dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img src="{{ asset('img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded-circle me-1" alt="Charles Hall" />
                                <span class="text-dark">{{ Auth::user()->name ?? 'Admin' }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- Admin Profile Link -->
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="align-middle me-1" data-feather="user"></i> Admin Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <!-- Admin Settings Link -->
                                <a class="dropdown-item" href="{{ route('admin.profile.settings') }}">
                                    <i class="align-middle me-1" data-feather="settings"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <!-- Logout Link -->
                                <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="align-middle me-1" data-feather="log-out"></i> Log out
                                </a>
                                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content placeholder -->
            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JS files for sidebar & navbar functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Feather icons
            feather.replace();
        });
    </script>

</body>
</html>