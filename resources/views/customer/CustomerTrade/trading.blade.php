@extends('customer.layouts.cmaster')

@section('title', 'Trade & Exchange | Toyspace')

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="{{ asset('assets/lib/components-font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/ctrade.css') }}" rel="stylesheet">
<link id="color-scheme" href="{{ asset('assets/css/colors/default.css') }}" rel="stylesheet">
<style>
    :root {
        --primary: #343a40;
        --primary-dark: #1a1a1a;
        --secondary: #495057;
        --accent: #6c757d;
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

    /* Hero Section - Original Black Design */
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

    /* Search Container Styles */
    .trade-search-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        align-items: center;
        max-width: 700px;
        margin: 0 auto;
        flex-wrap: wrap;
    }

    .trade-search-bar {
        display: flex;
        background: white;
        border-radius: 50px;
        padding: 5px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        flex: 1;
        min-width: 300px;
    }

    .trade-search-bar .form-control {
        border: none;
        background: transparent;
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 1rem;
    }

    .trade-search-bar .form-control:focus {
        outline: none;
        box-shadow: none;
    }

    .trade-search-btn {
        background: var(--primary);
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .trade-search-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-upload-trade {
        background: var(--success);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-upload-trade:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }

    /* Additional CSS for status badges */
    .trade-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        color: white;
        z-index: 10;
    }

    .badge-pending {
        background: #ffc107;
    }

    .badge-approved {
        background: #17a2b8;
    }

    .badge-active {
        background: #28a745;
    }

    .badge-rejected {
        background: #dc3545;
    }

    .badge-inactive {
        background: #6c757d;
    }

    .badge-completed {
        background: #6f42c1;
    }

    /* Owner indicator styles */
    .owner-indicator {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0, 123, 255, 0.9);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 10;
    }

    .owner-notice {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 8px;
        margin-bottom: 10px;
    }

    .owner-listing-message {
        text-align: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    .my-trades-section {
        padding: 40px 0;
        background: #f8f9fa;
    }

    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    .section-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .btn-activate-trade {
        background: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-activate-trade:hover {
        background: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .btn-complete-trade {
        background: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }

    .btn-complete-trade:hover {
        background: #5a2d91;
        border-color: #5a2d91;
        color: white;
    }

    .btn-trade {
        background: #007bff;
        border-color: #007bff;
        color: white;
        width: 100%;
    }

    .btn-trade:hover {
        background: #0056b3;
        border-color: #0056b3;
        color: white;
    }

    .trade-card {
        position: relative;
        transition: transform 0.2s ease-in-out;
    }

    .trade-card:hover {
        transform: translateY(-5px);
    }

    .trade-card-footer {
        padding: 15px;
        border-top: 1px solid #eee;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }

        .trade-search-container {
            flex-direction: column;
            gap: 15px;
        }

        .trade-search-bar {
            min-width: 100%;
        }

        .btn-upload-trade {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<main>
  <!-- Hero Section with Search - Original Black Design -->
  <section class="hero-section">
    <div class="hero-content">
      <div class="container">
        <h1 class="hero-title">TRADE & EXCHANGE</h1>
        <p class="hero-subtitle">Exchange toys with other collectors in our vibrant trading community</p>
        
        <div class="trade-search-container">
          <div class="trade-search-bar">
            <input type="text" class="form-control" placeholder="Search trade items..." id="tradeSearchInput">
            <button class="trade-search-btn" onclick="performTradeSearch()">
              <i class="fa fa-search"></i>
            </button>
          </div>
          <button class="btn btn-upload-trade" data-toggle="modal" data-target="#uploadTradeModal">
            <i class="fa fa-upload"></i> Upload Trade
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- My Trades Section -->
  @auth
  <section class="my-trades-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">My Trade Items</h2>
        <p class="section-subtitle">Manage your trade listings</p>
      </div>

      @php
        $myTrades = \App\Models\Trade::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
      @endphp

      @if($myTrades->count() > 0)
        <div class="row" id="myTradeListings">
          @foreach($myTrades as $trade)
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="trade-card">
              <div class="trade-card-image">
                @php
                  $displayImage = '';
                  if ($trade->image) {
                      if (is_string($trade->image) && json_decode($trade->image)) {
                          $images = json_decode($trade->image, true);
                          $displayImage = $images[0] ?? '';
                      } else {
                          $displayImage = $trade->image;
                      }
                  }
                @endphp
                <img src="{{ $displayImage ? asset('storage/' . $displayImage) : 'https://via.placeholder.com/400x300?text=No+Image' }}" 
                    alt="{{ $trade->name }}" 
                    loading="lazy"
                    style="width: 100%; height: 250px; object-fit: contain; background: #f8f9fa;"
                    onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                
                <!-- Status Badge -->
                <div class="trade-badge 
                  @if($trade->status === 'pending') badge-pending
                  @elseif($trade->status === 'approved') badge-approved
                  @elseif($trade->status === 'active') badge-active
                  @elseif($trade->status === 'rejected') badge-rejected
                  @elseif($trade->status === 'completed') badge-completed
                  @else badge-inactive @endif">
                  {{ ucfirst($trade->status) }}
                </div>
              </div>
              <div class="trade-card-body">
                <h4 class="trade-card-title">{{ $trade->name }}</h4>
                
                <div class="trade-card-meta">
                  <div class="meta-item">
                    <i class="fa fa-check-circle"></i>
                    <span>{{ ucfirst($trade->condition) }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="fa fa-cube"></i>
                    <span>{{ $trade->category }}</span>
                  </div>
                </div>

                <div class="trade-info-details">
                  <p class="trade-info-item">
                    <i class="fa fa-tag"></i>
                    <strong>Brand:</strong> {{ $trade->brand }}
                  </p>
                  
                  <p class="trade-info-item">
                    <i class="fa fa-map-marker"></i>
                    <strong>Location:</strong> 
                    @if($trade->location && trim($trade->location) !== '')
                      {{ $trade->location }}
                    @else
                      <span style="color: red; font-style: italic;">No location set</span>
                    @endif
                  </p>

                  <div class="trade-description-section">
                    <p class="description-label"><strong>Looking For:</strong></p>
                    <p class="trade-description">{{ $trade->description }}</p>
                  </div>

                  <div class="trade-preferences">
                    <i class="fa fa-handshake-o"></i>
                    <small>
                      @if($trade->trade_preferences && trim($trade->trade_preferences) !== '')
                        {{ $trade->trade_preferences }}
                      @else
                        <span style="color: red; font-style: italic;">No trade preferences set</span>
                      @endif
                    </small>
                  </div>
                </div>
              </div>
              <div class="trade-card-footer">
                @if($trade->status === 'approved')
                  <button type="button" class="btn btn-success btn-activate-trade" 
                          data-trade-id="{{ $trade->id }}"
                          data-trade-name="{{ $trade->name }}">
                    <i class="fa fa-play"></i> Activate Trade
                  </button>
                @elseif($trade->status === 'active')
                  <a href="{{ route('trading.show', $trade->id) }}" class="btn btn-trade">
                    <i class="fa fa-eye"></i> View Trade
                  </a>
                  <small class="text-success d-block mt-1">
                    <i class="fa fa-check-circle"></i> Live and visible to others
                  </small>
                @elseif($trade->status === 'completed')
                  <a href="{{ route('trading.show', $trade->id) }}" class="btn btn-trade">
                    <i class="fa fa-eye"></i> View Trade
                  </a>
                  <small class="text-info d-block mt-1">
                    <i class="fa fa-check-circle"></i> Trade Completed
                  </small>
                @elseif($trade->status === 'pending')
                  <small class="text-warning d-block">
                    <i class="fa fa-clock-o"></i> Waiting for admin approval
                  </small>
                @elseif($trade->status === 'rejected')
                  <small class="text-danger d-block">
                    <i class="fa fa-times-circle"></i> Trade was rejected
                  </small>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-4">
          <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
          <p class="text-muted">You haven't uploaded any trade items yet.</p>
        </div>
      @endif
    </div>
  </section>
  @endauth

  <!-- All Active Trades Section -->
  <section class="trade-listings-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Available Trade Items</h2>
        <p class="section-subtitle">Browse active trade listings from other collectors</p>
      </div>

      <div class="row" id="tradeListings">
        @php
          $activeTrades = \App\Models\Trade::where('status', 'active')
              ->orderBy('created_at', 'desc')
              ->paginate(12);
        @endphp

        @forelse($activeTrades as $trade)
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="trade-card">
            <div class="trade-card-image">
              @php
                // Handle both single image and multiple images
                $displayImage = '';
                if ($trade->image) {
                    if (is_string($trade->image) && json_decode($trade->image)) {
                        $images = json_decode($trade->image, true);
                        $displayImage = $images[0] ?? '';
                    } else {
                        $displayImage = $trade->image;
                    }
                }
              @endphp
              <img src="{{ $displayImage ? asset('storage/' . $displayImage) : 'https://via.placeholder.com/400x300?text=No+Image' }}" 
                  alt="{{ $trade->name }}" 
                  loading="lazy"
                  style="width: 100%; height: 250px; object-fit: contain; background: #f8f9fa;"
                  onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
              
              <!-- Status Badge -->
              <div class="trade-badge badge-active">Active</div>
              
              <!-- Owner Indicator -->
              @if(Auth::check() && Auth::id() === $trade->user_id)
                <div class="owner-indicator">
                  <i class="fa fa-user"></i> Your Listing
                </div>
              @endif
            </div>
            <div class="trade-card-body">
              <h4 class="trade-card-title">{{ $trade->name }}</h4>
              
              <div class="trade-card-meta">
                <div class="meta-item">
                  <i class="fa fa-check-circle"></i>
                  <span>{{ ucfirst($trade->condition) }}</span>
                </div>
                <div class="meta-item">
                  <i class="fa fa-cube"></i>
                  <span>{{ $trade->category }}</span>
                </div>
              </div>

              <div class="trade-info-details">
                <p class="trade-info-item">
                  <i class="fa fa-user"></i>
                  <strong>Uploaded by:</strong> 
                  @if(Auth::check() && Auth::id() === $trade->user_id)
                    <span class="text-primary">You</span>
                  @else
                    {{ $trade->user ? $trade->user->full_name : 'Unknown' }}
                  @endif
                </p>

                <p class="trade-info-item">
                  <i class="fa fa-tag"></i>
                  <strong>Brand:</strong> {{ $trade->brand }}
                </p>
                
                <p class="trade-info-item">
                  <i class="fa fa-map-marker"></i>
                  <strong>Location:</strong> 
                  @if($trade->location && trim($trade->location) !== '')
                    {{ $trade->location }}
                  @else
                    <span style="color: red; font-style: italic;">No location set</span>
                  @endif
                </p>

                <div class="trade-description-section">
                  <p class="description-label"><strong>Looking For:</strong></p>
                  <p class="trade-description">{{ $trade->description }}</p>
                </div>

                <div class="trade-preferences">
                  <i class="fa fa-handshake-o"></i>
                  <small>
                    @if($trade->trade_preferences && trim($trade->trade_preferences) !== '')
                      {{ $trade->trade_preferences }}
                    @else
                      <span style="color: red; font-style: italic;">No trade preferences set</span>
                    @endif
                  </small>
                </div>
              </div>
            </div>
            <div class="trade-card-footer">
              @if(Auth::check() && Auth::id() === $trade->user_id)
                <!-- For user's own listings in Available section -->
                <div class="owner-listing-message">
                  <i class="fa fa-user-circle text-primary"></i>
                  <span class="text-primary"><strong>This is your trade listing</strong></span>
                </div>
              @else
                <!-- For other users' listings - Only View Trade button -->
                <a href="{{ route('trading.show', $trade->id) }}" class="btn btn-trade w-100">
                    <i class="fa fa-eye"></i> View Trade Details
                </a>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
          <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
          <p class="text-muted">No active trade items available yet.</p>
          @auth
            <p class="text-muted">Be the first to upload a trade item!</p>
          @endauth
        </div>
        @endforelse
      </div>
      
      <!-- Pagination -->
      @if($activeTrades->hasPages())
      <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
          {{ $activeTrades->links() }}
        </div>
      </div>
      @endif
    </div>
  </section>
</main>

<!-- Upload Trade Modal -->
<div class="modal fade" id="uploadTradeModal" tabindex="-1" role="dialog" aria-labelledby="uploadTradeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadTradeModalLabel">Upload Trade Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="uploadTradeForm">
          
          <!-- Multiple Images Upload -->
          <div class="form-group">
            <label for="tradeImages">Item Images (Max 10) <span class="text-danger">*</span></label>
            <div class="image-upload-container" onclick="triggerImageUpload()">
              <input type="file" id="tradeImages" accept="image/*" multiple onchange="handleMultipleImageUpload(event)" required style="display: none;">
              <div class="image-preview-container" id="imagePreviewContainer">
                <div class="image-preview-placeholder">
                  <i class="fa fa-camera"></i>
                  <p>Click to upload images</p>
                  <small>Max 10 images</small>
                </div>
              </div>
            </div>
            <small class="form-text text-muted">Upload JPG, PNG, or GIF (Max 10MB each). You can select multiple images.</small>
          </div>

          <!-- Legal Documents Upload -->
          <div class="form-group">
            <label for="legalDocuments">Legal Documents <span class="text-danger">*</span></label>
            <div class="documents-upload-area" onclick="document.getElementById('legalDocuments').click()">
              <input type="file" id="legalDocuments" accept=".jpg,.jpeg,.png,.pdf" multiple onchange="handleDocumentsUpload(event)" style="display: none;" required>
              <i class="fa fa-file-text"></i>
              <p class="mb-0">Click to upload documents</p>
              <small class="text-muted">Valid ID, receipt, proof of ownership, etc.</small>
            </div>
            <div class="uploaded-files-list" id="uploadedFilesList"></div>
            <small class="form-text text-muted">Upload JPG, PNG, or PDF (Max 10MB per file)</small>
          </div>

          <!-- Item Name -->
          <div class="form-group">
            <label for="itemName">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="itemName" placeholder="Enter item name" required>
          </div>

          <!-- Category with enhanced wrapper -->
          <div class="form-group">
              <label for="itemCategory">Category <span class="text-danger">*</span></label>
              <div class="select-wrapper">
                  <select class="form-control" id="itemCategory" required>
                      <option value="">Select a category</option>
                      <option value="action-figures">Action Figures</option>
                      <option value="dolls-plushies">Dolls & Plushies</option>
                      <option value="building-sets">Building Sets</option>
                      <option value="vehicles">Vehicles</option>
                      <option value="board-games">Board Games</option>
                      <option value="educational-toys">Educational Toys</option>
                      <option value="outdoor-toys">Outdoor Toys</option>
                      <option value="collectibles">Collectibles</option>
                      <option value="electronic-toys">Electronic Toys</option>
                      <option value="other">Other</option>
                  </select>
              </div>
          </div>

          <!-- Brand -->
          <div class="form-group">
            <label for="itemBrand">Brand <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="itemBrand" placeholder="e.g., Hasbro, Mattel, LEGO, etc." required>
          </div>

          <!-- Location -->
          <div class="form-group">
            <label for="itemLocation">Location <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="itemLocation" placeholder="City, Country" required>
          </div>

          <!-- Condition -->
          <div class="form-group">
            <label for="itemCondition">Condition <span class="text-danger">*</span></label>
            <select class="form-control" id="itemCondition" required>
              <option value="">Select condition</option>
              <option value="sealed">Sealed</option>
              <option value="back-in-box">Back in Box</option>
              <option value="loose">Loose</option>
            </select>
          </div>

          <!-- Looking For -->
          <div class="form-group">
            <label for="lookingFor">Looking For... <span class="text-danger">*</span></label>
            <textarea class="form-control" id="lookingFor" rows="3" placeholder="Describe what you're looking for in exchange..." required></textarea>
          </div>

          <!-- Trade Preference -->
          <div class="form-group">
            <label>Trade Preference <span class="text-danger">*</span></label>
            <div class="checkbox-group">
              <div class="checkbox-item">
                <input type="checkbox" id="tradeOnly" name="tradePreference" value="trade-only">
                <label for="tradeOnly">Trade Only</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="tradeAddCash" name="tradePreference" value="trade-add-cash">
                <label for="tradeAddCash">Trade + Add Cash</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" id="openNegotiation" name="tradePreference" value="open-negotiation">
                <label for="openNegotiation">Open to Negotiation</label>
              </div>
            </div>
            <small class="form-text text-muted">Select at least one trade preference</small>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="submitTrade()">Upload Trade</button>
      </div>
    </div>
  </div>
</div>

<!-- Image Cropper Modal -->
<div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cropperModalLabel">Crop Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="img-container">
          <img id="cropperImage" style="max-width: 100%;">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="cropImage()">Crop & Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Activation Confirmation Modal -->
<div class="modal fade" id="activateTradeModal" tabindex="-1" role="dialog" aria-labelledby="activateTradeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="activateTradeModalLabel">Activate Trade</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="activateTradeMessage">Are you sure you want to activate this trade? It will become visible to other users.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmActivateTrade">Activate</button>
      </div>
    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
// CSRF Token Setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let uploadedImages = [];
let uploadedDocuments = [];
let currentTradeId = null;

// Trigger image upload
function triggerImageUpload() {
    document.getElementById('tradeImages').click();
}

// Handle multiple image upload
function handleMultipleImageUpload(event) {
    const files = Array.from(event.target.files);
    
    // Validate number of images
    if (uploadedImages.length + files.length > 10) {
        alert('Maximum 10 images allowed. You already have ' + uploadedImages.length + ' images.');
        event.target.value = '';
        return;
    }
    
    files.forEach(file => {
        // Check file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert(`File ${file.name} is too large. Max size is 10MB`);
            return;
        }
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert(`File ${file.name} has invalid format. Only JPG, PNG, and GIF are allowed.`);
            return;
        }
        
        uploadedImages.push(file);
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            
            // Remove placeholder if it exists
            const placeholder = previewContainer.querySelector('.image-preview-placeholder');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            const previewDiv = document.createElement('div');
            previewDiv.className = 'preview-image-container';
            previewDiv.style.position = 'relative';
            previewDiv.style.display = 'inline-block';
            
            previewDiv.innerHTML = `
                <img src="${e.target.result}" class="preview-image" alt="Preview">
                <div class="image-counter">${uploadedImages.length}</div>
                <button type="button" class="remove-preview-btn" onclick="removePreviewImage(${uploadedImages.length - 1})">
                    <i class="fa fa-times"></i>
                </button>
            `;
            
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });
    
    // Reset file input
    event.target.value = '';
}

// Remove preview image
function removePreviewImage(index) {
    uploadedImages.splice(index, 1);
    updateImagePreviews();
}

// Update image previews
function updateImagePreviews() {
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = '';
    
    if (uploadedImages.length === 0) {
        previewContainer.innerHTML = `
            <div class="image-preview-placeholder">
                <i class="fa fa-camera"></i>
                <p>Click to upload images</p>
                <small>Max 10 images</small>
            </div>
        `;
        return;
    }
    
    uploadedImages.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'preview-image-container';
            previewDiv.style.position = 'relative';
            previewDiv.style.display = 'inline-block';
            
            previewDiv.innerHTML = `
                <img src="${e.target.result}" class="preview-image" alt="Preview">
                <div class="image-counter">${index + 1}</div>
                <button type="button" class="remove-preview-btn" onclick="removePreviewImage(${index})">
                    <i class="fa fa-times"></i>
                </button>
            `;
            
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });
}

// Handle documents upload
function handleDocumentsUpload(event) {
    const files = Array.from(event.target.files);
    
    files.forEach(file => {
        // Check file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert(`File ${file.name} is too large. Max size is 10MB`);
            return;
        }
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            alert(`File ${file.name} has invalid format. Only JPG, PNG, and PDF are allowed.`);
            return;
        }
        
        uploadedDocuments.push(file);
    });
    
    displayUploadedFiles();
}

