@extends('super-admin.layouts.app')

@section('title', 'Super Admin Dashboard - ToyShop')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h2 text-danger"><i class="fas fa-tachometer-alt me-2"></i>Super Admin Dashboard</h1>
            <p class="mb-0 text-muted">Welcome back, {{ auth()->user()->full_name }}! Manage system administrators and settings.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('super-admin.profile') }}" class="btn btn-primary-custom">
                <i class="fas fa-user-edit me-2"></i>My Profile
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom border-left-danger h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Super Admins</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSuperAdmins }}</div>
                        <div class="mt-2">
                            <span class="text-success small font-weight-bold">
                                <i class="fas fa-shield-alt me-1"></i>System
                            </span>
                            <span class="text-muted small">Administrators</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom border-left-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Store Admins</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmins }}</div>
                        <div class="mt-2">
                            <span class="text-success small font-weight-bold">
                                <i class="fas fa-users me-1"></i>{{ $activeAdmins }} active
                            </span>
                            <span class="text-muted small">of {{ $totalAdmins }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users-cog fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Customers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                        <div class="mt-2">
                            <span class="text-success small font-weight-bold">
                                <i class="fas fa-user-plus me-1"></i>Registered
                            </span>
                            <span class="text-muted small">users</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom border-left-warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            System Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Online</div>
                        <div class="mt-2">
                            <span class="text-success small font-weight-bold">
                                <i class="fas fa-check-circle me-1"></i>All Systems
                            </span>
                            <span class="text-muted small">Operational</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-rocket me-2 text-danger"></i>System Management</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('super-admin.admins.index') }}" class="card action-card h-100 text-decoration-none">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-success">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <h6 class="mt-3 mb-2">Admin Management</h6>
                                <p class="text-muted small mb-0">Manage store administrators</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('super-admin.super-admins.index') }}" class="card action-card h-100 text-decoration-none">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-danger">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <h6 class="mt-3 mb-2">Super Admins</h6>
                                <p class="text-muted small mb-0">Manage super administrators</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('super-admin.profile') }}" class="card action-card h-100 text-decoration-none">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-info">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <h6 class="mt-3 mb-2">My Profile</h6>
                                <p class="text-muted small mb-0">Update account information</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <div class="card action-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-warning">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <h6 class="mt-3 mb-2">System Settings</h6>
                                <p class="text-muted small mb-0">Configure system options</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card action-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-secondary">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h6 class="mt-3 mb-2">System Reports</h6>
                                <p class="text-muted small mb-0">View system analytics</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card action-card h-100">
                            <div class="card-body text-center p-4">
                                <div class="action-icon bg-dark">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h6 class="mt-3 mb-2">Database</h6>
                                <p class="text-muted small mb-0">Database management</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Info & Recent Activity -->
    <div class="col-lg-4">
        <!-- System Info -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="info-item d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted">Last Login:</span>
                    <strong class="text-success">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M j, Y g:i A') : 'First login' }}</strong>
                </div>
                <div class="info-item d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted">Account Type:</span>
                    <strong class="text-danger">Super Admin</strong>
                </div>
                <div class="info-item d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted">Account Status:</span>
                    <strong class="{{ auth()->user()->is_active ? 'text-success' : 'text-danger' }}">
                        {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                    </strong>
                </div>
                <div class="info-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">Member Since:</span>
                    <strong>{{ auth()->user()->created_at->format('M j, Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card card-custom">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-warning"></i>User Distribution</h5>
            </div>
            <div class="card-body">
                <div class="stat-item text-center p-3 mb-3">
                    <div class="stat-value text-primary display-6 fw-bold">{{ $totalCustomers }}</div>
                    <div class="stat-label text-muted">Total Customers</div>
                </div>
                <div class="stat-item text-center p-3">
                    <div class="stat-value text-success display-6 fw-bold">{{ $totalAdmins }}</div>
                    <div class="stat-label text-muted">Store Admins</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .action-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: #f8f9fa;
        cursor: pointer;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        background: white;
    }
    
    .action-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 1.5rem;
    }
    
    .info-item:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    
    .stat-item {
        border-radius: 10px;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    }
</style>
@endpush