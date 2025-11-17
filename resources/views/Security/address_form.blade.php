{{-- resources/views/Security/address_form.blade.php --}}
@extends('customer.layouts.cmaster')

@section('title', 'Manage Addresses - Toy Collectible Platform')

@section('content')
<!-- Add Font Awesome CDN if not already in your layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="main">
    <section class="module">
        <div class="container">
            <div class="address-wrapper">
                <!-- Page Header -->
                <div class="page-header text-center mb-5">
                    <div class="header-avatar">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h2 class="module-title font-alt mt-3">Manage Addresses</h2>
                    <p class="text-muted">Select and manage your delivery addresses</p>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Address Type Tabs -->
                <div class="address-type-tabs mb-4">
                    <div class="tabs-container">
                        <a href="{{ route('address.switch-type', 'home') }}" 
                           class="tab {{ $addressType === 'home' ? 'active' : '' }}">
                            <i class="fas fa-home me-2"></i>
                            Home Address
                        </a>
                        <a href="{{ route('address.switch-type', 'work') }}" 
                           class="tab {{ $addressType === 'work' ? 'active' : '' }}">
                            <i class="fas fa-briefcase me-2"></i>
                            Work Address
                        </a>
                    </div>
                </div>

                <!-- Current Address Selection -->
                @php
                    $currentAddressForDisplay = collect($addresses)->firstWhere('category', $addressType);
                @endphp

                @if($currentAddressForDisplay)
                <div class="address-selection-card mb-4">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Current {{ ucfirst($addressType) }} Address</h3>
                    </div>
                    <div class="card-content white-bg">
                        <div class="current-address-display">
                            <div class="address-header">
                                <div class="address-title">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Current {{ ucfirst($addressType) }} Address</strong>
                                    @if($currentAddressForDisplay['is_default_shipping'])
                                        <span class="default-badge">Default Shipping</span>
                                    @endif
                                </div>
                            </div>
                            <div class="address-details">
                                <div class="address-line">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong>{{ $currentAddressForDisplay['label'] }}</strong>
                                </div>
                                <div class="address-line">
                                    <i class="fas fa-road me-2 text-muted"></i>
                                    {{ $currentAddressForDisplay['street'] }}
                                    @if($currentAddressForDisplay['unit'])
                                        , {{ $currentAddressForDisplay['unit'] }}
                                    @endif
                                </div>
                                <div class="address-line">
                                    <i class="fas fa-location-dot me-2 text-muted"></i>
                                    {{ $currentAddressForDisplay['district'] }}, {{ $currentAddressForDisplay['city'] }}, {{ $currentAddressForDisplay['region'] }}
                                </div>
                            </div>
                            <div class="address-actions mt-3">
                                @if(!$currentAddressForDisplay['is_default_shipping'])
                                <form action="{{ route('address.set-default', $currentAddressForDisplay['id']) }}" method="POST" class="d-inline me-2">
                                    @csrf
                                    <button type="submit" class="btn-set-default" title="Set as Default Shipping">
                                        <i class="fas fa-star me-1"></i>Set as Default
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('address.destroy', $currentAddressForDisplay['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Delete Address">
                                        <i class="fas fa-trash me-1"></i>Delete Address
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="no-addresses-card mb-4">
                    <div class="card-content white-bg text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h4>No {{ ucfirst($addressType) }} Address Yet</h4>
                        <p class="text-muted">Add your {{ $addressType }} address below to get started.</p>
                    </div>
                </div>
                @endif

                <!-- Add New Address Card -->
                <div class="address-form-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3>{{ $currentAddressForDisplay ? 'Update ' . ucfirst($addressType) . ' Address' : 'Add New ' . ucfirst($addressType) . ' Address' }}</h3>
                    </div>
                    <div class="card-content white-bg">
                        <form action="{{ route('address.store') }}" method="POST" id="addressForm">
                            @csrf
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address_label" class="form-label">
                                        <i class="fas fa-tag me-2"></i>Address Label (Optional)
                                    </label>
                                    <input type="text" class="form-control @error('address_label') is-invalid @enderror" 
                                           id="address_label" name="address_label" 
                                           value="{{ old('address_label', $currentAddress['label'] ?? ucfirst($addressType)) }}"
                                           placeholder="e.g., Home, Office, Parents' House"
                                           style="text-transform: none;">
                                    @error('address_label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Give this address a friendly name to identify it</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="address_category" class="form-label">
                                        <i class="fas fa-home me-2"></i>Address Type
                                    </label>
                                    <select class="form-control @error('address_category') is-invalid @enderror" id="address_category" name="address_category" required>
                                        <option value="home" {{ old('address_category', $addressType) == 'home' ? 'selected' : '' }}>Home Address</option>
                                        <option value="work" {{ old('address_category', $addressType) == 'work' ? 'selected' : '' }}>Work Address</option>
                                    </select>
                                    @error('address_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Switch tabs above to change address type</div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address_region" class="form-label">
                                        <i class="fas fa-map me-2"></i>Region <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('address_region') is-invalid @enderror" id="address_region" name="address_region" required>
                                        <option value="">Select Region</option>
                                        <option value="NCR" {{ old('address_region', $currentAddress['region'] ?? '') == 'NCR' ? 'selected' : '' }}>National Capital Region (NCR)</option>
                                        <option value="CAR" {{ old('address_region', $currentAddress['region'] ?? '') == 'CAR' ? 'selected' : '' }}>Cordillera Administrative Region (CAR)</option>
                                        <option value="Region I" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region I' ? 'selected' : '' }}>Region I - Ilocos</option>
                                        <option value="Region II" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region II' ? 'selected' : '' }}>Region II - Cagayan Valley</option>
                                        <option value="Region III" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region III' ? 'selected' : '' }}>Region III - Central Luzon</option>
                                        <option value="Region IV-A" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region IV-A' ? 'selected' : '' }}>Region IV-A - CALABARZON</option>
                                        <option value="Region IV-B" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region IV-B' ? 'selected' : '' }}>Region IV-B - MIMAROPA</option>
                                        <option value="Region V" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region V' ? 'selected' : '' }}>Region V - Bicol</option>
                                        <option value="Region VI" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region VI' ? 'selected' : '' }}>Region VI - Western Visayas</option>
                                        <option value="Region VII" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region VII' ? 'selected' : '' }}>Region VII - Central Visayas</option>
                                        <option value="Region VIII" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region VIII' ? 'selected' : '' }}>Region VIII - Eastern Visayas</option>
                                        <option value="Region IX" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region IX' ? 'selected' : '' }}>Region IX - Zamboanga Peninsula</option>
                                        <option value="Region X" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region X' ? 'selected' : '' }}>Region X - Northern Mindanao</option>
                                        <option value="Region XI" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region XI' ? 'selected' : '' }}>Region XI - Davao</option>
                                        <option value="Region XII" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region XII' ? 'selected' : '' }}>Region XII - SOCCSKSARGEN</option>
                                        <option value="Region XIII" {{ old('address_region', $currentAddress['region'] ?? '') == 'Region XIII' ? 'selected' : '' }}>Region XIII - Caraga</option>
                                        <option value="BARMM" {{ old('address_region', $currentAddress['region'] ?? '') == 'BARMM' ? 'selected' : '' }}>Bangsamoro Autonomous Region (BARMM)</option>
                                    </select>
                                    @error('address_region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="address_city" class="form-label">
                                        <i class="fas fa-city me-2"></i>City/Municipality <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('address_city') is-invalid @enderror" id="address_city" name="address_city" required>
                                        <option value="">Please select a region first</option>
                                    </select>
                                    @error('address_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address_district" class="form-label">
                                        <i class="fas fa-street-view me-2"></i>District/Barangay <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('address_district') is-invalid @enderror" 
                                           id="address_district" name="address_district" 
                                           value="{{ old('address_district', $currentAddress['district'] ?? '') }}" 
                                           required
                                           style="text-transform: none;"
                                           placeholder="Enter your barangay or district">
                                    @error('address_district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="address_street" class="form-label">
                                        <i class="fas fa-road me-2"></i>Street/Building Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('address_street') is-invalid @enderror" 
                                           id="address_street" name="address_street" 
                                           value="{{ old('address_street', $currentAddress['street'] ?? '') }}" 
                                           required
                                           style="text-transform: none;"
                                           placeholder="Enter your street address or building name">
                                    @error('address_street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address_unit" class="form-label">
                                        <i class="fas fa-building me-2"></i>Unit/Floor (Optional)
                                    </label>
                                    <input type="text" class="form-control @error('address_unit') is-invalid @enderror" 
                                           id="address_unit" name="address_unit" 
                                           value="{{ old('address_unit', $currentAddress['unit'] ?? '') }}"
                                           style="text-transform: none;"
                                           placeholder="e.g., Unit 5B, 3rd Floor">
                                    @error('address_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check-card">
                                    <input class="form-check-input" type="checkbox" id="is_default_shipping" name="is_default_shipping" 
                                           value="1" {{ old('is_default_shipping', $currentAddress['is_default_shipping'] ?? ($addressType === 'home')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default_shipping">
                                        <i class="fas fa-shipping-fast me-2"></i>
                                        Set as default shipping address
                                    </label>
                                    <div class="form-text">This address will be used as your primary shipping destination for all orders.</div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('profile.edit') }}" class="btn btn-cancel">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                                </a>
                                <button type="submit" class="btn btn-save">
                                    <i class="fas fa-save me-2"></i>{{ $currentAddressForDisplay ? 'Update ' . ucfirst($addressType) . ' Address' : 'Save ' . ucfirst($addressType) . ' Address' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
/* Modern CSS Variables - Purple & White Theme */
:root {
    --primary: #8b5cf6;
    --primary-dark: #7c3aed;
    --primary-light: #a78bfa;
    --secondary: #f59e0b;
    --accent: #ec4899;
    --success: #10b981;
    --warning: #f59e0b;
    --dark: #1f2937;
    --darker: #111827;
    --light: #ffffff;
    --gray: #6b7280;
    --light-gray: #f8fafc;
    --border: #e5e7eb;
    --shadow: 0 8px 30px rgba(0,0,0,0.08);
    --gradient: linear-gradient(135deg, #8b5cf6, #7c3aed);
    --gradient-dark: linear-gradient(135deg, #7c3aed, #6d28d9);
}

/* Address Wrapper */
.address-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* Address Type Tabs */
.address-type-tabs {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: var(--shadow);
}

.tabs-container {
    display: flex;
    gap: 0.5rem;
    border-radius: 10px;
    background: var(--light-gray);
    padding: 0.5rem;
}

.tab {
    flex: 1;
    padding: 0.75rem 1rem;
    text-align: center;
    text-decoration: none;
    color: var(--gray);
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tab:hover {
    color: var(--primary);
    background: rgba(139, 92, 246, 0.1);
}

.tab.active {
    background: var(--gradient);
    color: white;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

/* Page Header */
.page-header {
    margin-bottom: 3rem;
}

.header-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.8rem;
    margin: 0 auto 1rem auto;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}

/* Cards */
.address-selection-card,
.saved-addresses-card,
.no-addresses-card,
.address-form-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--border);
    margin-bottom: 2rem;
}

.address-selection-card:hover,
.saved-addresses-card:hover,
.no-addresses-card:hover,
.address-form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(139, 92, 246, 0.15);
}

.card-header {
    background: var(--darker);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid var(--border);
}

.card-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: var(--gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: white;
}

/* White Background for Content */
.card-content.white-bg {
    background: var(--light);
    padding: 2rem;
}

/* Current Address Display - Improved Alignment */
.current-address-display {
    padding: 1.5rem;
    background: var(--light-gray);
    border-radius: 12px;
    border-left: 4px solid var(--success);
}

.address-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.address-title {
    display: flex;
    align-items: center;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark);
}

.address-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.address-line {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    line-height: 1.4;
}

.address-line i {
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.address-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
}

.btn-set-default {
    background: var(--primary-light);
    color: var(--dark);
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
}

.btn-set-default:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.btn-delete {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
}

.btn-delete:hover {
    background: #dc2626;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.default-badge {
    background: var(--success);
    color: white;
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.75rem;
    display: inline-flex;
    align-items: center;
}

/* Form Styles */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .address-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .tabs-container {
        flex-direction: column;
    }
    
    .address-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .address-title {
        flex-wrap: wrap;
    }
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.form-label i {
    color: var(--primary);
    width: 16px;
}

.form-control {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    text-transform: none !important;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-control[readonly] {
    background-color: var(--light-gray);
    opacity: 1;
}

/* Enhanced Checkbox Styling */
.form-check-card {
    background: var(--light-gray);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--border);
    margin: 1.5rem 0;
    transition: all 0.3s ease;
}

.form-check-card:hover {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-check-input {
    width: 1.2em;
    height: 1.2em;
    margin-right: 0.75rem;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
}

.form-check-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: var(--dark);
    cursor: pointer;
    margin-bottom: 0.5rem;
}

.form-check-label i {
    color: var(--primary);
    width: 16px;
}

.form-text {
    font-size: 0.85rem;
    color: var(--gray);
    margin-top: 0.5rem;
    margin-left: 2rem;
}

/* Buttons */
.btn-save {
    background: var(--gradient);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
    background: var(--gradient-dark);
}

.btn-cancel {
    background: var(--light-gray);
    color: var(--gray);
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border);
    cursor: pointer;
}

.btn-cancel:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

/* Alerts */
.alert {
    border-radius: 12px;
    border: none;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #7f1d1d;
    border-left: 4px solid #ef4444;
}

.alert-info {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

/* Text Colors */
.text-muted {
    color: var(--gray) !important;
}

.text-danger {
    color: #ef4444 !important;
}

.text-primary {
    color: var(--primary) !important;
}

/* Ensure Font Awesome icons are visible */
.fas, .far, .fab {
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

/* Force text-transform none for all inputs */
input[type="text"],
input[type="tel"],
select,
.form-control {
    text-transform: none !important;
}
</style>
@endsection

@section('scripts')
<script>
// Philippine Cities/Municipalities by Region
const philippineCities = {
    'NCR': [
        'Caloocan', 'Las Piñas', 'Makati', 'Malabon', 'Mandaluyong', 
        'Manila', 'Marikina', 'Muntinlupa', 'Navotas', 'Parañaque', 
        'Pasay', 'Pasig', 'Pateros', 'Quezon City', 'San Juan', 
        'Taguig', 'Valenzuela'
    ],
    'Region I': [
        'Alaminos', 'Batac', 'Candon', 'Dagupan', 'Laoag', 
        'San Carlos', 'San Fernando', 'Urdaneta', 'Vigan',
        'Bacnotan', 'Balaoan', 'Bangar', 'Bauang', 'Burgos',
        'Caba', 'Luna', 'Naguilian', 'Pugo', 'Rosario',
        'San Fernando', 'San Juan', 'Santo Tomas', 'Santol',
        'Sudipen', 'Tubao'
    ],
    'Region II': [
        'Cauayan', 'Ilagan', 'Santiago', 'Tuguegarao',
        'Alicia', 'Angadanan', 'Aurora', 'Benito Soliven',
        'Burgos', 'Cabagan', 'Cabatuan', 'Cordon', 'Delfin Albano',
        'Dinapigue', 'Divilacan', 'Echague', 'Gamu', 'Jones',
        'Luna', 'Maconacon', 'Mallig', 'Naguilian', 'Palanan',
        'Quezon', 'Quirino', 'Ramon', 'Reina Mercedes', 'Roxas',
        'San Agustin', 'San Guillermo', 'San Isidro', 'San Manuel',
        'San Mariano', 'San Mateo', 'San Pablo', 'Santa Maria',
        'Santo Tomas', 'Tumauini'
    ],
    'Region III': [
        'Angeles', 'Balanga', 'Cabanatuan', 'Gapan', 'Mabalacat', 
        'Malolos', 'Meycauayan', 'Muñoz', 'Olongapo', 'Palayan', 
        'San Fernando', 'San Jose', 'San Jose del Monte', 'Tarlac',
        'Apalit', 'Arayat', 'Bacolor', 'Candaba', 'Floridablanca',
        'Guagua', 'Lubao', 'Mabalacat', 'Macabebe', 'Magalang',
        'Masantol', 'Mexico', 'Minalin', 'Porac', 'San Fernando',
        'San Luis', 'San Simon', 'Santa Ana', 'Santa Rita',
        'Santo Tomas', 'Sasmuan'
    ],
    'Region IV-A': [
        'Antipolo', 'Bacoor', 'Batangas', 'Binan', 'Cabuyao', 
        'Calamba', 'Cavite', 'Dasmarinas', 'General Trias', 
        'Imus', 'Lipa', 'Lucena', 'San Pablo', 'San Pedro', 
        'Santa Rosa', 'Santo Tomas', 'Tagaytay', 'Tanauan', 'Tayabas',
        'Alaminos', 'Bay', 'Calauan', 'Cavinti', 'Famy',
        'Kalayaan', 'Liliw', 'Los Baños', 'Luisiana', 'Lumban',
        'Mabitac', 'Magdalena', 'Majayjay', 'Nagcarlan', 'Paete',
        'Pagsanjan', 'Pakil', 'Pangil', 'Pila', 'Rizal',
        'San Pablo', 'San Pedro', 'Santa Cruz', 'Santa Maria',
        'Siniloan', 'Victoria'
    ],
    'Region IV-B': [
        'Calapan', 'Puerto Princesa',
        'Aborlan', 'Agutaya', 'Araceli', 'Balabac', 'Bataraza',
        'Brooke\'s Point', 'Busuanga', 'Cagayancillo', 'Coron',
        'Culion', 'Cuyo', 'Dumaran', 'El Nido', 'Kalayaan',
        'Linapacan', 'Magsaysay', 'Narra', 'Quezon', 'Rizal',
        'Roxas', 'San Vicente', 'Sofronio Española', 'Taytay'
    ],
    'Region V': [
        'Iriga', 'Legazpi', 'Ligao', 'Masbate', 'Naga', 'Sorsogon', 'Tabaco',
        'Bacacay', 'Camalig', 'Daraga', 'Guinobatan', 'Jovellar',
        'Libon', 'Malilipot', 'Malinao', 'Manito', 'Oas',
        'Pio Duran', 'Polangui', 'Rapu-Rapu', 'Santo Domingo',
        'Tiwi'
    ],
    'Region VI': [
        'Bacolod', 'Bago', 'Cadiz', 'Escalante', 'Himamaylan', 
        'Iloilo', 'Kabankalan', 'La Carlota', 'Passi', 'Roxas', 
        'Sagay', 'San Carlos', 'Silay', 'Sipalay', 'Talisay', 'Victorias',
        'Ajuy', 'Alimodian', 'Anilao', 'Badiangan', 'Balasan',
        'Banate', 'Barotac Nuevo', 'Barotac Viejo', 'Bingawan',
        'Cabatuan', 'Calinog', 'Carles', 'Concepcion', 'Dingle',
        'Dueñas', 'Dumangas', 'Estancia', 'Guimbal', 'Igbaras',
        'Janiuay', 'Lambunao', 'Leganes', 'Lemery', 'Leon',
        'Maasin', 'Miagao', 'Mina', 'New Lucena', 'Oton',
        'Pavia', 'Pototan', 'San Dionisio', 'San Enrique',
        'San Joaquin', 'San Miguel', 'San Rafael', 'Santa Barbara',
        'Sara', 'Tigbauan', 'Tubungan', 'Zarraga'
    ],
    'Region VII': [
        'Bais', 'Bayawan', 'Canlaon', 'Cebu', 'Danao', 'Dumaguete', 
        'Guihulngan', 'Lapu-Lapu', 'Mandaue', 'Talisay', 'Tanjay', 'Toledo',
        'Alcantara', 'Alcoy', 'Alegria', 'Aloguinsan', 'Argao',
        'Asturias', 'Badian', 'Balamban', 'Bantayan', 'Barili',
        'Bogo', 'Boljoon', 'Borbon', 'Carcar', 'Carmen',
        'Catmon', 'Compostela', 'Consolacion', 'Cordova', 'Daanbantayan',
        'Dalaguete', 'Dumanjug', 'Ginatilan', 'Liloan', 'Madridejos',
        'Malabuyoc', 'Medellin', 'Minglanilla', 'Moalboal', 'Oslob',
        'Pilar', 'Pinamungajan', 'Poro', 'Ronda', 'Samboan',
        'San Fernando', 'San Francisco', 'San Remigio', 'Santa Fe',
        'Santander', 'Sibonga', 'Sogod', 'Tabogon', 'Tabuelan',
        'Tuburan', 'Tudela'
    ],
    'Region VIII': [
        'Baybay', 'Borongan', 'Calbayog', 'Catbalogan', 'Maasin', 'Ormoc', 'Tacloban',
        'Abuyog', 'Alangalang', 'Albuera', 'Babatngon', 'Barugo',
        'Bato', 'Burauen', 'Capoocan', 'Carigara', 'Dagami',
        'Dulag', 'Hilongos', 'Hindang', 'Inopacan', 'Isabel',
        'Jaro', 'Javier', 'Julita', 'Kananga', 'La Paz',
        'Leyte', 'MacArthur', 'Mahaplag', 'Matag-ob', 'Matalom',
        'Mayorga', 'Merida', 'Palo', 'Palompon', 'Pastrana',
        'San Isidro', 'San Miguel', 'Santa Fe', 'Tabango',
        'Tabontabon', 'Tanauan', 'Tolosa', 'Tunga', 'Villaba'
    ],
    'Region IX': [
        'Dapitan', 'Dipolog', 'Isabela', 'Pagadian', 'Zamboanga',
        'Aurora', 'Bayog', 'Dimataling', 'Dinas', 'Dumalinao',
        'Dumingag', 'Guipos', 'Josefina', 'Kumalarang', 'Labangan',
        'Lakewood', 'Lapuyan', 'Mahayag', 'Margosatubig', 'Midsalip',
        'Molave', 'Pitogo', 'Ramon Magsaysay', 'San Miguel',
        'San Pablo', 'Sominot', 'Tabina', 'Tambulig', 'Tigbao',
        'Tukuran', 'Vincenzo A. Sagun'
    ],
    'Region X': [
        'Cagayan de Oro', 'El Salvador', 'Gingoog', 'Iligan', 'Malaybalay', 'Oroquieta', 'Ozamiz', 'Tangub', 'Valencia',
        'Alubijid', 'Balingasag', 'Balingoan', 'Binuangan',
        'Claveria', 'Gitagum', 'Initao', 'Jasaan', 'Kinoguitan',
        'Lagonglong', 'Laguindingan', 'Libertad', 'Lugait',
        'Magsaysay', 'Manticao', 'Medina', 'Naawan', 'Opol',
        'Salay', 'Sugbongcogon', 'Tagoloan', 'Talisayan',
        'Villanueva'
    ],
    'Region XI': [
        'Davao', 'Digos', 'Mati', 'Panabo', 'Samal', 'Tagum',
        'Asuncion', 'Braulio E. Dujali', 'Carmen', 'Kapalong',
        'New Corella', 'San Isidro', 'Santo Tomas', 'Talaingod'
    ],
    'Region XII': [
        'General Santos', 'Koronadal', 'Kidapawan', 'Tacurong',
        'Bagumbayan', 'Columbio', 'Esperanza', 'Isulan',
        'Kalamansig', 'Lambayong', 'Lebak', 'Lutayan',
        'Palimbang', 'President Quirino', 'Senator Ninoy Aquino'
    ],
    'Region XIII': [
        'Bayugan', 'Bislig', 'Butuan', 'Cabadbaran', 'Surigao', 'Tandag',
        'Alegria', 'Bacuag', 'Burgos', 'Claver', 'Dapa',
        'Del Carmen', 'General Luna', 'Gigaquit', 'Mainit',
        'Malimono', 'Pilar', 'Placer', 'San Benito', 'San Francisco',
        'San Isidro', 'Santa Monica', 'Sison', 'Socorro',
        'Tagana-an', 'Tubod'
    ],
    'CAR': [
        'Baguio', 'Tabuk',
        'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon',
        'Kabayan', 'Kapangan', 'Kibungan', 'La Trinidad',
        'Mankayan', 'Sablan', 'Tuba', 'Tublay'
    ],
    'BARMM': [
        'Cotabato', 'Lamitan', 'Marawi',
        'Bacolod-Kalawi', 'Balabagan', 'Balindong', 'Bayang',
        'Binidayan', 'Buadiposo-Buntong', 'Bubong', 'Bumbaran',
        'Butig', 'Calanogas', 'Ditsaan-Ramain', 'Ganassi',
        'Kapai', 'Kapatagan', 'Lumba-Bayabao', 'Lumbaca-Unayan',
        'Lumbatan', 'Lumbayanague', 'Madalum', 'Madamba',
        'Maguing', 'Malabang', 'Marantao', 'Marogong', 'Masiu',
        'Mulondo', 'Pagayawan', 'Piagapo', 'Poona Bayabao',
        'Pualas', 'Saguiaran', 'Sultan Dumalondong', 'Tagoloan II',
        'Tamparan', 'Taraka', 'Tubaran', 'Tugaya', 'Wao'
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    const regionSelect = document.getElementById('address_region');
    const citySelect = document.getElementById('address_city');
    const categorySelect = document.getElementById('address_category');
    const labelInput = document.getElementById('address_label');
    
    // Get current values from old input or existing address
    const currentRegion = '{{ old("address_region", $currentAddress["region"] ?? "") }}';
    const currentCity = '{{ old("address_city", $currentAddress["city"] ?? "") }}';
    const currentCategory = '{{ old("address_category", $addressType) }}';
    
    // Initialize on page load
    function initializeCityDropdown() {
        const selectedRegion = regionSelect.value || currentRegion;
        
        if (selectedRegion && philippineCities[selectedRegion]) {
            populateCities(selectedRegion, currentCity);
        } else {
            citySelect.innerHTML = '<option value="">Please select a region first</option>';
        }
    }
    
    // Event listener for region change
    regionSelect.addEventListener('change', function() {
        const selectedRegion = this.value;
        
        if (selectedRegion && philippineCities[selectedRegion]) {
            populateCities(selectedRegion);
        } else {
            citySelect.innerHTML = '<option value="">Please select a region first</option>';
        }
    });
    
    // Event listener for category change
    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        
        // Auto-fill label based on category if empty
        if (!labelInput.value.trim()) {
            const labels = {
                'home': 'Home',
                'work': 'Work'
            };
            labelInput.value = labels[selectedCategory] || '';
        }
        
        // Switch address type when dropdown changes
        const switchUrl = '{{ route("address.switch-type", ":type") }}'.replace(':type', selectedCategory);
        window.location.href = switchUrl;
    });
    
    function populateCities(region, selectedCity = '') {
        const cities = philippineCities[region];
        
        // Clear existing options
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        
        // Add cities to dropdown
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            
            // Select if this was the previously selected city
            if (city === selectedCity) {
                option.selected = true;
            }
            
            citySelect.appendChild(option);
        });
    }
    
    // Initialize on page load
    initializeCityDropdown();

    // Add animations
    const cards = document.querySelectorAll('.address-selection-card, .saved-addresses-card, .no-addresses-card, .address-form-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Form validation
    const form = document.getElementById('addressForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    }
});
</script>
@endsection