@extends('super-admin.layouts.app')

@section('title', 'My Profile - ToyShop Super Admin')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h2 text-danger"><i class="fas fa-user-edit me-2"></i>My Profile</h1>
            <p class="mb-0 text-muted">Manage your super admin account information and settings.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2 text-danger"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.profile.update') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                                   placeholder="Enter your first name" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                                   placeholder="Enter your last name" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $user->username) }}" 
                                   placeholder="Enter your username" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   placeholder="Enter your email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Change Section -->
                    <div class="border-top pt-4 mt-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-lock me-2"></i>Change Password</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" 
                                       placeholder="Enter current password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" 
                                       placeholder="Enter new password">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation" 
                                       placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <label class="form-label mb-0">Account Information</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Role: <strong class="text-danger">Super Admin</strong></small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Status: 
                                    <strong class="{{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </strong>
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">Last Login: <strong>{{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</strong></small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Member Since: <strong>{{ $user->created_at->format('M j, Y') }}</strong></small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection