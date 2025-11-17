@extends('customer.layouts.cmaster')

@section('title', 'My Auctions | Toyspace')

@section('styles')
<style>
    :root {
        --primary: #8B0000;
        --primary-dark: #660000;
        --secondary: #A52A2A;
        --accent: #ff6b6b;
        --light: #f8f9fa;
        --dark: #343a40;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --blue: #007bff;
        --gray: #6c757d;
        --gray-light: #e9ecef;
    }

    /* Hero Section - Updated to Dark Blue */
    .hero-section {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,192C1248,192,1344,128,1392,96L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        background-position: center bottom;
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
        opacity: 0.9;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .crud-section {
        padding: 60px 0;
        background: var(--light);
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        color: var(--dark);
    }

    .auction-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .auction-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid var(--gray-light);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .auction-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .auction-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: var(--gray-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auction-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .auction-card:hover .auction-image {
        transform: scale(1.05);
    }

    .auction-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .badge-pending {
        background: var(--warning);
        color: var(--dark);
    }

    .badge-active {
        background: var(--success);
        color: white;
    }
    
    .badge-ended {
        background: var(--danger);
        color: white;
    }

    .badge-rejected {
        background: var(--danger);
        color: white;
    }

    .badge-verified {
        background: var(--success);
        color: white;
    }

    .badge-owner {
        background: #1e3c72;
        color: white;
    }

    .badge-winner {
        background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
        color: var(--dark);
        font-weight: 700;
    }

    /* NEW: Escrow Status Badges */
    .badge-escrow-pending {
        background: #ffc107;
        color: #000;
    }

    .badge-escrow-approved {
        background: #28a745;
        color: white;
    }

    .badge-escrow-released {
        background: #17a2b8;
        color: white;
    }

    .badge-escrow-rejected {
        background: #dc3545;
        color: white;
    }

    .condition-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(0,0,0,0.7);
        color: white;
        z-index: 2;
    }

    .verification-badge {
        position: absolute;
        top: 40px;
        right: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 600;
        z-index: 2;
    }

    .badge-verified-small {
        background: var(--success);
        color: white;
    }

    .badge-pending-small {
        background: var(--warning);
        color: var(--dark);
    }

    .auction-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .auction-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .auction-meta {
        margin-bottom: 12px;
        flex-grow: 1;
    }

    .auction-brand {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 4px;
    }

    .auction-category {
        font-size: 0.85rem;
        color: #1e3c72;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .auction-price {
        margin-bottom: 12px;
    }

    .start-price {
        font-size: 0.9rem;
        color: var(--gray);
    }

    .current-bid {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--success);
    }

    .diamond-icon {
        color: #4fc3f7;
        font-weight: bold;
    }

    .verification-info {
        background: var(--light);
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 10px;
        font-size: 0.8rem;
    }

    .verification-item {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .verification-item.verified {
        color: var(--success);
    }

    .verification-item.pending {
        color: var(--warning);
    }

    .verification-item.missing {
        color: var(--danger);
    }

    .delivery-info {
        font-size: 0.8rem;
        color: var(--gray);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .market-value-info {
        font-size: 0.8rem;
        color: var(--info);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .auction-time {
        font-size: 0.8rem;
        color: var(--gray);
        margin-bottom: 15px;
        padding: 8px 12px;
        background: var(--light);
        border-radius: 8px;
        text-align: center;
    }

    .winner-info {
        background: linear-gradient(135deg, #fff9e6 0%, #fff2cc 100%);
        border: 1px solid #ffe58f;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 10px;
        font-size: 0.8rem;
        text-align: center;
    }

    /* NEW: Escrow Info Section */
    .escrow-info {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 10px;
        font-size: 0.8rem;
        text-align: center;
    }

    .escrow-info.approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 1px solid #28a745;
    }

    .escrow-info.rejected {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #dc3545;
    }

    .escrow-info.released {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 1px solid #17a2b8;
    }

    .auction-actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }

    .btn-action {
        flex: 1;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-edit {
        background: var(--warning);
        color: var(--dark);
    }
    
    .btn-edit:hover {
        background: #e0a800;
        color: var(--dark);
    }
    
    .btn-view {
        background: var(--info);
        color: white;
    }
    
    .btn-view:hover {
        background: #138496;
        color: white;
    }
    
    .btn-delete {
        background: var(--danger);
        color: white;
    }
    
    .btn-delete:hover {
        background: #c82333;
        color: white;
    }

    .btn-bid {
        background: var(--success);
        color: white;
    }
    
    .btn-bid:hover {
        background: #218838;
        color: white;
    }

    .btn-chat {
        background: var(--info);
        color: white;
    }
    
    .btn-chat:hover {
        background: #138496;
        color: white;
    }

    .btn-view-won {
        background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
        color: var(--dark);
        font-weight: 600;
    }
    
    .btn-view-won:hover {
        background: linear-gradient(135deg, #e6c200 0%, #e59400 100%);
        color: var(--dark);
    }

    .btn-add-auction {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    }

    .btn-add-auction:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    color: white;
    }   

    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: var(--gray);
        margin-bottom: 15px;
    }
    
    .empty-state-title {
        color: var(--gray);
        margin-bottom: 10px;
    }

    .alert-custom {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .pagination-custom .page-link {
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid var(--gray-light);
        color: #1e3c72;
    }
    
    .pagination-custom .page-item.active .page-link {
        background: #1e3c72;
        border-color: #1e3c72;
    }

    .section-divider {
        margin: 60px 0;
        border: none;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gray-light), transparent);
    }

    .status-count {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border-radius: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 0 10px 20px 0;
    }

    .status-count.pending { border-left: 4px solid var(--warning); }
    .status-count.active { border-left: 4px solid var(--success); }
    .status-count.ended { border-left: 4px solid var(--danger); }
    .status-count.verified { border-left: 4px solid var(--success); }

    .owner-notice {
        background: var(--light);
        border: 1px solid #1e3c72;
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        margin-top: 8px;
        font-size: 0.75rem;
        color: #1e3c72;
        font-weight: 500;
    }

    .winner-notice {
        background: linear-gradient(135deg, #fff9e6 0%, #fff2cc 100%);
        border: 1px solid #ffd700;
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        margin-top: 8px;
        font-size: 0.75rem;
        color: #b8860b;
        font-weight: 600;
    }

    .current-user-badge {
        background: #1e3c72;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .diamond-balance {
        background: linear-gradient(135deg, #4fc3f7 0%, #29b6f6 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(79, 195, 247, 0.3);
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .auction-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .auction-actions {
            flex-direction: column;
        }

        .status-count {
            margin: 0 5px 10px 0;
        }
    }

    @media (max-width: 576px) {
        .auction-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section - Updated to Dark Blue -->
<section class="hero-section">
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title">Auctions Marketplace</h1>
            <p class="hero-subtitle">Bid on exclusive collectibles and rare toys in our dynamic auction community</p>
        </div>
    </div>
</section>

<!-- CRUD Section -->
<section class="crud-section">
    <div class="container">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- SECTION 1: User's Auctions -->
        <div class="my-auctions-section">
            <div class="d-flex justify-content-between align-items-center section-header">
                <h2 class="section-title mb-0">My Auctions</h2>
                <a href="{{ route('customer.auctions.create') }}" class="btn btn-add-auction">
                    <i class="fa fa-plus-circle"></i> Add New Auction
                </a>
            </div>

            @php
                // FIXED: Only count user's personal auctions, NOT public marketplace
                $userAuctions = $userAuctions ?? collect();
                
                // Count only user's personal auctions
                $pendingCount = $userAuctions->where('status', 'pending')->count();
                
                // For active count, also check if auction hasn't expired
                $activeCount = $userAuctions->filter(function($auction) {
                    return $auction->status === 'active' && $auction->end_time > now();
                })->count();
                
                // For ended count, include both status='ended' AND active auctions that have expired
                $endedCount = $userAuctions->filter(function($auction) {
                    return $auction->status === 'ended' || 
                           ($auction->status === 'active' && $auction->end_time <= now());
                })->count();
            @endphp

            <!-- Status Counts -->
            <div class="text-center mb-4">
                @if($pendingCount > 0)
                    <div class="status-count pending d-inline-flex">
                        <span class="count">{{ $pendingCount }}</span>
                        <span>Pending Review</span>
                    </div>
                @endif
                @if($activeCount > 0)
                    <div class="status-count active d-inline-flex">
                        <span class="count">{{ $activeCount }}</span>
                        <span>Active</span>
                    </div>
                @endif
                @if($endedCount > 0)
                    <div class="status-count ended d-inline-flex">
                        <span class="count">{{ $endedCount }}</span>
                        <span>Ended</span>
                    </div>
                @endif
            </div>

            @if($userAuctions->count() > 0)
                <div class="auction-grid">
                    @foreach($userAuctions as $auction)
                    <div class="auction-card">
                        <!-- Auction Image - UPDATED: Use first_image_url from Auction model -->
                        <div class="auction-image-container">
                            @if($auction->has_images)
                                <img src="{{ $auction->first_image_url }}" 
                                     alt="{{ $auction->product_name }}"
                                     class="auction-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                
                                <!-- Fallback that shows when image fails to load -->
                                <div style="width: 100%; height: 100%; display: none; align-items: center; justify-content: center; flex-direction: column; background: var(--gray-light);">
                                    <i class="fa fa-gavel fa-3x text-muted mb-2"></i>
                                    <small class="text-muted">{{ $auction->category ?? 'Auction' }}</small>
                                </div>
                            @else
                                <!-- No images available -->
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <i class="fa fa-gavel fa-3x text-muted mb-2"></i>
                                    <small class="text-muted">{{ $auction->category ?? 'Auction' }}</small>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            @php
                                // Determine actual status considering end time
                                $actualStatus = $auction->status;
                                if ($auction->status === 'active' && $auction->end_time <= now()) {
                                    $actualStatus = 'ended';
                                }
                                if ($auction->status === 'completed') {
                                    $actualStatus = 'ended';
                                }
                            @endphp

                            @if($actualStatus === 'active')
                                <span class="auction-badge badge-active">Active</span>
                            @elseif($actualStatus === 'pending')
                                <span class="auction-badge badge-pending">Pending</span>
                            @elseif($actualStatus === 'ended')
                                <span class="auction-badge badge-ended">Ended</span>
                            @elseif($auction->status === 'rejected')
                                <span class="auction-badge badge-rejected">Rejected</span>
                            @endif

                            <!-- NEW: Escrow Status Badge -->
                            @if($auction->payout_status === 'pending' && $auction->payout_amount > 0)
                                <span class="auction-badge badge-escrow-pending" style="top: 40px; right: 10px;">
                                    <i class="fa fa-clock me-1"></i> Escrow
                                </span>
                            @elseif($auction->payout_status === 'approved')
                                <span class="auction-badge badge-escrow-approved" style="top: 40px; right: 10px;">
                                    <i class="fa fa-check me-1"></i> Approved
                                </span>
                            @elseif($auction->payout_status === 'released')
                                <span class="auction-badge badge-escrow-released" style="top: 40px; right: 10px;">
                                    <i class="fa fa-money-bill me-1"></i> Paid
                                </span>
                            @elseif($auction->payout_status === 'rejected')
                                <span class="auction-badge badge-escrow-rejected" style="top: 40px; right: 10px;">
                                    <i class="fa fa-times me-1"></i> Rejected
                                </span>
                            @endif

                            <!-- Winner Badge -->
                            @if($actualStatus === 'ended' && $auction->winner_id)
                                @if($auction->winner_id === Auth::id())
                                    <span class="auction-badge badge-winner" style="top: 70px; right: 10px;">
                                        <i class="fa fa-trophy me-1"></i> You Won!
                                    </span>
                                @elseif($auction->user_id === Auth::id())
                                    <span class="auction-badge" style="top: 70px; right: 10px; background: #6c757d; color: white;">
                                        <i class="fa fa-user me-1"></i> Sold
                                    </span>
                                @endif
                            @endif

                            <!-- Condition Badge -->
                            <span class="condition-badge">
                                @if($auction->condition === 'sealed')
                                    Sealed
                                @elseif($auction->condition === 'back in box')
                                    Boxed
                                @else
                                    Loose
                                @endif
                            </span>

                            <!-- Image Count Badge (if multiple images) -->
                            @if($auction->has_multiple_images)
                                <span class="auction-badge" style="top: 100px; right: 10px; background: var(--info); color: white; font-size: 0.6rem;">
                                    <i class="fa fa-images me-1"></i> {{ $auction->image_count }}
                                </span>
                            @endif
                        </div>

                        <!-- Auction Info -->
                        <div class="auction-info">
                            <h3 class="auction-title">{{ $auction->product_name ?? '—' }}</h3>
                            
                            <div class="auction-meta">
                                <div class="auction-brand">
                                    <strong>Brand:</strong> {{ $auction->brand ?? '—' }}
                                </div>
                                <div class="auction-category">
                                    {{ $auction->category ?? '—' }}
                                </div>
                                
                                <!-- NEW: Escrow Information -->
                                @if($auction->payout_status === 'pending' && $auction->payout_amount > 0)
                                <div class="escrow-info">
                                    <i class="fa fa-clock text-warning me-1"></i>
                                    <strong>Payout in Escrow:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                    <br><small>Pending admin approval</small>
                                </div>
                                @elseif($auction->payout_status === 'approved')
                                <div class="escrow-info approved">
                                    <i class="fa fa-check text-success me-1"></i>
                                    <strong>Payout Approved:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                    <br><small>Ready for release</small>
                                </div>
                                @elseif($auction->payout_status === 'released')
                                <div class="escrow-info released">
                                    <i class="fa fa-money-bill text-info me-1"></i>
                                    <strong>Payout Completed:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                    <br><small>Funds released to you</small>
                                </div>
                                @elseif($auction->payout_status === 'rejected')
                                <div class="escrow-info rejected">
                                    <i class="fa fa-times text-danger me-1"></i>
                                    <strong>Payout Rejected:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                    <br><small>Buyer refunded</small>
                                </div>
                                @endif

                                <!-- Verification Status - Only show for pending auctions -->
                                @if($auction->status === 'pending')
                                <div class="verification-info">
                                    <div class="verification-item {{ $auction->owner_proof ? 'verified' : 'missing' }}">
                                        <i class="fa {{ $auction->owner_proof ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Ownership Proof
                                    </div>
                                    <div class="verification-item {{ $auction->market_value_proof ? 'verified' : 'missing' }}">
                                        <i class="fa {{ $auction->market_value_proof ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Market Value Proof
                                    </div>
                                    <div class="verification-item {{ $auction->minimum_market_value > 0 ? 'verified' : 'missing' }}">
                                        <i class="fa {{ $auction->minimum_market_value > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        Market Value Set
                                    </div>
                                </div>
                                @endif

                                <!-- Winner Information -->
                                @if($actualStatus === 'ended' && $auction->winner_id)
                                    <div class="winner-info">
                                        <i class="fa fa-trophy text-warning me-1"></i>
                                        @if($auction->winner_id === Auth::id())
                                            <strong>You won this auction!</strong>
                                        @elseif($auction->user_id === Auth::id())
                                            <strong>Sold to: {{ $auction->winner->username ?? 'User' }}</strong>
                                        @else
                                            <strong>Auction ended</strong>
                                        @endif
                                    </div>
                                @endif

                                <!-- Market Value Info -->
                                @if($auction->minimum_market_value > 0)
                                <div class="market-value-info">
                                    <i class="fa fa-chart-line"></i>
                                    Min Market Value: <span class="diamond-icon">💎</span>{{ number_format($auction->minimum_market_value, 0) }}
                                </div>
                                @endif

                                <!-- Delivery Info -->
                                <div class="delivery-info">
                                    <i class="fa fa-truck"></i>
                                    {{ $auction->delivery_method_text ?? 'Standard Delivery' }} 
                                    @if($auction->delivery_cost > 0)
                                    - <span class="diamond-icon">💎</span>{{ number_format($auction->delivery_cost, 0) }}
                                    @endif
                                </div>
                                
                                <div class="auction-price">
                                    <div class="start-price">
                                        Start: <strong><span class="diamond-icon">💎</span>{{ number_format($auction->starting_price, 0) }}</strong>
                                    </div>
                                    @if($auction->current_bid > 0)
                                    <div class="current-bid">
                                        Current: <span class="diamond-icon">💎</span>{{ number_format($auction->current_bid, 0) }}
                                    </div>
                                    @endif
                                    @if($auction->buyout_bid)
                                    <div class="buyout-price" style="font-size: 0.9rem; color: #ee5a24;">
                                        Buyout: <span class="diamond-icon">💎</span>{{ number_format($auction->buyout_bid, 0) }}
                                    </div>
                                    @endif
                                </div>

                                <div class="auction-time">
                                    <i class="fa fa-clock me-1"></i>
                                    @php
                                        $actualStatus = $auction->status;
                                        if ($auction->status === 'active' && $auction->end_time <= now()) {
                                            $actualStatus = 'ended';
                                        }
                                        if ($auction->status === 'completed') {
                                            $actualStatus = 'ended';
                                        }
                                    @endphp

                                    @if($actualStatus === 'active')
                                        Ends: {{ $auction->end_time->diffForHumans() }}
                                    @elseif($actualStatus === 'pending')
                                        @if($auction->owner_proof && $auction->market_value_proof && $auction->minimum_market_value > 0)
                                            Waiting Admin Approval
                                        @else
                                            Complete Verification
                                        @endif
                                    @elseif($actualStatus === 'ended')
                                        Auction Ended
                                    @else
                                        {{ ucfirst($auction->status) }}
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="auction-actions">
                                <!-- For ENDED auctions where current user is the winner -->
                                @if($actualStatus === 'ended' && $auction->winner_id === Auth::id())
                                    <a href="{{ route('customer.auctions.detail', $auction->id) }}" class="btn-action btn-view-won" style="flex: 1;">
                                        <i class="fa fa-trophy"></i> View Your Win
                                    </a>
                                    {{-- ✅ CHAT BUTTON ONLY FOR WINNERS --}}
                                    <a href="{{ url('/customer/chat/auction-winner-chat/' . $auction->id) }}" class="btn-action btn-chat" style="flex: 1;">
                                        <i class="fa fa-comments"></i> Chat Seller
                                    </a>
                                <!-- For PENDING auctions: Only show View and Delete (NO Edit button) -->
                                @elseif($auction->status === 'pending' && $auction->bids_count == 0)
                                    <a href="{{ route('customer.auctions.detail', $auction->id) }}" class="btn-action btn-view">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('customer.auctions.destroy', $auction->id) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete w-100" 
                                                onclick="return confirm('Are you sure you want to delete this auction?')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                <!-- For ACTIVE auctions with bids: Show View only (REMOVED End Auction button) -->
                                @elseif($auction->status === 'active' && $auction->user_id === Auth::id() && $auction->bids_count > 0)
                                    <a href="{{ route('customer.auctions.detail', $auction->id) }}" class="btn-action btn-view" style="flex: 1;">
                                        <i class="fa fa-eye"></i> View Details
                                    </a>
                                <!-- For other statuses: Just show View -->
                                @else
                                    <a href="{{ route('customer.auctions.detail', $auction->id) }}" class="btn-action btn-view" style="flex: 1;">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($userAuctions->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $userAuctions->links() }}
                </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-gavel"></i>
                    </div>
                    <h3 class="empty-state-title">No Auctions Found</h3>
                    <p class="text-muted">You haven't created any auctions yet.</p>
                </div>
            @endif
        </div>

        <!-- Section Divider -->
        <hr class="section-divider">

        <!-- SECTION 2: All Auctions - Shows active auctions from OTHER users only -->
        <div class="mt-5">
            <div class="section-header">
                <h2 class="section-title">All Auctions</h2>
                <p class="text-center text-muted">Showing {{ $publicAuctions->count() }} active auctions in the marketplace</p>
            </div>

            @if($publicAuctions->count() > 0)
                <div class="auction-grid">
                    @foreach($publicAuctions as $auction)
                    <div class="auction-card">
                        <!-- Auction Image - UPDATED: Use first_image_url from Auction model -->
                        <div class="auction-image-container">
                            @if($auction->has_images)
                                <img src="{{ $auction->first_image_url }}" 
                                     alt="{{ $auction->product_name }}"
                                     class="auction-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                
                                <!-- Fallback that shows when image fails to load -->
                                <div style="width: 100%; height: 100%; display: none; align-items: center; justify-content: center; flex-direction: column; background: var(--gray-light);">
                                    <i class="fa fa-gavel fa-3x text-muted mb-2"></i>
                                    <small class="text-muted">{{ $auction->category ?? 'Auction' }}</small>
                                </div>
                            @else
                                <!-- No images available -->
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <i class="fa fa-gavel fa-3x text-muted mb-2"></i>
                                    <small class="text-muted">{{ $auction->category ?? 'Auction' }}</small>
                                </div>
                            @endif
                            
                            <!-- Status Badge - Only show Active badge for public auctions -->
                            @php
                                // Determine actual status considering end time
                                $actualStatus = $auction->status;
                                if ($auction->status === 'active' && $auction->end_time <= now()) {
                                    $actualStatus = 'ended';
                                }
                                if ($auction->status === 'completed') {
                                    $actualStatus = 'ended';
                                }
                            @endphp

                            @if($actualStatus === 'active')
                                <span class="auction-badge badge-active">Active</span>
                            @elseif($actualStatus === 'ended')
                                <span class="auction-badge badge-ended">Ended</span>
                            @endif

                            <!-- NEW: Escrow Status Badge for ended auctions -->
                            @if($actualStatus === 'ended' && $auction->payout_status === 'pending' && $auction->payout_amount > 0)
                                <span class="auction-badge badge-escrow-pending" style="top: 40px; right: 10px;">
                                    <i class="fa fa-clock me-1"></i> Escrow
                                </span>
                            @endif

                            <!-- Winner Badge for ended auctions -->
                            @if($actualStatus === 'ended' && $auction->winner_id)
                                @if($auction->winner_id === Auth::id())
                                    <span class="auction-badge badge-winner" style="top: 70px; right: 10px;">
                                        <i class="fa fa-trophy me-1"></i> You Won!
                                    </span>
                                @endif
                            @endif

                            <!-- Owner Badge (if current user owns this auction) -->
                            @if(Auth::check() && $auction->user_id === Auth::id())
                                <span class="auction-badge badge-owner" style="top: 40px;">
                                    <i class="fa fa-user me-1"></i> Your Item
                                </span>
                            @endif

                            <!-- Condition Badge -->
                            <span class="condition-badge">
                                @if($auction->condition === 'sealed')
                                    Sealed
                                @elseif($auction->condition === 'back in box')
                                    Boxed
                                @else
                                    Loose
                                @endif
                            </span>

                            <!-- Image Count Badge (if multiple images) -->
                            @if($auction->has_multiple_images)
                                <span class="auction-badge" style="top: 100px; right: 10px; background: var(--info); color: white; font-size: 0.6rem;">
                                    <i class="fa fa-images me-1"></i> {{ $auction->image_count }}
                                </span>
                            @endif
                        </div>

                        <!-- Auction Info -->
                        <div class="auction-info">
                            <h3 class="auction-title">{{ $auction->product_name ?? '—' }}</h3>
                            
                            <div class="auction-meta">
                                <div class="auction-brand">
                                    <strong>Brand:</strong> {{ $auction->brand ?? '—' }}
                                </div>
                                <div class="auction-category">
                                    {{ $auction->category ?? '—' }}
                                </div>

                                <!-- NEW: Escrow Information for ended auctions -->
                                @if($actualStatus === 'ended' && $auction->payout_status === 'pending' && $auction->payout_amount > 0)
                                <div class="escrow-info">
                                    <i class="fa fa-clock text-warning me-1"></i>
                                    <strong>Payout in Escrow:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                    <br><small>Pending admin approval</small>
                                </div>
                                @endif

                                <!-- Winner Information for ended auctions -->
                                @if($actualStatus === 'ended' && $auction->winner_id)
                                    <div class="winner-info">
                                        <i class="fa fa-trophy text-warning me-1"></i>
                                        @if($auction->winner_id === Auth::id())
                                            <strong>Congratulations! You won this auction!</strong>
                                        @else
                                            <strong>Auction ended</strong>
                                        @endif
                                    </div>
                                @endif

                                <!-- Market Value Info -->
                                @if($auction->minimum_market_value > 0)
                                <div class="market-value-info">
                                    <i class="fa fa-chart-line"></i>
                                    Market Value: <span class="diamond-icon">💎</span>{{ number_format($auction->minimum_market_value, 0) }}
                                </div>
                                @endif

                                <!-- Delivery Info -->
                                <div class="delivery-info">
                                    <i class="fa fa-truck"></i>
                                    {{ $auction->delivery_method_text ?? 'Standard Delivery' }} 
                                    @if($auction->delivery_cost > 0)
                                    - <span class="diamond-icon">💎</span>{{ number_format($auction->delivery_cost, 0) }}
                                    @endif
                                </div>
                                
                                <div class="auction-price">
                                    <div class="start-price">
                                        Start: <strong><span class="diamond-icon">💎</span>{{ number_format($auction->starting_price, 0) }}</strong>
                                    </div>
                                    <div class="current-bid">
                                        Current: <span class="diamond-icon">💎</span>{{ number_format($auction->current_bid ?? $auction->starting_price, 0) }}
                                    </div>
                                    @if($auction->buyout_bid)
                                    <div class="buyout-price" style="font-size: 0.9rem; color: #ee5a24;">
                                        Buyout: <span class="diamond-icon">💎</span>{{ number_format($auction->buyout_bid, 0) }}
                                    </div>
                                    @endif
                                </div>

                                <div class="auction-time">
                                    <i class="fa fa-clock me-1"></i>
                                    @php
                                        $actualStatus = $auction->status;
                                        if ($auction->status === 'active' && $auction->end_time <= now()) {
                                            $actualStatus = 'ended';
                                        }
                                        if ($auction->status === 'completed') {
                                            $actualStatus = 'ended';
                                        }
                                    @endphp

                                    @if($actualStatus === 'active')
                                        Ends: {{ $auction->end_time->diffForHumans() }}
                                    @else
                                        Auction Ended
                                    @endif
                                </div>

                                <div class="bids-count">
                                    <small class="text-muted">
                                        <i class="fa fa-gavel me-1"></i>
                                        {{ $auction->bids_count ?? 0 }} bids
                                    </small>
                                </div>
                            </div>

                            <!-- Action Buttons - Show different buttons based on ownership and status -->
                            @if(Auth::check())
                                @if($auction->user_id !== Auth::id())
                                    <!-- Other users can bid or view based on status -->
                                    @if($actualStatus === 'active')
                                        <div class="auction-actions">
                                            {{-- ✅ FIXED: Use the correct route for auction details --}}
                                            <a href="{{ route('auction_details', $auction->id) }}" class="btn-action btn-view" style="flex: 1;">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        </div>
                                    @elseif($actualStatus === 'ended' && $auction->winner_id === Auth::id())
                                        <!-- Winner gets special view button -->
                                        <div class="auction-actions">
                                            <a href="{{ route('auction_details', $auction->id) }}" class="btn-action btn-view-won" style="flex: 1;">
                                                <i class="fa fa-trophy"></i> View Your Win
                                            </a>
                                            {{-- ✅ CHAT BUTTON ONLY FOR WINNERS --}}
                                            <a href="{{ url('/customer/chat/auction-winner-chat/' . $auction->id) }}" class="btn-action btn-chat" style="flex: 1;">
                                                <i class="fa fa-comments"></i> Chat Seller
                                            </a>
                                        </div>
                                    @else
                                        <!-- Ended auction for non-winners -->
                                        <div class="auction-actions">
                                            <a href="{{ route('auction_details', $auction->id) }}" class="btn-action btn-view" style="flex: 1;">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <!-- Show notice for user's own items -->
                                    <div class="owner-notice">
                                        <i class="fa fa-info-circle me-1"></i> This is your auction listing
                                    </div>
                                @endif
                            @else
                                <!-- Show login prompt for guests -->
                                <div class="auction-actions">
                                    <a href="{{ route('login') }}" class="btn-action btn-view" style="flex: 1;">
                                        <i class="fa fa-sign-in-alt"></i> Login to View
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($publicAuctions->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $publicAuctions->links() }}
                </div>
                @endif

            @else
                <!-- Empty State for Public Auctions -->
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">No Active Auctions Available</h3>
                    <p class="text-muted">
                        There are currently no active auctions available in the marketplace.
                        @auth
                            <br>Be the first to create an auction!
                        @endauth
                    </p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log('Auction page loaded');

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);

        // Add smooth animations to cards
        $('.auction-card').hover(
            function() {
                $(this).css('transform', 'translateY(-5px)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
            }
        );

        // Refresh page every 30 seconds to update auction times and statuses
        setInterval(() => {
            location.reload();
        }, 30000);
    });
</script>
@endsection