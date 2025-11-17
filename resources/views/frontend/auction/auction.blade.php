@extends('frontend.layout.master')

@section('content')
@php
    $auctions = isset($auctions) ? $auctions : collect();
@endphp

<div class="container-fluid mt-4 auction-page">
    <!-- Page header -->
    <h1 class="h3 mb-2 text-dark">Auction Monitoring</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Quick Actions Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.auctions.pending') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-clock"></i> Pending Review
                            @if($pendingCount = $auctions->where('status', 'pending')->count())
                                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.auctions.active') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-play-circle"></i> Active Auctions
                        </a>
                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-list"></i> All Auctions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-pills" id="statusTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-status="all">All Auctions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="pending">
                        Pending Review
                        @if($pendingCount = $auctions->where('status', 'pending')->count())
                            <span class="badge bg-warning ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="approved">Approved</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="active">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="ended">Ended</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="completed">Completed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="rejected">Rejected</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($auctions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th style="width: 140px;">Product Image</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Condition</th>
                                <th>Start Price</th>
                                <th>Current Bid</th>
                                <th>Bids Count</th>
                                <th>Status</th>
                                <th>End Time</th>
                                <th>Monitoring Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auctions as $auction)
                            <tr class="auction-row" data-status="{{ $auction->status ?? 'pending' }}">
                                <td>{{ $auction->id ?? '-' }}</td>
                                <td>
                                    @php
                                        // FIX: Handle both string and array product_img values
                                        $productImg = is_array($auction->product_img) ? ($auction->product_img[0] ?? null) : $auction->product_img;
                                        $imgPath = !empty($productImg) && is_string($productImg) ? 'storage/' . $productImg : null;
                                    @endphp
                                    
                                    @if(!empty($imgPath) && file_exists(public_path($imgPath)))
                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto; overflow: hidden;">
                                            <img 
                                                src="{{ asset($imgPath) }}" 
                                                alt="{{ $auction->product_name }}"
                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo='">
                                        </div>
                                    @else
                                        <div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                            <img 
                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQgMTZMMTAgMTBNMTAgMTZMMTkgNE0xOSA0TDE0IDRNMTkgNEwxOSA5TTE5IDRMMTQgOU0xNCA0TDEwIDEwIiBzdHJva2U9IiM2Yzc1N2QiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=" 
                                                alt="No Image"
                                                style="width: 60px; height: 60px; opacity: 0.5;">
                                        </div>
                                    @endif
                                </td>
                                <!-- Product Information -->
                                <td style="white-space: normal; word-wrap: break-word; max-width: 200px;">
                                    <strong>{{ $auction->product_name ?? '—' }}</strong>
                                    @if($auction->description)
                                        <br><small class="text-muted">{{ Str::limit($auction->description, 50) }}</small>
                                    @endif
                                </td>
                                <td style="white-space: normal; word-wrap: break-word; max-width: 120px;">
                                    {{ $auction->brand ?? '—' }}
                                </td>
                                <td style="white-space: normal; word-wrap: break-word; max-width: 120px;">
                                    {{ $auction->category ?? '—' }}
                                </td>
                                <td>
                                    @if($auction->condition === 'sealed')
                                        <span class="badge bg-success">Sealed</span>
                                    @elseif($auction->condition === 'back in box')
                                        <span class="badge bg-warning">Back in Box</span>
                                    @elseif($auction->condition === 'loose')
                                        <span class="badge bg-info">Loose</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($auction->condition ?? 'Unknown') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="diamond-icon">💎</span>{{ number_format($auction->starting_price, 0) }}
                                </td>
                                <td>
                                    <span class="diamond-icon">💎</span>{{ number_format($auction->current_bid ?? $auction->starting_price, 0) }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $auction->bids_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($auction->status === 'pending')
                                        <span class="badge bg-warning">Pending Review</span>
                                    @elseif($auction->status === 'approved')
                                        <span class="badge bg-info">Approved</span>
                                    @elseif($auction->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($auction->status === 'ended')
                                        <span class="badge bg-danger">Ended</span>
                                    @elseif($auction->status === 'completed')
                                        <span class="badge bg-dark">Completed</span>
                                    @elseif($auction->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-dark">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auction->end_time)
                                        <small>{{ $auction->end_time->format('M j, Y H:i') }}</small>
                                        @if($auction->end_time->isPast())
                                            <br><span class="badge bg-danger">Ended</span>
                                        @else
                                            <br><span class="badge bg-success">Active</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- View Details Button - Always Available -->
                                    <a href="{{ route('admin.auctions.show', $auction->id) }}" class="btn btn-sm btn-info mb-1" title="View Auction Details">
                                        <i class="bi bi-eye"></i> View
                                    </a>

                                    <!-- Pending Status: Approve Button -->
                                    @if($auction->status === 'pending')
                                        <form action="{{ route('admin.auctions.approve', $auction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Approve this auction?')" title="Approve Auction">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Approved Status: Activate Button -->
                                    @if($auction->status === 'approved')
                                        <form action="{{ route('admin.auctions.activate', $auction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Activate this auction?')" title="Activate Auction">
                                                <i class="bi bi-play-circle"></i> Activate
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Pending/Approved Status: Reject Button -->
                                    @if(in_array($auction->status, ['pending', 'approved']))
                                        <form action="{{ route('admin.auctions.reject', $auction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this auction?')" title="Reject Auction">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Ended Status: Complete Auction Button -->
                                    @if($auction->status === 'ended')
                                        <form action="{{ route('admin.auctions.complete', $auction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning mb-1" onclick="return confirm('Complete this auction and process payouts?')" title="Complete Auction">
                                                <i class="bi bi-check-all"></i> Complete
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Completed Status: Determine Winner Button -->
                                    @if($auction->status === 'completed' && !$auction->winner_id)
                                        <form action="{{ route('admin.auctions.determine-winner', $auction->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning mb-1" onclick="return confirm('Determine winner for this auction?')" title="Determine Winner">
                                                <i class="bi bi-trophy"></i> Winner
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $auctions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Auctions Found</h4>
                    <p class="text-muted">There are no customer auctions to monitor at the moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .auction-row {
        display: table-row;
    }
    
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .table th {
        background-color: #343a40;
        color: white;
        font-weight: 600;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .diamond-icon {
        color: #4fc3f7;
        font-weight: bold;
    }
    
    .quick-actions {
        margin-bottom: 1rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusTabs = document.getElementById('statusTabs');
        const auctionRows = document.querySelectorAll('.auction-row');
        
        statusTabs.addEventListener('click', function(e) {
            e.preventDefault();
            const target = e.target.closest('a');
            if (!target) return;
            
            // Update active tab
            document.querySelectorAll('#statusTabs .nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            target.classList.add('active');
            
            const status = target.getAttribute('data-status');
            
            // Filter rows
            auctionRows.forEach(row => {
                if (status === 'all') {
                    row.style.display = 'table-row';
                } else {
                    if (row.getAttribute('data-status') === status) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
@endsection