// Display uploaded files
function displayUploadedFiles() {
    const filesList = document.getElementById('uploadedFilesList');
    filesList.innerHTML = '';
    
    uploadedDocuments.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        
        let icon = 'fa-file';
        if (file.type === 'application/pdf') {
            icon = 'fa-file-pdf-o';
        } else if (file.type.startsWith('image/')) {
            icon = 'fa-file-image-o';
        }
        
        fileItem.innerHTML = `
            <span><i class="fa ${icon}"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
            <i class="fa fa-times remove-file" onclick="removeDocument(${index})"></i>
        `;
        
        filesList.appendChild(fileItem);
    });
}

// Remove document
function removeDocument(index) {
    uploadedDocuments.splice(index, 1);
    displayUploadedFiles();
}

// Submit trade with AJAX
function submitTrade() {
    const itemName = document.getElementById('itemName').value;
    const category = document.getElementById('itemCategory').value;
    const brand = document.getElementById('itemBrand').value;
    const location = document.getElementById('itemLocation').value;
    const condition = document.getElementById('itemCondition').value;
    const lookingFor = document.getElementById('lookingFor').value;
    
    // Get trade preferences
    const tradePreferences = [];
    document.querySelectorAll('input[name="tradePreference"]:checked').forEach(checkbox => {
        // Convert values to readable format
        let preferenceText = '';
        switch(checkbox.value) {
            case 'trade-only':
                preferenceText = 'Trade Only';
                break;
            case 'trade-add-cash':
                preferenceText = 'Trade + Add Cash';
                break;
            case 'open-negotiation':
                preferenceText = 'Open to Negotiation';
                break;
            default:
                preferenceText = checkbox.value;
        }
        tradePreferences.push(preferenceText);
    });

    // Validation
    if (!itemName || !category || !brand || !location || !condition || !lookingFor) {
        alert('Please fill in all required fields');
        return;
    }

    if (uploadedImages.length === 0) {
        alert('Please upload at least one item image');
        return;
    }

    if (uploadedImages.length > 10) {
        alert('Maximum 10 images allowed');
        return;
    }

    if (uploadedDocuments.length === 0) {
        alert('Please upload at least one legal document');
        return;
    }

    if (tradePreferences.length === 0) {
        alert('Please select at least one trade preference');
        return;
    }

    // Create FormData
    const formData = new FormData();
    formData.append('name', itemName);
    formData.append('brand', brand);
    formData.append('category', category);
    formData.append('condition', condition);
    formData.append('description', lookingFor);
    formData.append('location', location);
    formData.append('trade_preferences', tradePreferences.join(', '));
    
    // Append multiple images
    uploadedImages.forEach((image, index) => {
        formData.append('images[]', image);
    });
    
    // Append documents
    uploadedDocuments.forEach((doc, index) => {
        formData.append(`documents[]`, doc);
    });

    // Show loading
    const modalBody = $('#uploadTradeModal .modal-body');
    modalBody.append('<div class="text-center" id="loadingSpinner"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Uploading...</p></div>');
    
    // Disable button
    const submitBtn = $('button[onclick="submitTrade()"]');
    submitBtn.prop('disabled', true);

    // AJAX Request
    $.ajax({
        url: '{{ route("trading.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#loadingSpinner').remove();
            submitBtn.prop('disabled', false);
            
            alert('Trade submitted for admin approval! Your trade will be visible to other users once approved and activated.');
            
            // Reset form
            resetForm();
            
            // Close modal
            $('#uploadTradeModal').modal('hide');
            
            // Reload page to show new trade in "My Trades"
            location.reload();
        },
        error: function(xhr) {
            $('#loadingSpinner').remove();
            submitBtn.prop('disabled', false);
            
            let errorMsg = 'Error uploading trade item.\n\n';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg += Object.values(xhr.responseJSON.errors).flat().join('\n');
                console.log('Validation errors:', xhr.responseJSON.errors);
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg += xhr.responseJSON.message;
            } else {
                errorMsg += 'Please check all fields and try again.';
            }
            alert(errorMsg);
            console.error('Upload error:', xhr);
        }
    });
}

