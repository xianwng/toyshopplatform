<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Virtual Wallet - Toyspace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <!-- Add CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .payment-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .payment-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .payment-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .payment-method-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        .payment-method-card:hover {
            border-color: #6a11cb;
            transform: translateY(-2px);
        }
        
        .payment-method-card.selected {
            border-color: #6a11cb;
            background: linear-gradient(135deg, #f8f9ff 0%, #eef0ff 100%);
        }
        
        .qr-code-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }
        
        .qr-code-wrapper {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e0e0e0;
        }
        
        .qr-placeholder {
            width: 200px;
            height: 200px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #6c757d;
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
        
        .confirm-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .confirm-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }
        
        .confirm-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .payment-instructions {
            background: #e7f3ff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid #2575fc;
        }
        
        .payment-info {
            background: #fff3cd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid #ffc107;
        }
        
        #qrcode {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        #qrcode canvas {
            border-radius: 8px;
        }
        
        .qr-success-message {
            margin-top: 10px;
        }

        .success-alert {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <!-- Back Button -->
            <div class="text-start">
                <a href="{{ route('customer.wallet') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Wallet
                </a>
            </div>

            <!-- Payment Content -->
            <div class="payment-content">
                <!-- Payment Header -->
                <div class="payment-header">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="fas fa-credit-card fa-3x text-warning me-3"></i>
                        <div>
                            <h3 class="mb-1 fw-bold">Complete Your Purchase</h3>
                            <p class="mb-0 opacity-90">Scan QR code to pay</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Body -->
                <div class="p-4">
                    <!-- Order Summary -->
                    <div class="order-summary">
                        <h5 class="fw-bold text-dark mb-3">Order Summary</h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted small">Bundle</div>
                                <div class="h5 fw-bold text-primary">
                                    {{ number_format($bundle->diamond_amount) }} 
                                    <i class="fas fa-gem text-warning"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Total Amount</div>
                                <div class="h5 fw-bold text-success">
                                    ₱{{ number_format($bundle->price, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-muted small">Transaction ID</div>
                            <div class="fw-bold text-dark">{{ $transactionId }}</div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <h5 class="fw-bold text-dark mb-3">Select Payment Method</h5>
                    
                    <div class="payment-methods">
                        <!-- GCash -->
                        <div class="payment-method-card" data-method="gcash">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5b/GCash_logo.svg" alt="GCash" style="height: 40px;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">GCash</h6>
                                    <p class="text-muted small mb-0">Pay using GCash QR Code</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Maya -->
                        <div class="payment-method-card" data-method="paymaya">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/43/Maya_logo.png" alt="Maya" style="height: 40px;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">Maya</h6>
                                    <p class="text-muted small mb-0">Pay using Maya QR Code</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div id="qrCodeSection" style="display: none;">
                        <h5 class="fw-bold text-dark mb-3" id="qrTitle">Scan QR Code to Pay</h5>
                        
                        <div class="qr-code-container">
                            <div class="qr-code-wrapper">
                                <div id="qrCodePlaceholder" class="qr-placeholder">
                                    <div class="text-center">
                                        <i class="fas fa-qrcode fa-3x mb-2"></i>
                                        <div>QR Code will appear here</div>
                                    </div>
                                </div>
                                <div id="qrcode"></div>
                            </div>
                            <div id="qrSuccessMessage" class="qr-success-message"></div>
                            <div class="mt-3">
                                <div class="text-muted small">Amount to Pay</div>
                                <div class="h4 fw-bold text-success">₱{{ number_format($bundle->price, 2) }}</div>
                            </div>
                            
                            <!-- Payment Instructions -->
                            <div class="payment-instructions mt-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>How to Pay:</h6>
                                <ol class="small mb-0 ps-3">
                                    <li>Open your <span id="paymentAppName">payment app</span></li>
                                    <li>Tap "Scan QR Code"</li>
                                    <li>Point your camera at the QR code above</li>
                                    <li>Confirm the amount and complete payment</li>
                                    <li>Click "I have completed the payment" below</li>
                                </ol>
                            </div>
                            
                            <!-- Payment Information -->
                            <div class="payment-info mt-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-receipt me-2"></i>Payment Details:</h6>
                                <div class="small">
                                    <div><strong>Merchant:</strong> Toyspace Virtual Wallet</div>
                                    <div><strong>Reference:</strong> {{ $transactionId }}</div>
                                    <div><strong>Amount:</strong> ₱{{ number_format($bundle->price, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Payment Button -->
                        <form id="paymentForm" method="POST" action="{{ route('customer.wallet.process-payment') }}">
                            @csrf
                            <input type="hidden" name="bundle_id" value="{{ $bundle->id }}">
                            <input type="hidden" name="transaction_id" value="{{ $transactionId }}">
                            <input type="hidden" name="payment_method" id="paymentMethod" value="">
                            <button type="submit" class="confirm-btn" id="confirmPayment" disabled>
                                <i class="fas fa-check-circle me-2"></i>
                                I have completed the payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodCards = document.querySelectorAll('.payment-method-card');
        const qrCodeSection = document.getElementById('qrCodeSection');
        const qrTitle = document.getElementById('qrTitle');
        const paymentMethodInput = document.getElementById('paymentMethod');
        const confirmPaymentBtn = document.getElementById('confirmPayment');
        const qrCodePlaceholder = document.getElementById('qrCodePlaceholder');
        const paymentAppName = document.getElementById('paymentAppName');
        const qrcodeElement = document.getElementById('qrcode');
        const qrSuccessMessage = document.getElementById('qrSuccessMessage');

        let selectedMethod = '';
        let qrCodeGenerated = false;

        // Payment method selection
        paymentMethodCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                paymentMethodCards.forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('.fa-check-circle').style.display = 'none';
                });
                
                // Add selected class to clicked card
                this.classList.add('selected');
                this.querySelector('.fa-check-circle').style.display = 'block';

                // Update selected method
                selectedMethod = this.getAttribute('data-method');
                paymentMethodInput.value = selectedMethod;

                // Show QR code section
                qrCodeSection.style.display = 'block';
                
                // Update QR code title and app name
                const methodName = selectedMethod === 'gcash' ? 'GCash' : 'Maya';
                qrTitle.textContent = 'Scan ' + methodName + ' QR Code to Pay';
                paymentAppName.textContent = methodName;

                // Generate QR code
                generateQRCode(selectedMethod);
            });
        });

        // Generate QR Code function
        function generateQRCode(paymentMethod) {
            // Reset states
            qrCodeGenerated = false;
            confirmPaymentBtn.disabled = true;
            qrSuccessMessage.innerHTML = '';
            
            // Show loading state
            qrCodePlaceholder.style.display = 'flex';
            qrcodeElement.innerHTML = '';
            qrCodePlaceholder.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><div>Generating QR Code...</div></div>';
            
            // Generate payment data based on method
            let paymentData = '';
            const amount = '{{ $bundle->price }}';
            const transactionId = '{{ $transactionId }}';
            const merchant = 'Toyspace Virtual Wallet';
            const diamonds = '{{ $bundle->diamond_amount }}';
            
            if (paymentMethod === 'gcash') {
                paymentData = 'GCASH Payment\nAmount: ₱' + amount + '\nMerchant: ' + merchant + '\nReference: ' + transactionId + '\nDiamonds: ' + diamonds;
            } else if (paymentMethod === 'paymaya') {
                paymentData = 'MAYA Payment\nAmount: ₱' + amount + '\nMerchant: ' + merchant + '\nReference: ' + transactionId + '\nDiamonds: ' + diamonds;
            }
            
            // Generate QR code after a short delay
            setTimeout(function() {
                try {
                    // Clear previous QR code
                    qrcodeElement.innerHTML = '';
                    
                    // Generate QR code
                    const typeNumber = 0; // Auto detect
                    const errorCorrectionLevel = 'L';
                    const qr = qrcode(typeNumber, errorCorrectionLevel);
                    qr.addData(paymentData);
                    qr.make();
                    
                    // Create and style the QR code
                    const qrImage = qr.createImgTag(4, 8);
                    qrcodeElement.innerHTML = qrImage;
                    
                    // Hide placeholder and show QR code
                    qrCodePlaceholder.style.display = 'none';
                    
                    // Add success styling
                    const qrCanvas = qrcodeElement.querySelector('img');
                    if (qrCanvas) {
                        qrCanvas.style.borderRadius = '8px';
                        qrCanvas.style.border = '2px solid #28a745';
                    }
                    
                    // Enable confirm button
                    confirmPaymentBtn.disabled = false;
                    qrCodeGenerated = true;
                    
                    // Add success message below QR code
                    qrSuccessMessage.innerHTML = '<div class="text-center text-success small"><i class="fas fa-check-circle me-1"></i>QR Code Ready for Scanning</div>';
                    
                } catch (error) {
                    console.error('QR Code generation error:', error);
                    qrCodePlaceholder.innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><div>Failed to generate QR</div><small>Please try again</small></div>';
                    qrCodePlaceholder.style.display = 'flex';
                }
            }, 500);
        }

        // Form submission - FIXED VERSION
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener('submit', function(e) {
            console.log('Form submitted!');
            e.preventDefault(); // This prevents the normal form submission
            
            if (!selectedMethod) {
                alert('Please select a payment method first.');
                return;
            }
            
            if (!qrCodeGenerated) {
                alert('QR code is still generating. Please wait.');
                return;
            }

            const formData = new FormData(this);
            const originalText = confirmPaymentBtn.innerHTML;
            
            confirmPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            confirmPaymentBtn.disabled = true;

            console.log('Sending payment request...');

            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            
            console.log('CSRF Token:', csrfToken);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(function(response) {
                console.log('Response received:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                console.log('Payment response data:', data);
                if (data.success) {
                    // Show success message with animation
                    const successHTML = '<div class="alert alert-success alert-dismissible fade show success-alert" role="alert"><h4 class="alert-heading"><i class="fas fa-check-circle"></i> Payment Successful!</h4><p class="mb-1"><strong>' + data.purchased_diamonds.toLocaleString() + ' diamonds</strong> have been added to your wallet!</p><p class="mb-0">Your new balance: <strong>' + data.new_balance.toLocaleString() + ' diamonds</strong></p><div class="mt-2"><small>Redirecting you back to your wallet...</small></div></div>';
                    
                    // Insert success message
                    paymentForm.insertAdjacentHTML('beforebegin', successHTML);
                    
                    // Scroll to top to show success message
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    console.log('Payment successful! Redirecting in 3 seconds...');
                    
                    // Redirect after 3 seconds
                    setTimeout(function() {
                        window.location.href = "{{ route('customer.wallet') }}";
                    }, 3000);
                    
                } else {
                    console.error('Payment failed:', data.message);
                    alert('Payment failed: ' + (data.message || 'Unknown error'));
                    confirmPaymentBtn.innerHTML = originalText;
                    confirmPaymentBtn.disabled = false;
                }
            })
            .catch(function(error) {
                console.error('Fetch Error:', error);
                alert('An error occurred. Please try again. Error: ' + error.message);
                confirmPaymentBtn.innerHTML = originalText;
                confirmPaymentBtn.disabled = false;
            });
        });
    });
    </script>
</body>
</html>