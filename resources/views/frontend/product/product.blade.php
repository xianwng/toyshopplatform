@extends('frontend.layout.master')

@section('title', 'Product Management | Toyspace Admin')

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
        --info: #17a2b8;
        --blue: #007bff;
        --gray: #6c757d;
        --gray-light: #e9ecef;
    }

    .product-management-page .page-title {
        font-weight: 700;
        color: #2c3e50;
    }

    .table {
        width: 100%;
        table-layout: fixed;
        font-size: 0.9rem;
    }

    .table th, .table td {
        vertical-align: middle !important;
        white-space: normal !important;
        word-wrap: break-word;
        padding: 8px;
    }

    .small-table th, .small-table td {
        font-size: 0.9rem;
    }

    .text-success { color: #198754 !important; }
    .text-primary { color: #0d6efd !important; }

    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.6em;
    }

    .nav-tabs .nav-link {
        font-weight: 500;
    }

    .card-header-tabs .nav-link {
        border: none;
        color: #6c757d;
    }

    .card-header-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-bottom: 2px solid #0d6efd;
    }

    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }

    .crud-section {
        padding: 40px 0;
        background: var(--light);
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0 40px;
        text-align: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .page-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .page-subtitle {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
@php
    $products = isset($products) ? $products : collect();
@endphp

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Product Management</h1>
        <p class="page-subtitle">Monitor and manage customer-submitted products</p>
    </div>
</section>

<!-- Products Section -->
<section class="crud-section">
    <div class="container-fluid product-management-page">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Status Filter Tabs -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                            Pending Review
                            <span class="badge bg-warning ms-1">{{ $products->where('status', 'pending')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                            Approved
                            <span class="badge bg-success ms-1">{{ $products->where('status', 'approved')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                            Active (Public)
                            <span class="badge bg-primary ms-1">{{ $products->where('status', 'active')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                            Rejected
                            <span class="badge bg-danger ms-1">{{ $products->where('status', 'rejected')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                            All Products
                            <span class="badge bg-secondary ms-1">{{ $products->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="statusTabsContent">
                    <!-- Pending Products Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        @php $pendingProducts = $products->where('status', 'pending'); @endphp
                        @if($pendingProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle text-center small-table">
                                    <thead class="table-warning">
                                        <tr>
                                            <th style="width: 100px;">ASIN</th>
                                            <th style="width: 140px;">Product Image</th>
                                            <th style="width: 130px;">Product Name</th>
                                            <th style="width: 100px;">Brand</th>
                                            <th style="width: 90px;">Seller</th>
                                            <th style="width: 110px;">Category</th>
                                            <th style="width: 90px;">Condition</th>
                                            <th style="width: 120px;">Price (PHP)</th>
                                            <th style="width: 100px;">Stock</th>
                                            <th style="width: 150px;">Review Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingProducts as $product)
                                            <tr>
                                                <td>
                                                    <strong>{{ $product->asin ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if($product->hasImages())
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                                            <img 
                                                                src="{{ $product->first_image_url }}" 
                                                                alt="{{ $product->name }}"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                                        </div>
                                                    @else
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                                            <img 
                                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                                alt="No Image"
                                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">{{ $product->name }}</td>
                                                <td>{{ $product->brand }}</td>
                                                <td>
                                                    @if($product->user)
                                                        <small>{{ $product->user->username }}</small>
                                                    @else
                                                        <small class="text-muted">Unknown</small>
                                                    @endif
                                                </td>
                                                <td>{{ $product->category }}</td>
                                                <td>{{ $product->condition }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                    @if($product->amazon_price)
                                                        <br>
                                                        <small class="text-muted">${{ number_format($product->amazon_price, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->stock > 0)
                                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical" role="group">
                                                        <form action="{{ route('admin.products.approve', $product->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Approve this product? The seller will be able to activate it for public viewing.')">
                                                                <i class="bi bi-check-circle"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.products.reject', $product->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this product? This action cannot be undone.')">
                                                                <i class="bi bi-x-circle"></i> Reject
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i> View Details
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No products pending review</p>
                        @endif
                    </div>

                    <!-- Approved Products Tab -->
                    <div class="tab-pane fade" id="approved" role="tabpanel">
                        @php $approvedProducts = $products->where('status', 'approved'); @endphp
                        @if($approvedProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle text-center small-table">
                                    <thead class="table-success">
                                        <tr>
                                            <th style="width: 100px;">ASIN</th>
                                            <th style="width: 140px;">Product Image</th>
                                            <th style="width: 130px;">Product Name</th>
                                            <th style="width: 100px;">Brand</th>
                                            <th style="width: 90px;">Seller</th>
                                            <th style="width: 110px;">Category</th>
                                            <th style="width: 120px;">Price (PHP)</th>
                                            <th style="width: 100px;">Stock</th>
                                            <th style="width: 150px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvedProducts as $product)
                                            <tr>
                                                <td>
                                                    <strong>{{ $product->asin ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if($product->hasImages())
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                                            <img 
                                                                src="{{ $product->first_image_url }}" 
                                                                alt="{{ $product->name }}"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                                        </div>
                                                    @else
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                                            <img 
                                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                                alt="No Image"
                                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">{{ $product->name }}</td>
                                                <td>{{ $product->brand }}</td>
                                                <td>
                                                    @if($product->user)
                                                        <small>{{ $product->user->username }}</small>
                                                    @else
                                                        <small class="text-muted">Unknown</small>
                                                    @endif
                                                </td>
                                                <td>{{ $product->category }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                    @if($product->amazon_price)
                                                        <br>
                                                        <small class="text-muted">${{ number_format($product->amazon_price, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->stock > 0)
                                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Approved</span>
                                                    <small class="d-block text-muted">Waiting for seller activation</small>
                                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info mt-1">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No approved products</p>
                        @endif
                    </div>

                    <!-- Active Products Tab -->
                    <div class="tab-pane fade" id="active" role="tabpanel">
                        @php $activeProducts = $products->where('status', 'active'); @endphp
                        @if($activeProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle text-center small-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 100px;">ASIN</th>
                                            <th style="width: 140px;">Product Image</th>
                                            <th style="width: 130px;">Product Name</th>
                                            <th style="width: 100px;">Brand</th>
                                            <th style="width: 90px;">Seller</th>
                                            <th style="width: 110px;">Category</th>
                                            <th style="width: 120px;">Price (PHP)</th>
                                            <th style="width: 100px;">Stock</th>
                                            <th style="width: 150px;">Public Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeProducts as $product)
                                            <tr>
                                                <td>
                                                    <strong>{{ $product->asin ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if($product->hasImages())
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                                            <img 
                                                                src="{{ $product->first_image_url }}" 
                                                                alt="{{ $product->name }}"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                                        </div>
                                                    @else
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                                            <img 
                                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                                alt="No Image"
                                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">{{ $product->name }}</td>
                                                <td>{{ $product->brand }}</td>
                                                <td>
                                                    @if($product->user)
                                                        <small>{{ $product->user->username }}</small>
                                                    @else
                                                        <small class="text-muted">Unknown</small>
                                                    @endif
                                                </td>
                                                <td>{{ $product->category }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                    @if($product->amazon_price)
                                                        <br>
                                                        <small class="text-muted">${{ number_format($product->amazon_price, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->stock > 0)
                                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">Publicly Visible</span>
                                                    <small class="d-block text-muted">Active since: {{ $product->updated_at->format('M j, Y') }}</small>
                                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info mt-1">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No active products</p>
                        @endif
                    </div>

                    <!-- Rejected Products Tab -->
                    <div class="tab-pane fade" id="rejected" role="tabpanel">
                        @php $rejectedProducts = $products->where('status', 'rejected'); @endphp
                        @if($rejectedProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle text-center small-table">
                                    <thead class="table-danger">
                                        <tr>
                                            <th style="width: 100px;">ASIN</th>
                                            <th style="width: 140px;">Product Image</th>
                                            <th style="width: 130px;">Product Name</th>
                                            <th style="width: 100px;">Brand</th>
                                            <th style="width: 90px;">Seller</th>
                                            <th style="width: 110px;">Category</th>
                                            <th style="width: 120px;">Price (PHP)</th>
                                            <th style="width: 150px;">Rejection Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rejectedProducts as $product)
                                            <tr>
                                                <td>
                                                    <strong>{{ $product->asin ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if($product->hasImages())
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                                            <img 
                                                                src="{{ $product->first_image_url }}" 
                                                                alt="{{ $product->name }}"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                                        </div>
                                                    @else
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                                            <img 
                                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                                alt="No Image"
                                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">{{ $product->name }}</td>
                                                <td>{{ $product->brand }}</td>
                                                <td>
                                                    @if($product->user)
                                                        <small>{{ $product->user->username }}</small>
                                                    @else
                                                        <small class="text-muted">Unknown</small>
                                                    @endif
                                                </td>
                                                <td>{{ $product->category }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                    @if($product->amazon_price)
                                                        <br>
                                                        <small class="text-muted">${{ number_format($product->amazon_price, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.products.approve', $product->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success mb-1">
                                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Permanently delete this rejected product?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No rejected products</p>
                        @endif
                    </div>

                    <!-- All Products Tab -->
                    <div class="tab-pane fade" id="all" role="tabpanel">
                        @if($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle text-center small-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 100px;">ASIN</th>
                                            <th style="width: 140px;">Product Image</th>
                                            <th style="width: 130px;">Product Name</th>
                                            <th style="width: 100px;">Brand</th>
                                            <th style="width: 90px;">Seller</th>
                                            <th style="width: 110px;">Category</th>
                                            <th style="width: 90px;">Condition</th>
                                            <th style="width: 120px;">Price (PHP)</th>
                                            <th style="width: 100px;">Stock</th>
                                            <th style="width: 120px;">Status</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr>
                                                <td>
                                                    <strong>{{ $product->asin ?? 'N/A' }}</strong>
                                                </td>
                                                <td>
                                                    @if($product->hasImages())
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                                            <img 
                                                                src="{{ $product->first_image_url }}" 
                                                                alt="{{ $product->name }}"
                                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                                        </div>
                                                    @else
                                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                                            <img 
                                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                                alt="No Image"
                                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">{{ $product->name }}</td>
                                                <td>{{ $product->brand }}</td>
                                                <td>
                                                    @if($product->user)
                                                        <small>{{ $product->user->username }}</small>
                                                    @else
                                                        <small class="text-muted">Unknown</small>
                                                    @endif
                                                </td>
                                                <td>{{ $product->category }}</td>
                                                <td>{{ $product->condition }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</span>
                                                    @if($product->amazon_price)
                                                        <br>
                                                        <small class="text-muted">${{ number_format($product->amazon_price, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->stock > 0)
                                                        <span class="badge bg-success">{{ $product->stock }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Out of stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($product->status === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($product->status === 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($product->status === 'active')
                                                        <span class="badge bg-primary">Active</span>
                                                    @elseif($product->status === 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $product->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info mb-1">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                    @if($product->status === 'pending')
                                                        <form action="{{ route('admin.products.approve', $product->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-success mb-1">
                                                                <i class="bi bi-check"></i> Approve
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No products found</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Review</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('status', 'approved')->count() }}</h4>
                        <p class="mb-0">Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('status', 'active')->count() }}</h4>
                        <p class="mb-0">Active (Public)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('status', 'rejected')->count() }}</h4>
                        <p class="mb-0">Rejected</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Auto-refresh the page every 30 seconds to get updated status
    setTimeout(function() {
        window.location.reload();
    }, 30000);

    // Image error handler function
    function handleImageError(img) {
        console.log('Image failed to load:', img.src);
        img.onerror = null;
        
        // Replace with SVG placeholder
        img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=';
        img.style.width = '60px';
        img.style.height = '60px';
        img.style.opacity = '0.5';
    }

    // Initialize after page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Product management page loaded');
    });
</script>
@endsection