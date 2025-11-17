@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0">
                <!-- Professional Header with Back Button - WHITE TEXT VERSION -->
                <div class="card-header text-white py-3 position-relative" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.trading.management') }}" class="btn btn-light btn-sm me-3" style="background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.5); color: #0d1b2a;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h4 class="mb-0 fw-semibold text-white">Trade Details</h4>
                            <small class="opacity-75 text-white">Complete trade information and exchange details</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Trade Images Section -->
                        <div class="col-lg-5 col-md-6 mb-4">
                            <div class="sticky-top" style="top: 20px;">
                                <!-- Trade Images Gallery -->
                                <div class="images-container bg-white rounded-3 shadow-sm border p-3">
                                    @php
                                        // Use the Trade model's built-in method to get images
                                        $allImages = $trade->images_array ?? [];
                                        $displayImage = !empty($allImages) ? $allImages[0] : '';
                                    @endphp

                                    @if(!empty($allImages))
                                        <!-- Main Image Display -->
                                        <div class="product-image-container">
                                            <img id="mainImage" src="{{ $displayImage }}" alt="{{ $trade->name }}" class="product-image" style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px;">
                                        </div>

                                        <!-- Thumbnail Gallery for Multiple Images -->
                                        @if(count($allImages) > 1)
                                        <div class="image-gallery mt-3">
                                            @foreach($allImages as $index => $image)
                                            <img src="{{ $image }}" 
                                                 alt="{{ $trade->name }} - Image {{ $index + 1 }}"
                                                 class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                                 data-image="{{ $image }}"
                                                 onclick="changeMainImage(this)">
                                            @endforeach
                                        </div>
                                        @endif
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center text-muted bg-light rounded-3" style="height:400px;">
                                            <i class="bi bi-image fs-1 mb-3 opacity-50"></i>
                                            <h5 class="mb-2">No Images Available</h5>
                                            <p class="text-center small opacity-75">This trade doesn't have any images</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Trade Description -->
                                @if($trade->description)
                                <div class="description-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-text text-primary me-2"></i>Trade Description
                                    </h5>
                                    <div class="description-content bg-light bg-opacity-25 rounded-3 p-4 border">
                                        <p class="mb-0 text-dark lh-lg">{!! nl2br(e($trade->description)) !!}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Documentation Section -->
                                @php
                                    // Process documents directly in the view
                                    $documentsData = $trade->documents;
                                    $hasDocuments = false;
                                    $documentUrls = [];
                                    
                                    if ($documentsData) {
                                        // If documents is a string, try to decode it as JSON
                                        if (is_string($documentsData)) {
                                            $decoded = json_decode($documentsData, true);
                                            $documentsData = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$documentsData];
                                        }
                                        
                                        // Ensure documents is an array
                                        if (is_array($documentsData) && !empty($documentsData)) {
                                            $hasDocuments = true;
                                            foreach ($documentsData as $doc) {
                                                if (is_string($doc)) {
                                                    $documentUrls[] = [
                                                        'url' => asset('storage/' . $doc),
                                                        'type' => Str::endsWith($doc, '.pdf') ? 'pdf' : 'image',
                                                        'name' => basename($doc)
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if($hasDocuments)
                                <div class="documentation-section mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Legal Documents & Verification
                                    </h5>
                                    
                                    <div class="documentation-cards">
                                        @foreach($documentUrls as $document)
                                        <div class="document-card bg-white rounded-3 border p-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="document-icon {{ $document['type'] === 'pdf' ? 'bg-danger bg-opacity-10' : 'bg-info bg-opacity-10' }} rounded-circle p-2 me-3">
                                                    <i class="bi bi-{{ $document['type'] === 'pdf' ? 'file-pdf' : 'file-image' }}-fill {{ $document['type'] === 'pdf' ? 'text-danger' : 'text-info' }} fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $document['type'] === 'pdf' ? 'PDF Document' : 'Image Document' }}</h6>
                                                    <p class="mb-0 text-muted small">{{ $document['name'] }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Document Preview -->
                                            <div class="certificate-preview mt-3">
                                                <a href="{{ $document['url'] }}" target="_blank" class="certificate-image-link">
                                                    @if($document['type'] === 'pdf')
                                                    <div class="pdf-preview bg-light rounded-3 d-flex flex-column align-items-center justify-content-center p-4" style="height: 200px; border: 2px solid #e9ecef; cursor: pointer; transition: all 0.3s ease;">
                                                        <i class="bi bi-file-pdf text-danger fs-1 mb-2"></i>
                                                        <h6 class="text-dark mb-1">PDF Document</h6>
                                                        <small class="text-muted">Click to view/download</small>
                                                    </div>
                                                    @else
                                                    <img src="{{ $document['url'] }}" 
                                                         alt="Legal Document" 
                                                         class="certificate-image"
                                                         style="width: 100%; height: 200px; object-fit: contain; border-radius: 8px; border: 2px solid #e9ecef; cursor: pointer; transition: all 0.3s ease;">
                                                    @endif
                                                </a>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">Click to view full document</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Trade Information Section -->
                        <div class="col-lg-7 col-md-6">
                            <!-- Trade Header -->
                            <div class="product-header mb-4">
                                <h1 class="h2 fw-bold text-dark mb-2">{{ $trade->name ?? 'Untitled Trade' }}</h1>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge ms-3" style="background: rgba(0, 109, 119, 0.1); color: #006d77; border: 1px solid rgba(0, 109, 119, 0.3);">
                                        <i class="bi bi-{{ $trade->status === 'active' ? 'check-circle' : 'clock' }} me-1"></i>{{ ucfirst($trade->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Trade Specifications -->
                            <div class="specifications-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-grid-3x3-gap me-2" style="color: #0d1b2a;"></i>Trade Specifications
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
                                                    <strong class="text-dark">{{ $trade->brand ?? 'Not specified' }}</strong>
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
                                                    <strong class="text-dark">{{ $trade->category ?? 'Not specified' }}</strong>
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
                                                    <strong class="text-dark text-capitalize">{{ $trade->condition ?? 'Not specified' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    @if($trade->location)
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-geo-alt fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Location</small>
                                                    <strong class="text-dark">{{ $trade->location }}</strong>
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
                                                    <small class="text-muted d-block">Trade Status</small>
                                                    <span class="badge 
                                                        @if($trade->status === 'active') bg-success
                                                        @elseif($trade->status === 'inactive') bg-warning
                                                        @elseif($trade->status === 'completed') bg-dark
                                                        @elseif($trade->status === 'rejected') bg-danger
                                                        @else bg-secondary @endif text-capitalize">
                                                        {{ ucfirst($trade->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Created Date -->
                                    <div class="col-md-6">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #006d77 !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(0, 109, 119, 0.1);">
                                                    <i class="bi bi-calendar fs-5" style="color: #006d77;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Listed On</small>
                                                    <strong class="text-dark">{{ $trade->created_at->format('M d, Y') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trade Preferences -->
                                    @if($trade->trade_preferences)
                                    <div class="col-12">
                                        <div class="spec-card p-3 rounded-3 border" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-color: #0d1b2a !important;">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-container rounded-circle p-2 me-3" style="background: rgba(13, 27, 42, 0.1);">
                                                    <i class="bi bi-handshake fs-5" style="color: #0d1b2a;"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Trade Preferences</small>
                                                    <div class="mt-2">
                                                        @foreach(explode(',', $trade->trade_preferences) as $pref)
                                                            <span class="badge bg-primary me-1 mb-1">{{ trim($pref) }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Seller Information -->
                            @if($trade->user)
                            <div class="seller-section mb-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-person-badge me-2" style="color: #0d1b2a;"></i>Trader Information
                                </h5>
                                
                                <div class="seller-card rounded-3 border overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-color: #415a77 !important;">
                                    <!-- Seller Header -->
                                    <div class="seller-header p-4 border-bottom" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);">
                                        <div class="d-flex align-items-center">
                                            <div class="seller-avatar rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: rgba(255,255,255,0.2);">
                                                <i class="bi bi-person fs-4 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-white">{{ $trade->user->first_name }} {{ $trade->user->last_name }}</h6>
                                                <p class="mb-0 small text-white-50">
                                                    <i class="bi bi-at me-1"></i>{{ $trade->user->username }}
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
                                                    <strong class="text-dark">{{ $trade->user->email }}</strong>
                                                </div>
                                            </div>
                                            
                                            @if($trade->user->contact_number)
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-telephone me-2" style="color: #415a77;"></i>Contact Number
                                                    </small>
                                                    <strong class="text-dark">{{ $trade->user->contact_number }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if($trade->user->home_address)
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-geo-alt me-2" style="color: #006d77;"></i>Location
                                                    </small>
                                                    <strong class="text-dark">{{ $trade->user->home_address }}</strong>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Admin Actions -->
                            <div class="admin-actions-section mt-4">
                                <h5 class="fw-semibold text-dark mb-3 border-bottom pb-2" style="border-color: #0d1b2a !important;">
                                    <i class="bi bi-gear me-2" style="color: #0d1b2a;"></i>Admin Actions
                                </h5>
                                <div class="d-flex gap-2 flex-wrap">
                                    <!-- Inactive Status: Approve Button -->
                                    @if($trade->status === 'inactive')
                                        <form action="{{ route('admin.trading.approve', $trade->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%); color: white; border: none;">
                                                <i class="bi bi-check-circle me-1"></i>Approve Trade
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Inactive Status: Reject Button -->
                                    @if($trade->status === 'inactive')
                                        <form action="{{ route('admin.trading.reject', $trade->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #415a77 0%, #778da9 100%); color: white; border: none;">
                                                <i class="bi bi-x-circle me-1"></i>Reject Trade
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Trade Button -->
                                    <a href="{{ route('trading.edit', $trade->id) }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #415a77 0%, #778da9 100%); border: none;">
                                        <i class="bi bi-pencil me-1"></i>Edit Trade
                                    </a>
                                    
                                    <!-- Delete Trade Button -->
                                    <form action="{{ route('trading.destroy', $trade->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this trade?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none;">
                                            <i class="bi bi-trash me-1"></i>Delete Trade
                                        </button>
                                    </form>
                                </div>
                            </div>
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
    
    .admin-actions-section {
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

    /* PDF Preview Styles */
    .pdf-preview {
        transition: all 0.3s ease;
    }

    .pdf-preview:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #0d1b2a !important;
    }

    /* Description Section */
    .description-section {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
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

        .admin-actions-section .d-flex {
            flex-direction: column;
            gap: 0.5rem;
        }

        .admin-actions-section .btn {
            width: 100%;
            justify-content: center;
        }

        .pdf-preview {
            height: 150px !important;
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

    // Add hover effects to PDF previews
    const pdfPreviews = document.querySelectorAll('.pdf-preview');
    pdfPreviews.forEach(preview => {
        preview.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
            this.style.borderColor = '#0d1b2a';
        });
        
        preview.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
            this.style.borderColor = '#e9ecef';
        });
    });
});
</script>
@endsection