@extends('customer.layouts.cmaster')

@section('title', 'Exchange Proposal | Toyspace')

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="{{ asset('css/cexchange.css') }}" rel="stylesheet">
<link id="color-scheme" href="{{ asset('assets/css/colors/default.css') }}" rel="stylesheet">
<style>
:root {
    --primary-dark: #1a365d;
    --secondary-dark: #2d3748;
    --accent-dark: #4a5568;
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #f56565;
    --light-bg: #f7fafc;
    --border-color: #e2e8f0;
    --text-dark: #2d3748;
    --text-light: #718096;
    --success-color: #48bb78;
    --warning-color: #ed8936;
}

.exchange-container {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
    min-height: 100vh;
    padding: 40px 0;
}

.exchange-form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    border: none;
}

.exchange-header {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
    color: white;
    padding: 30px;
    position: relative;
}

.back-btn {
    color: white;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 16px;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.back-btn:hover {
    color: white;
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(-5px);
}

.exchange-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    color: white;
}

.exchange-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    margin-bottom: 0;
}

.trade-they-want {
    background: var(--light-bg);
    border-radius: 15px;
    padding: 25px;
    margin: 25px;
    border-left: 4px solid var(--primary-color);
}

.trade-they-want h4 {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.trade-they-want h4 i {
    color: var(--accent-color);
}

.trade-preview {
    display: flex;
    align-items: center;
    gap: 20px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.trade-preview:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.trade-preview-img {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.trade-preview-details h5 {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 8px;
}

.trade-preview-details p {
    color: var(--text-light);
    margin-bottom: 4px;
    font-size: 14px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    background: linear-gradient(135deg, var(--light-bg) 0%, #fff 100%);
    padding: 20px 25px;
    margin: 0 25px 25px;
    border-radius: 15px;
    border: 2px solid var(--border-color);
}

.user-avatar-large {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    border: 3px solid white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.user-details h4 {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 4px;
}

.user-details p {
    color: var(--text-light);
    margin-bottom: 0;
    font-size: 14px;
}

.form-section {
    padding: 0 25px 25px;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 20px;
}

.section-subtitle {
    color: var(--text-light);
    margin-bottom: 20px;
    font-size: 14px;
}

.form-label {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 12px;
    display: block;
    font-size: 16px;
}

.form-label i {
    color: var(--primary-color);
    margin-right: 8px;
}

.photo-count {
    background: var(--accent-color);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 10px;
}

.label-hint {
    color: var(--text-light);
    font-size: 12px;
    margin-left: 10px;
    font-weight: 400;
}

.photo-upload-container {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 20px;
    background: var(--light-bg);
    transition: all 0.3s ease;
}

.photo-upload-container:hover {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
}

.photo-upload-box {
    text-align: center;
    padding: 40px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 8px;
    background: white;
}

.photo-upload-box:hover {
    transform: scale(1.02);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.photo-upload-box i {
    font-size: 48px;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.photo-upload-box p {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 8px;
}

.photo-upload-box small {
    color: var(--text-light);
}

.photo-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.photo-preview-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.photo-preview-item:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.photo-preview-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.photo-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: var(--accent-color);
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

.photo-remove-btn:hover {
    background: #e53e3e;
    transform: scale(1.1);
}

.documents-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    background: var(--light-bg);
    transition: all 0.3s ease;
}

.documents-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(102, 126, 234, 0.05);
    transform: translateY(-2px);
}

.documents-upload-area i {
    font-size: 48px;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.uploaded-files-list {
    margin-top: 15px;
}

.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.file-item:hover {
    border-color: var(--primary-color);
    transform: translateX(5px);
}

.file-item i.fa-file-pdf-o {
    color: #e53e3e;
}

.file-item i.fa-file-image-o {
    color: var(--success-color);
}

.file-item i.fa-file {
    color: var(--text-light);
}

.remove-file {
    color: var(--accent-color);
    cursor: pointer;
    transition: all 0.3s ease;
}

.remove-file:hover {
    color: #e53e3e;
    transform: scale(1.2);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    border: 2px solid var(--border-color);
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
}

.char-count {
    color: var(--text-light);
    font-size: 12px;
    text-align: right;
    display: block;
    margin-top: 5px;
}

.delivery-options {
    display: grid;
    gap: 15px;
}

.delivery-option {
    background: var(--light-bg);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    padding: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.delivery-option:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.delivery-option input[type="radio"] {
    margin-right: 10px;
}

.delivery-option label {
    color: var(--primary-dark);
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 0;
}

.meetup-location-container {
    background: linear-gradient(135deg, var(--light-bg) 0%, #fff 100%);
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
    border-left: 4px solid var(--warning-color);
}

.meetup-location-container h4 {
    color: var(--primary-dark);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.meetup-location-container h4 i {
    color: var(--warning-color);
}

.hidden {
    display: none;
}

.form-actions {
    padding: 25px;
    background: var(--light-bg);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.btn {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-secondary {
    background: var(--accent-dark);
    color: white;
}

.btn-secondary:hover {
    background: var(--secondary-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(45, 55, 72, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-lg {
    padding: 15px 40px;
    font-size: 16px;
}

.preview-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    border: none;
}

.sticky-preview {
    position: sticky;
    top: 100px;
}

.preview-header {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
    color: white;
    padding: 25px;
    text-align: center;
}

.preview-header h3 {
    font-weight: 700;
    margin-bottom: 5px;
    color: white;
}

.preview-header p {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0;
}

.preview-content {
    padding: 0;
}

.preview-image-placeholder {
    height: 200px;
    background: linear-gradient(135deg, var(--light-bg) 0%, #fff 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    border-bottom: 1px solid var(--border-color);
}

.preview-image-placeholder i {
    font-size: 48px;
    margin-bottom: 15px;
    color: var(--primary-color);
}

.preview-image-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-details {
    padding: 25px;
}

.preview-title {
    color: var(--primary-dark);
    font-weight: 700;
    margin-bottom: 15px;
    font-size: 20px;
}

.preview-meta {
    display: grid;
    gap: 10px;
    margin-bottom: 15px;
}

.preview-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-light);
    font-size: 14px;
}

.preview-meta-item i {
    color: var(--success-color);
    width: 16px;
}

.preview-brand {
    background: var(--light-bg);
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid var(--primary-color);
}

.preview-brand strong {
    color: var(--primary-dark);
}

.preview-cash-amount {
    background: linear-gradient(135deg, #fed7d7 0%, #fff5f5 100%);
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid var(--accent-color);
}

.preview-cash-amount strong {
    color: var(--primary-dark);
}

.preview-description {
    background: var(--light-bg);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
}

.preview-description p {
    color: var(--text-dark);
    line-height: 1.6;
    margin-bottom: 0;
}

.preview-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    border: 2px solid white;
    box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
}

.user-name {
    color: var(--primary-dark);
    font-weight: 600;
}

.text-danger {
    color: var(--accent-color) !important;
}

.text-muted {
    color: var(--text-light) !important;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 20px;
}

.alert-danger {
    background: linear-gradient(135deg, #fed7d7 0%, #fff5f5 100%);
    color: var(--primary-dark);
    border-left: 4px solid var(--accent-color);
}

@media (max-width: 768px) {
    .exchange-container {
        padding: 20px 0;
    }
    
    .exchange-header {
        padding: 20px;
    }
    
    .exchange-title {
        font-size: 24px;
    }
    
    .trade-preview {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .sticky-preview {
        position: static;
        margin-top: 30px;
    }
}
</style>
@endpush

@section('content')
@php
    // Temporary debugging - check if trade exists
    if (!isset($trade)) {
        // Try to get trade from URL parameter
        $tradeId = request('tradeId') ?? request('trade_id');
        if ($tradeId) {
            $trade = \App\Models\Trade::with('user')->find($tradeId);
        }
    }
@endphp

@if(!isset($trade) || !$trade)
    <div class="exchange-container">
        <div class="container">
            <div class="alert alert-danger text-center">
                <h4>Trade Not Found</h4>
                <p>Unable to load the trade item. Please go back and select a valid trade.</p>
                <a href="{{ route('customer.trading') }}" class="btn btn-primary">Back to Trades</a>
            </div>
        </div>
    </div>
@elseif($trade->status === 'completed')
    <div class="exchange-container">
        <div class="container">
            <div class="alert alert-danger text-center">
                <h4>Trade Completed</h4>
                <p>This trade has been marked as completed and is no longer available for exchange.</p>
                <a href="{{ route('customer.trading') }}" class="btn btn-primary">Back to Trades</a>
            </div>
        </div>
    </div>
@else
<main>
  <div class="exchange-container">
    <div class="container">
      <div class="row">
        <!-- Left Side - Form -->
        <div class="col-md-7">
          <div class="exchange-form-card">
            <div class="exchange-header">
              <a href="{{ route('trading.show', $trade->id) }}" class="back-btn">
                <i class="fa fa-arrow-left"></i> Back
              </a>
              <h2 class="exchange-title">Exchange Proposal</h2>
              <p class="exchange-subtitle">Propose your item for exchange</p>
            </div>

            <!-- Trade They Want -->
            <div class="trade-they-want">
              <h4><i class="fa fa-star"></i> Item You Want to Exchange For:</h4>
              <div class="trade-preview">
                @php
                  // Get the first image from the trade
                  $tradeImage = null;
                  
                  // Check if images_array exists and has items
                  if (!empty($trade->images_array) && is_array($trade->images_array)) {
                    $tradeImage = $trade->images_array[0];
                  }
                  // Fallback to single image field
                  elseif (!empty($trade->image)) {
                    $tradeImage = asset('storage/' . $trade->image);
                  }
                  // Default placeholder
                  else {
                    $tradeImage = 'https://via.placeholder.com/80x80?text=No+Image';
                  }
                @endphp
                
                <img src="{{ $tradeImage }}" 
                    alt="{{ $trade->name }}" 
                    class="trade-preview-img"
                    onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                
                <div class="trade-preview-details">
                  <h5>{{ $trade->name }}</h5>
                  <p><strong>Owner:</strong> {{ $trade->user ? ($trade->user->first_name . ' ' . $trade->user->last_name) : 'Unknown' }}</p>
                  <p><strong>Brand:</strong> {{ $trade->brand }} | <strong>Condition:</strong> {{ ucfirst($trade->condition) }}</p>
                </div>
              </div>
            </div>

            <!-- Current User Info - FIXED WITH NULL SAFETY -->
            <div class="user-info">
              <div class="user-avatar-large">
                {{ substr(Auth::user()->first_name ?? 'U', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'S', 0, 1) }}
              </div>
              <div class="user-details">
                <h4>{{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</h4>
                <p>Your Exchange Proposal</p>
              </div>
            </div>

            <form id="exchangeProposalForm" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="receiverTradeId" name="receiver_trade_id" value="{{ $trade->id }}">

              <!-- Photo Upload Section -->
              <div class="form-section">
                <label class="form-label">
                  <i class="fa fa-camera"></i> Photos <span class="text-danger">*</span>
                  <span class="photo-count">0 / 10</span>
                  <span class="label-hint">- You can add up to 10 photos</span>
                </label>
                <div class="photo-upload-container">
                  <div class="photo-upload-box" onclick="document.getElementById('photoInput').click()">
                    <i class="fa fa-plus-circle"></i>
                    <p>Add photos</p>
                    <small>or drag and drop</small>
                  </div>
                  <input type="file" id="photoInput" name="proposed_item_images[]" multiple accept="image/*" style="display: none;" onchange="handlePhotoUpload(event)">
                  <div id="photoPreviewContainer" class="photo-preview-grid"></div>
                </div>
              </div>

              <!-- Legal Documents Upload -->
              <div class="form-section">
                <label class="form-label">
                  <i class="fa fa-file-text"></i> Legal Documents <span class="text-danger">*</span>
                </label>
                <div class="documents-upload-area" onclick="document.getElementById('legalDocuments').click()">
                  <input type="file" id="legalDocuments" name="proposed_item_documents[]" accept=".jpg,.jpeg,.png,.pdf" multiple onchange="handleDocumentsUpload(event)" style="display: none;">
                  <i class="fa fa-file-text"></i>
                  <p class="mb-0">Click to upload documents</p>
                  <small class="text-muted">Valid ID, receipt, proof of ownership, etc.</small>
                </div>
                <div class="uploaded-files-list" id="uploadedFilesList"></div>
                <small class="form-text text-muted">Upload JPG, PNG, or PDF (Max 10MB per file)</small>
              </div>

              <!-- Required Section -->
              <div class="form-section">
                <h3 class="section-title">Required Information</h3>
                <p class="section-subtitle">Be as descriptive as possible</p>

                <div class="form-group">
                  <label for="itemName">Item Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="itemName" name="proposed_item_name" placeholder="What are you offering?" required>
                </div>

                <div class="form-group">
                  <label for="itemCategory">Category <span class="text-danger">*</span></label>
                  <select class="form-control" id="itemCategory" name="proposed_item_category" required>
                    <option value="">Select category</option>
                    <option value="action-figures">Action Figures</option>
                    <option value="dolls">Dolls</option>
                    <option value="vehicles">Vehicles</option>
                    <option value="building-sets">Building Sets</option>
                    <option value="educational">Educational Toys</option>
                    <option value="electronic">Electronic Toys</option>
                    <option value="outdoor">Outdoor Toys</option>
                    <option value="collectibles">Collectibles</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="itemBrand">Brand <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="itemBrand" name="proposed_item_brand" placeholder="Enter brand name" required>
                </div>

                <div class="form-group">
                  <label for="itemLocation">Location <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="itemLocation" name="proposed_item_location" placeholder="City, Country" required>
                </div>

                <div class="form-group">
                  <label for="itemCondition">Condition <span class="text-danger">*</span></label>
                  <select class="form-control" id="itemCondition" name="proposed_item_condition" required>
                    <option value="">Select condition</option>
                    <option value="sealed">Sealed</option>
                    <option value="back-in-box">Back in Box</option>
                    <option value="loose">Loose</option>
                  </select>
                </div>

                <!-- Add Cash Amount Field -->
                <div class="form-group">
                  <label for="cashAmount">Add cash amount (optional)</label>
                  <input type="number" class="form-control" id="cashAmount" name="cash_amount" placeholder="Enter amount if adding cash to trade" min="0" step="0.01">
                  <small class="form-text text-muted">Leave blank for item-to-item exchange only</small>
                </div>
              </div>

              <!-- Description Section -->
              <div class="form-section">
                <h3 class="section-title">Description</h3>
                <div class="form-group">
                  <label for="itemDescription">Describe your item <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="itemDescription" name="proposed_item_description" rows="6" placeholder="Describe your item in detail..." required></textarea>
                  <small class="char-count">0 / 500 characters</small>
                </div>

                <div class="form-group">
                  <label for="proposalMessage">Message to owner (optional)</label>
                  <textarea class="form-control" id="proposalMessage" name="message" rows="3" placeholder="Add a personal message..."></textarea>
                </div>
              </div>

              <!-- Delivery Method -->
              <div class="form-section">
                <h3 class="section-title">Delivery Method</h3>
                <div class="delivery-options">
                  <div class="delivery-option">
                    <input type="radio" id="cashOnDelivery" name="delivery_method" value="cashOnDelivery" required>
                    <label for="cashOnDelivery">Cash On Delivery</label>
                  </div>
                  <div class="delivery-option">
                    <input type="radio" id="meetupOnly" name="delivery_method" value="meetupOnly" required>
                    <label for="meetupOnly">Meet-up Only (No shipping) - Please specify location</label>
                  </div>
                </div>

                <!-- Meetup Location Container (Hidden by default) -->
                <div id="meetupLocationContainer" class="meetup-location-container hidden">
                  <h4 style="margin-bottom: 15px;">
                    <i class="fa fa-map-marker"></i> Meet-up Location
                  </h4>
                  <div class="form-group">
                    <label for="meetupLocationInput">Specify meet-up location</label>
                    <input type="text" class="form-control" id="meetupLocationInput" name="meetup_location" placeholder="Enter meet-up location (e.g., SM Mall of Asia, Pasay City)">
                    <small class="form-text text-muted">Please provide specific details about where you'd like to meet</small>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                  <i class="fa fa-exchange"></i> Submit Proposal
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Right Side - Preview -->
        <div class="col-md-5">
          <div class="preview-card sticky-preview">
            <div class="preview-header">
              <h3>Preview</h3>
              <p>How your proposal will appear</p>
            </div>

            <div class="preview-content">
              <div class="preview-image-placeholder" id="previewImage">
                <i class="fa fa-image"></i>
                <p>Your photos will appear here</p>
              </div>

              <div class="preview-details">
                <h4 class="preview-title" id="previewTitle">Item Name</h4>
                
                <div class="preview-meta">
                  <div class="preview-meta-item">
                    <i class="fa fa-check-circle"></i>
                    <span id="previewCondition">Condition</span>
                  </div>
                  <div class="preview-meta-item">
                    <i class="fa fa-cube"></i>
                    <span id="previewCategory">Category</span>
                  </div>
                  <div class="preview-meta-item">
                    <i class="fa fa-map-marker"></i>
                    <span id="previewLocation">Location</span>
                  </div>
                </div>

                <div class="preview-brand">
                  <strong>Brand:</strong> <span id="previewBrand">Brand name</span>
                </div>

                <div class="preview-cash-amount" id="previewCashAmount" style="display: none;">
                  <strong>Cash Amount:</strong> ₱<span id="previewCashValue">0.00</span>
                </div>

                <div class="preview-description">
                  <p id="previewDesc">Description will appear here</p>
                </div>

                <!-- Preview User - FIXED WITH NULL SAFETY -->
                <div class="preview-user">
                  <div class="user-avatar">
                    {{ substr(Auth::user()->first_name ?? 'U', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'S', 0, 1) }}
                  </div>
                  <span class="user-name">{{ Auth::user()->first_name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Prevent textrotator errors
    if (typeof $.fn.textrotator === 'undefined') {
        $.fn.textrotator = function() {
            return this;
        };
    }

    let uploadedPhotos = [];
    let uploadedDocuments = [];
    let uploadedDocumentFiles = [];
    let uploadedPhotoFiles = [];

    // Toggle meetup location container
    document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const container = document.getElementById('meetupLocationContainer');
        if (this.value === 'meetupOnly' && this.checked) {
          container.classList.remove('hidden');
        } else {
          container.classList.add('hidden');
        }
      });
    });

    // Handle photo upload
    function handlePhotoUpload(event) {
      const files = Array.from(event.target.files);
      
      if (uploadedPhotos.length + files.length > 10) {
        alert('You can only upload up to 10 photos');
        return;
      }

      files.forEach(file => {
        if (file.type.startsWith('image/')) {
          // Store the actual file
          uploadedPhotoFiles.push(file);
          
          // Create preview
          const reader = new FileReader();
          reader.onload = function(e) {
            uploadedPhotos.push(e.target.result);
            updatePhotoPreview();
            updatePhotoCount();
          }
          reader.readAsDataURL(file);
        }
      });
    }

    // Update photo preview
    function updatePhotoPreview() {
      const container = document.getElementById('photoPreviewContainer');
      container.innerHTML = '';

      uploadedPhotos.forEach((photo, index) => {
        const photoDiv = document.createElement('div');
        photoDiv.className = 'photo-preview-item';
        photoDiv.innerHTML = `
          <img src="${photo}" alt="Photo ${index + 1}">
          <button type="button" class="photo-remove-btn" onclick="removePhoto(${index})">
            <i class="fa fa-times"></i>
          </button>
        `;
        container.appendChild(photoDiv);
      });

      // Update main preview
      if (uploadedPhotos.length > 0) {
        document.getElementById('previewImage').innerHTML = `<img src="${uploadedPhotos[0]}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">`;
      }
    }

    // Remove photo
    function removePhoto(index) {
      uploadedPhotos.splice(index, 1);
      uploadedPhotoFiles.splice(index, 1);
      updatePhotoPreview();
      updatePhotoCount();
    }

    // Update photo count
    function updatePhotoCount() {
      document.querySelector('.photo-count').textContent = `${uploadedPhotos.length} / 10`;
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
        
        uploadedDocumentFiles.push(file);
      });
      
      displayUploadedFiles();
    }

    // Display uploaded files
    function displayUploadedFiles() {
      const filesList = document.getElementById('uploadedFilesList');
      filesList.innerHTML = '';
      
      uploadedDocumentFiles.forEach((file, index) => {
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
      uploadedDocumentFiles.splice(index, 1);
      displayUploadedFiles();
    }

    // Real-time preview updates
    document.getElementById('itemName').addEventListener('input', function() {
      document.getElementById('previewTitle').textContent = this.value || 'Item Name';
    });

    document.getElementById('itemCategory').addEventListener('change', function() {
      const text = this.options[this.selectedIndex].text;
      document.getElementById('previewCategory').textContent = text || 'Category';
    });

    document.getElementById('itemBrand').addEventListener('input', function() {
      document.getElementById('previewBrand').textContent = this.value || 'Brand name';
    });

    document.getElementById('itemLocation').addEventListener('input', function() {
      document.getElementById('previewLocation').textContent = this.value || 'Location';
    });

    document.getElementById('itemCondition').addEventListener('change', function() {
      const text = this.options[this.selectedIndex].text;
      document.getElementById('previewCondition').textContent = text || 'Condition';
    });

    document.getElementById('cashAmount').addEventListener('input', function() {
      const cashAmount = this.value;
      const cashPreview = document.getElementById('previewCashAmount');
      const cashValue = document.getElementById('previewCashValue');
      
      if (cashAmount && parseFloat(cashAmount) > 0) {
        cashValue.textContent = parseFloat(cashAmount).toFixed(2);
        cashPreview.style.display = 'block';
      } else {
        cashPreview.style.display = 'none';
      }
    });

    document.getElementById('itemDescription').addEventListener('input', function() {
      const charCount = this.value.length;
      document.querySelector('.char-count').textContent = `${charCount} / 500 characters`;
      document.getElementById('previewDesc').textContent = this.value || 'Description will appear here';
    });

    // Form submission - SIMPLIFIED AND WORKING VERSION
    document.getElementById('exchangeProposalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted - starting validation');

        // Basic validation
        if (uploadedPhotoFiles.length === 0) {
            alert('Please upload at least one photo of your item');
            return;
        }

        if (uploadedDocumentFiles.length === 0) {
            alert('Please upload at least one legal document');
            return;
        }

        const itemName = document.getElementById('itemName').value;
        const category = document.getElementById('itemCategory').value;
        const brand = document.getElementById('itemBrand').value;
        const location = document.getElementById('itemLocation').value;
        const condition = document.getElementById('itemCondition').value;
        const description = document.getElementById('itemDescription').value;

        if (!itemName || !category || !brand || !location || !condition || !description) {
            alert('Please fill in all required fields');
            return;
        }

        // Check if delivery method is selected
        const deliveryMethodSelected = document.querySelector('input[name="delivery_method"]:checked');
        if (!deliveryMethodSelected) {
            alert('Please select a delivery method');
            return;
        }

        // Check if meetup is selected and location is specified
        if (deliveryMethodSelected.value === 'meetupOnly') {
            const meetupLocation = document.getElementById('meetupLocationInput').value;
            if (!meetupLocation) {
                alert('Please specify a meet-up location');
                return;
            }
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

        // Create FormData
        const formData = new FormData(this);

        // Add files to FormData
        uploadedPhotoFiles.forEach((file, index) => {
            formData.append('proposed_item_images[]', file);
        });

        uploadedDocumentFiles.forEach((file, index) => {
            formData.append('proposed_item_documents[]', file);
        });

        console.log('Sending form data...');

        // Send AJAX request
        fetch('{{ route("trading.proposals.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            console.log('Response received:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Success data:', data);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            if (data.success) {
                alert('Exchange proposal sent successfully! A chat has been started with the trade owner.');
                
                // Redirect based on response
                if (data.conversation_id) {
                    window.location.href = '{{ route("customer.chat") }}?conversation=' + data.conversation_id;
                } else {
                    window.location.href = '{{ route("trading.proposals.sent") }}';
                }
            } else {
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert('Error sending proposal. Please check your connection and try again.');
        });
    });

    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Exchange proposal form loaded');
        // Add any additional initialization here
    });
</script>
@endpush