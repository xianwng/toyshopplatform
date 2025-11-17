@extends('customer.layouts.cmaster')

@section('title', $auction->product_name . ' | Toyspace')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .purple-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .header-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    .header-icon {
        font-size: 3rem;
        opacity: 0.8;
    }

    .product-image-container {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        position: relative;
        padding: 1rem;
    }

    .auction-main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
    }

    .model-placeholder {
        height: 400px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-radius: 12px;
        position: relative;
    }

    .back-button-overlay {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 10;
    }

    .back-button-icon {
        background: rgba(255, 255, 255, 0.95);
        color: #6c757d;
        border: 2px solid #6c757d;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-size: 1.2rem;
    }

    .back-button-icon:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
    }

    .image-gallery {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        overflow-x: auto;
        padding: 15px 0;
    }

    .thumbnail {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 12px;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .thumbnail:hover,
    .thumbnail.active {
        border-color: #667eea;
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }

    .product-details-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .product-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .product-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.8rem;
        font-size: 1rem;
        color: #495057;
    }

    .meta-item i {
        width: 20px;
        margin-right: 10px;
        color: #007bff;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .badge-active { background: #28a745; color: white; }
    .badge-pending { background: #ffc107; color: #212529; }
    .badge-ended { background: #dc3545; color: white; }
    .badge-approved { background: #17a2b8; color: white; }
    .badge-escrow-pending { background: #ffc107; color: #000; }
    .badge-escrow-approved { background: #28a745; color: white; }
    .badge-escrow-released { background: #17a2b8; color: white; }
    .badge-escrow-rejected { background: #dc3545; color: white; }
    .badge-escrow-refunded { background: #6c757d; color: white; }

    .description-section {
        margin-top: 2rem;
        padding: 1.5rem;
        border-radius: 12px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .description-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .description-content {
        line-height: 1.6;
        color: #555;
        font-size: 1rem;
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .btn-custom {
        padding: 14px 28px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 140px;
    }

    .btn-primary { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-primary:hover { 
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-2px);
    }
    
    .btn-success { 
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    .btn-success:hover { 
        background: linear-gradient(135deg, #218838 0%, #1ba87e 100%);
        transform: translateY(-2px);
    }
    
    .btn-warning { 
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: #212529;
    }
    .btn-warning:hover { 
        background: linear-gradient(135deg, #e0a800 0%, #e36407 100%);
        transform: translateY(-2px);
    }
    
    .btn-info { 
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
    }
    .btn-info:hover { 
        background: linear-gradient(135deg, #138496 0%, #5a359c 100%);
        transform: translateY(-2px);
    }

    .escrow-section {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .escrow-section.approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
    }

    .escrow-section.rejected {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 2px solid #dc3545;
    }

    .escrow-section.released {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 2px solid #17a2b8;
    }

    .escrow-section.refunded {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border: 2px solid #6c757d;
    }

    .escrow-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #856404;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .escrow-title.approved { color: #155724; }
    .escrow-title.rejected { color: #721c24; }
    .escrow-title.released { color: #0c5460; }
    .escrow-title.refunded { color: #495057; }

    .escrow-details {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .escrow-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .escrow-row:last-child {
        border-bottom: none;
    }

    .escrow-label {
        font-weight: 600;
        color: #495057;
    }

    .escrow-value {
        font-weight: 700;
        color: #1a1a1a;
    }

    .escrow-note {
        font-size: 0.9rem;
        color: #6c757d;
        text-align: center;
        margin-top: 1rem;
        padding: 10px;
        background: rgba(255,255,255,0.7);
        border-radius: 6px;
    }

    .bid-form {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #e9ecef;
    }

    .bid-form-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
        text-align: center;
    }

    .bid-input-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .bid-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 500;
        text-align: center;
    }

    .bid-input:focus {
        border-color: #667eea;
        outline: none;
    }

    .balance-info {
        text-align: center;
        font-size: 0.9rem;
        color: #28a745;
        margin-bottom: 15px;
        padding: 10px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 6px;
        border: 1px solid #28a745;
    }

    .min-bid-info {
        text-align: center;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .btn-bid {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        justify-content: center;
    }

    .btn-bid:hover {
        background: linear-gradient(135deg, #218838 0%, #1ba87e 100%);
        transform: translateY(-1px);
    }

    .current-bidder {
        background: linear-gradient(135deg, #fff9e6 0%, #fff2cc 100%);
        border: 1px solid #ffe58f;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
    }

    .bidder-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bidder-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffc107;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }

    .bidder-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .bid-amount {
        font-weight: 700;
        color: #28a745;
        margin-left: auto;
    }

    .bids-history-section {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #e9ecef;
    }

    .bids-history {
        max-height: 300px;
        overflow-y: auto;
    }

    .bid-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .bid-item:last-child {
        border-bottom: none;
    }

    .bidder-info-small {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }

    .bidder-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: white;
        font-weight: 600;
    }

    .bidder-name-small {
        font-weight: 500;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bid-time {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .bid-amount-small {
        font-weight: 700;
        color: #28a745;
        font-size: 1rem;
    }

    .buyout-badge {
        background: #dc3545;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .alert {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid #28a745;
        color: #28a745;
    }

    .alert-error {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid #dc3545;
        color: #dc3545;
    }

    .seller-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 2rem;
    }

    .seller-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .seller-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-card {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .info-value {
        color: #1a1a1a;
        font-weight: 500;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
    }

    .received-section {
        margin-top: 2rem;
    }

    .received-section .alert {
        border-radius: 12px;
        padding: 1.5rem;
    }

    @media (max-width: 768px) {
        .purple-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .header-title {
            font-size: 2rem;
        }

        .header-subtitle {
            font-size: 1rem;
        }

        .header-icon {
            font-size: 2rem;
        }

        .action-buttons { 
            flex-direction: column; 
        }
        
        .btn-custom { 
            width: 100%; 
        }

        .back-button-overlay {
            top: 0.5rem;
            left: 0.5rem;
        }

        .back-button-icon {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
        }

        .seller-info {
            grid-template-columns: 1fr;
        }

        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .thumbnail {
            width: 70px;
            height: 70px;
        }
    }
</style>
@endsection

@section('content')
<div class="main">
    <div class="purple-header">
        <div class="container">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Auction Details</h1>
                    <div class="header-subtitle">Bid and win amazing collectibles</div>
                </div>
                <div class="header-icon">
                    <i class="fas fa-gavel"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="module-small">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="product-image-container">
                        <div class="back-button-overlay">
                            <a href="javascript:history.back()" class="back-button-icon" title="Back to Previous Page">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>

                        <!-- UPDATED: Use Auction model accessors for image gallery -->
                        @if($auction->has_images)
                            <img id="mainImage" src="{{ $auction->first_image_url }}" 
                                 alt="{{ $auction->product_name }}"
                                 class="auction-main-image">
                        @else
                            <div class="model-placeholder">
                                <i class="fa-solid fa-gavel fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted mb-1">No Image Available</h5>
                                <small class="text-muted">{{ $auction->category ?? 'Auction Item' }}</small>
                            </div>
                        @endif
                        
                        <!-- UPDATED: Image gallery using Auction model accessors -->
                        @if($auction->has_multiple_images)
                        <div class="image-gallery">
                            @foreach($auction->image_gallery as $index => $image)
                                <img src="{{ $image['url'] }}" 
                                     alt="{{ $auction->product_name }} - Image {{ $index + 1 }}"
                                     class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                     data-image="{{ $image['url'] }}"
                                     onclick="changeMainImage(this)">
                            @endforeach
                        </div>
                        @endif

                        <div class="stats-container">
                            <div class="stat-card">
                                <div class="stat-number">{{ $auction->bids_count ?? $auction->bids->count() }}</div>
                                <div class="stat-label">Total Bids</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    <span>💎</span>{{ number_format($auction->current_bid ?? $auction->starting_price, 0) }}
                                </div>
                                <div class="stat-label">Current Bid</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number">
                                    @if($auction->status === 'active')
                                        {{ $auction->end_time->diffForHumans(['parts' => 2]) }}
                                    @else
                                        {{ ucfirst($auction->status) }}
                                    @endif
                                </div>
                                <div class="stat-label">
                                    @if($auction->status === 'active') Time Left @else Status @endif
                                </div>
                            </div>
                        </div>

                        @if($auction->description)
                        <div class="description-section">
                            <h3 class="description-title">
                                <i class="fa-solid fa-file-lines text-info"></i>
                                Item Description
                            </h3>
                            <div class="description-content">
                                {!! nl2br(e($auction->description)) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="product-details-card">
                        @if($auction->status === 'active')
                            <div class="status-badge badge-active">
                                <i class="fas fa-play-circle"></i> LIVE AUCTION
                            </div>
                        @elseif($auction->status === 'pending')
                            <div class="status-badge badge-pending">
                                <i class="fas fa-clock"></i> PENDING REVIEW
                            </div>
                        @elseif($auction->status === 'approved')
                            <div class="status-badge badge-approved">
                                <i class="fas fa-check-circle"></i> APPROVED
                            </div>
                        @elseif($auction->status === 'ended')
                            <div class="status-badge badge-ended">
                                <i class="fas fa-flag"></i> AUCTION ENDED
                            </div>
                        @endif

                        @if($auction->payout_status === 'pending' && $auction->payout_amount > 0)
                            <div class="status-badge badge-escrow-pending">
                                <i class="fas fa-clock"></i> PAYOUT IN ESCROW
                            </div>
                        @elseif($auction->payout_status === 'approved')
                            <div class="status-badge badge-escrow-approved">
                                <i class="fas fa-check"></i> PAYOUT APPROVED
                            </div>
                        @elseif($auction->payout_status === 'released')
                            <div class="status-badge badge-escrow-released">
                                <i class="fas fa-money-bill-wave"></i> PAYOUT COMPLETED
                            </div>
                        @elseif($auction->payout_status === 'rejected')
                            <div class="status-badge badge-escrow-rejected">
                                <i class="fas fa-times"></i> PAYOUT REJECTED
                            </div>
                        @elseif($auction->payout_status === 'refunded')
                            <div class="status-badge badge-escrow-refunded">
                                <i class="fas fa-undo"></i> PAYOUT REFUNDED
                            </div>
                        @endif

                        <h1 class="product-title">{{ $auction->product_name }}</h1>
                        
                        <div class="product-price">
                            💎{{ number_format($auction->current_bid ?? $auction->starting_price, 0) }}
                        </div>

                        <div class="meta-item">
                            <i class="fa-solid fa-tag"></i>
                            <strong>Category:</strong> {{ $auction->category ?? 'N/A' }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-building"></i>
                            <strong>Brand:</strong> {{ $auction->brand ?? 'N/A' }}
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-box"></i>
                            <strong>Condition:</strong> <span class="text-capitalize">{{ $auction->condition ?? 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-gem"></i>
                            <strong>Starting Price:</strong> 
                            <span class="text-success">💎{{ number_format($auction->starting_price, 0) }}</span>
                        </div>
                        @if($auction->buyout_bid)
                        <div class="meta-item">
                            <i class="fa-solid fa-bolt"></i>
                            <strong>Buyout Price:</strong> 
                            <span class="text-danger">💎{{ number_format($auction->buyout_bid, 0) }}</span>
                        </div>
                        @endif

                        @if($auction->payout_status === 'pending' && $auction->payout_amount > 0)
                        <div class="escrow-section">
                            <h3 class="escrow-title">
                                <i class="fas fa-clock text-warning"></i>
                                Payout in Escrow
                            </h3>
                            <div class="escrow-details">
                                <div class="escrow-row">
                                    <span class="escrow-label">Amount in Escrow:</span>
                                    <span class="escrow-value">💎{{ number_format($auction->payout_amount, 0) }}</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Status:</span>
                                    <span class="escrow-value">Pending Admin Approval</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Held Since:</span>
                                    <span class="escrow-value">{{ $auction->escrow_held_at->format('M j, Y g:i A') }}</span>
                                </div>
                                @if($auction->seller_reply_deadline)
                                <div class="escrow-row">
                                    <span class="escrow-label">Seller Reply Deadline:</span>
                                    <span class="escrow-value {{ $auction->is_seller_reply_overdue ? 'text-danger' : 'text-warning' }}">
                                        {{ $auction->seller_reply_deadline->format('M j, Y g:i A') }}
                                        @if($auction->is_seller_reply_overdue)
                                            <span class="badge bg-danger ms-2">OVERDUE</span>
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="escrow-note">
                                <i class="fas fa-info-circle"></i>
                                The payout is held in escrow until approved by admin.
                            </div>
                        </div>
                        @elseif($auction->payout_status === 'approved')
                        <div class="escrow-section approved">
                            <h3 class="escrow-title approved">
                                <i class="fas fa-check text-success"></i>
                                Payout Approved
                            </h3>
                            <div class="escrow-details">
                                <div class="escrow-row">
                                    <span class="escrow-label">Approved Amount:</span>
                                    <span class="escrow-value">💎{{ number_format($auction->payout_amount, 0) }}</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Status:</span>
                                    <span class="escrow-value">Ready for Release</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Approved At:</span>
                                    <span class="escrow-value">{{ $auction->payout_approved_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                            <div class="escrow-note">
                                <i class="fas fa-info-circle"></i>
                                The payout has been approved and is ready to be released to the seller.
                            </div>
                        </div>
                        @elseif($auction->payout_status === 'released')
                        <div class="escrow-section released">
                            <h3 class="escrow-title released">
                                <i class="fas fa-money-bill-wave text-info"></i>
                                Payout Completed
                            </h3>
                            <div class="escrow-details">
                                <div class="escrow-row">
                                    <span class="escrow-label">Released Amount:</span>
                                    <span class="escrow-value">💎{{ number_format($auction->payout_amount, 0) }}</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Status:</span>
                                    <span class="escrow-value">Completed</span>
                                </div>
                            </div>
                            <div class="escrow-note">
                                <i class="fas fa-info-circle"></i>
                                The payout has been successfully released to the seller.
                            </div>
                        </div>
                        @elseif($auction->payout_status === 'rejected')
                        <div class="escrow-section rejected">
                            <h3 class="escrow-title rejected">
                                <i class="fas fa-times text-danger"></i>
                                Payout Rejected
                            </h3>
                            <div class="escrow-details">
                                <div class="escrow-row">
                                    <span class="escrow-label">Rejected Amount:</span>
                                    <span class="escrow-value">💎{{ number_format($auction->payout_amount, 0) }}</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Status:</span>
                                    <span class="escrow-value">Rejected by Admin</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Rejected At:</span>
                                    <span class="escrow-value">{{ $auction->payout_approved_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                            <div class="escrow-note">
                                <i class="fas fa-info-circle"></i>
                                The payout was rejected by admin. The buyer has been refunded.
                            </div>
                        </div>
                        @elseif($auction->payout_status === 'refunded')
                        <div class="escrow-section refunded">
                            <h3 class="escrow-title refunded">
                                <i class="fas fa-undo text-secondary"></i>
                                Payout Refunded
                            </h3>
                            <div class="escrow-details">
                                <div class="escrow-row">
                                    <span class="escrow-label">Refunded Amount:</span>
                                    <span class="escrow-value">💎{{ number_format($auction->payout_amount, 0) }}</span>
                                </div>
                                <div class="escrow-row">
                                    <span class="escrow-label">Status:</span>
                                    <span class="escrow-value">Refunded to Buyer</span>
                                </div>
                            </div>
                            <div class="escrow-note">
                                <i class="fas fa-info-circle"></i>
                                The payout has been refunded to the buyer.
                            </div>
                        </div>
                        @endif

                        @auth
                            @if($auction->status === 'active' && $auction->user_id !== Auth::id())
                                <div class="bid-form">
                                    <h3 class="bid-form-title">Place Your Bid</h3>

                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="alert alert-error">
                                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                        </div>
                                    @endif

                                    <div class="balance-info">
                                        <i class="fas fa-gem me-2"></i>
                                        Your Balance: 💎{{ number_format(Auth::user()->diamond_balance, 0) }}
                                    </div>

                                    @php
                                        $minBid = $auction->current_bid > 0 ? $auction->current_bid + 1 : $auction->starting_price;
                                    @endphp
                                    <div class="min-bid-info">
                                        Minimum bid: <strong>💎{{ number_format($minBid, 0) }}</strong>
                                        @if($auction->buyout_bid)
                                            <br>Buyout price: <strong class="text-danger">💎{{ number_format($auction->buyout_bid, 0) }}</strong>
                                            <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Bidding 💎{{ number_format($auction->buyout_bid, 0) }} will trigger instant buyout!</small>
                                        @endif
                                    </div>

                                    <form action="{{ route('customer.auctions.bid', $auction->id) }}" method="POST" id="bidForm">
                                        @csrf
                                        <div class="bid-input-group">
                                            <span>💎</span>
                                            <input type="number" 
                                                   name="bid_amount" 
                                                   class="bid-input" 
                                                   id="bidAmount"
                                                   placeholder="Enter bid amount"
                                                   min="{{ $minBid }}"
                                                   @if($auction->buyout_bid)
                                                       max="{{ $auction->buyout_bid }}"
                                                   @endif
                                                   required
                                                   value="{{ $minBid }}">
                                        </div>
                                        <button type="submit" class="btn-bid" id="bidButton">
                                            <i class="fas fa-gavel"></i> Place Bid
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <div class="bid-form text-center">
                                <h3 class="bid-form-title">Want to Bid?</h3>
                                <p class="min-bid-info">Login to place your bid on this item</p>
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login to Bid
                                </a>
                            </div>
                        @endauth

                        @php
                            $highestBid = $auction->bids->sortByDesc('amount')->first();
                        @endphp
                        
                        @if($highestBid && $auction->current_bid > $auction->starting_price)
                        <div class="current-bidder">
                            <h3 class="description-title">Current Leader</h3>
                            <div class="bidder-info">
                                <div class="bidder-avatar">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div>
                                    <div class="bidder-name">
                                        @if($highestBid->user)
                                            {{ $highestBid->user->username ?? $highestBid->user->first_name . ' ' . $highestBid->user->last_name }}
                                        @else
                                            Unknown Bidder
                                        @endif
                                    </div>
                                    <small class="text-muted">Leading Bid</small>
                                </div>
                                <div class="bid-amount">
                                    💎{{ number_format($highestBid->amount, 0) }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($auction->bids->count() > 0)
                        <div class="bids-history-section">
                            <h3 class="description-title">
                                <i class="fa-solid fa-history text-info"></i>
                                Bid History
                            </h3>
                            <div class="bids-history">
                                @foreach($auction->bids->sortByDesc('created_at') as $bid)
                                <div class="bid-item">
                                    <div class="bidder-info-small">
                                        <div class="bidder-avatar-small">
                                            {{ substr($bid->user->username ?? 'Unknown', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="bidder-name-small">
                                                {{ $bid->user->username ?? 'Unknown Bidder' }}
                                                @if($bid->is_buyout)
                                                    <span class="buyout-badge">BUYOUT</span>
                                                @endif
                                            </div>
                                            <div class="bid-time">
                                                {{ $bid->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bid-amount-small">
                                        💎{{ number_format($bid->amount, 0) }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @auth
                            @if($auction->status === 'ended' && $auction->winner_id === Auth::id() && $auction->isPayoutInEscrow() && !$auction->is_item_received)
                            <div class="received-section">
                                <div class="alert alert-info">
                                    <h4><i class="fas fa-box-open"></i> Item Delivery Status</h4>
                                    <p>Once you receive the item, click the button below to automatically release the payment to the seller.</p>
                                    
                                    <div class="escrow-info mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Amount in Escrow:</strong> 💎{{ number_format($auction->payout_amount, 0) }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Seller Reply Deadline:</strong> 
                                                @if($auction->seller_reply_deadline)
                                                    @if($auction->is_seller_reply_overdue)
                                                        <span class="badge bg-danger">OVERDUE</span>
                                                    @else
                                                        {{ $auction->seller_reply_deadline->diffForHumans() }}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-success btn-lg mt-3 w-100" id="markReceivedBtn">
                                        <i class="fas fa-check-circle"></i> I Have Received The Item
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($auction->is_item_received)
                            <div class="received-section">
                                <div class="alert alert-success">
                                    <h4><i class="fas fa-check-circle"></i> Item Received</h4>
                                    <p>You marked this item as received on {{ $auction->item_received_at->format('M j, Y g:i A') }}.</p>
                                    <p><strong>Payment Status:</strong> 
                                        @if($auction->isPayoutReleased())
                                            <span class="badge bg-success">Released to Seller</span>
                                        @else
                                            <span class="badge bg-warning">Processing Release</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        @endauth

                        <div class="action-buttons">
                            @auth
                                @if($auction->user_id === Auth::id() && in_array($auction->status, ['pending', 'approved']))
                                    <a href="{{ route('customer.auctions.edit', $auction->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Auction
                                    </a>
                                @endif
                                
                                @if($auction->user_id !== Auth::id() && $auction->status === 'active')
                                    <a href="{{ route('customer.chat.auction', $auction->id) }}" class="btn btn-info">
                                        <i class="fas fa-comments"></i> Chat with Seller
                                    </a>
                                @endif
                                
                                @if($auction->status === 'ended' && $auction->winner_id === Auth::id())
                                    <a href="{{ route('customer.chat.auction-winner', $auction->id) }}" class="btn btn-success">
                                        <i class="fas fa-trophy"></i> Chat as Winner
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="seller-section">
                        <h3 class="seller-title">
                            <i class="fa-solid fa-store text-primary"></i>
                            Seller Information
                        </h3>
                        
                        <div class="seller-info">
                            <div class="info-card">
                                <div class="info-label">Seller Name</div>
                                <div class="info-value">
                                    {{ $auction->user->first_name ?? 'N/A' }} 
                                    {{ $auction->user->middle_name ? $auction->user->middle_name . ' ' : '' }}
                                    {{ $auction->user->last_name ?? '' }}
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Username</div>
                                <div class="info-value">{{ $auction->user->username ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value">
                                    @if($auction->user->contact_number)
                                        {{ $auction->user->contact_number }}
                                    @else
                                        Not provided
                                    @endif
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $auction->user->email ?? 'N/A' }}</div>
                            </div>
                        </div>

                        @if($auction->user->home_address)
                        <div class="info-card">
                            <div class="info-label">Shipping Address</div>
                            <div class="info-value">
                                {{ $auction->user->home_address }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#bidAmount').on('input', function() {
        const bidAmount = parseInt($(this).val()) || 0;
        const buyoutPrice = parseInt("{{ $auction->buyout_bid ?? 0 }}");
        const bidButton = $('#bidButton');
        
        if (buyoutPrice > 0 && bidAmount === buyoutPrice) {
            bidButton.html('<i class="fas fa-bolt"></i> Buyout Now');
            bidButton.css('background', 'linear-gradient(135deg, #dc3545 0%, #e83e8c 100%)');
        } else {
            bidButton.html('<i class="fas fa-gavel"></i> Place Bid');
            bidButton.css('background', 'linear-gradient(135deg, #28a745 0%, #20c997 100%)');
        }
    });

    $('#bidForm').on('submit', function(e) {
        const bidAmount = parseInt($('#bidAmount').val());
        const minBid = parseInt($('#bidAmount').attr('min'));
        const userBalance = parseInt("{{ Auth::check() ? Auth::user()->diamond_balance : 0 }}");
        const buyoutPrice = parseInt("{{ $auction->buyout_bid ?? 0 }}");

        if (bidAmount < minBid) {
            e.preventDefault();
            alert('Bid amount must be at least 💎' + minBid);
            return false;
        }

        if (bidAmount > userBalance) {
            e.preventDefault();
            alert('Insufficient diamonds. You need 💎' + bidAmount + ' but only have 💎' + userBalance);
            return false;
        }

        if (buyoutPrice > 0 && bidAmount >= buyoutPrice) {
            if (!confirm('This will immediately end the auction and you will win the item! Are you sure?')) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm('Place bid of 💎' + bidAmount + '?')) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });

    if ($('#bidAmount').length > 0) {
        setTimeout(() => {
            $('#bidAmount').focus();
        }, 500);
    }

    const sessionMessages = document.getElementById('session-messages');
    if (sessionMessages) {
        const successMsg = sessionMessages.dataset.success;
        const errorMsg = sessionMessages.dataset.error;
        
        if (successMsg) {
            setTimeout(() => {
                alert(successMsg);
            }, 500);
        }
        
        if (errorMsg) {
            setTimeout(() => {
                alert(errorMsg);
            }, 500);
        }
    }

    $('#markReceivedBtn').on('click', function() {
        if (confirm('Are you sure you have received the item? This will release payment to the seller.')) {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Releasing Payment...');
            
            $.ajax({
                url: '{{ route("customer.auctions.mark-received", $auction->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    btn.html('<i class="fas fa-check-circle"></i> Payment Released');
                    alert(response.success);
                    location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> I Have Received The Item');
                    alert(xhr.responseJSON.error || 'Failed to mark item as received.');
                }
            });
        }
    });

    setInterval(() => {
        location.reload();
    }, 30000);
});

function changeMainImage(thumbnail) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = thumbnail.getAttribute('data-image');
        
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }
}
</script>
@endsection