// Reset form
function resetForm() {
    document.getElementById('uploadTradeForm').reset();
    document.getElementById('imagePreviewContainer').innerHTML = `
        <div class="image-preview-placeholder">
            <i class="fa fa-camera"></i>
            <p>Click to upload images</p>
            <small>Max 10 images</small>
        </div>
    `;
    document.getElementById('uploadedFilesList').innerHTML = '';
    uploadedImages = [];
    uploadedDocuments = [];
}

// Search functionality
function performTradeSearch() {
    const searchTerm = document.getElementById('tradeSearchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.trade-card');
    let foundCount = 0;
    
    cards.forEach(card => {
        const title = card.querySelector('.trade-card-title').textContent.toLowerCase();
        const description = card.querySelector('.trade-description').textContent.toLowerCase();
        const parentCol = card.closest('.col-sm-6');
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            parentCol.style.display = 'block';
            foundCount++;
        } else {
            parentCol.style.display = 'none';
        }
    });
    
    if (foundCount === 0 && searchTerm !== '') {
        alert('No results found for: ' + searchTerm);
    }
}

// Allow Enter key to trigger search
document.getElementById('tradeSearchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performTradeSearch();
    }
});

// Reset form when modal is closed
$('#uploadTradeModal').on('hidden.bs.modal', function () {
    resetForm();
    $('#loadingSpinner').remove();
});

