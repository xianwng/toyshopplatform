<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Wallet - Toyspace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .wallet-container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .wallet-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .wallet-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .balance-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .exchange-rate {
            background: linear-gradient(135deg, #fff9c4 0%, #fffde7 100%);
            border: 2px solid #ffd700;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        
        .bundles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .bundle-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border: 2px solid #e0e0e0;
            user-select: none;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .bundle-card:hover {
            transform: translateY(-5px);
            border-color: #6a11cb;
            box-shadow: 0 8px 20px rgba(106,17,203,0.15);
        }
        
        .bundle-card.selected {
            background: linear-gradient(135deg, #f8f9ff 0%, #eef0ff 100%);
            border-color: #6a11cb;
            box-shadow: 0 8px 20px rgba(106,17,203,0.2);
        }
        
        .purchase-btn {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .purchase-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .purchase-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106,17,203,0.3);
        }
        
        .back-btn {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .bundle-badge {
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 0.75rem;
            margin-top: 8px;
            display: inline-block;
            font-weight: 600;
        }
        
        .badge-primary { background: #6a11cb; color: white; }
        .badge-warning { background: #ffd700; color: #2c3e50; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-default { background: #6c757d; color: white; }

        .user-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            color: white;
            text-align: center;
        }

        .transaction-history {
            margin-top: 20px;
        }

        .transaction-item {
            border-left: 4px solid #6a11cb;
            padding: 10px 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }

        .transaction-positive {
            border-left-color: #28a745;
        }

        .transaction-negative {
            border-left-color: #dc3545;
        }

        .price-breakdown {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }

        .tax-info {
            background: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            font-size: 0.85rem;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wallet-container">
            <!-- Back Button -->
            <div class="text-start">
                <a href="{{ route('chome') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </div>

            <!-- User Info -->
            <div class="user-info text-center">
                <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                <small class="opacity-75">{{ $user->email }}</small>
            </div>

            <!-- Wallet Content -->
            <div class="wallet-content">
                <!-- Wallet Header -->
                <div class="wallet-header">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-gem fa-3x text-warning me-3"></i>
                        <div>
                            <h3 class="mb-1 fw-bold">Virtual Wallet</h3>
                            <p class="mb-0 opacity-90">Your exclusive Toyspace currency</p>
                        </div>
                    </div>
                </div>

                <!-- Wallet Body -->
                <div class="p-4">
                    <!-- Balance Card -->
                    <div class="balance-card">
                        <div class="text-muted small mb-2">CURRENT DIAMOND BALANCE</div>
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                            <i class="fas fa-gem fa-2x text-warning"></i>
                            <span id="currentBalance" class="display-4 fw-bold text-dark">{{ $user->diamond_balance ?? 0 }}</span>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-gem me-1"></i> Diamonds Available
                        </span>
                    </div>

                    <!-- Exchange Rate -->
                    <div class="exchange-rate">
                        <div class="text-muted small mb-2">EXCHANGE RATE</div>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <span class="fw-bold">1 Peso</span>
                            <i class="fas fa-equals text-muted"></i>
                            <i class="fas fa-gem text-warning"></i>
                            <span class="fw-bold">1 Diamond</span>
                        </div>
                        <div class="tax-info">
                            <i class="fas fa-info-circle me-1"></i>
                            All prices include 12% VAT as required by Philippine law
                        </div>
                    </div>

                    <!-- Diamond Bundles -->
                    <div class="diamond-bundles-section">
                        <h4 class="fw-semibold text-dark text-center mb-4">Choose Diamond Bundle</h4>
                        
                        @if($bundles->count() > 0)
                        <div class="bundles-grid">
                            @foreach($bundles as $bundle)
                            <div class="bundle-card" 
                                 data-diamonds="{{ $bundle->diamond_amount }}" 
                                 data-amount="{{ $bundle->final_price }}"
                                 data-bundle-id="{{ $bundle->id }}">
                                <i class="fas fa-gem fa-2x text-warning mb-3"></i>
                                <div class="h4 fw-bold text-dark mb-2">
                                    {{ number_format($bundle->diamond_amount) }}
                                </div>
                                <div class="h5 text-success fw-semibold mb-1">
                                    ₱{{ number_format($bundle->final_price, 2) }}
                                </div>
                                <div class="price-breakdown">
                                    <small class="text-muted">
                                        Base: ₱{{ number_format($bundle->original_price, 2) }} + VAT: ₱{{ number_format($bundle->tax_amount, 2) }}
                                    </small>
                                </div>
                                @if($bundle->badge_text)
                                    @php
                                        $badgeClass = 'badge-default';
                                        if ($bundle->badge_type == 'primary') $badgeClass = 'badge-primary';
                                        elseif ($bundle->badge_type == 'warning') $badgeClass = 'badge-warning';
                                        elseif ($bundle->badge_type == 'danger') $badgeClass = 'badge-danger';
                                    @endphp
                                    <div class="bundle-badge {{ $badgeClass }}">
                                        {{ $bundle->badge_text }}
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <!-- Buy Button - Redirects to Payment Page -->
                        <form id="purchaseForm" method="GET" action="{{ route('customer.wallet.payment') }}">
                            <input type="hidden" name="bundle_id" id="selectedBundleId" value="">
                            <input type="hidden" name="diamonds" id="selectedDiamondsValue" value="">
                            <input type="hidden" name="amount" id="selectedAmountValue" value="">
                            <button type="submit" class="purchase-btn" id="confirmBuyDiamonds" disabled>
                                <i class="fas fa-shopping-cart me-2"></i>
                                Select a bundle to purchase
                            </button>
                        </form>
                        @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-gem fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No diamond bundles available at the moment.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions mt-4">
                        <h5 class="fw-semibold text-dark text-center mb-3">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.wallet.transactions') }}" class="btn btn-outline-info">
                                <i class="fas fa-history me-2"></i>View Transaction History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Success!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p id="successMessage">Payment processed successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Virtual Wallet loaded successfully!');
        
        const bundleCards = document.querySelectorAll('.bundle-card');
        const buyButton = document.getElementById('confirmBuyDiamonds');
        const selectedBundleId = document.getElementById('selectedBundleId');
        const selectedDiamondsValue = document.getElementById('selectedDiamondsValue');
        const selectedAmountValue = document.getElementById('selectedAmountValue');
        
        console.log('Found bundle cards:', bundleCards.length);
        
        let selectedDiamonds = 0;
        let selectedAmount = 0;
        let selectedBundleIdValue = 0;

        // Bundle click handler
        bundleCards.forEach(card => {
            card.addEventListener('click', function() {
                console.log('🎯 Bundle clicked!');
                
                // Remove selected class from all cards
                bundleCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');

                // Update selected values
                selectedDiamonds = parseInt(this.getAttribute('data-diamonds'));
                selectedAmount = parseFloat(this.getAttribute('data-amount'));
                selectedBundleIdValue = parseInt(this.getAttribute('data-bundle-id'));

                console.log('Selected:', selectedDiamonds, 'diamonds for ₱', selectedAmount, 'Bundle ID:', selectedBundleIdValue);

                // Update hidden form values
                selectedBundleId.value = selectedBundleIdValue;
                selectedDiamondsValue.value = selectedDiamonds;
                selectedAmountValue.value = selectedAmount;

                // Update buy button
                buyButton.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>Buy ${selectedDiamonds.toLocaleString()} Diamonds - ₱${selectedAmount.toFixed(2)}`;
                buyButton.disabled = false;
                
                console.log('✅ Buy button enabled! Form values updated.');
            });
        });

        // Form submission handler to ensure it works
        const purchaseForm = document.getElementById('purchaseForm');
        purchaseForm.addEventListener('submit', function(e) {
            console.log('Form submitting to payment page...');
            console.log('Bundle ID:', selectedBundleId.value);
            console.log('Diamonds:', selectedDiamondsValue.value);
            console.log('Amount:', selectedAmountValue.value);
            
            if (!selectedBundleId.value) {
                e.preventDefault();
                alert('Please select a bundle first.');
                return;
            }
            
            // Let the form submit normally
            console.log('✅ Form submitted successfully to payment page');
        });
        
        console.log('Virtual Wallet ready! Click any bundle to select it.');
    });

    // Check URL for success parameter
    function checkForSuccessMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment') === 'success') {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Remove the parameter from URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }

    // Check for success message on page load
    checkForSuccessMessage();
    </script>
</body>
</html>