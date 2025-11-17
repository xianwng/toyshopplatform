@extends('layouts.dashboard')

@section('title', 'My Profile - Toy Collectible Platform')

@section('content')
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">Hello, {{ auth()->user()->first_name }}! 👋</h1>
        <p class="welcome-subtitle">Welcome to your Toy Collectible Platform dashboard</p>
    </div>

    <!-- Verification Reminder -->
    @if(!auth()->user()->home_address || !auth()->user()->contact_number)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Complete your profile to become a verified user!</strong>
            Please provide your home address and contact number to unlock full platform features.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Email Verification Reminder -->
    @if(!auth()->user()->email_verified_at && !auth()->user()->google_id)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-envelope me-2"></i>
            <strong>Verify your email address!</strong>
            Please verify your email to access all platform features.
            <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="sendEmailVerificationBtn">
                <i class="fas fa-paper-plane me-1"></i>Send Verification Email
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Collectibles</div>
            <div class="stat-value">24</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Active Auctions</div>
            <div class="stat-value">3</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Wishlist Items</div>
            <div class="stat-value">12</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Cart Items</div>
            <div class="stat-value">2</div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar-container">
                <div class="profile-avatar">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="profile-avatar-img">
                    @else
                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#profilePictureModal">
                    <i class="fas fa-camera me-1"></i> Change Photo
                </button>
            </div>
            <div class="profile-info">
                <h3>{{ auth()->user()->first_name }} {{ auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '' }}{{ auth()->user()->last_name }}</h3>
                <p>Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                <div class="verification-status">
                    @if(auth()->user()->home_address && auth()->user()->contact_number && auth()->user()->email_verified_at)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Verified User
                        </span>
                    @else
                        <span class="badge bg-warning">
                            <i class="fas fa-exclamation-circle me-1"></i>Verification Pending
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="profile-details">
            <div>
                <div class="detail-group">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">
                        {{ auth()->user()->first_name }} {{ auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '' }}{{ auth()->user()->last_name }}
                        @if(auth()->user()->name_updated_at && \Carbon\Carbon::parse(auth()->user()->name_updated_at)->addDays(90)->isFuture())
                            <span class="badge bg-info ms-1" data-bs-toggle="tooltip" title="You can change your name again on {{ \Carbon\Carbon::parse(auth()->user()->name_updated_at)->addDays(90)->format('M d, Y') }}">
                                <i class="fas fa-clock me-1"></i>Name Change Cooldown
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value">
                        {{ auth()->user()->email }}
                        @if(auth()->user()->email_verified_at)
                            <span class="badge bg-success ms-1">
                                <i class="fas fa-check-circle me-1"></i>Verified
                            </span>
                        @elseif(auth()->user()->google_id)
                            <span class="badge bg-primary ms-1">
                                <i class="fab fa-google me-1"></i>Google Verified
                            </span>
                        @else
                            <span class="badge bg-warning ms-1">
                                <i class="fas fa-exclamation-circle me-1"></i>Unverified
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Username</div>
                    <div class="detail-value">
                        {{ auth()->user()->username ?? 'Not set' }}
                        @if(auth()->user()->username_updated_at && \Carbon\Carbon::parse(auth()->user()->username_updated_at)->addDays(90)->isFuture())
                            <span class="badge bg-warning ms-1">
                                <i class="fas fa-clock me-1"></i>Cooldown Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <div class="detail-group">
                    <div class="detail-label">Home Address</div>
                    <div class="detail-value">
                        {{ auth()->user()->home_address ?? 'Not provided' }}
                        @if(!auth()->user()->home_address)
                            <span class="badge bg-danger ms-1">Required</span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Detailed Address</div>
                    <div class="detail-value">
                        @if(auth()->user()->address_region)
                            {{ auth()->user()->address_street }}, 
                            {{ auth()->user()->address_unit ? auth()->user()->address_unit . ', ' : '' }}
                            {{ auth()->user()->address_district }}, 
                            {{ auth()->user()->address_city }}, 
                            {{ auth()->user()->address_region }}
                            @if(auth()->user()->is_default_shipping)
                                <span class="badge bg-primary ms-1">Default Shipping</span>
                            @endif
                        @else
                            Not provided
                            <span class="badge bg-danger ms-1">Required</span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Contact Number</div>
                    <div class="detail-value">
                        @if(auth()->user()->contact_number)
                            {{ auth()->user()->contact_number }}
                            @if(auth()->user()->contact_number_verified_at)
                                <span class="badge bg-success ms-1">
                                    <i class="fas fa-check-circle me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-warning ms-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>Unverified
                                </span>
                            @endif
                        @else
                            Not provided
                            <span class="badge bg-danger ms-1">Required</span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-group">
                    <div class="detail-label">Account Status</div>
                    <div class="detail-value">
                        <span class="badge bg-success">Active</span>
                        @if(auth()->user()->home_address && auth()->user()->contact_number && auth()->user()->email_verified_at && auth()->user()->contact_number_verified_at)
                            <span class="badge bg-primary ms-1">Fully Verified</span>
                        @else
                            <span class="badge bg-warning ms-1">Verification Required</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="profile-card">
        <h4 class="mb-4">Quick Actions</h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Picture Modal -->
    <div class="modal fade" id="profilePictureModal" tabindex="-1" aria-labelledby="profilePictureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('profile.update-picture') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="profilePictureModalLabel">Change Profile Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <div class="profile-avatar-large mb-3">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="profile-avatar-img-large">
                                @else
                                    {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Choose Profile Picture</label>
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted formats: JPG, PNG, GIF. Max size: 2MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if(auth()->user()->profile_picture)
                            <button type="submit" form="removePictureForm" class="btn btn-danger me-auto">Remove Picture</button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Picture</button>
                    </div>
                </form>
                @if(auth()->user()->profile_picture)
                    <form id="removePictureForm" action="{{ route('profile.remove-picture') }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.profile-avatar-container {
    text-align: center;
}
.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    color: white;
    margin: 0 auto;
    position: relative;
    overflow: hidden;
}
.profile-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: bold;
    color: white;
    margin: 0 auto;
    position: relative;
    overflow: hidden;
}
.profile-avatar-img-large {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.verification-status {
    margin-top: 10px;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}
.stat-title {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}
.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #495057;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeEmailVerification();
        initializeTooltips();
    });

    // Email Verification
    function initializeEmailVerification() {
        const sendEmailVerificationBtn = document.getElementById('sendEmailVerificationBtn');
        
        if (sendEmailVerificationBtn) {
            sendEmailVerificationBtn.addEventListener('click', function() {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';

                fetch('{{ route("profile.send-email-verification") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Email Sent';
                    } else {
                        alert(data.message);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Verification Email';
                    }
                })
                .catch(() => {
                    alert('Failed to send verification email. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Send Verification Email';
                });
            });
        }
    }

    // Tooltips
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Profile picture preview
    document.getElementById('profile_picture')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector('.profile-avatar-img-large');
                if (img) img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush