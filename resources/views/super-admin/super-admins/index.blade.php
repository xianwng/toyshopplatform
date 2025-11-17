@extends('super-admin.layouts.app')

@section('title', 'Super Admins Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Super Admins Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('super-admin.super-admins.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Super Admin
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('super-admin.super-admins.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, or username..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Super Admins Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($superAdmins as $superAdmin)
                                <tr>
                                    <td>{{ $superAdmin->id }}</td>
                                    <td>{{ $superAdmin->first_name }} {{ $superAdmin->last_name }}</td>
                                    <td>{{ $superAdmin->email }}</td>
                                    <td>{{ $superAdmin->username }}</td>
                                    <td>
                                        <span class="badge badge-{{ $superAdmin->is_active ? 'success' : 'danger' }}">
                                            {{ $superAdmin->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $superAdmin->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('super-admin.super-admins.edit', $superAdmin->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($superAdmin->id !== auth()->id())
                                            <form action="{{ route('super-admin.super-admins.toggle-status', $superAdmin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-{{ $superAdmin->is_active ? 'warning' : 'success' }} btn-sm">
                                                    <i class="fas fa-{{ $superAdmin->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('super-admin.super-admins.destroy', $superAdmin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this super admin?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="badge badge-info">Current User</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No super admins found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $superAdmins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any JavaScript needed for this page
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality can be enhanced here
        console.log('Super Admins page loaded');
    });
</script>
@endsection