// Trade activation functionality
$('.btn-activate-trade').on('click', function() {
    currentTradeId = $(this).data('trade-id');
    const tradeName = $(this).data('trade-name');
    
    $('#activateTradeMessage').text(`Are you sure you want to activate "${tradeName}"? It will become visible to other users.`);
    $('#activateTradeModal').modal('show');
});

$('#confirmActivateTrade').on('click', function() {
    if (currentTradeId) {
        activateTrade(currentTradeId);
    }
});

function activateTrade(tradeId) {
    $.ajax({
        url: `/trading/${tradeId}/activate`, // Use customer route, not admin route
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#activateTradeModal').modal('hide');
                alert('Trade activated successfully! It is now visible to other users.');
                location.reload();
            } else {
                alert('Error activating trade: ' + response.message);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error activating trade. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}

// Trade completion functionality (removed from My Trade Items section)
// This function is kept for potential use in customer chat interface
function completeTrade(tradeId) {
    $.ajax({
        url: `/trading/${tradeId}/complete`,
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

// Trade approval functionality (for admin)
function approveTrade(tradeId) {
    $.ajax({
        url: `/admin/trading-management/${tradeId}/approve`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Trade approved successfully!');
                location.reload();
            } else {
                alert('Error approving trade: ' + response.message);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error approving trade. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}

// Trade rejection functionality (for admin)
function rejectTrade(tradeId) {
    $.ajax({
        url: `/admin/trading-management/${tradeId}/reject`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Trade rejected successfully!');
                location.reload();
            } else {
                alert('Error rejecting trade: ' + response.message);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error rejecting trade. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}
</script>
@endpush