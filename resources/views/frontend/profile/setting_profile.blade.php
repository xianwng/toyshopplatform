@extends('frontend.layout.master')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box text-center">
                <h4 class="fw-bold text-primary mb-1">
                    <i class="feather-settings me-2"></i>Account Settings
                </h4>
                <p class="text-muted">Manage your account preferences and security settings</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Sidebar - Quick Settings -->
        <div class="col-xl-3 col-lg-4">
            <div class="card shadow-lg border-0 rounded-3 sticky-top" style="top: 100px;">
                <div class="card-header bg-gradient-primary text-white py-3 border-0">
                    <h6 class="fw-bold mb-0">
                        <i class="feather-sliders me-2"></i>Quick Settings
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#profile-info" class="list-group-item list-group-item-action active d-flex align-items-center py-3">
                            <i class="feather-user text-primary me-3"></i>
                            <div>
                                <span class="fw-semibold">Profile Info</span>
                                <small class="d-block text-muted">Basic information</small>
                            </div>
                        </a>
                        <a href="#notifications" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <i class="feather-bell text-warning me-3"></i>
                            <div>
                                <span class="fw-semibold">Notifications</span>
                                <small class="d-block text-muted">Alerts & preferences</small>
                            </div>
                        </a>
                        <a href="#security" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <i class="feather-shield text-danger me-3"></i>
                            <div>
                                <span class="fw-semibold">Security</span>
                                <small class="d-block text-muted">Password & 2FA</small>
                            </div>
                        </a>
                        <a href="#preferences" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <i class="feather-globe text-info me-3"></i>
                            <div>
                                <span class="fw-semibold">Preferences</span>
                                <small class="d-block text-muted">Language & region</small>
                            </div>
                        </a>
                        <a href="#system" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <i class="feather-monitor text-success me-3"></i>
                            <div>
                                <span class="fw-semibold">System</span>
                                <small class="d-block text-muted">System preferences</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-8">
            <!-- Profile Information Section -->
            <div id="profile-info" class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-user text-primary me-2"></i> Profile Information
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                        <i class="feather-edit me-1"></i>Editable
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="feather-user text-primary me-1"></i>Display Name
                                </label>
                                <input type="text" class="form-control form-control-lg border-2 border-primary bg-light" value="Charles Hall">
                                <small class="form-text text-muted">Your name as it appears to other users</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="feather-mail text-primary me-1"></i>Email Address
                                </label>
                                <input type="email" class="form-control form-control-lg border-2 border-success bg-light" value="charles@example.com">
                                <small class="form-text text-muted">Primary contact email</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="feather-phone text-primary me-1"></i>Phone Number
                                </label>
                                <input type="text" class="form-control form-control-lg border-2 border-info bg-light" value="+1 (555) 123-4567">
                                <small class="form-text text-muted">For important notifications</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="feather-map-pin text-primary me-1"></i>Location
                                </label>
                                <input type="text" class="form-control form-control-lg border-2 border-warning bg-light" value="New York, USA">
                                <small class="form-text text-muted">Your primary location</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div id="notifications" class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-bell text-warning me-2"></i> Notification Preferences
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-check form-switch custom-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label fw-semibold text-dark" for="emailNotifications">
                                    Email Notifications
                                </label>
                                <small class="form-text text-muted d-block">Receive updates via email</small>
                            </div>
                            
                            <div class="form-check form-switch custom-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="pushNotifications" checked>
                                <label class="form-check-label fw-semibold text-dark" for="pushNotifications">
                                    Push Notifications
                                </label>
                                <small class="form-text text-muted d-block">Browser notifications</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch custom-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="smsNotifications">
                                <label class="form-check-label fw-semibold text-dark" for="smsNotifications">
                                    SMS Alerts
                                </label>
                                <small class="form-text text-muted d-block">Text message alerts</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Notification Frequency</label>
                                <select class="form-select border-2 border-secondary">
                                    <option selected>Real-time</option>
                                    <option>Daily Digest</option>
                                    <option>Weekly Summary</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security" class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-shield text-danger me-2"></i> Security Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-2 border-warning" placeholder="Enter current password">
                                    <span class="input-group-text bg-warning bg-opacity-10 border-warning">
                                        <i class="feather-lock text-warning"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-2 border-success" placeholder="Enter new password">
                                    <span class="input-group-text bg-success bg-opacity-10 border-success">
                                        <i class="feather-key text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control border-2 border-success" placeholder="Confirm new password">
                                    <span class="input-group-text bg-success bg-opacity-10 border-success">
                                        <i class="feather-check-circle text-success"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Two-Factor Authentication</label>
                                <select class="form-select border-2 border-info">
                                    <option selected>Disabled</option>
                                    <option>Enabled - Authenticator App</option>
                                    <option>Enabled - SMS Verification</option>
                                </select>
                                <small class="form-text text-muted">Add an extra layer of security</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Preferences -->
            <div id="system" class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-monitor text-success me-2"></i> System Preferences
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Theme</label>
                                <select class="form-select border-2 border-primary">
                                    <option selected>Default Dark</option>
                                    <option>Light Theme</option>
                                    <option>Blue Theme</option>
                                    <option>Green Theme</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Dashboard Layout</label>
                                <select class="form-select border-2 border-info">
                                    <option selected>Compact</option>
                                    <option>Spacious</option>
                                    <option>Minimal</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Items Per Page</label>
                                <select class="form-select border-2 border-warning">
                                    <option>10 items</option>
                                    <option selected>25 items</option>
                                    <option>50 items</option>
                                    <option>100 items</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label fw-semibold text-dark mb-2">Auto-logout Timer</label>
                                <select class="form-select border-2 border-danger">
                                    <option>15 minutes</option>
                                    <option selected>30 minutes</option>
                                    <option>1 hour</option>
                                    <option>4 hours</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary px-4 fw-semibold me-2">
                                <i class="feather-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-danger px-4 fw-semibold me-2">
                                <i class="feather-refresh-ccw me-2"></i>Reset All
                            </button>
                            <button type="button" class="btn btn-primary px-4 fw-semibold">
                                <i class="feather-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease-in-out;
    border: none;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(46, 46, 46, 0.15) !important;
}
.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}
.form-control, .form-select {
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
    border-width: 2px;
}
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
    transform: scale(1.1);
}
.form-check-input {
    transition: all 0.3s ease;
}
.btn {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    border-width: 2px;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}
.list-group-item {
    border: none;
    transition: all 0.3s ease;
    border-radius: 8px !important;
    margin: 2px 8px;
    width: auto;
}
.list-group-item:hover, .list-group-item.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    transform: translateX(5px);
}
.badge {
    font-weight: 600;
    padding: 8px 12px;
}
.sticky-top {
    z-index: 1020;
}
.page-title-box {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    padding: 20px;
    border-radius: 15px;
    border-left: 4px solid #007bff;
    text-align: center;
}
.input-group-text {
    border-radius: 0 12px 12px 0;
    background: rgba(0, 123, 255, 0.1);
    border: 2px solid #007bff;
    border-left: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Smooth scroll for sidebar navigation
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Remove active class from all items
                document.querySelectorAll('.list-group-item').forEach(i => {
                    i.classList.remove('active');
                });
                // Add active class to clicked item
                this.classList.add('active');
                
                // Smooth scroll to target
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation to form elements on focus
    document.querySelectorAll('.form-control, .form-select').forEach(element => {
        element.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        element.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>
@endsection