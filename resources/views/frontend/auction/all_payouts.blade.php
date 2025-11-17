@extends('frontend.layout.master')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-dark">All Processed Payouts</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($auctions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Auction ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>Payout Amount</th>
                                <th>Status</th>
                                <th>Approved/Rejected At</th>
                                <th>Approved By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auctions as $auction)
                            <tr>
                                <td>{{ $auction->id }}</td>
                                <td>
                                    <strong>{{ $auction->product_name }}</strong>
                                </td>
                                <td>{{ $auction->user->username ?? 'Unknown' }}</td>
                                <td>
                                    <span class="diamond-icon">💎</span>{{ number_format($auction->payout_amount, 0) }}
                                </td>
                                <td>
                                    @if($auction->payout_status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auction->payout_approved_at)
                                        {{ $auction->payout_approved_at->format('M j, Y H:i') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($auction->payout_approved_by)
                                        Admin #{{ $auction->payout_approved_by }}
                                    @else
                                        <span class="text-muted">—</span>
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
                    <h4 class="text-muted mt-3">No Processed Payouts</h4>
                    <p class="text-muted">No payouts have been processed yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .diamond-icon {
        color: #4fc3f7;
        font-weight: bold;
    }
</style>
@endsection