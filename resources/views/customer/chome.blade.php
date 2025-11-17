@extends('customer.layouts.cmaster')

@section('title', 'Home | Toyspace')

@section('styles')
<style>
    :root {
        --primary: #667eea;
        --primary-dark: #5a6fd8;
        --secondary: #764ba2;
        --accent: #ff6b6b;
        --light: #f8f9fa;
        --dark: #343a40;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --gray: #6c757d;
        --gray-light: #e9ecef;
    }

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
    
    .hero-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-hero {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom {
        background: white;
        color: var(--primary);
        border: none;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .btn-outline-light-custom {
        border: 2px solid white;
        color: white;
        background: transparent;
    }
    
    .btn-outline-light-custom:hover {
        background: white;
        color: var(--primary);
    }

    /* Features Section */
    .features-section {
        padding: 80px 0;
        background: var(--light);
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 50px;
        font-weight: 700;
        color: var(--dark);
        position: relative;
    }
    
    .section-title::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--primary);
        margin: 15px auto;
        border-radius: 2px;
    }
    
    .feature-card {
        background: white;
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--gray-light);
        height: 100%;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 2rem;
    }

    /* Categories Section */
    .categories-section {
        padding: 80px 0;
    }
    
    .category-card {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        height: 200px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
    }
    
    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .category-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 20px;
        color: white;
    }
    
    .category-title {
        font-weight: 600;
        margin: 0;
        font-size: 1.2rem;
    }

    /* Products Section */
    .products-section {
        padding: 80px 0;
        background: var(--light);
    }
    
    .product-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 30px;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: var(--gray-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--accent);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 2;
    }
    
    .product-info {
        padding: 20px;
    }
    
    .product-title {
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 1.1rem;
        color: var(--dark);
    }
    
    .product-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    .product-meta {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 15px;
    }
    
    .product-actions {
        display: flex;
        justify-content: space-between;
    }
    
    .btn-add-cart {
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 5px;
        padding: 8px 15px;
        font-size: 0.9rem;
        transition: background 0.3s ease;
        flex-grow: 1;
        margin-right: 10px;
    }
    
    .btn-add-cart:hover {
        background: var(--primary-dark);
    }
    
    .btn-wishlist {
        background: transparent;
        border: 1px solid var(--gray-light);
        border-radius: 5px;
        padding: 8px 12px;
        color: var(--gray);
        transition: all 0.3s ease;
    }
    
    .btn-wishlist:hover {
        color: var(--accent);
        border-color: var(--accent);
    }

    /* Testimonials Section */
    .testimonials-section {
        padding: 80px 0;
    }
    
    .testimonial-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        border: 1px solid var(--gray-light);
    }
    
    .testimonial-text {
        font-style: italic;
        margin-bottom: 20px;
        color: var(--gray);
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
    }
    
    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }
    
    .author-name {
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .author-role {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 0;
    }

    /* Newsletter Section */
    .newsletter-section {
        padding: 80px 0;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        text-align: center;
    }
    
    .newsletter-title {
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .newsletter-form {
        max-width: 500px;
        margin: 0 auto;
    }
    
    .form-control-newsletter {
        border-radius: 50px;
        padding: 12px 20px;
        border: none;
        margin-right: 10px;
    }
    
    .btn-newsletter {
        border-radius: 50px;
        padding: 12px 30px;
        background: var(--accent);
        color: white;
        border: none;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    
    .btn-newsletter:hover {
        background: #ff5252;
    }

    /* Login Success */
    .login-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 10px;
        padding: 15px;
        margin: 20px 0;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-hero {
            width: 100%;
            max-width: 250px;
        }
    }
</style>
@endsection

@section('content')
<!-- Success Message -->
@if(auth()->check() && session('login_success'))
<div class="container mt-4">
    <div class="login-success">
        <h4>🎉 Login Successful!</h4>
        <p>Welcome to Toyspace! You have successfully logged in.</p>
    </div>
</div>
@endif

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title">Discover Amazing Toys & Collectibles</h1>
            <p class="hero-subtitle">Find the perfect toys for kids and collectors alike. From action figures to educational toys, we have something for everyone.</p>
            <div class="hero-buttons">
                <a href="/home" class="btn btn-hero btn-primary-custom">Shop Now</a>
                <a href="#featured" class="btn btn-hero btn-outline-light-custom">Explore Collections</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Why Choose Toyspace?</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-cube"></i>
                    </div>
                    <h4>3D Product Views</h4>
                    <p>Experience our products in stunning 3D. Rotate, zoom, and explore every detail before you buy.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-gavel"></i>
                    </div>
                    <h4>Live Auctions</h4>
                    <p>Participate in exciting auctions for rare and collectible items. Bid and win amazing deals.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-exchange"></i>
                    </div>
                    <h4>Trade System</h4>
                    <p>Trade your toys with other collectors. Find perfect matches for your collection.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Shop By Category</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="category-card">
                    <div class="category-image" style="background: #ff9f43;"></div>
                    <div class="category-overlay">
                        <h3 class="category-title">Action Figures</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card">
                    <div class="category-image" style="background: #54a0ff;"></div>
                    <div class="category-overlay">
                        <h3 class="category-title">Building Sets</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card">
                    <div class="category-image" style="background: #ee5253;"></div>
                    <div class="category-overlay">
                        <h3 class="category-title">Educational Toys</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card">
                    <div class="category-image" style="background: #10ac84;"></div>
                    <div class="category-overlay">
                        <h3 class="category-title">Collectibles</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section" id="featured">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        
        @if(isset($products) && $products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <div class="model-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                <i class="fa fa-cube fa-3x text-muted mb-2"></i>
                                <small class="text-muted">{{ $product->category ?? 'Product' }}</small>
                            </div>
                            <div class="product-badge">2D</div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">{{ Str::limit($product->name, 40) }}</h3>
                            <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                            <div class="product-meta">
                                <div><strong>Brand:</strong> {{ $product->brand ?? 'N/A' }}</div>
                                <div><strong>Stock:</strong> {{ $product->stock ?? 0 }} available</div>
                            </div>
                            <div class="product-actions">
                                <button class="btn-add-cart add-to-cart" data-product-id="{{ $product->id }}">
                                    <i class="fa fa-shopping-cart mr-1"></i> Add to Cart
                                </button>
                                <button class="btn-wishlist">
                                    <i class="fa fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="/home" class="btn btn-primary btn-lg">View All Products</a>
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <h4>No products found</h4>
                        <p>Sorry, no products are available at the moment. Please check back later.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title">What Our Customers Say</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"Toyspace has the best collection of action figures I've ever seen. The 3D view feature helped me make the right choice!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">JD</div>
                        <div>
                            <h4 class="author-name">John Doe</h4>
                            <p class="author-role">Collector</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"My kids love the educational toys from Toyspace. The quality is exceptional and they learn while having fun!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: #764ba2; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">SJ</div>
                        <div>
                            <h4 class="author-name">Sarah Johnson</h4>
                            <p class="author-role">Parent</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <p class="testimonial-text">"The trading system is fantastic! I was able to trade my duplicate collectibles for ones I needed to complete my set."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: #ff6b6b; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">MR</div>
                        <div>
                            <h4 class="author-name">Mike Roberts</h4>
                            <p class="author-role">Toy Enthusiast</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <h2 class="newsletter-title">Stay Updated</h2>
        <p class="mb-4">Subscribe to our newsletter for the latest toy releases, exclusive deals, and collector tips.</p>
        <form class="newsletter-form d-flex">
            <input type="email" class="form-control form-control-newsletter" placeholder="Your email address" required>
            <button type="submit" class="btn btn-newsletter">Subscribe</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h4 class="font-weight-bold mb-3">Toyspace</h4>
                <p>Your ultimate destination for amazing toys and collectibles. We bring joy to kids and collectors alike.</p>
                <div class="social-links mt-3">
                    <a href="#" class="text-white mr-3"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="text-white mr-3"><i class="fa fa-twitter"></i></a>
                    <a href="#" class="text-white mr-3"><i class="fa fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fa fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="font-weight-bold mb-3">Shop</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50">All Products</a></li>
                    <li><a href="#" class="text-white-50">New Arrivals</a></li>
                    <li><a href="#" class="text-white-50">Featured</a></li>
                    <li><a href="#" class="text-white-50">On Sale</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="font-weight-bold mb-3">Help</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50">Customer Service</a></li>
                    <li><a href="#" class="text-white-50">Shipping Info</a></li>
                    <li><a href="#" class="text-white-50">Returns</a></li>
                    <li><a href="#" class="text-white-50">Size Guide</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="font-weight-bold mb-3">Contact Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa fa-map-marker mr-2"></i> 123 Toy Street, Manila, Philippines</li>
                    <li class="mb-2"><i class="fa fa-phone mr-2"></i> +63 912 345 6789</li>
                    <li class="mb-2"><i class="fa fa-envelope mr-2"></i> info@toyspace.com</li>
                </ul>
            </div>
        </div>
        <hr class="bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">&copy; 2025 Toyspace, All Rights Reserved</p>
            </div>
            <div class="col-md-6 text-md-right">
                <div class="payment-methods">
                    <span class="mr-2">We accept:</span>
                    <i class="fa fa-cc-visa mr-1 text-white"></i>
                    <i class="fa fa-cc-mastercard mr-1 text-white"></i>
                    <i class="fa fa-cc-paypal mr-1 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</footer>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.add-to-cart').click(function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            
            // Show a more attractive notification
            const notification = $(`
                <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong> Product added to cart.
                </div>
            `);
            
            $('body').append(notification);
            
            // Remove notification after 3 seconds
            setTimeout(function() {
                notification.alert('close');
            }, 3000);
        });
        
        // Wishlist button functionality
        $('.btn-wishlist').click(function() {
            const $btn = $(this);
            const isActive = $btn.hasClass('active');
            
            if (isActive) {
                $btn.removeClass('active');
                $btn.html('<i class="fa fa-heart"></i>');
            } else {
                $btn.addClass('active');
                $btn.html('<i class="fa fa-heart text-danger"></i>');
                
                // Show notification
                const notification = $(`
                    <div class="alert alert-info alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Added to wishlist!</strong>
                    </div>
                `);
                
                $('body').append(notification);
                
                // Remove notification after 3 seconds
                setTimeout(function() {
                    notification.alert('close');
                }, 3000);
            }
        });
    });
</script>
@endsection