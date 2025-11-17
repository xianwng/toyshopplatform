@extends('customer.layouts.cmaster')

@section('title', $product->name . ' | Toyspace')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        
        /* New refined dark color scheme - 3 sophisticated dark tones */
        --dark-navy: #0d1b2a;
        --dark-navy-light: #1b263b;
        --dark-slate: #415a77;
        --dark-slate-light: #778da9;
        --dark-teal: #006d77;
        --dark-teal-light: #00838f;
    }

    .main {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .product-hero {
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }

    .product-hero::before {
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

    .breadcrumb-nav {
        margin-bottom: 15px;
    }

    .breadcrumb-nav a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb-nav a:hover {
        color: white;
    }

    .breadcrumb-nav .separator {
        margin: 0 10px;
        color: rgba(255,255,255,0.6);
    }

    model-viewer {
        width: 100%;
        height: 500px;
        background: #f8f9fa;
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .product-image-container {
        width: 100%;
        height: 500px;
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

    .model-placeholder {
        height: 500px;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-radius: 20px;
        position: relative;
        color: white;
    }

    .image-placeholder {
        height: 500px;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-radius: 20px;
        position: relative;
        color: white;
    }

    .back-button-overlay {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 10;
    }

    .back-button-icon {
        background: rgba(255, 255, 255, 0.95);
        color: var(--dark-navy);
        border: 2px solid var(--dark-navy);
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
        background: var(--dark-navy);
        color: white;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.3);
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
        border-color: var(--dark-navy);
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(13, 27, 42, 0.3);
    }

    .product-details-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .product-details-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
    }

    .product-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .product-price {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--dark-teal);
        margin-bottom: 2rem;
        background: linear-gradient(135deg, var(--dark-teal), var(--dark-teal-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .meta-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.2rem;
        border-radius: 12px;
        border-left: 4px solid var(--dark-navy);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .meta-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .meta-icon {
        font-size: 1.5rem;
        color: var(--dark-navy);
        margin-bottom: 0.5rem;
    }

    .meta-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meta-value {
        color: #1a1a1a;
        font-weight: 600;
        font-size: 1rem;
    }

    .stock-badge {
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .stock-in { 
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 2px solid #28a745;
    }
    .stock-out { 
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border: 2px solid #dc3545;
    }

    .owner-notice {
        background: linear-gradient(135deg, #e7f3ff 0%, #d1ecf1 100%);
        border: 2px solid var(--dark-navy);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        margin: 2rem 0;
    }

    .owner-notice i {
        color: var(--dark-navy);
        margin-right: 10px;
        font-size: 1.2rem;
    }

    /* Quantity and Buy Now Section - Side by Side */
    .purchase-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin: 2rem 0;
        flex-wrap: wrap;
    }

    .quantity-section {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 250px;
    }

    .quantity-label {
        font-weight: 600;
        color: var(--dark);
        font-size: 1.1rem;
        min-width: 80px;
    }

    .quantity-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--dark-navy);
        border-radius: 50px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 15px rgba(13, 27, 42, 0.1);
        transition: all 0.3s ease;
    }

    .quantity-wrapper:focus-within {
        box-shadow: 0 6px 25px rgba(13, 27, 42, 0.2);
        transform: translateY(-2px);
    }

    .quantity-btn {
        width: 50px;
        height: 50px;
        border: none;
        background: var(--dark-navy);
        color: white;
        font-size: 1.4rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quantity-btn:hover:not(:disabled) {
        background: var(--dark-slate);
        transform: scale(1.1);
    }

    .quantity-btn:disabled {
        background: #cbd5e1;
        color: #64748b;
        cursor: not-allowed;
    }

    .quantity-input {
        width: 80px;
        height: 50px;
        text-align: center;
        border: none;
        border-left: 2px solid var(--dark-navy);
        border-right: 2px solid var(--dark-navy);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark);
        background: #fff;
    }

    .quantity-input:focus {
        outline: none;
        background: #f8f9fa;
    }

    .buy-now-section {
        flex: 1;
        min-width: 200px;
    }

    .btn-buy-now {
        width: 100%;
        background: linear-gradient(135deg, var(--dark-teal), var(--dark-teal-light));
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(0, 109, 119, 0.2);
        height: 50px;
    }

    .btn-buy-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 109, 119, 0.3);
        background: linear-gradient(135deg, var(--dark-teal-light), var(--dark-teal));
    }

    .btn-disabled { 
        background: #cbd5e1 !important;
        color: #64748b !important;
        cursor: not-allowed;
        opacity: 0.6;
        box-shadow: none !important;
    }

    .btn-disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    /* Description Section - Moved below images */
    .description-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid #e9ecef;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--dark-navy);
    }

    .section-title i {
        color: var(--dark-navy);
    }

    .description-content {
        line-height: 1.8;
        color: #555;
        font-size: 1.05rem;
        background: transparent;
        padding: 0;
    }

    /* Shipping Methods Section */
    .shipping-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid #e9ecef;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .shipping-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .shipping-method-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }

    .shipping-method-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
    }

    .shipping-method-card:hover {
        border-color: var(--dark-navy);
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 30px rgba(13, 27, 42, 0.15);
    }

    .shipping-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .shipping-name {
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .shipping-desc {
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.4;
    }

    /* Seller Information Section */
    .seller-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 2.5rem;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        margin-top: 3rem;
        position: relative;
        overflow: hidden;
    }

    .seller-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
    }

    .seller-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-radius: 15px;
        border-left: 4px solid var(--dark-navy);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #1a1a1a;
        font-weight: 600;
        font-size: 1.05rem;
    }

    /* Loading spinner */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s ease infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .product-title {
            font-size: 2rem;
        }
        
        .product-price {
            font-size: 1.8rem;
        }
        
        .meta-grid {
            grid-template-columns: 1fr;
        }
        
        .seller-info {
            grid-template-columns: 1fr;
        }
        
        .shipping-methods {
            grid-template-columns: 1fr;
        }
        
        .product-image-container {
            height: 400px;
        }
        
        .image-placeholder {
            height: 400px;
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

        .purchase-section {
            flex-direction: column;
            align-items: stretch;
        }

        .quantity-section {
            justify-content: space-between;
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
    <!-- Product Hero Section -->
    <section class="product-hero">
        <div class="container hero-content">
            <div class="breadcrumb-nav">
                <a href="{{ route('cproduct') }}" style="color: white;">Products</a>
                <span class="separator">/</span>
                <span style="color: white;">{{ $product->category ?? 'Product' }}</span>
                <span class="separator">/</span>
                <span style="color: white;">{{ $product->name }}</span>
            </div>
            <h1 class="product-title" style="color: white;">{{ $product->name }}</h1>
            <p class="hero-subtitle" style="color: white;">Explore this amazing product from our marketplace</p>
        </div>
    </section>

    <section class="module-small">
        <div class="container">
            <div class="row">
                <!-- Product Image / 3D Model -->
                <div class="col-lg-6 mb-4">
                    <div class="product-image-container">
                        <!-- Back Button as Icon Overlay -->
                        <div class="back-button-overlay">
                            <a href="{{ route('cproduct') }}" class="back-button-icon" title="Back to Products">
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>

                        @if(!empty($product->model_file))
                            <model-viewer 
                                src="{{ asset('storage/' . $product->model_file) }}"
                                alt="3D Model of {{ $product->name }}"
                                auto-rotate
                                camera-controls
                                loading="eager">
                            </model-viewer>
                        @elseif($product->hasImages())
                            <img id="mainImage" src="{{ $product->first_image_url }}" alt="{{ $product->name }}" class="product-image">
                        @else
                            <div class="image-placeholder">
                                <i class="fa-solid fa-cube fa-5x mb-3"></i>
                                <h3 class="mb-1">No Image Available</h3>
                                <p class="mb-0">Product visualization not provided</p>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Gallery for Multiple Images -->
                    @if($product->hasImages() && count($product->image_gallery) > 1)
                    <div class="image-gallery">
                        @foreach($product->image_urls as $index => $imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $product->name }} - Image {{ $index + 1 }}"
                             class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                             data-image="{{ $imageUrl }}"
                             onclick="changeMainImage(this)">
                        @endforeach
                    </div>
                    @endif

                    <!-- Product Description - MOVED BELOW IMAGES -->
                    @if($product->description)
                    <div class="description-section">
                        <h3 class="section-title">
                            <i class="fa-solid fa-file-lines"></i>
                            Product Description
                        </h3>
                        <div class="description-content">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="col-lg-6">
                    <div class="product-details-card">
                        <div class="product-price">₱{{ number_format($product->price, 2) }}</div>

                        <div class="meta-grid">
                            <div class="meta-card">
                                <div class="meta-icon">
                                    <i class="fa-solid fa-tag"></i>
                                </div>
                                <div class="meta-label">Category</div>
                                <div class="meta-value">{{ $product->category ?? 'Not specified' }}</div>
                            </div>
                            
                            <div class="meta-card">
                                <div class="meta-icon">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="meta-label">Brand</div>
                                <div class="meta-value">{{ $product->brand ?? 'Not specified' }}</div>
                            </div>
                            
                            <div class="meta-card">
                                <div class="meta-icon">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                                <div class="meta-label">Condition</div>
                                <div class="meta-value text-capitalize">{{ $product->condition ?? 'Not specified' }}</div>
                            </div>
                            
                            <div class="meta-card">
                                <div class="meta-icon">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div class="meta-label">Stock Status</div>
                                <div class="meta-value">
                                    <span class="stock-badge {{ $product->stock > 0 ? 'stock-in' : 'stock-out' }}">
                                        <i class="fa-solid {{ $product->stock > 0 ? 'fa-check' : 'fa-times' }}"></i>
                                        {{ $product->stock > 0 ? $product->stock . ' Available' : 'Out of Stock' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Check if current user is the product owner -->
                        @php
                            $isOwner = Auth::check() && $product->user_id === Auth::id();
                        @endphp

                        @if($isOwner)
                            <!-- Owner View -->
                            <div class="owner-notice">
                                <i class="fa-solid fa-user-shield"></i>
                                <strong>This is your product listing.</strong> You can manage this product from your dashboard.
                            </div>
                        @elseif($product->stock <= 0)
                            <!-- Out of Stock -->
                            <div class="alert alert-warning border-0 mb-4">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                <strong>Out of Stock</strong>
                                <br>
                                This product is currently unavailable. Check back later or browse similar products.
                            </div>
                        @endif

                        @if(!$isOwner && $product->stock > 0)
                        <!-- Quantity and Buy Now Section - Side by Side -->
                        <div class="purchase-section">
                            <div class="quantity-section">
                                <label class="quantity-label">Quantity:</label>
                                <div class="quantity-wrapper">
                                    <button type="button" class="quantity-btn" id="decreaseQty">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" id="quantityInput" value="1" min="1" max="{{ $product->stock }}">
                                    <button type="button" class="quantity-btn" id="increaseQty">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="buy-now-section">
                                <form id="buyNowForm" action="{{ route('customer.chat.buy-now-chat', ['productId' => $product->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                                    <button type="submit" class="btn-buy-now" id="buyNowBtn">
                                        <i class="fa-solid fa-bolt me-2"></i>Buy Now
                                    </button>
                                </form>
                            </div>
                        </div>
                        @elseif(!$isOwner)
                        <!-- Disabled State -->
                        <div class="purchase-section">
                            <div class="quantity-section">
                                <label class="quantity-label">Quantity:</label>
                                <div class="quantity-wrapper">
                                    <button type="button" class="quantity-btn" disabled>
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" value="1" disabled>
                                    <button type="button" class="quantity-btn" disabled>
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="buy-now-section">
                                <button class="btn-buy-now btn-disabled" disabled>
                                    <i class="fa-solid fa-times me-2"></i>Out of Stock
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Shipping Methods -->
                        <div class="shipping-section">
                            <h3 class="section-title">
                                <i class="fa-solid fa-truck"></i>
                                Available Shipping Methods
                            </h3>
                            
                            <div class="shipping-methods">
                                @php
                                    $shippingMethods = $shippingMethods ?? [];
                                @endphp
                                
                                @if(!empty($shippingMethods) && is_array($shippingMethods) && count($shippingMethods) > 0)
                                    @foreach($shippingMethods as $method)
                                        @if(is_string($method))
                                            @if($method === 'lalamove')
                                                <div class="shipping-method-card">
                                                    <div class="shipping-icon">
                                                        <i class="fa-solid fa-motorcycle"></i>
                                                    </div>
                                                    <div class="shipping-name">Lalamove Delivery</div>
                                                    <div class="shipping-desc">Fast and reliable motorcycle delivery service for quick local shipments</div>
                                                </div>
                                            @elseif($method === 'lbc')
                                                <div class="shipping-method-card">
                                                    <div class="shipping-icon">
                                                        <i class="fa-solid fa-shipping-fast"></i>
                                                    </div>
                                                    <div class="shipping-name">LBC Express</div>
                                                    <div class="shipping-desc">Trusted nationwide courier with multiple branch locations across the country</div>
                                                </div>
                                            @elseif($method === 'jnt')
                                                <div class="shipping-method-card">
                                                    <div class="shipping-icon">
                                                        <i class="fa-solid fa-truck"></i>
                                                    </div>
                                                    <div class="shipping-name">J&T Express</div>
                                                    <div class="shipping-desc">Fast and affordable nationwide delivery with real-time tracking</div>
                                                </div>
                                            @else
                                                <div class="shipping-method-card">
                                                    <div class="shipping-icon">
                                                        <i class="fa-solid fa-question"></i>
                                                    </div>
                                                    <div class="shipping-name">{{ ucfirst($method) }}</div>
                                                    <div class="shipping-desc">Available shipping method</div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <div class="info-card w-100 text-center">
                                        <div class="info-value text-muted">
                                            <i class="fa-solid fa-info-circle me-2"></i>
                                            Shipping methods to be discussed with seller
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Seller Information Section -->
                    <div class="seller-section">
                        <h3 class="section-title">
                            <i class="fa-solid fa-store"></i>
                            Seller Information
                        </h3>
                        
                        <div class="seller-info">
                            <div class="info-card">
                                <div class="info-label">Seller Name</div>
                                <div class="info-value">
                                    {{ $seller->first_name ?? 'N/A' }} 
                                    {{ $seller->middle_name ? $seller->middle_name . ' ' : '' }}
                                    {{ $seller->last_name ?? '' }}
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Username</div>
                                <div class="info-value">{{ $seller->username ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value">
                                    @if($seller->contact_number)
                                        <i class="fa-solid fa-phone me-2" style="color: var(--dark-navy);"></i>{{ $seller->contact_number }}
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">Email Address</div>
                                <div class="info-value">
                                    <i class="fa-solid fa-envelope me-2" style="color: var(--dark-navy);"></i>{{ $seller->email ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        @if($seller->home_address)
                        <div class="info-card">
                            <div class="info-label">Shipping Address</div>
                            <div class="info-value">
                                <i class="fa-solid fa-map-marker-alt me-2" style="color: var(--dark-navy);"></i>
                                {{ $seller->home_address }}
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
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

<script>
$(document).ready(function() {
    const maxStock = <?php echo $product->stock ?? 0; ?>;
    const qtyInput = $('#quantityInput');
    const buyNowQuantity = $('#buyNowQuantity');
    const decreaseBtn = $('#decreaseQty');
    const increaseBtn = $('#increaseQty');
    const buyNowBtn = $('#buyNowBtn');
    const buyNowForm = $('#buyNowForm');

    function updateButtonStates() {
        const currentValue = parseInt(qtyInput.val());
        decreaseBtn.prop('disabled', currentValue <= 1);
        increaseBtn.prop('disabled', currentValue >= maxStock);
        
        // Update the form quantity field
        buyNowQuantity.val(currentValue);
    }

    updateButtonStates();

    increaseBtn.click(function() {
        let value = parseInt(qtyInput.val());
        if (value < maxStock) {
            qtyInput.val(value + 1);
            updateButtonStates();
        }
    });

    decreaseBtn.click(function() {
        let value = parseInt(qtyInput.val());
        if (value > 1) {
            qtyInput.val(value - 1);
            updateButtonStates();
        }
    });

    qtyInput.on('input', function() {
        let value = parseInt($(this).val());
        if (isNaN(value) || value < 1) {
            $(this).val(1);
        } else if (value > maxStock) {
            $(this).val(maxStock);
        }
        updateButtonStates();
    });

    // ✅ FIXED: Buy Now form submission - store data in session then redirect
    buyNowForm.on('submit', function(e) {
        e.preventDefault();
        
        // Update form quantity before submission
        const quantity = parseInt(qtyInput.val());
        buyNowQuantity.val(quantity);
        
        // Show loading state
        buyNowBtn.addClass('btn-loading');
        buyNowBtn.prop('disabled', true);
        
        // Store buy now data in session via AJAX
        const productId = $(this).find('input[name="product_id"]').val();
        const totalPrice = <?php echo $product->price; ?> * quantity;
        
        $.ajax({
            url: '{{ route("customer.chat.store-buy-now-data") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity,
                total_price: totalPrice
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to the buy now chat route
                    window.location.href = "{{ route('customer.chat.buy-now-chat', ['productId' => ':productId']) }}".replace(':productId', productId);
                } else {
                    alert('Error: ' + response.message);
                    buyNowBtn.removeClass('btn-loading');
                    buyNowBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('Error storing buy now data:', xhr);
                alert('Error processing purchase. Please try again.');
                buyNowBtn.removeClass('btn-loading');
                buyNowBtn.prop('disabled', false);
            }
        });
    });

    // Show success/error messages if any
    <?php if(session('success')): ?>
        alert("<?php echo addslashes(session('success')); ?>");
    <?php endif; ?>

    <?php if(session('error')): ?>
        alert("<?php echo addslashes(session('error')); ?>");
    <?php endif; ?>
});

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
</script>
@endsection