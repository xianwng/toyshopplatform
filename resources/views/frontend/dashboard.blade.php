@extends('frontend.layout.master')

@section('content')
<div class="dashboard-content container-fluid">
    <!-- Low Stock Notification Banner -->
    @php
        $lowStockProducts = \App\Models\Product::where('stock', '<=', 9)->count();
        $hasLowStock = $lowStockProducts > 0;
    @endphp

    @if($hasLowStock)
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);">
        <div class="d-flex align-items-center">
            <div class="alert-icon bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1 fw-bold">Low Stock Alert!</h5>
                <p class="mb-0">You have <strong>{{ $lowStockProducts }} product(s)</strong> with critically low stock (≤9 units). Please restock soon.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Dashboard Overview</h1>
            <p class="text-muted mb-0">Welcome back! Here's what's happening with your store today.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark fs-6 p-3 rounded-3" style="border: 1px solid #e9ecef;">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                {{ now()->format('F j, Y') }}
            </span>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100 {{ $lowStockProducts > 0 ? 'border-warning border-2' : '' }}" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <h6 class="card-title text-muted mb-0 fw-semibold">Total Products</h6>
                                @if($lowStockProducts > 0)
                                <span class="badge bg-danger ms-2" style="font-size: 0.7em;">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $lowStockProducts }} low
                                </span>
                                @endif
                            </div>
                            <h2 class="fw-bold text-primary mb-1">{{ $totalProducts }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-danger fw-medium">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $lowStockProducts }} critical stock
                                </small>
                            </div>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-10 ms-3">
                            <i class="fas fa-box text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2 fw-semibold">Ongoing Auctions</h6>
                            <h2 class="fw-bold text-success mb-1">{{ $activeAuctions }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-warning fw-medium">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $endingSoonAuctions }} ending soon
                                </small>
                            </div>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10 ms-3">
                            <i class="fas fa-gavel text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2 fw-semibold">Active Trades</h6>
                            <h2 class="fw-bold text-warning mb-1">{{ $activeTrades }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-success fw-medium">
                                    <i class="fas fa-plus-circle me-1"></i>
                                    {{ $newTrades }} new this week
                                </small>
                            </div>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-10 ms-3">
                            <i class="fas fa-exchange-alt text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted mb-2 fw-semibold">Pending Orders</h6>
                            <h2 class="fw-bold text-info mb-1">{{ $pendingOrders }}</h2>
                            <div class="d-flex align-items-center">
                                <small class="text-muted fw-medium">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $completedOrders }} completed
                                </small>
                            </div>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-10 ms-3">
                            <i class="fas fa-shopping-cart text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Distribution Full Width -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 py-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Stock Distribution</h5>
                    @if($lowStockCount > 0)
                    <span class="badge bg-danger p-3 rounded-3">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        {{ $lowStockCount }} need attention
                    </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-4 rounded-4 border-start border-5 border-danger position-relative bg-danger bg-opacity-5 h-100">
                                @if($lowStockCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75em;">
                                    {{ $lowStockCount }}
                                    <span class="visually-hidden">low stock items</span>
                                </span>
                                @endif
                                <div class="icon-circle bg-danger bg-opacity-10 mx-auto mb-3">
                                    <i class="fas fa-arrow-down text-danger fs-4"></i>
                                </div>
                                <h2 class="fw-bold text-danger mb-2">{{ $lowStockCount }}</h2>
                                <p class="text-muted mb-1 fw-semibold">Critical Stock</p>
                                <small class="text-muted">≤9 items remaining</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-4 rounded-4 border-start border-5 border-warning bg-warning bg-opacity-5 h-100">
                                <div class="icon-circle bg-warning bg-opacity-10 mx-auto mb-3">
                                    <i class="fas fa-minus text-warning fs-4"></i>
                                </div>
                                <h2 class="fw-bold text-warning mb-2">{{ $mediumStockCount }}</h2>
                                <p class="text-muted mb-1 fw-semibold">Low Stock</p>
                                <small class="text-muted">10-20 items remaining</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-4 rounded-4 border-start border-5 border-success bg-success bg-opacity-5 h-100">
                                <div class="icon-circle bg-success bg-opacity-10 mx-auto mb-3">
                                    <i class="fas fa-arrow-up text-success fs-4"></i>
                                </div>
                                <h2 class="fw-bold text-success mb-2">{{ $goodStockCount }}</h2>
                                <p class="text-muted mb-1 fw-semibold">Good Stock</p>
                                <small class="text-muted">20+ items remaining</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Horizontal -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 py-4">
                    <h5 class="mb-0 fw-bold text-dark">Quick Actions</h5>
                    <p class="text-muted mb-0 mt-1">Quick access to frequently used features</p>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('product') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative quick-action-btn" style="border-radius: 12px; border-width: 2px; min-height: 140px;">
                                @if($lowStockProducts > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $lowStockProducts }}
                                    <span class="visually-hidden">low stock products</span>
                                </span>
                                @endif
                                <div class="icon-circle bg-primary bg-opacity-10 mb-3">
                                    <i class="fas fa-box text-primary fs-3"></i>
                                </div>
                                <strong class="text-center mb-1">Manage Products</strong>
                                <small class="text-muted text-center">Add, edit, or view products</small>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('auction') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 quick-action-btn" style="border-radius: 12px; border-width: 2px; min-height: 140px;">
                                <div class="icon-circle bg-success bg-opacity-10 mb-3">
                                    <i class="fas fa-gavel text-success fs-3"></i>
                                </div>
                                <strong class="text-center mb-1">View Auctions</strong>
                                <small class="text-muted text-center">Manage ongoing auctions</small>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('trading') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 quick-action-btn" style="border-radius: 12px; border-width: 2px; min-height: 140px;">
                                <div class="icon-circle bg-warning bg-opacity-10 mb-3">
                                    <i class="fas fa-exchange-alt text-warning fs-3"></i>
                                </div>
                                <strong class="text-center mb-1">Manage Trades</strong>
                                <small class="text-muted text-center">Handle trade requests</small>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('order') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 quick-action-btn" style="border-radius: 12px; border-width: 2px; min-height: 140px;">
                                <div class="icon-circle bg-info bg-opacity-10 mb-3">
                                    <i class="fas fa-shopping-cart text-info fs-3"></i>
                                </div>
                                <strong class="text-center mb-1">View Orders</strong>
                                <small class="text-muted text-center">Process customer orders</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Styles -->
<style>
    .stat-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border-radius: 16px;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .icon-circle {
        transform: scale(1.1);
    }
    
    .card {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.08);
    }
    
    .badge {
        font-weight: 600;
        padding: 0.5em 0.8em;
    }
    
    .quick-action-btn {
        transition: all 0.3s ease-in-out;
        border: 2px solid;
        background: white;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        background: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
    }
    
    .quick-action-btn:hover .icon-circle {
        background: rgba(255,255,255,0.2) !important;
    }
    
    .quick-action-btn:hover .icon-circle i {
        color: white !important;
    }
    
    .quick-action-btn:hover small {
        color: rgba(255,255,255,0.9) !important;
    }
    
    .alert-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .border-start {
        border-left-width: 5px !important;
    }
    
    /* Specific hover effects for each quick action button */
    .btn-outline-success:hover {
        background: var(--bs-success) !important;
        border-color: var(--bs-success) !important;
    }
    
    .btn-outline-warning:hover {
        background: var(--bs-warning) !important;
        border-color: var(--bs-warning) !important;
        color: #000 !important;
    }
    
    .btn-outline-warning:hover small {
        color: rgba(0,0,0,0.8) !important;
    }
    
    .btn-outline-info:hover {
        background: var(--bs-info) !important;
        border-color: var(--bs-info) !important;
    }
    
    /* Improved typography */
    .card-title {
        font-size: 0.9rem;
        letter-spacing: 0.3px;
    }
    
    h2 {
        font-size: 2.5rem;
    }
    
    h3 {
        font-size: 2rem;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .icon-circle {
            width: 50px;
            height: 50px;
            font-size: 1.1rem;
        }
        
        h2 {
            font-size: 2rem;
        }
        
        .card-body.p-4 {
            padding: 1.5rem !important;
        }
        
        .quick-action-btn {
            min-height: 120px !important;
        }
    }
    
    @media (max-width: 576px) {
        .d-flex.justify-content-between.align-items-center.mb-4 {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .text-end {
            text-align: left !important;
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth loading animations
    const cards = document.querySelectorAll('.stat-card, .quick-action-btn');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Add click effects to quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection