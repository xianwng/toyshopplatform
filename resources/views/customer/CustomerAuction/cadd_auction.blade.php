<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Auction | Toyspace</title>
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

        .auction-form-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            margin-top: 30px;
            position: relative;
            z-index: 10;
        }

        .auction-form-card .card-header {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            color: white;
            padding: 25px 20px 15px;
            border-bottom: none;
            position: relative;
        }

        .auction-form-card .card-header h4 {
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .auction-form-card .card-header p {
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

        /* Auction specific styles */
        .current-user-badge {
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--dark-slate) 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(13, 27, 42, 0.3);
        }

        .diamond-balance {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .currency-input .input-group-text {
            background: transparent;
            color: var(--dark);
            border: 2px solid #e2e8f0;
            border-right: none;
            font-weight: 600;
            padding: 14px 18px;
            font-size: 1.1rem;
        }

        .currency-input .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .diamond-input .input-group-text {
            background: transparent;
            color: var(--dark);
            border: 2px solid #e2e8f0;
            border-right: none;
            font-weight: 600;
            padding: 14px 18px;
            font-size: 1.1rem;
        }

        .diamond-input .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group {
            border-radius: 12px;
        }

        .input-group .form-control:first-child {
            border-radius: 12px 0 0 12px;
        }

        .input-group .input-group-text:first-child {
            border-radius: 12px 0 0 12px;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--dark-navy);
        }

        .terms-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            border-left: 4px solid var(--dark-navy);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .terms-content {
            max-height: 250px;
            overflow-y: auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .terms-content h6 {
            color: var(--dark-navy);
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .terms-content p {
            color: var(--dark);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .file-preview-container {
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            background: #f8fafc;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .file-preview-container:hover {
            border-color: var(--dark-navy);
        }

        .reference-links {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 25px;
            margin-top: 15px;
            border: 1px solid #e2e8f0;
        }

        .reference-links textarea {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .reference-links textarea:focus {
            border-color: var(--dark-navy);
            box-shadow: 0 0 0 3px rgba(13, 27, 42, 0.1);
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(135deg, var(--dark-navy) 0%, transparent 100%);
            margin: 2.5rem 0;
            opacity: 0.3;
        }

        /* Price calculation info */
        .price-calculation {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 8px;
            font-size: 0.8rem;
        }

        .price-calculation .calculation-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .price-calculation .calculation-item:last-child {
            margin-bottom: 0;
            font-weight: 600;
            border-top: 1px dashed #bae6fd;
            padding-top: 4px;
        }

        /* Date Validation Styles */
        .date-validation-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #0369a1;
        }

        .date-validation-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #92400e;
        }

        .date-validation-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 1px solid #ef4444;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #dc2626;
        }

        .date-validation-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #065f46;
        }

        .validation-icon {
            margin-right: 8px;
            font-size: 1rem;
        }

        .countdown-timer {
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 5px;
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
                <h1 class="hero-title">Create New Auction</h1>
                <p class="hero-subtitle">List your collectible items and start competitive bidding</p>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm auction-form-card">
                    <div class="card-header text-center">
                        <!-- Back Button - Inside Header -->
                        <a href="{{ route('customer.auctions.index') }}" class="btn btn-sm header-back-btn">
                            <i class="fa fa-arrow-left"></i> Back to Auctions
                        </a>
                        <h4 class="mb-0"><i class="fa fa-gavel"></i> Auction Creation Form</h4>
                        <p class="mb-0 mt-2">Complete all required information for auction verification</p>
                    </div>
                    <div class="card-body">
                        <!-- Current User Display -->
                        @auth
                        <div class="text-center mb-4">
                            <div class="current-user-badge">
                                <i class="fa fa-user-circle"></i>
                                Creating auction as: {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </div>
                            <div class="diamond-balance">
                                <i class="fa fa-gem"></i>
                                Available Diamonds: 💎{{ number_format(Auth::user()->diamond_balance, 0) }}
                            </div>
                        </div>
                        @endauth

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

                        <form action="{{ route('customer.auctions.store') }}" method="POST" enctype="multipart/form-data" id="auctionForm">
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
                                            <label for="search_category" class="form-label">Category</label>
                                            <select name="search_category" id="search_category" class="form-select">
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
                                            <label for="search_brand" class="form-label">Brand</label>
                                            <input type="text" class="form-control" id="search_brand" name="search_brand" placeholder="Enter brand...">
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

                            <!-- Product Information Section -->
                            <div class="card mb-4 border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fa fa-cube section-icon"></i> Product Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label required-field">Category</label>
                                            <select name="category" id="category" class="form-select" required readonly>
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

                                        <div class="col-md-6 mb-3">
                                            <label for="product_name" class="form-label required-field">Product Name</label>
                                            <input type="text" class="form-control" id="product_name" name="product_name" 
                                                   value="{{ old('product_name') }}" placeholder="Enter product name" required readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="brand" class="form-label required-field">Brand</label>
                                            <input type="text" class="form-control" id="brand" name="brand" 
                                                   value="{{ old('brand', 'Unknown') }}" placeholder="Enter brand name" required readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="condition" class="form-label required-field">Condition</label>
                                            <select class="form-select" id="condition" name="condition" required>
                                                <option value="">Select Condition</option>
                                                <option value="sealed" {{ old('condition') == 'sealed' ? 'selected' : '' }}>Sealed</option>
                                                <option value="back in box" {{ old('condition') == 'back in box' ? 'selected' : '' }}>Back in Box</option>
                                                <option value="loose" {{ old('condition') == 'loose' ? 'selected' : '' }}>Loose</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3 currency-input">
                                        <label for="minimum_market_value" class="form-label required-field">Minimum Market Value</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" step="0.01" min="1" class="form-control" 
                                                   id="minimum_market_value" name="minimum_market_value" 
                                                   value="{{ old('minimum_market_value') }}" 
                                                   placeholder="Based on market research" required>
                                        </div>
                                        <div class="form-text">Documented minimum market value in Philippine Pesos</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Product Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                                  placeholder="Enter detailed product description (optional)">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Auction Settings Section -->
                            <div class="card mb-4 border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0"><i class="fa fa-chart-line section-icon"></i> Auction Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="diamond-input">
                                                <label for="starting_price" class="form-label required-field">Starting Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">💎</span>
                                                    <input type="number" step="1" min="1" class="form-control" 
                                                           id="starting_price" name="starting_price" value="{{ old('starting_price') }}" 
                                                           placeholder="Enter starting price" required>
                                                </div>
                                                <div class="price-calculation">
                                                    <div class="calculation-item">
                                                        <span>Market Value:</span>
                                                        <span id="marketValueDisplay">₱0.00</span>
                                                    </div>
                                                    <div class="calculation-item">
                                                        <span>80% Discount:</span>
                                                        <span id="discountDisplay">-₱0.00</span>
                                                    </div>
                                                    <div class="calculation-item">
                                                        <span>Starting Price:</span>
                                                        <span id="calculatedStartingPrice">💎0</span>
                                                    </div>
                                                </div>
                                                <div class="form-text">Automatically calculated as 20% of market value</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="diamond-input">
                                                <label for="buyout_bid" class="form-label">Buyout Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">💎</span>
                                                    <input type="number" step="1" min="1" class="form-control" 
                                                           id="buyout_bid" name="buyout_bid" value="{{ old('buyout_bid') }}" 
                                                           placeholder="Optional buyout price">
                                                </div>
                                                <div class="price-calculation">
                                                    <div class="calculation-item">
                                                        <span>Market Value:</span>
                                                        <span id="marketValueDisplay2">₱0.00</span>
                                                    </div>
                                                    <div class="calculation-item">
                                                        <span>20% Premium:</span>
                                                        <span id="premiumDisplay">+₱0.00</span>
                                                    </div>
                                                    <div class="calculation-item">
                                                        <span>Buyout Price:</span>
                                                        <span id="calculatedBuyoutPrice">💎0</span>
                                                    </div>
                                                </div>
                                                <div class="form-text">Automatically calculated as 120% of market value</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="end_time" class="form-label required-field">Auction End Date & Time</label>
                                        @php
                                            // Set default end time to 5 days from now
                                            $defaultEndTime = now()->addDays(5)->format('Y-m-d\TH:i');
                                            // Set minimum time to 5 days from now
                                            $minTime = now()->addDays(5)->format('Y-m-d\TH:i');
                                        @endphp
                                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                                               value="{{ old('end_time', $defaultEndTime) }}" 
                                               min="{{ $minTime }}" required>
                                        
                                        <!-- Date Validation Notification -->
                                        <div id="dateValidation" class="date-validation-info">
                                            <i class="fas fa-info-circle validation-icon"></i>
                                            <strong>Auction Duration Requirement:</strong> Minimum 5 days required
                                            <div class="countdown-timer" id="countdownTimer">
                                                Selected duration: <span id="durationDisplay">5 days</span>
                                            </div>
                                        </div>
                                        <div class="form-text">Auction must run for at least 5 days. You will be notified when the auction ends.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media & Documentation Section -->
                            <div class="card mb-4 border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fa fa-images section-icon"></i> Media & Documentation</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Product Image Upload - Multiple Images -->
                                    <div class="mb-4">
                                        <label class="form-label required-field">Product Images</label>
                                        <input type="file" class="form-control" id="product_img" name="product_img[]" multiple accept=".jpeg,.png,.jpg,.gif,.webp" required>
                                        <div class="form-text">
                                            <strong>Upload 1-6 product images from different angles.</strong><br>
                                            • Front view • Back view • Side views • Close-up details • Any imperfections<br>
                                            Accepted formats: JPEG, PNG, JPG, GIF, WEBP. Maximum file size: 10MB per image.
                                        </div>
                                        @error('product_img')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        @error('product_img.*')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Image Preview Area -->
                                    <div class="mb-4">
                                        <label class="form-label">Image Preview</label>
                                        <div id="imagePreview" class="d-flex flex-wrap gap-3 mt-2"></div>
                                    </div>

                                    <!-- Owner Proof Upload -->
                                    <div class="mb-4">
                                        <label for="owner_proof" class="form-label required-field">Proof of Ownership</label>
                                        <input type="file" class="form-control" id="owner_proof" name="owner_proof" accept=".jpeg,.jpg,.png,.pdf" required>
                                        <div class="form-text">
                                            <strong>Where to get ownership proof:</strong><br>
                                            • Original purchase receipts<br>
                                            • Certificate of authenticity<br>
                                            • Registration documents<br>
                                            • Previous transaction records<br>
                                            Formats: JPEG, JPG, PNG, PDF. Maximum file size: 5MB.
                                        </div>
                                        @error('owner_proof')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Market Value Proof Upload -->
                                    <div class="mb-4">
                                        <label for="market_value_proof" class="form-label required-field">Market Value Proof</label>
                                        <input type="file" class="form-control" id="market_value_proof" name="market_value_proof" accept=".jpeg,.jpg,.png,.pdf" required>
                                        <div class="form-text">
                                            <strong>How to acquire market value proof:</strong><br>
                                            • Screenshots of recent eBay sold listings for similar items<br>
                                            • PriceCharting.com historical data reports<br>
                                            • Recent auction house results<br>
                                            • Professional appraisal documents<br>
                                            • Online marketplace price comparisons<br>
                                            Formats: JPEG, JPG, PNG, PDF. Maximum file size: 5MB.
                                        </div>
                                        @error('market_value_proof')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Reference Links Section -->
                                    <div class="mb-3">
                                        <label class="form-label">Additional Reference Links (Optional)</label>
                                        <div class="reference-links">
                                            <textarea class="form-control" id="reference_links" name="reference_links" rows="3" 
                                                      placeholder="Paste your reference links here (one per line). Examples:&#10;https://ebay.com/item/123&#10;https://amazon.com/product/456&#10;https://priceguide.example.com">{{ old('reference_links') }}</textarea>
                                        </div>
                                        <div class="form-text">Add URLs to additional proof, market research, or similar item listings (e.g., eBay, Amazon, etc.). Put one link per line.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-file-contract section-icon"></i> Auction Terms & Conditions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="terms-content">
                                        <h6>1. Item Authenticity & Ownership</h6>
                                        <p>You certify that the item listed is authentic and you are the legal owner. Providing false documentation may result in account suspension.</p>
                                        
                                        <h6>2. Market Value Verification</h6>
                                        <p>The minimum market value must be supported by recent sales data or reputable price guides. Inflated values may lead to auction rejection.</p>
                                        
                                        <h6>3. Delivery Responsibility</h6>
                                        <p>As the seller, you are responsible for delivering the item as described. Failure to deliver may result in diamond penalties.</p>
                                        
                                        <h6>4. Verification Process</h6>
                                        <p>All auctions undergo admin verification. Approval may take 24-48 hours. You will be notified of the status.</p>
                                        
                                        <h6>5. Prohibited Items</h6>
                                        <p>Counterfeit items, illegal goods, or items violating intellectual property rights are strictly prohibited.</p>

                                        <h6>6. 12-Hour Seller Response Requirement</h6>
                                        <p>After auction ends, you must respond to the winner within 12 hours. Failure to respond will result in automatic refund to the buyer.</p>

                                        <h6>7. Automatic Price Calculation</h6>
                                        <p>Starting price is automatically set at 20% of market value. Buyout price is automatically set at 120% of market value.</p>

                                        <h6>8. Minimum Auction Duration</h6>
                                        <p>All auctions must run for a minimum of 5 days. You will receive notifications when your auction is about to end.</p>
                                    </div>
                                    
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required>
                                        <label class="form-check-label" for="terms_accepted">
                                            I have read and agree to the Auction Terms & Conditions
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('customer.auctions.index') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-arrow-left"></i> Back to Auctions
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fa fa-plus-circle"></i> Create Auction
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

        // Global function to clear selection
        function clearSelection() {
            selectedProduct = null;
            
            // Clear all form fields
            document.getElementById('product_name').value = '';
            document.getElementById('brand').value = '';
            document.getElementById('category').value = '';
            document.getElementById('minimum_market_value').value = '';
            document.getElementById('description').value = '';
            
            // Make fields readonly again
            document.getElementById('product_name').setAttribute('readonly', true);
            document.getElementById('brand').setAttribute('readonly', true);
            document.getElementById('category').setAttribute('readonly', true);
            
            document.getElementById('searchResultsContainer').classList.add('d-none');
            
            // Clear price calculations
            updatePriceCalculations(0);
        }

        // Global function to update price calculations
        function updatePriceCalculations(marketValue) {
            // Convert PHP to Diamonds (assuming 1 PHP = 1 Diamond for simplicity)
            // You can adjust this conversion rate as needed
            const phpToDiamondRate = 1;
            
            const marketValueInDiamonds = Math.round(marketValue * phpToDiamondRate);
            const startingPrice = Math.round(marketValueInDiamonds * 0.2); // 20% of market value
            const buyoutPrice = Math.round(marketValueInDiamonds * 1.2); // 120% of market value
            const discountAmount = marketValueInDiamonds - startingPrice;
            const premiumAmount = buyoutPrice - marketValueInDiamonds;

            // Update displays
            document.getElementById('marketValueDisplay').textContent = '₱' + marketValue.toFixed(2);
            document.getElementById('marketValueDisplay2').textContent = '₱' + marketValue.toFixed(2);
            document.getElementById('discountDisplay').textContent = '-₱' + discountAmount.toFixed(2);
            document.getElementById('premiumDisplay').textContent = '+₱' + premiumAmount.toFixed(2);
            document.getElementById('calculatedStartingPrice').textContent = '💎' + startingPrice;
            document.getElementById('calculatedBuyoutPrice').textContent = '💎' + buyoutPrice;

            // Update form fields
            document.getElementById('starting_price').value = startingPrice;
            document.getElementById('buyout_bid').value = buyoutPrice;
        }

        // Global function to search products
        function searchProducts(searchQuery) {
            const category = document.getElementById('search_category').value;
            const brand = document.getElementById('search_brand').value.trim();

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
                                <strong class="text-dark">Category:</strong> ${product.category || 'Other'}
                            </p>
                            <small class="text-muted">ASIN: ${product.asin || 'N/A'}</small>
                        </div>
                        <button type="button" class="btn btn-sm" style="background: var(--dark-navy); color: white; border: none;" onclick="window.selectProduct(${index})">
                            <i class="fa fa-check"></i> Use
                        </button>
                    </div>
                `;
                item.dataset.product = JSON.stringify(product);
                container.appendChild(item);
            });
        }

        // Global function to select product - FIXED: Made it available globally
        window.selectProduct = function(index) {
            const items = document.querySelectorAll('.search-result-item');
            const selectedItem = items[index];
            const productData = JSON.parse(selectedItem.dataset.product);
            
            // Store selected product globally
            selectedProduct = productData;
            
            // Fill form fields
            document.getElementById('product_name').value = productData.title || '';
            document.getElementById('brand').value = productData.brand || '';
            document.getElementById('category').value = productData.category || 'Other';
            
            // Set minimum market value based on product price
            const marketValue = productData.php_price || 0;
            document.getElementById('minimum_market_value').value = marketValue.toFixed(2);
            
            // Update price calculations
            updatePriceCalculations(marketValue);
            
            // Fill search fields for reference
            document.getElementById('productSearch').value = productData.title || '';
            document.getElementById('search_category').value = productData.category || '';
            document.getElementById('search_brand').value = productData.brand || '';
            
            // Make form fields editable
            document.getElementById('product_name').removeAttribute('readonly');
            document.getElementById('brand').removeAttribute('readonly');
            document.getElementById('category').removeAttribute('readonly');
            
            // Hide search results
            document.getElementById('searchResultsContainer').classList.add('d-none');
            
            // Show success message
            showToast('Product details filled! Prices automatically calculated.');
        };

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
            console.log('Auction creation page loaded');
            
            const searchInput = document.getElementById('productSearch');
            const categorySelect = document.getElementById('search_category');
            const brandInput = document.getElementById('search_brand');
            const imageInput = document.getElementById('product_img');
            const imagePreview = document.getElementById('imagePreview');
            const marketValueInput = document.getElementById('minimum_market_value');
            const endTimeInput = document.getElementById('end_time');
            const dateValidation = document.getElementById('dateValidation');
            const durationDisplay = document.getElementById('durationDisplay');
            const submitBtn = document.getElementById('submitBtn');

            // Multiple image preview functionality
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

            // Update price calculations when market value changes manually
            marketValueInput.addEventListener('input', function() {
                const marketValue = parseFloat(this.value) || 0;
                updatePriceCalculations(marketValue);
            });

            // Date validation and notification system
            function validateEndTime() {
                const selectedDate = new Date(endTimeInput.value);
                const now = new Date();
                const minDate = new Date(now);
                minDate.setDate(minDate.getDate() + 5);
                
                const timeDiff = selectedDate - now;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                
                // Update duration display
                if (daysDiff >= 1) {
                    durationDisplay.textContent = `${daysDiff} day${daysDiff !== 1 ? 's' : ''}`;
                } else {
                    const hoursDiff = Math.ceil(timeDiff / (1000 * 60 * 60));
                    durationDisplay.textContent = `${hoursDiff} hour${hoursDiff !== 1 ? 's' : ''}`;
                }
                
                // Update validation message based on duration
                if (selectedDate < minDate) {
                    dateValidation.className = 'date-validation-error';
                    dateValidation.innerHTML = `
                        <i class="fas fa-exclamation-triangle validation-icon"></i>
                        <strong>Invalid Duration:</strong> Auction must run for at least 5 days
                        <div class="countdown-timer">Selected duration: <span id="durationDisplay">${daysDiff} day${daysDiff !== 1 ? 's' : ''}</span></div>
                    `;
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                } else if (daysDiff === 5) {
                    dateValidation.className = 'date-validation-warning';
                    dateValidation.innerHTML = `
                        <i class="fas fa-info-circle validation-icon"></i>
                        <strong>Minimum Duration:</strong> Auction will run for exactly 5 days
                        <div class="countdown-timer">Selected duration: <span id="durationDisplay">${daysDiff} day${daysDiff !== 1 ? 's' : ''}</span></div>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else if (daysDiff > 5 && daysDiff <= 7) {
                    dateValidation.className = 'date-validation-success';
                    dateValidation.innerHTML = `
                        <i class="fas fa-check-circle validation-icon"></i>
                        <strong>Valid Duration:</strong> Auction will run for ${daysDiff} days
                        <div class="countdown-timer">Selected duration: <span id="durationDisplay">${daysDiff} day${daysDiff !== 1 ? 's' : ''}</span></div>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else if (daysDiff > 7) {
                    dateValidation.className = 'date-validation-info';
                    dateValidation.innerHTML = `
                        <i class="fas fa-calendar-check validation-icon"></i>
                        <strong>Extended Duration:</strong> Auction will run for ${daysDiff} days
                        <div class="countdown-timer">Selected duration: <span id="durationDisplay">${daysDiff} day${daysDiff !== 1 ? 's' : ''}</span></div>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                }
            }

            // Initialize date validation
            validateEndTime();
            
            // Add event listener for date changes
            endTimeInput.addEventListener('change', validateEndTime);
            endTimeInput.addEventListener('input', validateEndTime);

            // Form submission validation
            document.getElementById('auctionForm').addEventListener('submit', function(e) {
                console.log('=== AUCTION FORM SUBMISSION DEBUG ===');
                
                // Check terms acceptance
                const termsAccepted = document.getElementById('terms_accepted').checked;
                console.log('Terms accepted:', termsAccepted);
                
                if (!termsAccepted) {
                    console.log('❌ Terms not accepted - showing alert');
                    e.preventDefault();
                    alert('Please accept the Terms & Conditions to continue.');
                    document.getElementById('terms_accepted').focus();
                    return false;
                }

                // Check if at least one image is uploaded
                const imageCount = imageInput.files.length;
                console.log('Images selected:', imageCount);
                
                if (imageCount === 0) {
                    console.log('❌ NO images selected - showing alert');
                    e.preventDefault();
                    alert('Please upload at least one product image.');
                    return false;
                }

                if (imageCount > 6) {
                    console.log('❌ Too many images - showing alert');
                    e.preventDefault();
                    alert('You can only upload up to 6 images.');
                    return false;
                }

                // Check if product is selected
                console.log('Product selected:', selectedProduct ? 'YES' : 'NO');
                if (!selectedProduct) {
                    console.log('❌ NO product selected - showing alert');
                    e.preventDefault();
                    alert('Please search for and select a product first.');
                    return false;
                }

                // Final date validation
                const selectedDate = new Date(endTimeInput.value);
                const now = new Date();
                const minDate = new Date(now);
                minDate.setDate(minDate.getDate() + 5);
                
                console.log('Selected date:', selectedDate);
                console.log('Minimum date:', minDate);
                
                if (selectedDate < minDate) {
                    console.log('❌ Invalid date - showing alert');
                    e.preventDefault();
                    alert('Auction must run for at least 5 days. Please select a later end date.');
                    endTimeInput.focus();
                    return false;
                }

                console.log('✅ Form validation passed - submitting form');
                console.log('=== END DEBUG ===');
            });

            // Set minimum end time to 5 days from now
            const now = new Date();
            now.setDate(now.getDate() + 5);
            const minDateTime = now.toISOString().slice(0, 16);
            endTimeInput.setAttribute('min', minDateTime);

            // Initialize price calculations with 0
            updatePriceCalculations(0);
        });
    </script>
</body>
</html>