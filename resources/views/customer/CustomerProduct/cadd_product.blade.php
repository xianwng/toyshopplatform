<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product | Toyspace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --teal: #20c997;
            --indigo: #6610f2;
            --purple: #6f42c1;
            --pink: #e83e8c;
            --orange: #fd7e14;
            --yellow: #ffc107;
            
            /* New refined dark color scheme - 3 sophisticated dark tones */
            --dark-navy: #0d1b2a;
            --dark-navy-light: #1b263b;
            --dark-slate: #415a77;
            --dark-slate-light: #778da9;
            --dark-teal: #006d77;
            --dark-teal-light: #00838f;
        }

        /* Hero Section - Dark Navy & Slate Gradient */
        .hero-section {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            color: white;
            padding: 60px 0 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
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
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .product-form-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            margin-top: 30px;
            position: relative;
            z-index: 10;
        }

        .product-form-card .card-header {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            color: white;
            padding: 25px 20px 15px;
            border-bottom: none;
            position: relative;
        }

        .product-form-card .card-header h4 {
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .product-form-card .card-header p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
        }

        .search-result-item { 
            cursor: pointer; 
            border: 1px solid var(--gray-light);
            margin-bottom: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .search-result-item:hover { 
            background-color: var(--light); 
            border-color: var(--dark-navy);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(13, 27, 42, 0.1);
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark);
        }
        input[readonly], select[readonly] {
            background-color: var(--light);
            cursor: not-allowed;
        }
        .form-select {
            display: block;
            width: 100%;
        }
        .card-body {
            padding-top: 20px;
        }
        #condition {
            cursor: pointer;
        }
        .shipping-method {
            transform: scale(1.2);
        }
        /* Shipping method cards styling */
        .shipping-method-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent !important;
        }
        .shipping-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
            border-color: var(--dark-navy) !important;
        }
        .shipping-method:checked + label {
            color: var(--dark-navy);
        }
        .shipping-method:checked ~ small {
            color: var(--dark-navy);
        }
        /* Image preview styling */
        .image-preview-item {
            position: relative;
            width: 120px;
            height: 120px;
            border: 2px solid var(--gray-light);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .image-preview-item:hover {
            border-color: var(--dark-navy);
        }
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .image-preview-remove:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }
        /* Character count styling */
        #charCount {
            font-weight: bold;
        }
        #charCount.warning {
            color: var(--warning);
        }
        #charCount.danger {
            color: var(--danger);
        }

        .form-text {
            color: var(--gray);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(13, 27, 42, 0.3);
            background: linear-gradient(135deg, var(--dark-slate) 0%, var(--dark-navy) 100%);
        }

        .btn-primary:disabled {
            background: var(--gray);
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-outline-primary {
            border-color: var(--dark-navy);
            color: var(--dark-navy);
        }

        .btn-outline-primary:hover {
            background: var(--dark-navy);
            border-color: var(--dark-navy);
            color: white;
        }

        /* Section-specific colors */
        .border-primary { border-color: var(--dark-navy) !important; }
        .border-info { border-color: var(--dark-teal) !important; }
        .border-warning { border-color: var(--dark-slate) !important; }
        .border-secondary { border-color: var(--dark-teal) !important; }
        .border-success { border-color: var(--dark-slate) !important; }

        .bg-primary { background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-navy-light) 100%) !important; }
        .bg-info { background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-teal-light) 100%) !important; }
        .bg-warning { background: linear-gradient(135deg, var(--dark-slate) 0%, var(--dark-slate-light) 100%) !important; }
        .bg-secondary { background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-teal-light) 100%) !important; }
        .bg-success { background: linear-gradient(135deg, var(--dark-slate) 0%, var(--dark-slate-light) 100%) !important; }

        /* New styles for improved UI */
        .header-back-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        
        .header-back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-50%) translateX(-2px);
        }
        
        .section-icon {
            margin-right: 10px;
            font-size: 1.2em;
        }
        
        .form-section {
            margin-bottom: 25px;
        }
        
        .price-highlight {
            font-weight: bold;
            color: var(--dark-navy);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--dark-navy);
            box-shadow: 0 0 0 0.2rem rgba(13, 27, 42, 0.25);
        }
        
        .card-header h5 {
            display: flex;
            align-items: center;
        }
        
        .step-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: var(--dark-navy);
            color: white;
            border-radius: 50%;
            font-size: 0.8rem;
            margin-right: 8px;
        }
        
        .required-field::after {
            content: " *";
            color: var(--danger);
        }
        
        .shipping-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        /* Colorful badges for status */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-new {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            color: white;
        }
        
        .status-featured {
            background: linear-gradient(135deg, var(--dark-teal) 0%, var(--dark-teal-light) 100%);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.8rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .card-body {
                padding-top: 80px;
            }
            
            .shipping-method-card {
                margin-bottom: 15px;
            }
            
            .btn-primary {
                width: 100%;
            }
            
            .header-back-btn {
                position: relative;
                left: 0;
                top: 0;
                transform: none;
                margin-bottom: 15px;
                background: rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="container">
                <h1 class="hero-title">Add New Product</h1>
                <p class="hero-subtitle">List your collectible toys in our marketplace</p>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm product-form-card">
                    <div class="card-header text-center">
                        <!-- Back Button - Inside Header -->
                        <a href="{{ route('cproduct') }}" class="btn btn-sm header-back-btn">
                            <i class="fa fa-arrow-left"></i> Back to Products
                        </a>
                        <h4 class="mb-0"><i class="fa fa-plus-circle"></i> Product Listing Form</h4>
                        <p class="mb-0 mt-2">Complete all required information for product verification</p>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('customer.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                            @csrf

                            <!-- Product Search Section -->
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fa fa-search section-icon"></i> Find Product</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Category Dropdown - Label on top -->
                                        <div class="col-md-4 mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select name="search_category" id="category" class="form-select">
                                                <option value="">-- Select Category --</option>
                                                <option value="Action Figures">Action Figures</option>
                                                <option value="Dolls & Plushies">Dolls & Plushies</option>
                                                <option value="Building Sets">Building Sets</option>
                                                <option value="Vehicles">Vehicles</option>
                                                <option value="Board Games">Board Games</option>
                                                <option value="Educational">Educational Toys</option>
                                                <option value="Outdoor">Outdoor Toys</option>
                                                <option value="Collectibles">Collectibles</option>
                                                <option value="Electronic">Electronic Toys</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <!-- Brand Input - Label on top -->
                                        <div class="col-md-4 mb-3">
                                            <label for="brand" class="form-label">Brand</label>
                                            <input type="text" class="form-control" id="brand" name="search_brand" placeholder="Enter brand...">
                                        </div>

                                        <!-- Product Name Input - Label on top -->
                                        <div class="col-md-4 mb-3">
                                            <label for="productSearch" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="productSearch" placeholder="Type product name...">
                                        </div>
                                    </div>

                                    <!-- Search Results -->
                                    <div class="mt-3 d-none" id="searchResultsContainer">
                                        <h6 class="text-muted mb-2">Search Results:</h6>
                                        <div class="list-group" id="searchResults"></div>
                                    </div>

                                    <!-- Search Loading -->
                                    <div class="text-center d-none" id="searchLoading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Searching...</span>
                                        </div>
                                        <p class="mt-2">Searching products...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details Section -->
                            <div class="card mb-4 border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fa fa-edit section-icon"></i> Product Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Product Name Input - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label required-field">Product Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required readonly>
                                        </div>

                                        <!-- Brand Input - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="brand" class="form-label required-field">Brand</label>
                                            <input type="text" class="form-control" id="finalBrand" name="brand" value="{{ old('brand') }}" required readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Category Dropdown - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="finalCategory" class="form-label required-field">Category</label>
                                            <select name="category" id="finalCategory" class="form-select" required readonly>
                                                <option value="">-- Select Category --</option>
                                                <option value="Action Figures" {{ old('category') == 'Action Figures' ? 'selected' : '' }}>Action Figures</option>
                                                <option value="Dolls & Plushies" {{ old('category') == 'Dolls & Plushies' ? 'selected' : '' }}>Dolls & Plushies</option>
                                                <option value="Building Sets" {{ old('category') == 'Building Sets' ? 'selected' : '' }}>Building Sets</option>
                                                <option value="Vehicles" {{ old('category') == 'Vehicles' ? 'selected' : '' }}>Vehicles</option>
                                                <option value="Board Games" {{ old('category') == 'Board Games' ? 'selected' : '' }}>Board Games</option>
                                                <option value="Educational" {{ old('category') == 'Educational' ? 'selected' : '' }}>Educational Toys</option>
                                                <option value="Outdoor" {{ old('category') == 'Outdoor' ? 'selected' : '' }}>Outdoor Toys</option>
                                                <option value="Collectibles" {{ old('category') == 'Collectibles' ? 'selected' : '' }}>Collectibles</option>
                                                <option value="Electronic" {{ old('category') == 'Electronic' ? 'selected' : '' }}>Electronic Toys</option>
                                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <!-- Price Input - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="price" class="form-label required-field">Price (PHP)</label>
                                            <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                            <div class="form-text">
                                                <span id="priceInfo">Original price</span>
                                                <span id="discountInfo" class="d-none text-success"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Stock Input - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="stock" class="form-label required-field">Stock Quantity</label>
                                            <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', '') }}" min="1" required>
                                            <div class="form-text">Enter how many units of this product you have available</div>
                                        </div>

                                        <!-- Condition Dropdown - Label on top -->
                                        <div class="col-md-6 mb-3">
                                            <label for="condition" class="form-label required-field">Condition</label>
                                            <select class="form-select" id="condition" name="condition" required>
                                                <option value="sealed" {{ old('condition') == 'sealed' ? 'selected' : '' }}>Sealed (0% discount)</option>
                                                <option value="bib" {{ old('condition') == 'bib' ? 'selected' : '' }}>BIB - Box in Box (15% discount)</option>
                                                <option value="loose" {{ old('condition') == 'loose' ? 'selected' : '' }}>Loose - No Box (20% discount)</option>
                                            </select>
                                            <div class="form-text">Price will adjust automatically based on condition</div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- ASIN Input - Label on top -->
                                        <div class="col-12 mb-3">
                                            <label for="asin" class="form-label required-field">Amazon ASIN</label>
                                            <input type="text" class="form-control" id="asin" name="asin" value="{{ old('asin') }}" required readonly>
                                            <div class="form-text">Amazon Standard Identification Number</div>
                                        </div>
                                    </div>

                                    <!-- Hidden Fields -->
                                    <input type="hidden" id="originalPrice" value="0">

                                    <!-- Description Textarea - Label on top - ENFORCED -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label required-field">Product Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide a detailed description of your product including features, condition details, accessories included, and any notable characteristics. Minimum 10 characters." required minlength="10" maxlength="2000">{{ old('description') }}</textarea>
                                        <div class="form-text">
                                            <span id="charCount">0</span>/2000 characters. Provide detailed information to help buyers make informed decisions.
                                        </div>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Product Images Section -->
                            <div class="card mb-4 border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0"><i class="fa fa-images section-icon"></i> Product Images</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Multiple Image Upload - Label on top -->
                                    <div class="mb-3">
                                        <label for="product_images" class="form-label required-field">Product Images</label>
                                        <input type="file" class="form-control" id="product_images" name="product_images[]" multiple accept=".jpeg,.png,.jpg,.gif" required>
                                        <div class="form-text">
                                            <strong>Upload 1-6 product images from different angles.</strong><br>
                                            • Front view • Back view • Side views • Close-up details • Any imperfections<br>
                                            Accepted formats: JPEG, PNG, JPG, GIF. Maximum file size: 5MB per image.
                                        </div>
                                        @error('product_images')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        @error('product_images.*')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Image Preview Area -->
                                    <div class="mb-3">
                                        <label class="form-label">Image Preview</label>
                                        <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documentation Section -->
                            <div class="card mb-4 border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fa fa-file-alt section-icon"></i> Documentation & Verification</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Certificate Upload -->
                                    <div class="mb-4">
                                        <label for="certificate" class="form-label">Product Certificate / Authentication</label>
                                        <input type="file" class="form-control" id="certificate" name="certificate" accept=".pdf,.jpg,.png">
                                        <div class="form-text">
                                            <strong>Where to get certificates:</strong><br>
                                            • Manufacturer authentication services<br>
                                            • Professional grading companies (PSA, CGC, etc.)<br>
                                            • Certified appraisers<br>
                                            • Original purchase receipts or documentation<br>
                                            Formats: PDF, JPG, PNG. Maximum file size: 10MB.
                                        </div>
                                        @error('certificate')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Market Value Proof Upload -->
                                    <div class="mb-3">
                                        <label for="market_value_proof" class="form-label">Market Value Proof</label>
                                        <input type="file" class="form-control" id="market_value_proof" name="market_value_proof" accept=".pdf,.jpg,.png">
                                        <div class="form-text">
                                            <strong>How to acquire market value proof:</strong><br>
                                            • Screenshots of recent eBay sold listings for similar items<br>
                                            • PriceCharting.com historical data reports<br>
                                            • Recent auction house results<br>
                                            • Professional appraisal documents<br>
                                            • Online marketplace price comparisons<br>
                                            Formats: PDF, JPG, PNG. Maximum file size: 10MB.
                                        </div>
                                        @error('market_value_proof')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Information Section -->
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-truck section-icon"></i> Shipping Information</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Address Information -->
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3">Your Shipping Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" class="form-control" value="{{ Auth::user()->first_name . ' ' . (Auth::user()->middle_name ? Auth::user()->middle_name . ' ' : '') . Auth::user()->last_name }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" value="{{ Auth::user()->contact_number ?? 'Not provided' }}" readonly>
                                                <input type="hidden" name="contact_number" value="{{ Auth::user()->contact_number ?? '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Complete Address</label>
                                                <textarea class="form-control" rows="2" readonly>{{ Auth::user()->home_address ?? 'Not provided' }}</textarea>
                                                <input type="hidden" name="home_address" value="{{ Auth::user()->home_address ?? '' }}">
                                            </div>
                                        </div>
                                        @if(!Auth::user()->contact_number || !Auth::user()->home_address)
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i> 
                                                Please update your contact information in your profile to complete your shipping details.
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Shipping Methods -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-3">Your Choice of Shipping Method <span class="text-danger">*</span></h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="card h-100 border shadow-sm shipping-method-card">
                                                    <div class="card-body text-center p-3">
                                                        <div class="shipping-icon">
                                                            <i class="fa fa-motorcycle" style="color: var(--dark-navy);"></i>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input shipping-method" type="radio" name="shipping_method" value="lalamove" id="lalamove" {{ old('shipping_method') == 'lalamove' ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold fs-6" for="lalamove">
                                                                Lalamove
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Fast motorcycle delivery within the city</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card h-100 border shadow-sm shipping-method-card">
                                                    <div class="card-body text-center p-3">
                                                        <div class="shipping-icon">
                                                            <i class="fa fa-shipping-fast" style="color: var(--dark-teal);"></i>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input shipping-method" type="radio" name="shipping_method" value="lbc" id="lbc" {{ old('shipping_method') == 'lbc' ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold fs-6" for="lbc">
                                                                LBC
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Nationwide courier service</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card h-100 border shadow-sm shipping-method-card">
                                                    <div class="card-body text-center p-3">
                                                        <div class="shipping-icon">
                                                            <i class="fa fa-truck" style="color: var(--dark-slate);"></i>
                                                        </div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input shipping-method" type="radio" name="shipping_method" value="jnt" id="jnt" {{ old('shipping_method') == 'jnt' ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold fs-6" for="jnt">
                                                                J&T Express
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Fast and reliable nationwide delivery</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @error('shipping_method')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-2">Select one shipping method you're willing to use for this product</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fa fa-plus-circle"></i> Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let selectedProduct = null;
        let searchTimeout = null;

        // Global validation function
        function validateForm() {
            const selectedMethod = document.querySelector('.shipping-method:checked');
            const hasImages = document.getElementById('product_images').files.length > 0;
            const hasContactInfo = document.querySelector('input[name="contact_number"]').value && 
                                document.querySelector('input[name="home_address"]').value;
            const hasDescription = document.getElementById('description').value.length >= 10;
            const hasStock = document.getElementById('stock').value > 0;
            
            const isValid = selectedMethod !== null && hasImages && hasContactInfo && selectedProduct && hasDescription && hasStock;
            
            const submitBtn = document.getElementById('submitBtn');
            if (isValid) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
            return isValid;
        }

        // Global function to clear selection
        function clearSelection() {
            selectedProduct = null;
            
            // Clear all form fields
            document.getElementById('name').value = '';
            document.getElementById('finalBrand').value = '';
            document.getElementById('price').value = '';
            document.getElementById('stock').value = '';
            document.getElementById('asin').value = '';
            document.getElementById('finalCategory').value = '';
            document.getElementById('condition').value = 'sealed';
            document.getElementById('description').value = '';
            document.getElementById('originalPrice').value = '0';
            
            // Reset price info
            document.getElementById('priceInfo').textContent = 'Original price';
            document.getElementById('discountInfo').classList.add('d-none');
            
            // Make price field readonly again
            document.getElementById('price').setAttribute('readonly', true);
            
            // Disable submit button
            document.getElementById('submitBtn').disabled = true;
            
            // Reset character count
            document.getElementById('charCount').textContent = '0';
            document.getElementById('charCount').className = 'danger';
            
            document.getElementById('searchResultsContainer').classList.add('d-none');
        }

        // Global function to adjust price based on condition
        function adjustPriceBasedOnCondition() {
            const originalPrice = parseFloat(document.getElementById('originalPrice').value);
            const condition = document.getElementById('condition').value;
            const priceInput = document.getElementById('price');
            const priceInfo = document.getElementById('priceInfo');
            const discountInfo = document.getElementById('discountInfo');

            if (!originalPrice || originalPrice <= 0) {
                return;
            }

            const discountRates = {
                'sealed': 0.00,
                'bib': 0.15,
                'loose': 0.20
            };

            const discountRate = discountRates[condition] || 0.00;
            const adjustedPrice = originalPrice * (1 - discountRate);
            const discountAmount = originalPrice - adjustedPrice;

            // Update the price field
            priceInput.value = adjustedPrice.toFixed(2);

            // Update price info display
            if (discountRate > 0) {
                priceInfo.textContent = `Original: ₱${originalPrice.toFixed(2)}`;
                discountInfo.textContent = ` | Discount: -₱${discountAmount.toFixed(2)} (${(discountRate * 100)}%)`;
                discountInfo.classList.remove('d-none');
            } else {
                priceInfo.textContent = 'Original price';
                discountInfo.classList.add('d-none');
            }
        }

        // Global function to search products
        function searchProducts(searchQuery) {
            const category = document.getElementById('category').value;
            const brand = document.getElementById('brand').value.trim();

            const searchData = {
                keywords: searchQuery,
                category: category,
                brand: brand
            };

            // First, try the actual API
            fetch('{{ route("frontend.products.search-amazon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(searchData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('searchLoading').classList.add('d-none');
                
                if (data.success && data.results && data.results.length > 0) {
                    displaySearchResults(data.results);
                    document.getElementById('searchResultsContainer').classList.remove('d-none');
                } else {
                    // If API fails or no results, show demo data
                    showDemoResults(searchQuery, category, brand);
                }
            })
            .catch(error => {
                document.getElementById('searchLoading').classList.add('d-none');
                console.log('API search failed, showing demo results');
                showDemoResults(searchQuery, category, brand);
            });
        }

        // Global function to show demo results
        function showDemoResults(searchQuery, category, brand) {
            // Create demo product data based on search
            const demoProducts = [
                {
                    title: `${searchQuery} Toy - Premium Edition`,
                    brand: brand || 'Popular Brand',
                    php_price: 1400.00,
                    stock: 'In Stock',
                    asin: 'B0' + Math.floor(100000000 + Math.random() * 900000000),
                    category: category || 'Action Figures'
                },
                {
                    title: `${searchQuery} - Collector's Item`,
                    brand: brand || 'Collector Brand',
                    php_price: 2240.00,
                    stock: 'In Stock',
                    asin: 'B0' + Math.floor(100000000 + Math.random() * 900000000),
                    category: category || 'Collectibles'
                },
                {
                    title: `${searchQuery} - Standard Version`,
                    brand: brand || 'Standard Brand',
                    php_price: 840.00,
                    stock: 'In Stock',
                    asin: 'B0' + Math.floor(100000000 + Math.random() * 900000000),
                    category: category || 'Other'
                }
            ];

            displaySearchResults(demoProducts);
            document.getElementById('searchResultsContainer').classList.remove('d-none');
        }

        // Global function to display search results
        function displaySearchResults(results) {
            const container = document.getElementById('searchResults');
            container.innerHTML = '';
            
            if (results.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No products found. Try different search terms.</div>';
                return;
            }
            
            results.forEach((product, index) => {
                const item = document.createElement('div');
                item.className = 'list-group-item search-result-item';
                
                item.innerHTML = `
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="color: var(--dark-navy);">${product.title || 'Unknown'}</h6>
                            <p class="mb-1">
                                <strong class="text-dark">Brand:</strong> ${product.brand || 'Unknown'} | 
                                <strong class="text-dark">Price:</strong> ₱${(product.php_price || 0).toFixed(2)} | 
                                <strong class="text-dark">Stock:</strong> ${product.stock || 'Unknown'}
                            </p>
                            <small class="text-muted">ASIN: ${product.asin || 'N/A'} | Category: ${product.category || 'Other'}</small>
                        </div>
                        <button type="button" class="btn btn-sm" style="background: var(--dark-navy); color: white; border: none;" onclick="selectProduct(${index})">
                            <i class="fa fa-check"></i> Use
                        </button>
                    </div>
                `;
                item.dataset.product = JSON.stringify(product);
                container.appendChild(item);
            });
        }

        // Global function to select product
        function selectProduct(index) {
            const items = document.querySelectorAll('.search-result-item');
            const selectedItem = items[index];
            const productData = JSON.parse(selectedItem.dataset.product);
            
            // Store selected product globally
            selectedProduct = productData;
            
            // Store original price in hidden field
            const originalPrice = productData.php_price || 0;
            document.getElementById('originalPrice').value = originalPrice;
            
            // Fill form fields
            document.getElementById('name').value = productData.title || '';
            document.getElementById('finalBrand').value = productData.brand || '';
            // Stock field is left EMPTY for customer to fill in
            document.getElementById('stock').value = '';
            document.getElementById('asin').value = productData.asin || '';
            document.getElementById('finalCategory').value = productData.category || 'Other';
            
            // Set initial price based on default condition (sealed)
            document.getElementById('price').value = originalPrice.toFixed(2);
            document.getElementById('price').removeAttribute('readonly'); // Make price editable
            
            // Fill search fields for reference
            document.getElementById('productSearch').value = productData.title || '';
            document.getElementById('category').value = productData.category || '';
            document.getElementById('brand').value = productData.brand || '';
            
            // Update price info display
            document.getElementById('priceInfo').textContent = 'Original price';
            document.getElementById('discountInfo').classList.add('d-none');
            
            // Hide search results
            document.getElementById('searchResultsContainer').classList.add('d-none');
            
            // Enable submit button only if shipping method is selected
            validateForm();
            
            // Show success message
            showToast('Product details filled! Please enter your stock quantity and adjust price if needed.');
        }

        // Global function to show toast
        function showToast(message) {
            // Create a simple toast notification
            const toast = document.createElement('div');
            toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <i class="fa fa-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }

        // DOM Content Loaded event listener
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Product creation page loaded');
            
            const searchInput = document.getElementById('productSearch');
            const categorySelect = document.getElementById('category');
            const brandInput = document.getElementById('brand');
            const conditionSelect = document.getElementById('condition');
            const priceInput = document.getElementById('price');
            const stockInput = document.getElementById('stock');
            const imageInput = document.getElementById('product_images');
            const imagePreview = document.getElementById('imagePreview');
            const descriptionInput = document.getElementById('description');
            const charCount = document.getElementById('charCount');

            // Character count for description
            descriptionInput.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;
                
                if (length < 10) {
                    charCount.className = 'danger';
                } else if (length > 1800) {
                    charCount.className = 'warning';
                } else {
                    charCount.className = '';
                }
                
                validateForm();
            });

            // Stock validation
            stockInput.addEventListener('input', function() {
                validateForm();
            });

            // Image preview functionality
            imageInput.addEventListener('change', function() {
                imagePreview.innerHTML = '';
                const files = this.files;
                
                if (files.length > 6) {
                    alert('You can only upload up to 6 images.');
                    this.value = '';
                    return;
                }
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Preview ${i + 1}">
                                <button type="button" class="image-preview-remove" data-index="${i}">
                                    <i class="fa fa-times"></i>
                                </button>
                            `;
                            imagePreview.appendChild(previewItem);
                            
                            // Add remove functionality
                            previewItem.querySelector('.image-preview-remove').addEventListener('click', function() {
                                removeImageFromPreview(this.getAttribute('data-index'));
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                }
                
                validateForm();
            });

            function removeImageFromPreview(index) {
                // Create a new FileList without the removed file
                const dt = new DataTransfer();
                const files = imageInput.files;
                
                for (let i = 0; i < files.length; i++) {
                    if (i != index) {
                        dt.items.add(files[i]);
                    }
                }
                
                imageInput.files = dt.files;
                
                // Refresh preview
                const event = new Event('change');
                imageInput.dispatchEvent(event);
            }

            // Auto-search when typing in search input
            searchInput.addEventListener('input', function() {
                if (selectedProduct) return; // Don't search if product is already selected
                const query = this.value.trim();
                
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Hide previous results
                document.getElementById('searchResultsContainer').classList.add('d-none');
                
                if (query.length >= 2) {
                    document.getElementById('searchLoading').classList.remove('d-none');
                    searchTimeout = setTimeout(() => {
                        searchProducts(query);
                    }, 500);
                }
            });

            // Auto-search when category changes
            categorySelect.addEventListener('change', function() {
                if (selectedProduct) return; // Don't search if product is already selected
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    document.getElementById('searchLoading').classList.remove('d-none');
                    searchProducts(query);
                }
            });

            // Auto-search when brand changes
            brandInput.addEventListener('input', function() {
                if (selectedProduct) return; // Don't search if product is already selected
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    document.getElementById('searchLoading').classList.remove('d-none');
                    searchProducts(query);
                }
            });

            // Clear selection when user starts typing again
            searchInput.addEventListener('focus', function() {
                if (selectedProduct && this.value !== selectedProduct.title) {
                    clearSelection();
                }
            });

            categorySelect.addEventListener('change', function() {
                if (selectedProduct && this.value !== selectedProduct.category) {
                    clearSelection();
                }
            });

            brandInput.addEventListener('input', function() {
                if (selectedProduct && this.value !== selectedProduct.brand) {
                    clearSelection();
                }
            });

            // Handle condition change for price adjustment
            conditionSelect.addEventListener('change', function() {
                adjustPriceBasedOnCondition();
            });

            // Make price field editable after selection
            priceInput.addEventListener('focus', function() {
                if (selectedProduct) {
                    this.removeAttribute('readonly');
                }
            });

            // Shipping method validation
            const shippingMethods = document.querySelectorAll('.shipping-method');
            const submitBtn = document.getElementById('submitBtn');
            
            // Add event listeners to shipping method radio buttons
            shippingMethods.forEach(method => {
                method.addEventListener('change', validateForm);
            });

            // Add click event to shipping method cards for better UX
            document.querySelectorAll('.shipping-method-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.matches('input[type="radio"]')) {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                            validateForm();
                        }
                    }
                });
            });

            // Initialize character count
            charCount.textContent = descriptionInput.value.length;
            if (descriptionInput.value.length < 10) {
                charCount.className = 'danger';
            }

            // Form submission with detailed logging
            document.getElementById('productForm').addEventListener('submit', function(e) {
                console.log('=== FORM SUBMISSION DEBUG ===');
                
                // Check shipping method
                const shippingMethod = document.querySelector('input[name="shipping_method"]:checked');
                console.log('Shipping method selected:', shippingMethod ? shippingMethod.value : 'NONE');
                
                // Check images
                const imageCount = imageInput.files.length;
                console.log('Images selected:', imageCount);
                
                // Check description
                const descriptionLength = descriptionInput.value.length;
                console.log('Description length:', descriptionLength);
                
                // Check stock
                const stockValue = stockInput.value;
                console.log('Stock quantity:', stockValue);
                
                if (!shippingMethod) {
                    console.log('❌ NO shipping method selected - showing alert');
                    e.preventDefault();
                    alert('Please select a shipping method.');
                    return false;
                }

                if (imageCount === 0) {
                    console.log('❌ NO images selected - showing alert');
                    e.preventDefault();
                    alert('Please upload at least one product image.');
                    return false;
                }

                if (descriptionLength < 10) {
                    console.log('❌ Description too short - showing alert');
                    e.preventDefault();
                    alert('Please provide a detailed description (at least 10 characters).');
                    return false;
                }

                if (!stockValue || stockValue < 1) {
                    console.log('❌ Invalid stock quantity - showing alert');
                    e.preventDefault();
                    alert('Please enter a valid stock quantity (minimum 1).');
                    return false;
                }

                // Check if user has contact information
                const contactNumber = document.querySelector('input[name="contact_number"]').value;
                const homeAddress = document.querySelector('input[name="home_address"]').value;
                
                console.log('Contact number:', contactNumber);
                console.log('Home address:', homeAddress);
                
                if (!contactNumber || !homeAddress) {
                    console.log('❌ Missing contact information');
                    e.preventDefault();
                    alert('Please update your contact information in your profile before adding products.');
                    return false;
                }
                
                console.log('✅ Form validation passed - submitting form');
                console.log('=== END DEBUG ===');
            });
        });
    </script>
</body>
</html>