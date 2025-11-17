<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toy Collectible Platform')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --light: #f8fafc;
            --dark: #1e293b;
            --border: #e2e8f0;
            --sidebar-width: 280px;
            --header-height: 70px;
            --radius: 12px;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid var(--border);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 0 25px 20px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }
        
        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            font-size: 0.85rem;
            color: var(--secondary);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu-item {
            margin-bottom: 5px;
        }
        
        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu-link:hover,
        .sidebar-menu-link.active {
            background: #f1f5f9;
            border-left-color: var(--primary);
            color: var(--primary);
        }
        
        .sidebar-menu-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Logout button in sidebar */
        .sidebar-logout {
            margin-top: auto;
            padding: 20px 25px 0;
            border-top: 1px solid var(--border);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #dc2626;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: var(--radius);
            background: #fef2f2;
            border: 1px solid #fecaca;
            width: 100%;
            font-weight: 500;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #dc2626;
            color: white;
        }
        
        .logout-btn i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: #f8fafc;
            border-radius: var(--radius);
            cursor: pointer;
            position: relative;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--secondary);
        }

        /* Dropdown menu for user profile */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            min-width: 200px;
            padding: 10px 0;
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .user-profile:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .user-dropdown-item:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        .user-dropdown-item i {
            width: 20px;
            margin-right: 10px;
            font-size: 1rem;
        }

        .user-dropdown-item.logout {
            color: #dc2626;
            border-top: 1px solid var(--border);
            margin-top: 5px;
            padding-top: 15px;
        }

        .user-dropdown-item.logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        .welcome-section {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .welcome-subtitle {
            color: var(--secondary);
            font-size: 1rem;
        }
        
        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin-right: 25px;
        }
        
        .profile-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: var(--secondary);
            margin-bottom: 0;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .detail-group {
            margin-bottom: 20px;
        }
        
        .detail-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        .detail-value:empty::before {
            content: "Not provided";
            color: var(--secondary);
            font-style: italic;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary);
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: var(--secondary);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .profile-details {
                grid-template-columns: 1fr;
            }
            
            .header-title {
                font-size: 1.2rem;
            }
            
            .user-info {
                display: none;
            }
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--dark);
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">ToyCollector</div>
                <div class="sidebar-subtitle">Collectible Platform</div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link active">
                        <i class="fas fa-user"></i>
                        My Profile
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <i class="fas fa-store"></i>
                        ToyMall
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <i class="fas fa-gavel"></i>
                        Auction
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <i class="fas fa-heart"></i>
                        Wishlist
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link">
                        <i class="fas fa-comments"></i>
                        ChatSupport
                    </a>
                </li>
            </ul>

            <!-- Logout Section in Sidebar -->
            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">My Dashboard</h1>
                <div class="header-actions">
                    <div class="user-profile">
                        <div class="user-avatar" id="userAvatar">
                            {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->full_name }}</span>
                            <span class="user-role">Collector</span>
                        </div>
                        
                        <!-- User Dropdown Menu -->
                        <div class="user-dropdown">
                            <a href="#" class="user-dropdown-item">
                                <i class="fas fa-user"></i>
                                My Profile
                            </a>
                            <a href="#" class="user-dropdown-item">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <a href="#" class="user-dropdown-item">
                                <i class="fas fa-bell"></i>
                                Notifications
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-dropdown-item logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
        });
        
        // Initialize user avatar with initials
        function updateUserAvatar() {
            const user = {
                firstName: '{{ auth()->user()->first_name }}',
                lastName: '{{ auth()->user()->last_name }}'
            };
            const avatar = document.getElementById('userAvatar');
            if (avatar) {
                const initials = (user.firstName.charAt(0) + user.lastName.charAt(0)).toUpperCase();
                avatar.textContent = initials;
            }
        }

        // Logout confirmation
        document.addEventListener('DOMContentLoaded', function() {
            updateUserAvatar();
            
            // Get the logout route URL from Blade
            const logoutRoute = '{{ route("logout") }}';
            const logoutForms = document.querySelectorAll(`form[action="${logoutRoute}"]`);
            
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to log out?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>