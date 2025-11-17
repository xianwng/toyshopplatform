@extends('customer.layouts.cmaster')

@section('title', 'My Profile - Toyspace')

@section('content')
<!-- Add Font Awesome CDN if not already in your layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="main">
    <section class="module">
        <div class="container">
            <div class="profile-wrapper">

                <!-- Profile Header with Background -->
                <div class="profile-header">
                    <div class="header-background"></div>
                    <div class="header-content">
                        <div class="avatar-container">
                            <div class="profile-avatar">
                                <i class="fas fa-user"></i>
                                <div class="online-indicator"></div>
                            </div>
                        </div>
                        <div class="user-info">
                            <h1 class="user-name">{{ $user->getFullName() }}</h1>
                            <p class="user-handle">@<span>{{ $user->username }}</span></p>
                            <div class="user-stats">
                                <div class="stat">
                                    <i class="fas fa-gem stat-icon"></i>
                                    <span class="stat-number">{{ $user->getFormattedDiamondBalance() }}</span>
                                    <span class="stat-label">Diamonds</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-calendar-alt stat-icon"></i>
                                    <span class="stat-number">{{ $user->created_at->format('M Y') }}</span>
                                    <span class="stat-label">Member Since</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-check-circle stat-icon"></i>
                                    <span class="stat-number">Active</span>
                                    <span class="stat-label">Status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="profile-grid">
                    <!-- Left Column -->
                    <div class="profile-column">
                        <!-- Account Information Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h3>Account Information</h3>
                                <div class="header-actions">
                                    <a href="{{ route('profile.edit') }}" class="btn-edit-profile">
                                        <i class="fas fa-edit me-2"></i>Edit Profile
                                    </a>
                                </div>
                            </div>
                            <div class="card-content white-bg">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Username</label>
                                        <span class="info-value">{{ $user->username }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Email Address</label>
                                        <span class="info-value">{{ $user->email }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Full Name</label>
                                        <span class="info-value">{{ $user->getFullName() }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Contact Number</label>
                                        <span class="info-value">{{ $user->contact_number ?? 'Not provided' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Overview Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <h3>Account Overview</h3>
                            </div>
                            <div class="card-content white-bg">
                                <div class="overview-item">
                                    <div class="overview-icon success">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="overview-content">
                                        <span class="overview-label">Member Since</span>
                                        <span class="overview-value">{{ $user->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="overview-item">
                                    <div class="overview-icon primary">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <div class="overview-content">
                                        <span class="overview-label">Last Updated</span>
                                        <span class="overview-value">{{ $user->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="profile-column">
                        <!-- Address Information Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <h3>Address Information</h3>
                                <div class="header-actions">
                                    <a href="{{ route('address.create') }}" class="btn-manage">
                                        <i class="fas fa-edit me-2"></i>Manage Addresses
                                    </a>
                                </div>
                            </div>
                            <div class="card-content white-bg">
                                <div class="address-section">
                                    <div class="address-type">
                                        <i class="fas fa-home"></i>
                                        <span>Home Address</span>
                                    </div>
                                    <div class="address-content">
                                        @if($user->home_address)
                                            <p>{{ $user->home_address }}</p>
                                        @else
                                            <p class="text-muted">No home address set</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="address-section">
                                    <div class="address-type">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Work Address</span>
                                    </div>
                                    <div class="address-content">
                                        @if($user->work_address)
                                            <p>{{ $user->work_address }}</p>
                                        @else
                                            <p class="text-muted">No work address set</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diamond Balance Card -->
                        <div class="profile-card diamond-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <h3>Diamond Balance</h3>
                            </div>
                            <div class="card-content white-bg">
                                <div class="diamond-display">
                                    <div class="diamond-icon-large">
                                        <i class="fas fa-gem"></i>
                                    </div>
                                    <div class="diamond-info">
                                        <span class="diamond-balance">{{ $user->getFormattedDiamondBalance() }}</span>
                                        <span class="diamond-label">Available Diamonds</span>
                                    </div>
                                </div>
                                <div class="diamond-actions">
                                    <button class="btn-diamond">
                                        <i class="fas fa-shopping-cart me-2"></i>Buy More
                                    </button>
                                    <button class="btn-diamond outline">
                                        <i class="fas fa-receipt me-2"></i>History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
/* Modern CSS Variables - Purple & White Theme */
:root {
    --primary: #8b5cf6;
    --primary-dark: #7c3aed;
    --primary-light: #a78bfa;
    --secondary: #f59e0b;
    --accent: #ec4899;
    --success: #10b981;
    --warning: #f59e0b;
    --dark: #1f2937;
    --darker: #111827;
    --light: #ffffff;
    --gray: #6b7280;
    --light-gray: #f8fafc;
    --border: #e5e7eb;
    --shadow: 0 8px 30px rgba(0,0,0,0.08);
    --gradient: linear-gradient(135deg, #8b5cf6, #7c3aed);
    --gradient-dark: linear-gradient(135deg, #7c3aed, #6d28d9);
}

/* Profile Wrapper */
.profile-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* Enhanced Profile Header */
.profile-header {
    position: relative;
    background: var(--gradient-dark);
    border-radius: 20px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(124, 58, 237, 0.25) 0%, transparent 50%);
}

.header-content {
    position: relative;
    padding: 3rem 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    color: white;
}

.avatar-container {
    position: relative;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    border: 4px solid rgba(255,255,255,0.3);
    position: relative;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.online-indicator {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 20px;
    height: 20px;
    background: var(--success);
    border: 3px solid var(--primary-dark);
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.user-info {
    flex: 1;
}

.user-name {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    background: linear-gradient(135deg, #fff, #e0e7ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.user-handle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0 0 1.5rem 0;
    color: #c4b5fd;
}

.user-stats {
    display: flex;
    gap: 2rem;
}

.stat {
    text-align: center;
    background: rgba(255,255,255,0.1);
    padding: 0.8rem 1rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.stat-icon {
    font-size: 1.2rem;
    color: #c4b5fd;
    margin-bottom: 0.2rem;
}

.stat-number {
    display: block;
    font-size: 1.3rem;
    font-weight: 700;
    color: white;
}

.stat-label {
    font-size: 0.8rem;
    opacity: 0.8;
    color: #c4b5fd;
}

/* Profile Grid */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 968px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        padding: 2rem;
    }
    
    .user-stats {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .stat {
        flex: 1;
        min-width: 120px;
    }
}

/* Profile Cards */
.profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--border);
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(139, 92, 246, 0.15);
}

.card-header {
    background: var(--darker);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
}

.card-header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: var(--gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: white;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

/* White Background for Content */
.card-content.white-bg {
    background: var(--light);
    padding: 1.5rem;
}

/* Info Items */
.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1rem;
    border: 1px solid var(--border);
}

.info-content {
    flex: 1;
}

.info-content label {
    display: block;
    font-size: 0.85rem;
    color: var(--gray);
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.info-value {
    font-size: 1rem;
    color: var(--dark);
    font-weight: 500;
}

/* Overview Items */
.overview-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border);
}

.overview-item:last-child {
    border-bottom: none;
}

.overview-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.overview-icon.success { background: var(--success); }
.overview-icon.primary { background: var(--primary); }

.overview-content {
    flex: 1;
}

.overview-label {
    display: block;
    font-size: 0.85rem;
    color: var(--gray);
    margin-bottom: 0.25rem;
}

.overview-value {
    font-size: 1rem;
    color: var(--dark);
    font-weight: 500;
}

/* Address Section */
.address-section {
    margin-bottom: 1.5rem;
    padding: 1.2rem;
    background: var(--light-gray);
    border-radius: 12px;
    border-left: 4px solid var(--primary);
    border: 1px solid var(--border);
}

.address-type {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--dark);
}

.address-content p {
    margin: 0;
    color: var(--gray);
    line-height: 1.5;
}

/* Diamond Card */
.diamond-card {
    background: white;
    border: 2px solid var(--primary);
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
}

.diamond-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    padding: 1rem 0;
}

.diamond-icon-large {
    font-size: 3rem;
    color: var(--primary);
    filter: drop-shadow(0 4px 12px rgba(139, 92, 246, 0.3));
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.diamond-info {
    text-align: center;
}

.diamond-balance {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark);
    line-height: 1;
}

.diamond-label {
    font-size: 0.9rem;
    color: var(--gray);
    margin-top: 0.5rem;
}

.diamond-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

/* Buttons */
.btn-manage, .btn-edit-profile {
    background: var(--gradient);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    font-size: 0.9rem;
    white-space: nowrap;
}

.btn-manage:hover, .btn-edit-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    background: var(--gradient-dark);
}

.btn-diamond {
    flex: 1;
    background: var(--primary);
    color: white;
    padding: 0.7rem 1rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.btn-diamond.outline {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-diamond:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
}

.btn-diamond.outline:hover {
    background: rgba(139, 92, 246, 0.1);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.profile-card {
    animation: fadeInUp 0.6s ease-out;
}

.profile-card:nth-child(2) {
    animation-delay: 0.1s;
}

.profile-card:nth-child(3) {
    animation-delay: 0.2s;
}

.profile-card:nth-child(4) {
    animation-delay: 0.3s;
}

/* Text Colors */
.text-muted {
    color: var(--gray) !important;
}

/* Ensure Font Awesome icons are visible */
.fas, .far, .fab {
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .btn-manage, .btn-edit-profile {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page with improved button placement loaded for {{ $user->username }}');
    
    // Add interactive elements
    const cards = document.querySelectorAll('.profile-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add pulse animation to diamond icon
    const diamondIcon = document.querySelector('.diamond-icon-large');
    if (diamondIcon) {
        setInterval(() => {
            diamondIcon.style.transform = 'scale(1.1)';
            setTimeout(() => {
                diamondIcon.style.transform = 'scale(1)';
            }, 600);
        }, 2000);
    }
});
</script>
@endsection