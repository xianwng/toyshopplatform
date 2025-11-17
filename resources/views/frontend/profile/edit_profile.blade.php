@extends('frontend.layout.master')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    
                    <!-- Page Header -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Edit Profile</h3>
                        <p class="text-muted">Update your account details below</p>
                        <hr class="mt-3 mb-4">
                    </div>

                    <!-- Top Section -->
                    <div class="d-flex align-items-center mb-4 bg-light rounded p-3">
                        <img src="{{ asset('img/avatars/avatar.jpg') }}" 
                             alt="Profile Picture" 
                             class="rounded-circle me-3 border border-3 border-primary shadow-sm" 
                             style="width:100px; height:100px; object-fit:cover;">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">John Doe</h4>
                            <p class="mb-1 text-primary">john@example.com</p>
                            <small class="text-muted">Administrator</small>
                        </div>
                    </div>

                    <!-- Account Section -->
                    <h5 class="fw-bold text-secondary mb-3">Account Information</h5>
                    <form>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold text-dark">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control border-primary" value="adminuser">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold text-dark">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control border-secondary" value="john@example.com" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold text-dark">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control border-danger" value="********">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label fw-semibold text-dark">Full Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control border-success" value="John Doe">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ url('profile') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-gradient px-4 fw-semibold">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 14px;
        background: #ffffff;
    }
    .col-form-label {
        color: #444;
    }
    input[readonly] {
        background-color: #f1f3f5;
        cursor: not-allowed;
    }
    .btn-gradient {
        background: linear-gradient(45deg, #007bff, #00c6ff);
        border: none;
        border-radius: 30px;
        color: #fff;
        transition: 0.3s;
    }
    .btn-gradient:hover {
        background: linear-gradient(45deg, #0056b3, #0099cc);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
</style>
@endsection
