@extends('frontend.layout.master')

@section('content')
@php
    $trades = isset($trades) ? $trades : collect();
    $pendingCount = \App\Models\Trade::where('status', 'pending')->count();
    $approvedCount = \App\Models\Trade::where('status', 'approved')->count();
    $activeCount = \App\Models\Trade::where('status', 'active')->count();
    $rejectedCount = \App\Models\Trade::where('status', 'rejected')->count();
@endphp

<div class="container-fluid mt-4 trading-management-page">
    <div class="mb-4 text-start">
        <h1 class="page-title">Trading Items Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-fill" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request()->is('admin/trading-management') ? 'active' : '' }}" 
                               href="{{ route('admin.trading.management') }}">
                                All Trades 
                                <span class="badge bg-secondary">{{ $trades->total() }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request()->is('admin/trading-management/pending') ? 'active' : '' }}" 
                               href="{{ route('admin.trading.pending') }}">
                                Pending Approval 
                                <span class="badge bg-warning">{{ $pendingCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request()->is('admin/trading-management/approved') ? 'active' : '' }}" 
                               href="{{ route('admin.trading.approved') }}">
                                Approved 
                                <span class="badge bg-info">{{ $approvedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request()->is('admin/trading-management/active') ? 'active' : '' }}" 
                               href="{{ route('admin.trading.active') }}">
                                Active 
                                <span class="badge bg-success">{{ $activeCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request()->is('admin/trading-management/rejected') ? 'active' : '' }}" 
                               href="{{ route('admin.trading.rejected') }}">
                                Rejected 
                                <span class="badge bg-danger">{{ $rejectedCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($trades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 align-middle text-center fixed-table">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 12%;">Image Preview</th>
                                <th class="product-name-col" style="width: 16%;">Product Name</th>
                                <th style="width: 12%;">Brand</th>
                                <th style="width: 12%;">Category</th>
                                <th style="width: 12%;">Condition</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 29%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trades as $trade)
                            <tr>
                                <td>{{ $trade->id ?? '-' }}</td>
                                <td>
                                    @php
                                        $imageData = $trade->image;
                                        $displayImage = '';
                                        $imageCount = 0;
                                        
                                        if ($imageData) {
                                            if (is_string($imageData) && str_starts_with(trim($imageData), '[')) {
                                                try {
                                                    $imagesArray = json_decode($imageData, true);
                                                    if (is_array($imagesArray) && !empty($imagesArray)) {
                                                        $displayImage = $imagesArray[0];
                                                        $imageCount = count($imagesArray);
                                                    }
                                                } catch (Exception $e) {
                                                    $displayImage = $imageData;
                                                    $imageCount = 1;
                                                }
                                            } else {
                                                $displayImage = $imageData;
                                                $imageCount = 1;
                                            }
                                            
                                            if ($displayImage) {
                                                $displayImage = str_replace('\\', '/', $displayImage);
                                                $displayImage = ltrim($displayImage, '/');
                                            }
                                        }
                                    @endphp
                                    
                                    @if($displayImage)
                                        <div class="image-preview-container" style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto; overflow: hidden; border: 1px solid #dee2e6;">
                                            <img src="{{ asset('storage/' . $displayImage) }}" 
                                                 alt="{{ $trade->name }}" 
                                                 style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80?text=No+Image';">
                                        </div>
                                        
                                        @if($imageCount > 1)
                                            <small class="text-muted mt-1 d-block">
                                                +{{ $imageCount - 1 }} more
                                            </small>
                                        @endif
                                    @else
                                        <div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid #dee2e6;">
                                            <div class="text-muted small">No Image</div>
                                        </div>
                                    @endif
                                </td>
                                <td class="product-name-col">{{ $trade->name ?? '—' }}</td>
                                <td style="white-space: normal; word-wrap: break-word; max-width: 120px;">
                                    {{ $trade->brand ?? '—' }}
                                </td>
                                <td style="white-space: normal; word-wrap: break-word; max-width: 120px;">
                                    {{ $trade->category ?? '—' }}
                                </td>
                                <td>
                                    {{ ucfirst($trade->condition) ?? '—' }}
                                </td>
                                <td>
                                    @if($trade->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Pending Approval
                                        </span>
                                    @elseif($trade->status === 'approved')
                                        <span class="badge bg-info">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                    @elseif($trade->status === 'active')
                                        <span class="badge bg-success">
                                            <i class="bi bi-play-circle"></i> Active
                                        </span>
                                    @elseif($trade->status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($trade->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        @if($trade->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success approve-btn" 
                                                    data-trade-id="{{ $trade->id }}">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger reject-btn" 
                                                    data-trade-id="{{ $trade->id }}">
                                                <i class="bi bi-x-lg"></i> Reject
                                            </button>
                                        @endif

                                        @if($trade->status === 'approved')
                                            <button type="button" class="btn btn-sm btn-primary activate-btn" 
                                                    data-trade-id="{{ $trade->id }}">
                                                <i class="bi bi-play-circle"></i> Activate
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.trading.view', $trade->id) }}" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($trades, 'links'))
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $trades->links() }}
                    </div>
                @endif

            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="text-muted mt-3">No trade items found</p>
                    @if(request()->is('admin/trading-management/pending'))
                        <p class="text-muted">There are no pending trades awaiting approval.</p>
                    @elseif(request()->is('admin/trading-management/approved'))
                        <p class="text-muted">There are no approved trades waiting for activation.</p>
                    @elseif(request()->is('admin/trading-management/active'))
                        <p class="text-muted">There are no active trades.</p>
                    @elseif(request()->is('admin/trading-management/rejected'))
                        <p class="text-muted">There are no rejected trades.</p>
                    @else
                        <p class="text-muted">There are no trade items in the system.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    .fixed-table {
        table-layout: fixed;
        width: 100%;
    }

    td, th {
        text-align: center;
        vertical-align: middle;
    }

    td.product-name-col, th.product-name-col {
        white-space: normal !important;
        word-wrap: break-word;
        word-break: break-word;
        max-width: 200px;
    }

    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }

    .nav-pills .nav-link {
        color: #495057;
        border: 1px solid #dee2e6;
        margin: 0 2px;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .nav-pills .nav-link .badge {
        margin-left: 5px;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin: 1px;
    }

    .image-preview-container img {
        min-width: 100%;
        min-height: 100%;
    }
    
    .spinner {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store original button states
    const buttonStates = new Map();

    document.querySelectorAll('.approve-btn, .reject-btn, .activate-btn').forEach(button => {
        buttonStates.set(button, {
            html: button.innerHTML,
            disabled: button.disabled
        });
        
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const tradeId = this.getAttribute('data-trade-id');
            const action = this.classList.contains('approve-btn') ? 'approve' : 
                          this.classList.contains('reject-btn') ? 'reject' : 'activate';
            
            submitAction(tradeId, action, this);
        });
    });

    function submitAction(tradeId, action, button) {
        // Show loading state
        button.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Processing...';
        button.disabled = true;

        let url = '';
        
        if (action === 'approve') {
            url = `/admin/trading-management/${tradeId}/approve`;
        } else if (action === 'reject') {
            url = `/admin/trading-management/${tradeId}/reject`;
        } else if (action === 'activate') {
            url = `/admin/trading-management/${tradeId}/activate`;
        }

        // Get CSRF token from multiple possible locations
        let token = '';
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfInput = document.querySelector('input[name="_token"]');
        
        if (csrfMeta) {
            token = csrfMeta.getAttribute('content');
        } else if (csrfInput) {
            token = csrfInput.value;
        } else {
            // Try to get from Laravel's default CSRF token name
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                token = csrfToken.value;
            }
        }

        if (!token) {
            resetButton(button);
            alert('CSRF token not found. Please refresh the page.');
            return;
        }

        // Create form data
        const formData = new FormData();
        formData.append('_token', token);

        // Create abort controller for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
        }, 10000); // 10 second timeout

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message briefly then reload
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                throw new Error(data.message || 'Action failed');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('Error:', error);
            
            if (error.name === 'AbortError') {
                alert('Request timed out. Please try again.');
            } else {
                alert('Error: ' + error.message);
            }
            
            // Reset button state
            resetButton(button);
        });
    }

    function resetButton(button) {
        const originalState = buttonStates.get(button);
        if (originalState) {
            button.innerHTML = originalState.html;
            button.disabled = originalState.disabled;
        } else {
            // Fallback if original state not found
            if (button.classList.contains('approve-btn')) {
                button.innerHTML = '<i class="bi bi-check-lg"></i> Approve';
            } else if (button.classList.contains('reject-btn')) {
                button.innerHTML = '<i class="bi bi-x-lg"></i> Reject';
            } else if (button.classList.contains('activate-btn')) {
                button.innerHTML = '<i class="bi bi-play-circle"></i> Activate';
            }
            button.disabled = false;
        }
    }
});
</script>

<!-- Add CSRF Token Meta Tag if missing in layout -->
@if(!strpos($__env->yieldContent('csrf-token'), 'csrf-token'))
<meta name="csrf-token" content="{{ csrf_token() }}">
@endif
@endsection