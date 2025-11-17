@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-dark">Pending Payout Approvals</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($pendingPayouts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Auction ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>Winner</th>
                                <th>Payout Amount</th>
                                <th>Ended At</th>
                                <th>Seller Reply Deadline</th>
                                <th>Escrow Held For</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPayouts as $auction)
                            <tr>
                                <td>{{ $auction->id }}</td>
                                <td>
                                    <strong>{{ $auction->product_name }}</strong>
                                    @if($auction->description)
                                        <br><small class="text-muted">{{ Str::limit($auction->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $auction->user->username ?? 'Unknown' }}</td>
                                <td>{{ $auction->winner->username ?? 'No Winner' }}</td>
                                <td>
                                    <span class="diamond-icon">💎</span>{{ number_format($auction->payout_amount, 0) }}
                                </td>
                                <td>{{ $auction->updated_at->format('M j, Y H:i') }}</td>
                                <td>
                                    @if($auction->seller_reply_deadline)
                                        @if($auction->is_seller_reply_overdue)
                                            <span class="badge bg-danger">OVERDUE</span>
                                            <br><small>{{ $auction->seller_reply_deadline->format('M j, Y H:i') }}</small>
                                        @else
                                            <span class="badge bg-warning">{{ $auction->seller_reply_deadline->diffForHumans() }}</span>
                                            <br><small>{{ $auction->seller_reply_deadline->format('M j, Y H:i') }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auction->escrow_held_at)
                                        {{ $auction->escrow_days_held }} hours
                                        <br><small>Since {{ $auction->escrow_held_at->format('M j, Y H:i') }}</small>
                                    @else
                                        <span class="text-muted">Not held</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.auctions.show', $auction->id) }}" class="btn btn-sm btn-info mb-1">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.auctions.approve-payout', $auction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-1 confirm-approve" data-amount="{{ number_format($auction->payout_amount, 0) }}" data-seller="{{ $auction->user->username }}">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.auctions.reject-payout', $auction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger mb-1 confirm-reject">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                    @if($auction->is_seller_reply_overdue)
                                    <form action="{{ route('admin.auctions.force-refund-buyer', $auction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning mb-1 confirm-refund">
                                            <i class="bi bi-arrow-counterclockwise"></i> Auto Refund
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
                    {{ $pendingPayouts->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Pending Payouts</h4>
                    <p class="text-muted">All payouts have been processed.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Escrow Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total in Escrow</h5>
                    <h3 class="card-text">💎{{ number_format($totalPendingAmount, 0) }}</h3>
                    <p class="card-text">{{ $pendingPayouts->count() }} pending payouts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Overdue Replies</h5>
                    <h3 class="card-text">{{ $pendingPayouts->where('is_seller_reply_overdue', true)->count() }}</h3>
                    <p class="card-text">Need auto-refund</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ready for Release</h5>
                    <h3 class="card-text">{{ $pendingPayouts->where('is_seller_reply_overdue', false)->count() }}</h3>
                    <p class="card-text">Seller replied on time</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Process Overdue</h5>
                    <form action="{{ route('admin.auctions.process-overdue-replies') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light w-100 confirm-bulk-process">
                            <i class="bi bi-arrow-repeat"></i> Run Auto-Refund
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .diamond-icon {
        color: #4fc3f7;
        font-weight: bold;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle approve payout buttons
    const approveBtns = document.querySelectorAll('.confirm-approve');
    approveBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const amount = this.getAttribute('data-amount');
            const seller = this.getAttribute('data-seller');
            if (!confirm(`Approve payout of 💎${amount} to ${seller}?`)) {
                e.preventDefault();
            }
        });
    });

    // Handle reject payout buttons
    const rejectBtns = document.querySelectorAll('.confirm-reject');
    rejectBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Reject payout for this auction? This will refund the buyer.')) {
                e.preventDefault();
            }
        });
    });

    // Handle auto-refund buttons
    const refundBtns = document.querySelectorAll('.confirm-refund');
    refundBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Force refund to buyer? Seller didn\'t reply within 12 hours.')) {
                e.preventDefault();
            }
        });
    });

    // Handle bulk process button
    const bulkProcessBtn = document.querySelector('.confirm-bulk-process');
    if (bulkProcessBtn) {
        bulkProcessBtn.addEventListener('click', function(e) {
            if (!confirm('Process all overdue seller replies and auto-refund?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection