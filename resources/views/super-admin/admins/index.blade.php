@extends('super-admin.layouts.app')

@section('title', 'Admin Management - ToyShop Super Admin')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h2 text-danger"><i class="fas fa-users-cog me-2"></i>Admin Management</h1>
            <p class="mb-0 text-muted">Manage store administrators and their permissions.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary-custom">
                <i class="fas fa-plus-circle me-2"></i>Add New Admin
            </a>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('super-admin.admins.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name, email, or username...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('super-admin.admins.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Admins List -->
<div class="card card-custom">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-list me-2 text-success"></i>Store Administrators</h5>
    </div>
    <div class="card-body">
        @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Admin</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $admin->full_name }}</h6>
                                        <small class="text-muted">@{{ $admin->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-muted small">{{ $admin->email }}</div>
                                    @if($admin->contact_number)
                                    <div class="text-muted small">{{ $admin->contact_number }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $admin->is_active ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas fa-circle me-1 small"></i>
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if($admin->last_login_at)
                                    <small class="text-muted">{{ $admin->last_login_at->format('M j, Y g:i A') }}</small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('super-admin.admins.edit', $admin) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('super-admin.admins.toggle-status', $admin) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $admin->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                title="{{ $admin->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas {{ $admin->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.admins.destroy', $admin) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this admin?')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} entries
                </div>
                {{ $admins->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Administrators Found</h5>
                <p class="text-muted">Get started by creating your first store administrator.</p>
                <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary-custom">
                    <i class="fas fa-plus-circle me-2"></i>Add New Admin
                </a>
            </div>
        @endif
    </div>
</div>
@endsection