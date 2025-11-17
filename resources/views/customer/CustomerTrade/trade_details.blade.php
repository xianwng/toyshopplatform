@extends('customer.layouts.cmaster')

@section('title', 'Trade Details | Toyspace')

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="{{ asset('assets/lib/components-font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
/* Your existing CSS styles remain exactly the same */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.trade-details-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 100px 20px 40px;
    margin-top: 0;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    margin-bottom: 30px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    text-decoration: none;
    transform: translateX(-5px);
}

.details-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    max-width: 1200px;
    margin: 0 auto;
}

.details-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px 40px;
    position: relative;
}

.details-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
}

.uploader-info {
    font-size: 18px !important;
    font-weight: 500;
    margin-top: 15px;
    opacity: 0.95;
    line-height: 1.4;
}

.uploader-username {
    font-size: 16px;
    opacity: 0.8;
    font-weight: 400;
}

.status-badge {
    position: absolute;
    top: 30px;
    right: 40px;
    background: #48bb78;
    color: #fff;
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
}

.status-badge.inactive {
    background: #f56565;
}

.status-badge.completed {
    background: #6f42c1;
}

.details-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    padding: 40px;
}

.image-slideshow {
    position: relative;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.main-image-container {
    width: 100%;
    height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    position: relative;
}

.main-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    cursor: zoom-in;
    transition: transform 0.3s ease;
}

.image-thumbnails {
    display: flex;
    gap: 10px;
    padding: 15px;
    overflow-x: auto;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.thumbnail-item {
    width: 60px;
    height: 60px;
    border: 2px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.thumbnail-item.active {
    border-color: #667eea;
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-item:hover {
    transform: scale(1.05);
}

.slideshow-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.slideshow-nav:hover {
    background: #fff;
    transform: translateY(-50%) scale(1.1);
}

.prev-nav {
    left: 15px;
}

.next-nav {
    right: 15px;
}

.image-counter {
    position: absolute;
    bottom: 80px;
    right: 15px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 14px;
    font-weight: 500;
}

.info-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-group {
    background: #f7fafc;
    padding: 15px 20px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.info-group h3 {
    font-size: 12px;
    color: #667eea;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    font-weight: 600;
}

.info-group p {
    font-size: 16px;
    color: #2d3748;
    font-weight: 500;
    margin: 0;
}

.info-group.description p {
    font-size: 15px;
    line-height: 1.6;
    color: #4a5568;
    font-weight: 400;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.trade-preferences-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.preference-tag {
    background: #667eea;
    color: #fff;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
}

.documents-section {
    grid-column: 1 / -1;
    margin-top: 20px;
}

.documents-section h2 {
    font-size: 24px;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.document-item {
    position: relative;
    background: #f7fafc;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.document-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.document-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.document-item .pdf-placeholder {
    width: 100%;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.document-item .pdf-placeholder i {
    font-size: 48px;
}

.document-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: 10px;
    color: #fff;
    font-size: 12px;
}

.action-buttons {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 15px;
}

.btn-propose {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 12px 40px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    max-width: 300px;
    text-decoration: none;
}

.btn-propose:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    text-decoration: none;
    color: #fff;
}

.btn-complete {
    background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
    color: #fff;
    padding: 12px 40px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    max-width: 300px;
    text-decoration: none;
}

.btn-complete:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(111, 66, 193, 0.4);
    text-decoration: none;
    color: #fff;
}

.completed-notice {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 2px solid #28a745;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin: 20px 0;
}

.completed-notice h3 {
    color: #155724;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.completed-notice p {
    color: #155724;
    margin-bottom: 0;
    font-weight: 500;
}

.image-lightbox {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
}

.lightbox-content {
    position: relative;
    margin: auto;
    padding: 20px;
    max-width: 90%;
    max-height: 90%;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
}

.lightbox-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    background: none;
    border: none;
    z-index: 10000;
}

.lightbox-close:hover {
    color: #667eea;
    transform: scale(1.1);
}

.lightbox-caption {
    color: #fff;
    font-size: 18px;
    margin-top: 20px;
    text-align: center;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
    transform: translateY(-50%);
}

.nav-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .trade-details-container {
        padding: 80px 15px 30px;
    }
    
    .details-body {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-propose, .btn-complete {
        width: 100%;
        max-width: 100%;
    }
    
    .details-header {
        padding: 20px;
    }
    
    .status-badge {
        position: static;
        display: inline-block;
        margin-top: 10px;
    }
    
    .lightbox-content {
        max-width: 95%;
        max-height: 95%;
    }
    
    .nav-btn {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .uploader-info {
        font-size: 16px !important;
    }
    
    .uploader-username {
        font-size: 14px;
    }
    
    .main-image-container {
        height: 350px;
    }
    
    .thumbnail-item {
        width: 50px;
        height: 50px;
    }
}

.no-documents {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px;
    color: #a0aec0;
}

body {
    padding-top: 0 !important;
}

main {
    margin-top: 0 !important;
}
</style>
@endpush

@section('content')
<div class="trade-details-container">
    <div class="container">
        <a href="{{ route('trading') }}" class="back-button">
            <i class="fa fa-arrow-left"></i> Back to Trades
        </a>

        <div class="details-card">
            <div class="details-header">
                <h1>{{ $trade->name }}</h1>
                <div class="uploader-info">
                    <i class="fa fa-user"></i>
                    <strong>Uploaded by:</strong> {{ $trade->user ? ($trade->user->first_name . ' ' . $trade->user->last_name) : 'Unknown User' }}
                    @if($trade->user && $trade->user->username)
                        <span class="uploader-username">({{ '@' . $trade->user->username }})</span>
                    @endif
                </div>
                <span class="status-badge {{ $trade->status === 'active' ? '' : ($trade->status === 'completed' ? 'completed' : 'inactive') }}">
                    {{ ucfirst($trade->status) }}
                </span>
            </div>

            @if($trade->status === 'completed')
            <div class="completed-notice">
                <h3><i class="fa fa-check-circle"></i> Trade Completed</h3>
                <p>This trade has been marked as completed and is no longer available for exchange.</p>
            </div>
            @endif

            <div class="details-body">
                <div class="image-section">
                    <div class="image-slideshow">
                        <div class="main-image-container">
                            @if(!empty($trade->images_array) && count($trade->images_array) > 0)
                                <img src="{{ $trade->images_array[0] }}" 
                                     alt="{{ $trade->name }}" 
                                     class="main-image"
                                     id="mainImage">
                            @else
                                <img src="https://via.placeholder.com/600x450?text=No+Image" 
                                     alt="No Image" 
                                     class="main-image"
                                     id="mainImage">
                            @endif
                        </div>
                        
                        @if(!empty($trade->images_array) && count($trade->images_array) > 1)
                        <div class="image-thumbnails">
                            @foreach($trade->images_array as $index => $image)
                            <div class="thumbnail-item {{ $loop->first ? 'active' : '' }}" 
                                 data-index="{{ $index }}">
                                <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}">
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(!empty($trade->images_array) && count($trade->images_array) > 1)
                        <button class="slideshow-nav prev-nav" id="prevNav">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="slideshow-nav next-nav" id="nextNav">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        
                        <div class="image-counter">
                            <span id="currentImage">1</span> / {{ count($trade->images_array) }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-grid">
                        <div class="info-group">
                            <h3>Category</h3>
                            <p><i class="fa fa-cube"></i> {{ $trade->category }}</p>
                        </div>

                        <div class="info-group">
                            <h3>Brand</h3>
                            <p><i class="fa fa-tag"></i> {{ $trade->brand }}</p>
                        </div>

                        <div class="info-group">
                            <h3>Condition</h3>
                            <p><i class="fa fa-check-circle"></i> {{ ucfirst($trade->condition) }}</p>
                        </div>

                        @if($trade->location)
                        <div class="info-group">
                            <h3>Location</h3>
                            <p><i class="fa fa-map-marker"></i> {{ $trade->location }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="info-group description">
                        <h3>Description / Looking For</h3>
                        <p>{{ $trade->description }}</p>
                    </div>

                    @if($trade->trade_preferences)
                    <div class="info-group">
                        <h3>Trade Preferences</h3>
                        <div class="trade-preferences-list">
                            @foreach(explode(',', $trade->trade_preferences) as $pref)
                                <span class="preference-tag">{{ trim($pref) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="documents-section">
                    <h2>
                        <i class="fa fa-file-text"></i>
                        Legal Documents & Verification
                    </h2>
                    
                    @if(!empty($trade->documents_array) && count($trade->documents_array) > 0)
                        <div class="documents-grid">
                            @foreach($trade->documents_array as $doc)
                                @php
                                    // Check if document is stored as full URL or just path
                                    $docUrl = $doc;
                                    if (!Str::startsWith($doc, ['http://', 'https://'])) {
                                        $docUrl = asset('storage/' . $doc);
                                    }
                                    $isPdf = Str::endsWith($doc, '.pdf');
                                @endphp
                                <a href="{{ $docUrl }}" target="_blank" class="document-item">
                                    @if($isPdf)
                                        <div class="pdf-placeholder">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </div>
                                        <div class="document-overlay">
                                            PDF Document
                                        </div>
                                    @else
                                        <img src="{{ $docUrl }}" alt="Legal Document" onerror="this.style.display='none'; this.parentNode.querySelector('.pdf-placeholder').style.display='flex';">
                                        <div class="pdf-placeholder" style="display: none;">
                                            <i class="fa fa-file-image-o"></i>
                                        </div>
                                        <div class="document-overlay">
                                            Click to view
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="no-documents">
                            <i class="fa fa-folder-open fa-3x"></i>
                            <p>No documents uploaded</p>
                        </div>
                    @endif
                </div>

                <div class="action-buttons">
                    @if($trade->status === 'active')
                        @if(Auth::check() && Auth::id() === $trade->user_id)
                            <button type="button" class="btn-complete" onclick="completeTrade('{{ $trade->id }}')">
                                <i class="fa fa-check-circle"></i>
                                Mark as Completed
                            </button>
                        @else
                            <a href="{{ route('trading.proposals.create', $trade->id) }}" class="btn-propose">
                                <i class="fa fa-exchange"></i>
                                Propose Exchange
                            </a>
                        @endif
                    @elseif($trade->status === 'completed')
                        <div class="completed-notice" style="margin: 0;">
                            <p><i class="fa fa-info-circle"></i> This trade has been completed and is no longer available for exchange.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fixed Lightbox - Moved outside main container and fixed structure -->
<div id="imageLightbox" class="image-lightbox">
    <button class="lightbox-close" id="lightboxClose">&times;</button>
    <div class="lightbox-nav">
        <button class="nav-btn prev-btn" id="lightboxPrev">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="nav-btn next-btn" id="lightboxNext">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
    <div class="lightbox-content">
        <img class="lightbox-image" id="lightboxImage" src="" alt="Enlarged view">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

<!-- Complete Trade Confirmation Modal -->
<div class="modal fade" id="completeTradeModal" tabindex="-1" role="dialog" aria-labelledby="completeTradeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="completeTradeModalLabel">Complete Trade</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="completeTradeMessage">Are you sure you want to mark this trade as completed? This will end the exchange and change the trade status from "Active" to "Completed".</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmCompleteTrade">Complete Trade</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the image paths directly from PHP without JSON encoding
    const tradeImages = <?php echo json_encode($trade->images_array ?? []); ?>;
    
    let currentImageIndex = 0;
    let lightboxCurrentIndex = 0;

    // Get DOM elements
    const mainImage = document.getElementById('mainImage');
    const currentImageCounter = document.getElementById('currentImage');
    const thumbnailItems = document.querySelectorAll('.thumbnail-item');
    const prevNav = document.getElementById('prevNav');
    const nextNav = document.getElementById('nextNav');
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');

    // Change main image function
    function changeMainImage(index) {
        if (tradeImages.length === 0 || index < 0 || index >= tradeImages.length) return;
        
        currentImageIndex = index;
        if (mainImage) mainImage.src = tradeImages[index];
        if (currentImageCounter) currentImageCounter.textContent = index + 1;
        
        // Update active thumbnail
        thumbnailItems.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    // Navigation functions
    function changeImage(direction) {
        if (tradeImages.length <= 1) return;
        
        let newIndex = currentImageIndex + direction;
        if (newIndex >= tradeImages.length) newIndex = 0;
        if (newIndex < 0) newIndex = tradeImages.length - 1;
        
        changeMainImage(newIndex);
    }

    // Lightbox functions - FIXED
    function openLightbox(index) {
        if (tradeImages.length === 0 || index < 0 || index >= tradeImages.length) return;
        
        lightboxCurrentIndex = index;
        if (lightboxImage) {
            lightboxImage.src = tradeImages[index];
            lightboxImage.alt = "Enlarged view of {{ $trade->name }}";
        }
        if (lightboxCaption) lightboxCaption.textContent = "{{ $trade->name }}";
        if (lightbox) {
            lightbox.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // Show/hide navigation
        const showNav = tradeImages.length > 1;
        if (lightboxPrev) lightboxPrev.style.display = showNav ? 'flex' : 'none';
        if (lightboxNext) lightboxNext.style.display = showNav ? 'flex' : 'none';
    }

    function closeLightbox() {
        if (lightbox) {
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    function changeLightboxImage(direction) {
        if (tradeImages.length <= 1) return;
        
        let newIndex = lightboxCurrentIndex + direction;
        if (newIndex >= tradeImages.length) newIndex = 0;
        if (newIndex < 0) newIndex = tradeImages.length - 1;
        
        lightboxCurrentIndex = newIndex;
        if (lightboxImage) lightboxImage.src = tradeImages[newIndex];
        changeMainImage(newIndex);
    }

    // Event listeners
    if (thumbnailItems.length > 0) {
        thumbnailItems.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                changeMainImage(index);
            });
        });
    }

    if (prevNav) prevNav.addEventListener('click', () => changeImage(-1));
    if (nextNav) nextNav.addEventListener('click', () => changeImage(1));
    
    // Fixed lightbox event listeners
    if (mainImage) {
        mainImage.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openLightbox(currentImageIndex);
        });
    }

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxPrev) lightboxPrev.addEventListener('click', (e) => {
        e.stopPropagation();
        changeLightboxImage(-1);
    });
    if (lightboxNext) lightboxNext.addEventListener('click', (e) => {
        e.stopPropagation();
        changeLightboxImage(1);
    });

    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (lightbox && lightbox.style.display === 'block') {
            switch(e.key) {
                case 'Escape': 
                    closeLightbox(); 
                    break;
                case 'ArrowLeft': 
                    changeLightboxImage(-1); 
                    break;
                case 'ArrowRight': 
                    changeLightboxImage(1); 
                    break;
            }
        } else {
            if (e.key === 'ArrowLeft') changeImage(-1);
            else if (e.key === 'ArrowRight') changeImage(1);
        }
    });

    // Prevent lightbox from closing when clicking on the image
    if (lightboxImage) {
        lightboxImage.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

// Trade completion functionality
let currentTradeId = null;

function completeTrade(tradeId) {
    currentTradeId = tradeId;
    $('#completeTradeMessage').text('Are you sure you want to mark this trade as completed? This will end the exchange and change the trade status from "Active" to "Completed".');
    $('#completeTradeModal').modal('show');
}

$('#confirmCompleteTrade').on('click', function() {
    if (currentTradeId) {
        $.ajax({
            url: `/trading/${currentTradeId}/complete`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#completeTradeModal').modal('hide');
                    alert('Trade marked as completed successfully! The trade status has been updated.');
                    location.reload();
                } else {
                    alert('Error completing trade: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error completing trade. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    }
});
</script>
@endpush