@extends('customer.layouts.cmaster')

@section('title', 'Products Marketplace | Toyspace')

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

    /* Hero Section - Updated to Dark Red */
    .hero-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .product-card {
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

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: var(--gray-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .image-placeholder {
        width: 100%;
        height: 100%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: var(--gray);
    }

    .clickable-overlay {
        position: absolute;
        inset: 0;
        background: transparent;
        cursor: pointer;
        z-index: 20;
    }

    .product-badge {
        position: absolute;
        top: 10px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 2;
    }

    .badge-left {
        left: 10px;
    }

    .badge-right {
        right: 10px;
    }

    .badge-in-stock {
        background: var(--success);
        color: white;
    }
    
    .badge-out-of-stock {
        background: var(--danger);
        color: white;
    }

    .badge-owner {
        background: var(--primary);
        color: white;
    }

    .badge-pending {
        background: var(--warning);
        color: var(--dark);
    }

    .badge-approved {
        background: var(--info);
        color: white;
    }

    .badge-active {
        background: var(--success);
        color: white;
    }

    .product-info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .product-meta {
        margin-bottom: 10px;
        flex-grow: 1;
    }

    .product-brand {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 3px;
    }

    .product-category {
        font-size: 0.8rem;
        color: var(--primary);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .product-price {
        margin-bottom: 10px;
    }

    .price-php {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--success);
    }

    .product-stock {
        font-size: 0.75rem;
        color: var(--gray);
        margin-bottom: 12px;
        padding: 6px 10px;
        background: var(--light);
        border-radius: 6px;
        text-align: center;
    }

    .product-actions {
        display: flex;
        gap: 6px;
        margin-top: auto;
    }

    .btn-action {
        flex: 1;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .btn-view {
        background: var(--blue);
        color: white;
        border: 1px solid var(--blue);
    }
    
    .btn-view:hover {
        background: #0056b3;
        border-color: #0056b3;
        color: white;
    }
    
    .btn-edit {
        background: var(--warning);
        color: var(--dark);
        border: 1px solid var(--warning);
    }
    
    .btn-delete {
        background: var(--danger);
        color: white;
        border: 1px solid var(--danger);
    }

    .btn-delete:hover {
        background: #c82333;
        border-color: #bd2130;
    }

    .btn-buy {
        background: var(--success);
        color: white;
        border: 1px solid var(--success);
    }

    .btn-buy:hover {
        background: #218838;
        border-color: #1e7e34;
    }

    .btn-warning {
        background: var(--warning);
        color: var(--dark);
        border: 1px solid var(--warning);
    }

    .btn-warning:hover {
        background: #e0a800;
        border-color: #d39e00;
        color: var(--dark);
    }

    .btn-chat {
        background: var(--info);
        color: white;
        border: 1px solid var(--info);
    }

    .btn-chat:hover {
        background: #138496;
        border-color: #117a8b;
        color: white;
    }

    /* Updated to Green */
    .btn-add-product {
        background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
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
    
    .btn-add-product:hover {
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

    .owner-notice {
        background: var(--light);
        border: 1px solid var(--primary);
        border-radius: 6px;
        padding: 8px;
        text-align: center;
        margin-top: 8px;
        font-size: 0.75rem;
        color: var(--primary);
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .section-header {
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section - Updated to Dark Red -->
<section class="hero-section">
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title">Products Marketplace</h1>
            <p class="hero-subtitle">Buy and sell toys with other collectors in our vibrant community marketplace</p>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="crud-section">
    <div class="container">
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

        <!-- User's Products Section -->
        @auth
        <div class="my-products-section">
            <div class="d-flex justify-content-between align-items-center section-header">
                <h2 class="section-title mb-0">My Products</h2>
                <a href="{{ url('/customer/products/cadd') }}" class="btn btn-add-product">
                    <i class="fa fa-plus-circle"></i> Add New Product
                </a>
            </div>

            @if($userProducts->count() > 0)
                <div class="product-grid">
                    @foreach($userProducts as $product)
                    <div class="product-card" id="product-{{ $product->id }}">
                        <div class="product-image-container">
                            @if($product->hasImages())
                                <!-- SIMPLIFIED: Show only one image with fallback -->
                                <img src="{{ $product->first_image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-image"
                                     onerror="handleImageError(this)">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa fa-cube fa-2x mb-2"></i>
                                    <small>No Image Available</small>
                                </div>
                            @endif
                            
                            {{-- Stock badge - simplified: only In Stock or Out of Stock --}}
                            @if($product->stock > 0)
                                <span class="product-badge badge-right badge-in-stock">In Stock</span>
                            @else
                                <span class="product-badge badge-right badge-out-of-stock">Out of Stock</span>
                            @endif

                            {{-- Your Item badge - top left --}}
                            <span class="product-badge badge-left badge-owner">
                                <i class="fa fa-user me-1"></i> Your Item
                            </span>

                            {{-- Status badge --}}
                            @if($product->status === 'pending')
                                <span class="product-badge badge-left" style="top: 40px; background: var(--warning); color: var(--dark);">
                                    <i class="fa fa-clock me-1"></i> Pending Approval
                                </span>
                            @elseif($product->status === 'approved')
                                <span class="product-badge badge-left" style="top: 40px; background: var(--info); color: white;">
                                    <i class="fa fa-check me-1"></i> Approved
                                </span>
                            @elseif($product->status === 'active')
                                <span class="product-badge badge-left" style="top: 40px; background: var(--success); color: white;">
                                    <i class="fa fa-play me-1"></i> Active
                                </span>
                            @endif

                            <a href="{{ route('customer.products.show', $product->id) }}" class="clickable-overlay"></a>
                        </div>

                        <div class="product-info">
                            <h3 class="product-title">{{ $product->name ?? '—' }}</h3>
                            
                            <div class="product-meta">
                                <div class="product-brand">
                                    <strong>Brand:</strong> {{ $product->brand ?? '—' }}
                                </div>
                                <div class="product-category">
                                    {{ $product->category ?? '—' }}
                                </div>
                                
                                <div class="product-price">
                                    <div class="price-php">₱{{ number_format($product->price, 2) }}</div>
                                </div>

                                <div class="product-stock">
                                    <i class="fa fa-box me-1"></i>
                                    @if($product->stock > 0)
                                        {{ $product->stock }} items available
                                    @else
                                        Out of stock
                                    @endif
                                </div>
                            </div>

                            {{-- Action buttons for user's own products in MY PRODUCTS section --}}
                            <div class="product-actions">
                                {{-- Always show View button in My Products section --}}
                                <a href="{{ route('customer.products.show', $product->id) }}" class="btn-action btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>

                                @if($product->status === 'pending')
                                    {{-- Cancel button for pending products --}}
                                    <button type="button" class="btn-action btn-delete cancel-product-btn" 
                                            data-product-id="{{ $product->id }}" 
                                            data-product-name="{{ $product->name }}">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                @elseif($product->status === 'approved')
                                    {{-- Activate and Cancel buttons for approved products --}}
                                    <button type="button" class="btn-action btn-buy activate-product-btn" 
                                            data-product-id="{{ $product->id }}" 
                                            data-product-name="{{ $product->name }}">
                                        <i class="fa fa-play"></i> Activate
                                    </button>
                                    <button type="button" class="btn-action btn-delete cancel-product-btn" 
                                            data-product-id="{{ $product->id }}" 
                                            data-product-name="{{ $product->name }}">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                @elseif($product->status === 'active')
                                    {{-- For active products in My Products, show only View button --}}
                                    <div class="owner-notice" style="flex: 2; margin: 0; padding: 8px;">
                                        <i class="fa fa-check-circle me-1"></i> Active Listing
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-cube"></i>
                    </div>
                    <h3 class="empty-state-title">No Products Found</h3>
                    <p class="text-muted">You haven't created any products yet.</p>
                    <!-- Removed the "Add Your First Product" button -->
                </div>
            @endif
        </div>
        @endauth

        <!-- All Products Section -->
        <div class="mt-5">
            <div class="section-header">
                <h2 class="section-title">All Products</h2>
                <p class="text-center text-muted">Showing {{ $totalProducts }} active products in the marketplace</p>
            </div>

            @if($products->count() > 0)
                <div class="product-grid">
                    @foreach($products as $product)
                    <div class="product-card">
                        <div class="product-image-container">
                            @if($product->hasImages())
                                <!-- SIMPLIFIED: Show only one image with fallback -->
                                <img src="{{ $product->first_image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-image"
                                     onerror="handleImageError(this)">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa fa-cube fa-2x mb-2"></i>
                                    <small>No Image Available</small>
                                </div>
                            @endif
                            
                            {{-- Stock badge - simplified --}}
                            @if($product->stock > 0)
                                <span class="product-badge badge-right badge-in-stock">In Stock</span>
                            @else
                                <span class="product-badge badge-right badge-out-of-stock">Out of Stock</span>
                            @endif

                            {{-- Your Item badge - top left --}}
                            @if(Auth::check() && isset($product->user_id) && $product->user_id === Auth::id())
                                <span class="product-badge badge-left badge-owner">
                                    <i class="fa fa-user me-1"></i> Your Item
                                </span>
                            @endif

                            <a href="{{ route('customer.products.show', $product->id) }}" class="clickable-overlay"></a>
                        </div>

                        <div class="product-info">
                            <h3 class="product-title">{{ $product->name ?? '—' }}</h3>
                            
                            <div class="product-meta">
                                <div class="product-brand">
                                    <strong>Brand:</strong> {{ $product->brand ?? '—' }}
                                </div>
                                <div class="product-category">
                                    {{ $product->category ?? '—' }}
                                </div>
                                
                                <div class="product-price">
                                    <div class="price-php">₱{{ number_format($product->price, 2) }}</div>
                                </div>

                                <div class="product-stock">
                                    <i class="fa fa-box me-1"></i>
                                    @if($product->stock > 0)
                                        {{ $product->stock }} items available
                                    @else
                                        Out of stock
                                    @endif
                                </div>
                            </div>

                            @php
                                $isOwner = Auth::check() && isset($product->user_id) && $product->user_id === Auth::id();
                            @endphp

                            @if(Auth::check() && !$isOwner)
                                {{-- For other users: View and Chat buttons --}}
                                <div class="product-actions">
                                    <a href="{{ route('customer.products.show', $product->id) }}" class="btn-action btn-view">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('customer.chat.regular', $product->id) }}" class="btn-action btn-chat">
                                        <i class="fa fa-comments"></i> Chat
                                    </a>
                                </div>
                            @elseif(!Auth::check())
                                {{-- For non-logged in users --}}
                                <div class="product-actions">
                                    <a href="{{ route('customer.products.show', $product->id) }}" class="btn-action btn-view" style="flex: 1;">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="{{ url('/login') }}" class="btn-action btn-chat" style="flex: 1;">
                                        <i class="fa fa-comments"></i> Chat
                                    </a>
                                </div>
                            @else
                                {{-- For product owner in ALL PRODUCTS section: Show only owner notice --}}
                                <div class="owner-notice">
                                    <i class="fa fa-info-circle me-1"></i> This is your product listing
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($products->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
                @endif

            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">No Products Available</h3>
                    <p class="text-muted">
                        There are currently no active products available in the marketplace.
                        @auth
                            <br>Be the first to add a product!
                        @endauth
                    </p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Cancel Product Confirmation Modal -->
<div class="modal fade cancel-modal" id="cancelProductModal" tabindex="-1" aria-labelledby="cancelProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelProductModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i> Cancel Product
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this product?</p>
                <p><strong>Product:</strong> <span id="cancelProductName"></span></p>
                <p class="text-danger"><small>This action cannot be undone. The product will be removed from the marketplace.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                <button type="button" class="btn cancel-confirm-btn" id="confirmCancelBtn">
                    <i class="fa fa-times me-1"></i> Yes, Cancel Product
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Image error handler function
    function handleImageError(img) {
        img.onerror = null;
        img.src = '{{ asset("images/default-product.png") }}';
    }

    $(document).ready(function() {
        // Auto-close alerts after 5 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);

        // Product card hover effect
        $('.product-card').hover(
            function() {
                $(this).css('transform', 'translateY(-5px)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
            }
        );

        // Clickable overlay functionality
        $('.clickable-overlay').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            window.location.href = url;
        });

        // Cancel Product Functionality
        let currentProductId = null;

        $('.cancel-product-btn').on('click', function() {
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            
            currentProductId = productId;
            $('#cancelProductName').text(productName);
            $('#cancelProductModal').modal('show');
        });

        $('#confirmCancelBtn').on('click', function() {
            if (currentProductId) {
                cancelProduct(currentProductId);
            }
        });

        // Activation functionality
        $('.activate-product-btn').on('click', function() {
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            
            if (confirm(`Are you sure you want to activate "${productName}"? It will become visible to the public.`)) {
                activateProduct(productId);
            }
        });

        function cancelProduct(productId) {
            $.ajax({
                url: '/customer/products/' + productId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the product card from the DOM
                        $('#product-' + productId).fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Show success message
                        showAlert('Product cancelled successfully!', 'success');
                    } else {
                        showAlert('Error cancelling product: ' + response.message, 'danger');
                    }
                    $('#cancelProductModal').modal('hide');
                },
                error: function(xhr) {
                    let errorMessage = 'Error cancelling product. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'danger');
                    $('#cancelProductModal').modal('hide');
                }
            });
        }

        function activateProduct(productId) {
            $.ajax({
                url: '/customer/products/' + productId + '/activate',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        // Reload the page to show updated status
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert('Error: ' + response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error activating product. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'danger');
                }
            });
        }

        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('.container').prepend(alertHtml);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    });
</script>
@endsection