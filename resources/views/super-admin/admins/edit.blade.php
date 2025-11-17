@extends('super-admin.layouts.app')

@section('title', 'Edit Admin - ToyShop Super Admin')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h2 text-danger"><i class="fas fa-user-edit me-2"></i>Edit Admin</h1>
            <p class="mb-0 text-muted">Update store administrator information.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Admins
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2 text-success"></i>Admin Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.admins.update', $admin) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $admin->first_name) }}" 
                                   placeholder="Enter first name" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $admin->last_name) }}" 
                                   placeholder="Enter last name" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username', $admin->username) }}" 
                                   placeholder="Enter username" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $admin->email) }}" 
                                   placeholder="Enter email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ $admin->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Active Account</strong>
                            </label>
                        </div>
                        <small class="text-muted">When inactive, the admin cannot login to the system.</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> Passwords cannot be changed here. Admins can change their own passwords 
                        through their profile, or you can reset it by contacting technical support.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Update Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection