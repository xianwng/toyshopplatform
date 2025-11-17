@extends('frontend.layout.master')

@section('content')
<div class="container-fluid py-5">
    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <!-- Gray Header -->
                <div class="text-center py-4" style="background: #2c2c2cff;">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('img/avatars/avatar.jpg') }}" 
                             class="rounded-circle shadow-sm border border-4 border-white"
                             alt="Profile Picture" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-3 border-white">
                            <i class="feather-check text-white" style="width: 14px; height: 14px;"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-white mt-3 mb-0">Charles Hall</h4>
                    <p class="text-light small mb-2">Administrator</p>
                    <span class="badge bg-light text-success px-3 py-2 rounded-pill">
                        <i class="feather-user-check me-1"></i> Active
                    </span>
                </div>

                <div class="card-body text-center p-4">
                    <!-- Quick Actions -->
                    <div class="d-grid gap-2 mt-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary rounded-pill">
                            <i class="feather-edit-2 me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-user text-primary me-2"></i> Personal Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-muted">Full Name</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-user text-primary me-2"></i>
                                <span class="fw-semibold">Charles Hall</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Email Address</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-mail text-primary me-2"></i>
                                <span class="fw-semibold">charles@example.com</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Phone Number</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-phone text-primary me-2"></i>
                                <span class="fw-semibold">+1 (555) 123-4567</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Location</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-map-pin text-primary me-2"></i>
                                <span class="fw-semibold">New York, USA</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Member Since</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-calendar text-primary me-2"></i>
                                <span class="fw-semibold">January 15, 2024</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Last Login</small>
                            <div class="d-flex align-items-center mt-1">
                                <i class="feather-clock text-primary me-2"></i>
                                <span class="fw-semibold">2 hours ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card shadow-lg border-0 rounded-3 mt-4">
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="feather-info text-primary me-2"></i> Additional Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <label class="form-label text-muted small mb-2">Bio</label>
                    <div class="border rounded-3 p-3 bg-light">
                        <p class="mb-0 text-muted">
                            Experienced administrator with a focus on system management and user support. 
                            Passionate about creating efficient workflows and improving user experiences.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.25s ease-in-out;
}
.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(46, 46, 46, 0.1);
}
h5.card-title {
    font-size: 1.15rem;
}
.btn {
    transition: 0.2s ease-in-out;
}
.btn:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection