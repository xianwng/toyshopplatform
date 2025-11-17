@extends('customer.layouts.cmaster')

@section('title', 'Transaction History | Toyspace')

@section('styles')
<style>
    :root {
        --primary: #667eea;
        --secondary: #764ba2;
        --light: #f8f9fa;
        --dark: #343a40;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --gray: #6c757d;
        --gray-light: #e9ecef;
    }

    .transaction-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 100px 0 60px;
        text-align: center;
        position: relative;
    }

    .transaction-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 15px;
    }
    
    .transaction-section {
        padding: 60px 0;
        background: var(--light);
        min-height: 70vh;
    }

    .transaction-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid var(--gray-light);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .transaction-card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 20px 25px;
    }
    
    .transaction-card-body {
        padding: 0;
    }

    .transaction-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-light);
        transition: all 0.3s ease;
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-item:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .transaction-info {
        flex: 1;
    }

    .transaction-type {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .transaction-description {
        color: var(--gray);
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .transaction-meta {
        display: flex;
        gap: 15px;
        font-size: 0.8rem;
        color: var(--gray);
    }

    .transaction-amount {
        text-align: right;
        min-width: 120px;
    }

    .amount-positive {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--success);
    }

    .amount-negative {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--danger);
    }

    .transaction-date {
        font-size: 0.85rem;
        color: var(--gray);
        margin-top: 5px;
    }

    .type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-purchase {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }

    .badge-bonus {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success);
    }

    .badge-used {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: var(--gray);
        margin-bottom: 20px;
    }
    
    .empty-state-title {
        color: var(--gray);
        margin-bottom: 15px;
        font-size: 1.5rem;
    }
    
    .empty-state-text {
        color: var(--gray);
        margin-bottom: 30px;
    }

    .filter-section {
        background: white;
        padding: 20px 25px;
        border-bottom: 1px solid var(--gray-light);
    }

    .filter-group {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-select {
        border: 2px solid var(--gray-light);
        border-radius: 8px;
        padding: 8px 12px;
        background: white;
        color: var(--dark);
    }

    .filter-select:focus {
        border-color: var(--primary);
        outline: none;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        border: 1px solid var(--gray-light);
        margin-bottom: 20px;
    }

    .stats-title {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 8px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .stats-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stats-value.positive {
        color: var(--success);
    }

    .stats-value.negative {
        color: var(--danger);
    }

    .current-balance {
        background: linear-gradient(135deg, #4fc3f7 0%, #29b6f6 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }

    .balance-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    .balance-amount {
        font-size: 2rem;
        font-weight: 700;
    }

    .btn-back {
        background: var(--gray-light);
        color: var(--dark);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-back:hover {
        background: #d1d5db;
        transform: translateY(-2px);
        color: var(--dark);
        text-decoration: none;
    }

    .pagination-custom .page-link {
        border-radius: 8px;
        margin: 0 3px;
        border: 1px solid var(--gray-light);
        color: var(--primary);
    }
    
    .pagination-custom .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
    }

    @media (max-width: 768px) {
        .transaction-title {
            font-size: 2.2rem;
        }
        
        .transaction-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .transaction-amount {
            text-align: left;
            min-width: auto;
        }
        
        .filter-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-select {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .transaction-header {
            padding: 80px 0 40px;
        }
        
        .transaction-section {
            padding: 40px 0;
        }
        
        .transaction-card-header {
            padding: 15px 20px;
        }
        
        .transaction-item {
            padding: 15px 20px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="transaction-header">
    <div class="container">
        <h1 class="transaction-title">Transaction History</h1>
        <p class="text-white opacity-90 fs-5">Track your diamond purchases and usage</p>
    </div>
</section>

<!-- Transaction Section -->
<section class="transaction-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <!-- Current Balance -->
                <div class="current-balance">
                    <div class="balance-label">Current Diamond Balance</div>
                    <div class="balance-amount">💎{{ number_format(Auth::user()->diamond_balance, 0) }}</div>
                </div>

                <!-- Quick Stats -->
                <div class="stats-card">
                    <div class="stats-title">Total Purchased</div>
                    <div class="stats-value positive">💎{{ number_format($totalPurchased, 0) }}</div>
                </div>

                <div class="stats-card">
                    <div class="stats-title">Total Used</div>
                    <div class="stats-value negative">💎{{ number_format($totalUsed, 0) }}</div>
                </div>

                <div class="stats-card">
                    <div class="stats-title">Total Transactions</div>
                    <div class="stats-value">{{ $totalTransactions }}</div>
                </div>

                <!-- Back Button -->
                <a href="{{ route('customer.wallet') }}" class="btn-back w-100 justify-content-center">
                    <i class="fa fa-arrow-left me-2"></i> Back to Wallet
                </a>
            </div>

            <div class="col-lg-9">
                <div class="transaction-card">
                    <div class="transaction-card-header">
                        <h3 class="mb-0"><i class="fa fa-history me-2"></i>All Transactions</h3>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="filter-group">
                            <select class="filter-select" id="typeFilter">
                                <option value="all">All Types</option>
                                <option value="purchase">Purchases</option>
                                <option value="bonus">Bonuses</option>
                                <option value="used">Usage</option>
                            </select>

                            <select class="filter-select" id="sortFilter">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                            </select>

                            <input type="text" class="filter-select" id="searchFilter" placeholder="Search transactions...">
                        </div>
                    </div>

                    <div class="transaction-card-body" id="transactionList">
                        @if($transactions->count() > 0)
                            @foreach($transactions as $transaction)
                            <div class="transaction-item" data-type="{{ $transaction->transaction_type }}">
                                <div class="transaction-info">
                                    <div class="transaction-type">
                                        <span class="type-badge badge-{{ $transaction->transaction_type }}">
                                            {{ ucfirst($transaction->transaction_type) }}
                                        </span>
                                        {{ $transaction->description }}
                                    </div>
                                    <div class="transaction-meta">
                                        <span class="transaction-id">
                                            <i class="fa fa-hashtag me-1"></i>{{ $transaction->reference_id }}
                                        </span>
                                        <span class="transaction-date">
                                            <i class="fa fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('M j, Y g:i A') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="transaction-amount">
                                    @if($transaction->transaction_type === 'purchase' || $transaction->transaction_type === 'bonus')
                                        <div class="amount-positive">+💎{{ number_format($transaction->amount, 0) }}</div>
                                    @else
                                        <div class="amount-negative">-💎{{ number_format($transaction->amount, 0) }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <!-- Pagination -->
                            @if($transactions->hasPages())
                            <div class="p-4">
                                {{ $transactions->links() }}
                            </div>
                            @endif

                        @else
                            <!-- Empty State -->
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fa fa-exchange-alt"></i>
                                </div>
                                <h3 class="empty-state-title">No Transactions Found</h3>
                                <p class="empty-state-text">You haven't made any diamond transactions yet.</p>
                                <a href="{{ route('customer.wallet') }}" class="btn-back">
                                    <i class="fa fa-shopping-cart me-2"></i> Purchase Diamonds
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Filter functionality
        $('#typeFilter, #sortFilter, #searchFilter').on('change keyup', function() {
            filterTransactions();
        });

        function filterTransactions() {
            const typeFilter = $('#typeFilter').val();
            const searchFilter = $('#searchFilter').val().toLowerCase();
            
            $('.transaction-item').each(function() {
                const transactionType = $(this).data('type');
                const transactionText = $(this).text().toLowerCase();
                
                let typeMatch = typeFilter === 'all' || transactionType === typeFilter;
                let searchMatch = transactionText.includes(searchFilter);
                
                if (typeMatch && searchMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Auto-dismiss alerts
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);

        // Add hover effects
        $('.transaction-item').hover(
            function() {
                $(this).css('transform', 'translateX(5px)');
            },
            function() {
                $(this).css('transform', 'translateX(0)');
            }
        );
    });
</script>
@endsection