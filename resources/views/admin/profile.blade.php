@extends('admin.layouts.app')

@section('title', 'My Profile - ToyShop Admin')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h2 text-success"><i class="fas fa-user-edit me-2"></i>My Profile</h1>
            <p class="mb-0 text-muted">Manage your admin account information.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2 text-success"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.update') }}">
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

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <label class="form-label mb-0">Account Information</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Role: <strong class="text-success">Store Admin</strong></small>
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

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> For password changes and account activation/deactivation, 
                        please contact the Super Administrator.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-md-2">
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