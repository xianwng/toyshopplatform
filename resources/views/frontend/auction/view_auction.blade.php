@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0">
                <!-- Professional Header with Back Button - WHITE TEXT VERSION -->
                <div class="card-header text-white py-3 position-relative" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-light btn-sm me-3" style="background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.5); color: #0d1b2a;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h4 class="mb-0 fw-semibold text-white">Auction Details</h4>
                            <small class="opacity-75 text-white">Complete auction information and bidding history</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Auction Images Section -->
                        <div class="col-lg-5 col-md-6 mb-4">
                            <div class="sticky-top" style="top: 20px;">
                                <!-- Auction Images Gallery -->
                                <div class="images-container bg-white rounded-3 shadow-sm border p-3">
                                    @if($auction->has_images)
                                        <!-- Main Image Display -->
                                        <div class="product-image-container">
                                            <img id="mainImage" src="{{ $auction->first_image_url }}" alt="{{ $auction->product_name }}" class="product-image" style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                        </div>

                                        <!-- Thumbnail Gallery for Multiple Images -->
                                        @if($auction->has_multiple_images)
                                        <div class="image-gallery mt-3">
                                            @foreach($auction->image_gallery as $index => $image)
                                            <img src="{{ $image['url'] }}" 
                                                 alt="{{ $auction->product_name }} - Image {{ $index + 1 }}"
                                                 class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                                 data-image="{{ $image['url'] }}"
                                                 onclick="changeMainImage(this)">
                                            @endforeach
                                        </div>
                                        @endif
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center text-muted bg-light rounded-3" style="height:400px;">
                                            <i class="bi bi-image fs-1 mb-3 opacity-50"></i>
                                            <h5 class="mb-2">No Images Available</h5>
                                            <p class="text-center small opacity-75">This auction doesn't have any images</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Auction Description -->
                                @if($auction->description)
                                <div class="description-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-text text-primary me-2"></i>Auction Description
                                    </h5>
                                    <div class="description-content bg-light bg-opacity-25 rounded-3 p-4 border">
                                        <p class="mb-0 text-dark lh-lg">{!! nl2br(e($auction->description)) !!}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Documentation Section -->
                                @if($auction->hasOwnerProof() || $auction->hasMarketValueProof())
                                <div class="documentation-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Documentation & Verification
                                    </h5>
                                    
                                    <div class="documentation-cards">
                                        <!-- Owner Proof -->
                                        @if($auction->hasOwnerProof())
                                        <div class="document-card bg-white rounded-3 border p-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="document-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-person-check-fill text-success fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Ownership Proof</h6>
                                                </div>
                                            </div>
                                            
                                            <!-- Owner Proof Image Preview -->
                                            <div class="certificate-preview mt-3">
                                                <a href="{{ $auction->getOwnerProofUrl() }}" target="_blank" class="certificate-image-link">
                                                    <img src="{{ $auction->getOwnerProofUrl() }}" 
                                                         alt="Ownership Proof" 
                                                         class="certificate-image"
                                                         style="width: 100%; height: 200px; object-fit: contain; border-radius: 8px; border: 2px solid #e9ecef; cursor: pointer; transition: all 0.3s ease;">
                                                </a>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">Click image to view full size</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Market Value Proof -->
                                        @if($auction->hasMarketValueProof())
                                        <div class="document-card bg-white rounded-3 border p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="document-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-graph-up text-info fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Market Value Proof</h6>
                                                </div>
                                            </div>
                                            
                                            <!-- Market Value Proof Image Preview -->
                                            <div class="market-proof-preview mt-3">
                                                <a href="{{ $auction->getMarketValueProofUrl() }}" target="_blank" class="market-proof-image-link">
                                                    <img src="{{ $auction->getMarketValueProofUrl() }}" 
                                                         alt="Market Value Proof" 
                                                         class="market-proof-image"
                                                         style="width: 100%; height: 200px; object-fit: contain; border-radius: 8px; border: 2px solid #e9ecef; cursor: pointer; transition: all 0.3s ease;">
                                                </a>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">Click image to view full size</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Auction Information Section -->
                        <div class="col-lg-7 col-md-6">
                            <!-- Auction Header -->
                            <div class="product-header mb-4">
                                <h1 class="h2 fw-bold text-dark mb-2">{{ $auction->product_name ?? 'Untitled Auction' }}</h1>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="h3 fw-bold" style="color: #006d77;">💎{{ number_format($auction->current_bid ?? $auction->starting_price, 0) }}</span>
                                    <span class="badge ms-3" style="background: rgba(0, 109, 119, 0.1); color: #006d77; border: 1px solid rgba(0, 109, 119, 0.3);">
                                        <i class="bi bi-{{ $auction->isActive() ? 'play-circle' : 'pause-circle' }} me-1"></i>{{ $auction->getStatusText() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Auction Specifications -->
                            <div class="specifications-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-grid-3x3-gap me-2" style="color: #0d1b2a;"></i>Auction Specifications
                                </h5>
                                
                                <div class="row g-3">
                                    <!-- Brand -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-shop fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Brand</small>
                                                    <strong class="text-dark">{{ $auction->brand ?? 'Not specified' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-tags fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Category</small>
                                                    <strong class="text-dark">{{ $auction->category ?? 'Not specified' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Condition -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-box-seam fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Condition</small>
                                                    <strong class="text-dark text-capitalize">{{ $auction->condition ?? 'Not specified' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rarity -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-star fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Rarity</small>
                                                    <strong class="text-dark text-capitalize">{{ $auction->rarity ?? 'Common' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Starting Price -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-flag fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Starting Price</small>
                                                    <strong class="text-dark">💎{{ number_format($auction->starting_price, 0) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buyout Price -->
                                    @if($auction->buyout_bid)
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-lightning fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Buyout Price</small>
                                                    <strong class="text-dark">💎{{ number_format($auction->buyout_bid, 0) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-info-circle fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Auction Status</small>
                                                    <span class="badge 
                                                        @if($auction->status === 'active') bg-success
                                                        @elseif($auction->status === 'pending') bg-warning
                                                        @elseif($auction->status === 'approved') bg-info
                                                        @elseif($auction->status === 'ended') bg-secondary
                                                        @elseif($auction->status === 'completed') bg-dark
                                                        @elseif($auction->status === 'rejected') bg-danger
                                                        @else bg-secondary @endif text-capitalize">
                                                        {{ $auction->getStatusText() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payout Status -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-cash-coin fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Payout Status</small>
                                                    <span class="badge 
                                                        @if($auction->payout_status === 'pending') bg-warning
                                                        @elseif($auction->payout_status === 'approved') bg-success
                                                        @elseif($auction->payout_status === 'released') bg-info
                                                        @elseif($auction->payout_status === 'rejected') bg-danger
                                                        @elseif($auction->payout_status === 'refunded') bg-secondary
                                                        @else bg-secondary @endif text-capitalize">
                                                        {{ $auction->payout_status_text ?? 'Not Applicable' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Time Remaining -->
                                    @if($auction->isActive())
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-clock fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Time Remaining</small>
                                                    <strong class="text-dark">{{ $auction->time_remaining }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Total Bids -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-hand-index-thumb fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Total Bids</small>
                                                    <strong class="text-dark">{{ $auction->bids_count ?? $auction->bids->count() }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Start Time -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-play-circle fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Start Time</small>
                                                    <strong class="text-dark">{{ $auction->start_time ? \Carbon\Carbon::parse($auction->start_time)->format('M d, Y h:i A') : 'Not started' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- End Time -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-stop-circle fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">End Time</small>
                                                    <strong class="text-dark">{{ $auction->end_time ? \Carbon\Carbon::parse($auction->end_time)->format('M d, Y h:i A') : 'Not set' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Winner -->
                                    @if($auction->winner_id)
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-trophy fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Winner</small>
                                                    <strong class="text-dark">
                                                        @if($auction->winner)
                                                            {{ $auction->winner->username ?? $auction->winner->first_name . ' ' . $auction->winner->last_name }}
                                                        @else
                                                            User #{{ $auction->winner_id }}
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Payout Amount -->
                                    @if($auction->payout_amount > 0)
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-currency-dollar fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Payout Amount</small>
                                                    <strong class="text-dark">💎{{ number_format($auction->payout_amount, 0) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Bids History Section -->
                            @if($auction->bids->count() > 0)
                            <div class="bids-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-list-ol me-2" style="color: #0d1b2a;"></i>Bid History
                                </h5>
                                
                                <div class="bids-container bg-white rounded-3 border" style="max-height: 300px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-secondary" style="position: sticky; top: 0; z-index: 1;">
                                                <tr>
                                                    <th class="border-0">Bidder</th>
                                                    <th class="border-0">Amount</th>
                                                    <th class="border-0">Time</th>
                                                    <th class="border-0">Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($auction->bids->sortByDesc('created_at') as $bid)
                                                <tr class="{{ $bid->is_buyout ? 'table-warning' : '' }}">
                                                    <td>
                                                        @if($bid->user)
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                    <i class="bi bi-person text-primary"></i>
                                                                </div>
                                                                <div>
                                                                    <strong class="text-dark">{{ $bid->user->username ?? $bid->user->first_name . ' ' . $bid->user->last_name }}</strong>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Unknown</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold text-success">💎{{ number_format($bid->amount, 0) }}</td>
                                                    <td class="text-muted small">{{ $bid->created_at->format('M d, Y h:i A') }}</td>
                                                    <td>
                                                        @if($bid->is_buyout)
                                                            <span class="badge bg-danger">BUYOUT</span>
                                                        @else
                                                            <span class="badge bg-primary">BID</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Seller Information -->
                            @if($auction->user)
                            <div class="seller-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-person-badge me-2" style="color: #0d1b2a;"></i>Seller Information
                                </h5>
                                
                                <div class="seller-card rounded-3 border overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-color: #415a77 !important;">
                                    <!-- Seller Header -->
                                    <div class="seller-header p-4 border-bottom" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                                        <div class="d-flex align-items-center">
                                            <div class="seller-avatar rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2);">
                                                <i class="bi bi-person fs-4 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-white">{{ $auction->user->first_name }} {{ $auction->user->last_name }}</h6>
                                                <p class="mb-0 small text-white-50">
                                                    <i class="bi bi-at me-1"></i>{{ $auction->user->username }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Seller Details -->
                                    <div class="seller-details p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-envelope me-2" style="color: #0d1b2a;"></i>Email Address
                                                    </small>
                                                    <strong class="text-dark">{{ $auction->user->email }}</strong>
                                                </div>
                                            </div>
                                            
                                            @if($auction->user->contact_number)
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-telephone me-2" style="color: #415a77;"></i>Contact Number
                                                    </small>
                                                    <strong class="text-dark">{{ $auction->user->contact_number }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if($auction->user->home_address)
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-geo-alt me-2" style="color: #006d77;"></i>Location
                                                    </small>
                                                    <strong class="text-dark">{{ $auction->user->home_address }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --dark-navy: #0d1b2a;
        --dark-navy-light: #1b263b;
        --dark-slate: #415a77;
        --dark-slate-light: #778da9;
        --dark-teal: #006d77;
        --dark-teal-light: #00838f;
    }

    .card {
        border: none;
        border-radius: 16px;
    }
    
    .card-header {
        border-radius: 16px 16px 0 0 !important;
    }
    
    .spec-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .spec-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 27, 42, 0.1);
    }
    
    .seller-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .seller-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 27, 42, 0.1);
    }
    
    .images-container {
        border: 1px solid #e9ecef;
        transition: box-shadow 0.3s ease;
    }
    
    .images-container:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .icon-container {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .description-content {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .seller-avatar {
        transition: transform 0.3s ease;
    }
    
    .seller-card:hover .seller-avatar {
        transform: scale(1.05);
    }
    
    .sticky-top {
        z-index: 1;
    }
    
    .card-header .btn-light {
        border: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .card-header .btn-light:hover {
        background-color: rgba(255, 255, 255, 1);
        color: #0d1b2a;
        transform: translateX(-2px);
        box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
    }

    /* Image Gallery Styles */
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
        border-color: #0d1b2a;
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.3);
    }

    .product-image-container {
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-image-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .document-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .document-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Documentation Image Styles */
    .certificate-image:hover,
    .market-proof-image:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #0d1b2a !important;
    }

    .certificate-image-link,
    .market-proof-image-link {
        text-decoration: none;
        display: block;
    }

    .certificate-image-link:hover,
    .market-proof-image-link:hover {
        text-decoration: none;
    }

    /* Description Section */
    .description-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    /* Bids Section */
    .bids-container {
        border: 1px solid #e9ecef;
    }

    .bids-container .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .sticky-top {
            position: relative !important;
        }
        
        .product-header h1 {
            font-size: 1.5rem;
        }
        
        .product-header .h3 {
            font-size: 1.25rem;
        }
        
        .card-header .btn-light {
            padding: 0.375rem 0.5rem;
        }
        
        .document-card .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .certificate-image,
        .market-proof-image {
            height: 150px !important;
        }

        .thumbnail {
            width: 70px;
            height: 70px;
        }

        .product-image-container {
            height: 300px;
        }

        .description-section {
            padding: 1.5rem;
        }
    }
</style>

<script>
// Function to change main image when clicking thumbnails
function changeMainImage(thumbnail) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = thumbnail.getAttribute('data-image');
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations for cards
    const cards = document.querySelectorAll('.spec-card, .seller-card, .document-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // Add hover effects to documentation images
    const certificateImages = document.querySelectorAll('.certificate-image, .market-proof-image');
    certificateImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
            this.style.borderColor = '#0d1b2a';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
            this.style.borderColor = '#e9ecef';
        });
    });

    // Auto-scroll bids table to top on load
    const bidsContainer = document.querySelector('.bids-container');
    if (bidsContainer) {
        bidsContainer.scrollTop = 0;
    }
});
</script>
@endsection