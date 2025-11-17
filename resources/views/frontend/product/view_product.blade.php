@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0">
                <!-- Professional Header with Back Button - WHITE TEXT VERSION -->
                <div class="card-header text-white py-3 position-relative" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('product') }}" class="btn btn-light btn-sm me-3" style="background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.5); color: #0d1b2a;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h4 class="mb-0 fw-semibold text-white">Product Details</h4>
                            <small class="opacity-75 text-white">Complete product information and specifications</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Product Images Section -->
                        <div class="col-lg-5 col-md-6 mb-4">
                            <div class="sticky-top" style="top: 20px;">
                                <!-- Product Images Gallery -->
                                <div class="images-container bg-white rounded-3 shadow-sm border p-3">
                                    @if($product->hasImages())
                                        <!-- Main Image Display -->
                                        <div class="product-image-container">
                                            <img id="mainImage" src="{{ $product->first_image_url }}" alt="{{ $product->name }}" class="product-image" style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                        </div>

                                        <!-- Thumbnail Gallery for Multiple Images -->
                                        @if(count($product->image_gallery) > 1)
                                        <div class="image-gallery mt-3">
                                            @foreach($product->image_urls as $index => $imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                 class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                                 data-image="{{ $imageUrl }}"
                                                 onclick="changeMainImage(this)">
                                            @endforeach
                                        </div>
                                        @endif
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center text-muted bg-light rounded-3" style="height:400px;">
                                            <i class="bi bi-image fs-1 mb-3 opacity-50"></i>
                                            <h5 class="mb-2">No Images Available</h5>
                                            <p class="text-center small opacity-75">This product doesn't have any images</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Description -->
                                @if($product->description)
                                <div class="description-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-text text-primary me-2"></i>Product Description
                                    </h5>
                                    <div class="description-content bg-light bg-opacity-25 rounded-3 p-4 border">
                                        <p class="mb-0 text-dark lh-lg">{!! nl2br(e($product->description)) !!}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Documentation Section -->
                                @if($product->hasCertificate() || $product->hasMarketValueProof())
                                <div class="documentation-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Documentation & Verification
                                    </h5>
                                    
                                    <div class="documentation-cards">
                                        <!-- Product Certificate -->
                                        @if($product->hasCertificate())
                                        <div class="document-card bg-white rounded-3 border p-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="document-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-award-fill text-success fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Product Certificate</h6>
                                                </div>
                                            </div>
                                            
                                            <!-- Certificate Image Preview -->
                                            <div class="certificate-preview mt-3">
                                                <a href="{{ $product->certificate_url }}" target="_blank" class="certificate-image-link">
                                                    <img src="{{ $product->certificate_url }}" 
                                                         alt="Product Certificate" 
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
                                        @if($product->hasMarketValueProof())
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
                                                <a href="{{ $product->market_value_proof_url }}" target="_blank" class="market-proof-image-link">
                                                    <img src="{{ $product->market_value_proof_url }}" 
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

                        <!-- Product Information Section -->
                        <div class="col-lg-7 col-md-6">
                            <!-- Product Header -->
                            <div class="product-header mb-4">
                                <h1 class="h2 fw-bold text-dark mb-2">{{ $product->name ?? 'Untitled Product' }}</h1>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="h3 fw-bold" style="color: #006d77;">₱{{ number_format($product->price, 2) }}</span>
                                    <span class="badge ms-3" style="background: rgba(0, 109, 119, 0.1); color: #006d77; border: 1px solid rgba(0, 109, 119, 0.3);">
                                        <i class="bi bi-check-circle me-1"></i>Available
                                    </span>
                                </div>
                            </div>

                            <!-- Product Specifications -->
                            <div class="specifications-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-grid-3x3-gap me-2" style="color: #0d1b2a;"></i>Product Specifications
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
                                                    <strong class="text-dark">{{ $product->brand ?? 'Not specified' }}</strong>
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
                                                    <strong class="text-dark">{{ $product->category ?? 'Not specified' }}</strong>
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
                                                    <strong class="text-dark text-capitalize">{{ $product->condition_display ?? 'Not specified' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-archive fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Available Stock</small>
                                                    <strong class="text-dark">{{ $product->stock ?? 0 }} units</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #415a77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(65, 90, 119, 0.1);">
                                                    <i class="bi bi-info-circle fs-5" style="color: #415a77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Listing Status</small>
                                                    <span class="badge 
                                                        @if($product->status === 'active') bg-success
                                                        @elseif($product->status === 'pending') bg-warning
                                                        @elseif($product->status === 'approved') bg-info
                                                        @elseif($product->status === 'rejected') bg-danger
                                                        @else bg-secondary @endif text-capitalize">
                                                        {{ $product->status_display ?? 'pending' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ASIN -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-upc-scan fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Amazon ASIN</small>
                                                    <strong class="text-dark">{{ $product->asin ?? 'Not provided' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Methods -->
                            @php
                                $shippingMethods = $product->shipping_methods ?? [];
                                if (!is_array($shippingMethods) && is_string($shippingMethods)) {
                                    $decoded = json_decode($shippingMethods, true);
                                    $shippingMethods = is_array($decoded) ? $decoded : [];
                                }
                            @endphp

                            @if(!empty($shippingMethods) && is_array($shippingMethods))
                            <div class="shipping-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-truck me-2" style="color: #0d1b2a;"></i>Shipping Methods
                                </h5>
                                <div class="row g-2">
                                    @foreach($shippingMethods as $method)
                                        @if(is_string($method))
                                            <div class="col-md-4 col-sm-6">
                                                <div class="shipping-method-card rounded-3 p-3 border text-center" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-color: #415a77 !important;">
                                                    @if($method === 'lalamove')
                                                        <i class="bi bi-bicycle fs-2 mb-2" style="color: #0d1b2a;"></i>
                                                        <h6 class="fw-semibold mb-1" style="color: #0d1b2a;">Lalamove</h6>
                                                        <small class="text-muted">Same-day delivery</small>
                                                    @elseif($method === 'lbc')
                                                        <i class="bi bi-box-seam fs-2 mb-2" style="color: #415a77;"></i>
                                                        <h6 class="fw-semibold mb-1" style="color: #415a77;">LBC Express</h6>
                                                        <small class="text-muted">Nationwide courier</small>
                                                    @elseif($method === 'jnt')
                                                        <i class="bi bi-truck fs-2 mb-2" style="color: #006d77;"></i>
                                                        <h6 class="fw-semibold mb-1" style="color: #006d77;">J&T Express</h6>
                                                        <small class="text-muted">Fast delivery</small>
                                                    @else
                                                        <i class="bi bi-shield-check fs-2 mb-2" style="color: #0d1b2a;"></i>
                                                        <h6 class="fw-semibold mb-1" style="color: #0d1b2a;">{{ ucfirst($method) }}</h6>
                                                        <small class="text-muted">Available</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Seller Information -->
                            @if($product->user)
                            <div class="seller-section">
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
                                                <h6 class="fw-bold mb-1 text-white">{{ $product->user->first_name }} {{ $product->user->last_name }}</h6>
                                                <p class="mb-0 small text-white-50">
                                                    <i class="bi bi-at me-1"></i>{{ $product->user->username }}
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
                                                    <strong class="text-dark">{{ $product->user->email }}</strong>
                                                </div>
                                            </div>
                                            
                                            @if($product->user->contact_number)
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-telephone me-2" style="color: #415a77;"></i>Contact Number
                                                    </small>
                                                    <strong class="text-dark">{{ $product->user->contact_number }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if($product->user->home_address)
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-geo-alt me-2" style="color: #006d77;"></i>Location
                                                    </small>
                                                    <strong class="text-dark">{{ $product->user->home_address }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Customer Actions (if needed) -->
                            @if(auth()->check() && auth()->user()->role === 'customer')
                            <div class="customer-actions-section mt-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-cart me-2" style="color: #0d1b2a;"></i>Purchase Options
                                </h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart me-1"></i>Buy Now
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-chat me-1"></i>Contact Seller
                                    </button>
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
    
    .shipping-method-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .shipping-method-card:hover {
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
    
    .customer-actions-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #dee2e6;
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
    const cards = document.querySelectorAll('.spec-card, .shipping-method-card, .seller-card, .document-card');
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
});
</script>
@endsection