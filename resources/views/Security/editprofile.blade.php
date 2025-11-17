@extends('customer.layouts.cmaster')

@section('title', 'Edit Profile - Toyspace')

@section('content')
<!-- Add Font Awesome CDN if not already in your layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="main">
    <section class="module">
        <div class="container">
            <div class="profile-wrapper">

                <!-- Profile Header -->
                <div class="profile-header text-center mb-5">
                    <div class="avatar-section">
                        <div class="profile-avatar">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h2 class="module-title font-alt mt-3">Edit Profile</h2>
                        <p class="text-muted">Update your account information</p>
                    </div>
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

                <!-- Edit Profile Form -->
                <div class="profile-grid">
                    <!-- Left Column -->
                    <div class="profile-column">
                        <!-- Personal Information Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <h3>Personal Information</h3>
                            </div>
                            <div class="card-content white-bg">
                                <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                                    @csrf
                                    @method('PUT')
                                    
                                    @php
                                        $canChangeName = true;
                                        $canChangeUsername = true;
                                        
                                        if (auth()->user()->name_updated_at) {
                                            $nextChangeDate = \Carbon\Carbon::parse(auth()->user()->name_updated_at)->addDays(90);
                                            $canChangeName = $nextChangeDate->isPast();
                                        }
                                        
                                        if (auth()->user()->username_updated_at) {
                                            $nextUsernameDate = \Carbon\Carbon::parse(auth()->user()->username_updated_at)->addDays(90);
                                            $canChangeUsername = $nextUsernameDate->isPast();
                                        }
                                    @endphp

                                    <!-- Name Change Warning -->
                                    @if(auth()->user()->name_updated_at && !$canChangeName)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You can change your name again on <strong>{{ \Carbon\Carbon::parse(auth()->user()->name_updated_at)->addDays(90)->format('F d, Y') }}</strong> (90-day cooldown).
                                        </div>
                                    @endif

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="first_name" class="form-label">
                                                <i class="fas fa-user me-2"></i>First Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                                   id="first_name" name="first_name" 
                                                   value="{{ old('first_name', auth()->user()->first_name) }}" 
                                                   @if(!$canChangeName) readonly @endif
                                                   minlength="3" maxlength="20" required
                                                   style="text-transform: none;">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="middle_name" class="form-label">
                                                <i class="fas fa-user me-2"></i>Middle Initial (Optional)
                                            </label>
                                            <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                                   id="middle_name" name="middle_name" 
                                                   value="{{ old('middle_name', auth()->user()->middle_name) }}"
                                                   @if(!$canChangeName) readonly @endif
                                                   maxlength="1"
                                                   style="text-transform: none;">
                                            @error('middle_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">One character only (e.g., "A")</div>
                                        </div>

                                        <div class="form-group">
                                            <label for="last_name" class="form-label">
                                                <i class="fas fa-user me-2"></i>Last Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                                   id="last_name" name="last_name" 
                                                   value="{{ old('last_name', auth()->user()->last_name) }}" 
                                                   @if(!$canChangeName) readonly @endif
                                                   minlength="3" maxlength="20" required
                                                   style="text-transform: none;">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Username Change Warning -->
                                    @if(auth()->user()->username_updated_at && !$canChangeUsername)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You can change your username again on <strong>{{ \Carbon\Carbon::parse(auth()->user()->username_updated_at)->addDays(90)->format('F d, Y') }}</strong> (90-day cooldown).
                                        </div>
                                    @endif
                                    
                                    <div class="form-group">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-at me-2"></i>Username <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                               id="username" name="username" 
                                               value="{{ old('username', auth()->user()->username) }}" 
                                               @if(!$canChangeUsername) readonly @endif
                                               minlength="6" maxlength="20" required
                                               style="text-transform: none;">
                                        <!-- Hidden field to ensure username is submitted even when readonly -->
                                        @if(!$canChangeUsername)
                                            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                                        @endif
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_number" class="form-label">
                                            <i class="fas fa-mobile-alt me-2"></i>Contact Number (Philippines) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" 
                                                   id="contact_number" name="contact_number" 
                                                   value="{{ old('contact_number', auth()->user()->contact_number) }}"
                                                   maxlength="11"
                                                   required
                                                   style="text-transform: none;">
                                            @if(auth()->user()->contact_number && !auth()->user()->contact_number_verified_at)
                                                <button type="button" class="btn btn-outline-primary" id="sendOtpBtn">
                                                    <i class="fas fa-sms me-1"></i>Verify
                                                </button>
                                            @endif
                                        </div>
                                        <div class="form-text">Format: 09XXXXXXXXX</div>
                                        @if(auth()->user()->contact_number && !auth()->user()->contact_number_verified_at)
                                            <div id="otpSection" class="mt-2" style="display: none;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="otp_code" name="otp_code" 
                                                           placeholder="Enter OTP code" maxlength="6" pattern="[0-9]{6}"
                                                           style="text-transform: none;">
                                                    <button type="button" class="btn btn-success" id="verifyOtpBtn">
                                                        <i class="fas fa-check me-1"></i>Verify OTP
                                                    </button>
                                                </div>
                                                <div id="otpFeedback" class="form-text"></div>
                                            </div>
                                        @endif
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-actions">
                                        <a href="{{ route('my_profile') }}" class="btn btn-cancel">
                                            <i class="fas fa-arrow-left me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-save" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="profile-column">
                        <!-- Address Information Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <h3>Address Information</h3>
                            </div>
                            <div class="card-content white-bg">
                                <div class="address-section">
                                    <div class="address-type">
                                        <i class="fas fa-home"></i>
                                        <span>Home Address</span>
                                    </div>
                                    <div class="address-content">
                                        @if(auth()->user()->home_address)
                                            <p style="text-transform: none;">{{ auth()->user()->home_address }}</p>
                                        @else
                                            <p class="text-muted">No home address set</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="address-section">
                                    <div class="address-type">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Work Address</span>
                                    </div>
                                    <div class="address-content">
                                        @if(auth()->user()->work_address)
                                            <p style="text-transform: none;">{{ auth()->user()->work_address }}</p>
                                        @else
                                            <p class="text-muted">No work address set</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="address-actions">
                                    <a href="{{ route('address.create') }}" class="btn-manage">
                                        <i class="fas fa-edit me-2"></i>Manage Addresses
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Current Information Card -->
                        <div class="profile-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <h3>Current Information</h3>
                            </div>
                            <div class="card-content white-bg">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Email Address</label>
                                        <span class="info-value" style="text-transform: none;">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Member Since</label>
                                        <span class="info-value">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-sync"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Last Updated</label>
                                        <span class="info-value">{{ auth()->user()->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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

/* Profile Wrapper */
.profile-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* Profile Header */
.profile-header {
    margin-bottom: 2rem;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2rem;
    margin: 0 auto 1rem auto;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}

/* Profile Grid */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

@media (max-width: 968px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

/* Profile Cards */
.profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--border);
}

.profile-card:hover {
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
    padding: 1.5rem;
}

/* Form Styles */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
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

.form-text {
    font-size: 0.85rem;
    color: var(--gray);
    margin-top: 0.25rem;
}

/* Info Items */
.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--light-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1rem;
    border: 1px solid var(--border);
}

.info-content {
    flex: 1;
}

.info-content label {
    display: block;
    font-size: 0.85rem;
    color: var(--gray);
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.info-value {
    font-size: 1rem;
    color: var(--dark);
    font-weight: 500;
    text-transform: none !important;
}

/* Address Section */
.address-section {
    margin-bottom: 1.5rem;
    padding: 1.2rem;
    background: var(--light-gray);
    border-radius: 12px;
    border-left: 4px solid var(--primary);
    border: 1px solid var(--border);
}

.address-type {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--dark);
}

.address-content p {
    margin: 0;
    color: var(--gray);
    line-height: 1.5;
    text-transform: none !important;
}

.address-actions {
    margin-top: 1.5rem;
    text-align: center;
}

/* Buttons */
.btn-manage, .btn-save {
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

.btn-manage:hover, .btn-save:hover {
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

/* Input Group */
.input-group {
    display: flex;
    gap: 0.5rem;
}

.input-group .form-control {
    flex: 1;
}

.input-group .btn {
    white-space: nowrap;
}

/* Text Colors */
.text-muted {
    color: var(--gray) !important;
}

.text-danger {
    color: #ef4444 !important;
}

/* Ensure Font Awesome icons are visible */
.fas, .far, .fab {
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

/* Force text-transform none for all user input fields */
input[type="text"],
input[type="tel"],
.info-value,
.address-content p {
    text-transform: none !important;
}
</style>
@endsection

@section('scripts')
<script>
// Basic form validation
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    const firstName = document.getElementById('first_name')?.value;
    const lastName = document.getElementById('last_name')?.value;
    const username = document.getElementById('username')?.value;
    const contactNumber = document.getElementById('contact_number')?.value;
    
    let errors = [];
    
    if (!firstName || firstName.length < 3) {
        errors.push('First name must be at least 3 characters');
    }
    
    if (!lastName || lastName.length < 3) {
        errors.push('Last name must be at least 3 characters');
    }
    
    // Only validate username if it's not readonly (user can change it)
    const usernameField = document.getElementById('username');
    if (!usernameField.readOnly && (!username || username.length < 6)) {
        errors.push('Username must be at least 6 characters');
    }
    
    if (!contactNumber || contactNumber.trim().length === 0) {
        errors.push('Contact number is required');
    } else if (!/^(09)\d{9}$/.test(contactNumber)) {
        errors.push('Please enter a valid Philippine phone number (09XXXXXXXXX)');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
    }
});

// OTP functionality
document.getElementById('sendOtpBtn')?.addEventListener('click', function() {
    const contactNumber = document.getElementById('contact_number').value;
    
    if (!/^(09)\d{9}$/.test(contactNumber)) {
        alert('Please enter a valid Philippine phone number first');
        return;
    }
    
    // Send OTP request
    fetch('/send-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            contact_number: contactNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('otpSection').style.display = 'block';
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending OTP');
    });
});

document.getElementById('verifyOtpBtn')?.addEventListener('click', function() {
    const otpCode = document.getElementById('otp_code').value;
    
    if (!/^[0-9]{6}$/.test(otpCode)) {
        alert('Please enter a valid 6-digit OTP code');
        return;
    }
    
    // Verify OTP request
    fetch('/verify-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            otp_code: otpCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('sendOtpBtn').disabled = true;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error verifying OTP');
    });
});

// Add animations
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit profile page loaded');
    
    const cards = document.querySelectorAll('.profile-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection