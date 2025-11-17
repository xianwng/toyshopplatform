<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

// Frontend Controllers
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController\FrontController;
use App\Http\Controllers\CustomerController\CustomerProductController;
use App\Http\Controllers\CustomerController\CustomerAuctionController;
use App\Http\Controllers\CustomerController\CustomerCurrencyController;
use App\Http\Controllers\CustomerController\CustomerChatController;
use App\Http\Controllers\Api\AmazonProductController;
use App\Http\Controllers\Security\AuthController;
use App\Http\Controllers\Security\ProfileController;
use App\Http\Controllers\Security\AddressController;

// Admin Controllers - ADD THESE
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AuctionController as AdminAuctionController;

// Super Admin Controllers
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\ProfileController as SuperAdminProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================
// CUSTOMER FRONTEND ROUTES - FIXED
// ========================

// ✅ FIXED: Homepage should point to actual index.blade.php
Route::get('/', function () {
    return view('customer.index'); // This loads your actual homepage
})->name('index');

// ========================
// CORRECTED FIX FOR PRODUCT IMAGES - RUN THIS FIRST
// ========================
Route::get('/fix-product-images-corrected', function() {
    $products = \App\Models\Product::all();
    $fixedCount = 0;
    
    foreach ($products as $product) {
        $images = $product->product_images;
        if (is_array($images)) {
            $fixedImages = [];
            foreach ($images as $image) {
                // Remove any existing 'models/' prefix to avoid duplication
                $cleanImage = basename($image);
                $fixedImages[] = $cleanImage; // Store just the filename without path
            }
            $product->product_images = $fixedImages;
            $product->save();
            $fixedCount++;
        }
    }
    
    return "Fixed {$fixedCount} products. Image paths now point to filenames only. The Product model will automatically look in 'models/' directory.";
});

// ========================
// AUTHENTICATION ROUTES
// ========================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ========================
// REGISTRATION ROUTES
// ========================
Route::get('/register', function () {
    return view('Security.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// ========================
// GOOGLE OAUTH ROUTES
// ========================
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// ========================
// REAL-TIME VALIDATION ROUTES
// ========================
Route::get('/check-user-exists', [AuthController::class, 'checkUserExists']);
Route::post('/check-credentials', [AuthController::class, 'checkCredentials']);
Route::get('/check-username-available', [AuthController::class, 'checkUsername']);
Route::get('/check-email-available', [AuthController::class, 'checkEmailExists']);

// ========================
// HOME ROUTE
// ========================
Route::get('/home', function () {
    return view('customer.chome');
})->name('home');

// ========================
// CUSTOMER PRODUCT ROUTES - UPDATED WITH MULTIPLE IMAGES
// ========================
Route::get('/customer-product', [CustomerProductController::class, 'cproduct'])->name('customer-product');
Route::get('/home/search', [CustomerProductController::class, 'search'])->name('home.search');
Route::get('/cproduct', [CustomerProductController::class, 'cproduct'])->name('cproduct');

// ✅ FIXED: Changed ProductController to AdminProductController
Route::get('/products', [AdminProductController::class, 'index'])->name('frontend.products.index');

Route::get('/products/{product}', [CustomerProductController::class, 'show'])->name('customer.products.show');
Route::get('/products/category/{category}', [CustomerProductController::class, 'category'])->name('customer.products.category');
Route::get('/products/rarity/{rarity}', [CustomerProductController::class, 'rarity'])->name('customer.products.rarity');
Route::get('/products/sort', [CustomerProductController::class, 'sort'])->name('customer.products.sort');

// ========================
// CUSTOMER TRADE & EXCHANGE ROUTES (FIXED ORDER) - UPDATED WITH FIXED ROUTES
// ========================
Route::get('/customer-trading', [TradeController::class, 'index'])->name('customer.trading');
Route::get('/trading', [TradeController::class, 'index'])->name('trading');
Route::post('/trading', [TradeController::class, 'store'])->name('trading.store');

// ✅ ADDED: Customer trade activation route
Route::post('/trading/{trade}/activate', [TradeController::class, 'customerActivateTrade'])->name('trading.activate');

// ✅ ADDED: Trade completion route
Route::post('/trading/{trade}/complete', [TradeController::class, 'completeTrade'])->name('trading.complete');

// ✅ FIXED: Exchange proposal routes - CORRECTED PARAMETER BINDING
Route::get('/trading/{trade}/proposal/create', [TradeController::class, 'showProposal'])->name('trading.proposals.create');
Route::post('/trading/proposals/store', [TradeController::class, 'storeProposal'])->name('trading.proposals.store');
Route::get('/trading/my-proposals', [TradeController::class, 'myProposals'])->name('trading.proposals.my');
// ✅ ADDED: Alias for sent proposals to fix "Route [trading.proposals.sent] not defined"
Route::get('/trading/sent-proposals', [TradeController::class, 'myProposals'])->name('trading.proposals.sent');
Route::get('/trading/received-proposals', [TradeController::class, 'receivedProposals'])->name('trading.proposals.received');
Route::get('/trading/proposals/{proposalId}', [TradeController::class, 'viewProposal'])->name('trading.proposals.view');
Route::post('/trading/proposals/{proposalId}/respond', [TradeController::class, 'respondToProposal'])->name('trading.proposals.respond');
Route::post('/trading/proposals/{proposalId}/cancel', [TradeController::class, 'cancelProposal'])->name('trading.proposals.cancel');

// ✅ ADDED: Missing search route for products
Route::get('/products/search', [CustomerProductController::class, 'search'])->name('customer.products.search');

// ✅ UPDATED: Customer Product Creation Routes with Multiple Images
Route::get('/customer/products/create', [CustomerProductController::class, 'create'])->name('customer.products.create');
Route::get('/customer/products/cadd', [CustomerProductController::class, 'cadd'])->name('customer.products.cadd');
Route::post('/customer/products/store', [CustomerProductController::class, 'store'])->name('customer.products.store');
Route::delete('/customer/products/{product}', [CustomerProductController::class, 'destroy'])->name('customer.products.destroy');
Route::post('/customer/products/{product}/activate', [CustomerProductController::class, 'activate'])->name('customer.products.activate');
Route::post('/customer/products/{product}/deactivate', [CustomerProductController::class, 'deactivate'])->name('customer.products.deactivate');

// ✅ ADDED: Buy Now Route - FIXES THE "Route [buy_now] not defined" ERROR
Route::post('/customer/products/buy-now', [CustomerProductController::class, 'buyNow'])->name('buy_now');

// ========================
// CUSTOMER AUCTION ROUTES - UPDATED WITH MISSING DETAIL ROUTE
// ========================
Route::prefix('customer')->group(function () {
    // Customer Auction CRUD Routes with customer parameter
    Route::get('/auctions', [CustomerAuctionController::class, 'index'])->name('customer.auctions.index');
    Route::get('/auctions/create', [CustomerAuctionController::class, 'create'])->name('customer.auctions.create');
    Route::post('/auctions/store', [CustomerAuctionController::class, 'store'])->name('customer.auctions.store');
    Route::get('/auctions/{id}', [CustomerAuctionController::class, 'show'])->name('customer.auctions.show');
    Route::get('/auctions/{id}/edit', [CustomerAuctionController::class, 'edit'])->name('customer.auctions.edit');
    Route::put('/auctions/{id}', [CustomerAuctionController::class, 'update'])->name('customer.auctions.update');
    Route::delete('/auctions/{id}', [CustomerAuctionController::class, 'destroy'])->name('customer.auctions.destroy');
    
    // ✅ FIXED: Added missing detail route that was referenced in blade file
    Route::get('/auctions/{id}/detail', [CustomerAuctionController::class, 'detail'])->name('customer.auctions.detail');
    
    // Additional customer auction routes
    Route::get('/my-auctions', [CustomerAuctionController::class, 'myAuctions'])->name('customer.auctions.my');
    Route::get('/auctions-search', [CustomerAuctionController::class, 'search'])->name('customer.auctions.search');
    
    // Public auction routes for customers
    Route::get('/public-auctions', [CustomerAuctionController::class, 'publicAuctions'])->name('customer.auctions.public');
    Route::get('/public-auctions/search', [CustomerAuctionController::class, 'searchPublicAuctions'])->name('customer.auctions.public.search');
    Route::get('/public-auctions/{id}', [CustomerAuctionController::class, 'publicShow'])->name('customer.auctions.public.show');
    
    // ✅ UPDATED: Bidding Route with ESCROW system and 12-hour seller reply deadline
    Route::post('/auctions/{id}/bid', [CustomerAuctionController::class, 'placeBid'])->name('customer.auctions.bid');
    
    // ✅ NEW: Escrow management routes with AUTOMATIC payout release
    Route::post('/auctions/{id}/mark-received', [CustomerAuctionController::class, 'markItemReceived'])->name('customer.auctions.mark-received');
    
    // ✅ NEW: Auction ending and escrow processing routes
    Route::post('/auctions/{id}/end-expired', [CustomerAuctionController::class, 'endExpiredAuction'])->name('customer.auctions.end-expired');
    Route::get('/auctions/expired/list', [CustomerAuctionController::class, 'getExpiredAuctions'])->name('customer.auctions.expired-list');
    Route::post('/auctions/auto-end-expired', [CustomerAuctionController::class, 'autoEndExpiredAuctions'])->name('customer.auctions.auto-end-expired');
    
    // ✅ NEW: Escrow status and monitoring routes
    Route::get('/auctions/{id}/escrow-status', [CustomerAuctionController::class, 'getEscrowStatus'])->name('customer.auctions.escrow-status');
    Route::get('/seller/{sellerId}/balance', [CustomerAuctionController::class, 'getSellerBalance'])->name('customer.auctions.seller-balance');
    
    // ✅ NEW: Automatic system checks (for cron/scheduler)
    Route::post('/auctions/schedule-checks', [CustomerAuctionController::class, 'scheduleAutomaticChecks'])->name('customer.auctions.schedule-checks');
});

// ========================
// AJAX AMAZON DATA FETCH ROUTES
// ========================
// ✅ FIXED: Changed ProductController to CustomerProductController for Amazon routes
Route::post('/products/fetch-amazon-data', [CustomerProductController::class, 'fetchAmazonData'])->name('frontend.products.fetch-amazon-data');
// ✅ ENHANCED: Amazon product search route with category and brand filtering
Route::post('/products/search-amazon', [CustomerProductController::class, 'searchAmazon'])->name('frontend.products.search-amazon');

// ========================
// PAYMONGO PAYMENT ROUTES
// ========================
Route::get('/paymongo-payment', [CustomerCurrencyController::class, 'createPaymongoPayment'])->name('customer.create_paymongo_payment');
Route::post('/paymongo-payment-ajax', [CustomerCurrencyController::class, 'createPaymongoPaymentAjax'])->name('customer.create_paymongo_payment_ajax');
Route::get('/payment/success', [CustomerCurrencyController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failed', [CustomerCurrencyController::class, 'paymentFailed'])->name('payment.failed');
Route::post('/payment/webhook', [CustomerCurrencyController::class, 'handleWebhook'])->name('payment.webhook');
Route::get('/check-payment-status/{orderId}', [CustomerCurrencyController::class, 'checkPaymentStatus'])->name('check_payment_status');

// ✅ FIXED: QR Code Generation Route (POST method)
Route::post('/customer/create-display-qr', [CustomerCurrencyController::class, 'generateQRCode'])->name('customer.create_display_qr');

// ========================
// PUBLIC AUCTION ROUTES (For customers to browse and bid)
// ========================
Route::get('/customer-auction', [CustomerAuctionController::class, 'index'])->name('customer.auction');

// ✅ FIXED: Updated this route to use the correct controller method for auction details
Route::get('/auction-details/{id}', [CustomerAuctionController::class, 'detail'])->name('auction_details');

// ========================
// VIRTUAL WALLET ROUTES (Protected by auth middleware)
// ========================
Route::middleware(['auth'])->group(function () {
    Route::get('/wallet', [CustomerCurrencyController::class, 'showWallet'])->name('customer.wallet');
    Route::get('/wallet/payment', [CustomerCurrencyController::class, 'showPaymentPage'])->name('customer.wallet.payment');
    Route::post('/wallet/process-payment', [CustomerCurrencyController::class, 'processPayment'])->name('customer.wallet.process-payment');
    Route::get('/wallet/balance', [CustomerCurrencyController::class, 'getWalletBalance'])->name('customer.wallet.balance');
    Route::get('/wallet/transactions', [CustomerCurrencyController::class, 'showTransactionHistory'])->name('customer.wallet.transactions');
    Route::get('/wallet/transactions-api', [CustomerCurrencyController::class, 'getTransactionHistory'])->name('customer.wallet.transactions.api');
    Route::get('/wallet/purchases', [CustomerCurrencyController::class, 'getPurchaseHistory'])->name('customer.wallet.purchases');
    Route::post('/wallet/update-balance', [CustomerCurrencyController::class, 'updateWalletBalance'])->name('customer.wallet.update-balance');
});

// ========================
// ✅ FIXED CUSTOMER CHAT ROUTES - CORRECTED PARAMETER NAMES
// ========================

// ✅ FIXED: Chat main page
Route::get('/customer-chat', [CustomerChatController::class, 'chatPage'])->name('customer.chat');

// ✅ FIXED: Chat initiation routes - CORRECTED PARAMETER NAMES
Route::get('/customer/chat/auction-chat/{auctionId}', [CustomerChatController::class, 'startAuctionChat'])->name('customer.chat.auction');
Route::get('/customer/chat/auction-winner-chat/{auctionId}', [CustomerChatController::class, 'startAuctionWinnerChat'])->name('customer.chat.auction-winner');

// ✅ FIXED: Changed {productId} to {product} to match Laravel route model binding
Route::get('/customer/chat/regular-chat/{product}', [CustomerChatController::class, 'startRegularChat'])->name('customer.chat.regular');

Route::get('/customer/chat/buy-now-chat/{productId}', [CustomerChatController::class, 'startBuyNowChat'])->name('customer.chat.buy-now-chat');

// ✅ ADDED: Trade proposal chat routes
Route::get('/customer/chat/trade-proposal/{tradeId}', [CustomerChatController::class, 'startTradeProposalChat'])->name('customer.chat.trade-proposal');
Route::get('/customer/chat/trade-proposal/{tradeId}/{proposalId}', [CustomerChatController::class, 'startTradeProposalChat'])->name('customer.chat.trade-proposal-with-id');

// ✅ ADDED: Missing route that was referenced in JavaScript
Route::get('/customer/chat/start-auction-winner-chat/{auctionId}', [CustomerChatController::class, 'startAuctionWinnerChat'])->name('customer.chat.start-auction-winner-chat');

// ✅ FIXED: Chat API routes grouped under prefix
Route::prefix('customer/chat')->group(function () {
    // ✅ NEW: Store buy now data route
    Route::post('/store-buy-now-data', [CustomerChatController::class, 'storeBuyNowData'])->name('customer.chat.store-buy-now-data');
    
    // ✅ FIXED: Dynamic chat routes with payment features and ESCROW management
    Route::get('/conversations', [CustomerChatController::class, 'getConversations'])->name('customer.chat.conversations');
    Route::get('/messages', [CustomerChatController::class, 'getMessages'])->name('customer.chat.messages');
    Route::post('/send-message', [CustomerChatController::class, 'sendMessage'])->name('customer.chat.send-message');
    Route::post('/find-or-create', [CustomerChatController::class, 'findOrCreateConversation'])->name('customer.chat.find-or-create');
    Route::post('/mark-payment-received', [CustomerChatController::class, 'markPaymentReceived'])->name('customer.chat.mark-payment-received');
    
    // ✅ KEEP: Clear auto message route
    Route::post('/clear-auto-message', [CustomerChatController::class, 'clearAutoMessage'])->name('customer.chat.clear-auto-message');
    
    // ✅ ADDED: Clear session route to fix old images
    Route::get('/clear-session', [CustomerChatController::class, 'clearChatSession'])->name('customer.chat.clear-session');
    
    // ✅ NEW: Auto create winner chat route (for system use)
    Route::get('/auto-create-winner-chat/{auctionId}/{winnerId}', [CustomerChatController::class, 'autoCreateWinnerChat'])->name('customer.chat.auto-create-winner');
    
    // ✅ NEW: Auto create trade proposal chat route (for system use)
    Route::get('/auto-create-trade-proposal-chat/{tradeId}/{proposalId}/{senderId}', [CustomerChatController::class, 'autoCreateTradeProposalChat'])->name('customer.chat.auto-create-trade-proposal');
    
    // ✅ NEW: Chat statistics and management routes
    Route::get('/stats', [CustomerChatController::class, 'getChatStats'])->name('customer.chat.stats');
    Route::post('/clear-all-data', [CustomerChatController::class, 'clearAllChatData'])->name('customer.chat.clear-all-data');
});

// ========================
// CUSTOMER PRODUCT CHAT ROUTE - KEEP FOR BACKWARD COMPATIBILITY
// ========================
Route::get('/customer/products/{product}/chat', [CustomerProductController::class, 'chatWithSeller'])->name('customer.products.chat');

// ========================
// OTHER CUSTOMER ROUTES
// ========================
Route::get('/product-view', [FrontController::class, 'productView'])->name('product.view');
Route::get('/my-profile', [FrontController::class, 'myProfile'])->name('my_profile');

// ========================
// PROFILE ROUTES - MOVED DOWN TO PREVENT CONFLICTS
// ========================
Route::get('/profile/settings', function () {
    return view('frontend.profile.setting_profile');
})->name('profile.settings');

Route::get('/profile/edit', function () {
    // Check if user is authenticated
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login to edit your profile.');
    }
    
    $user = Auth::user();
    return view('Security.editprofile', compact('user'));
})->name('profile.edit');

Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

// ========================
// ADDRESS ROUTES - UPDATED FOR MULTIPLE ADDRESSES
// ========================
Route::get('/address/create', [AddressController::class, 'create'])->name('address.create');
Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');
// ✅ ADDED: Multiple address management routes
Route::post('/address/set-default', [AddressController::class, 'setDefaultFromDropdown'])->name('address.set-default');
Route::post('/address/{addressId}/set-default', [AddressController::class, 'setDefault'])->name('address.set-default-id');
Route::delete('/address/{addressId}', [AddressController::class, 'destroy'])->name('address.destroy');
// ✅ ADDED: Address type switching route
Route::get('/address/switch/{type}', [AddressController::class, 'switchType'])->name('address.switch-type');

Route::get('/watchlist', function () {
    return view('customer.CustomerAuction.watchlist');
})->name('watchlist');

Route::get('/chome', function () {
    return view('customer.chome');
})->name('chome');

// ========================
// LOGOUT ROUTE
// ========================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========================
// SUPER ADMIN ROUTES (Separate System)
// ========================
Route::prefix('super-admin')->group(function () {
    // Authentication routes
    Route::get('/login', [SuperAdminAuthController::class, 'showLogin'])->name('super-admin.login');
    Route::post('/login', [SuperAdminAuthController::class, 'login']);
    Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('super-admin.logout');

    // Protected super admin routes
    Route::middleware(['auth', 'super_admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
        
        // Profile routes
        Route::get('/profile', [SuperAdminProfileController::class, 'showProfile'])->name('super-admin.profile');
        Route::post('/profile', [SuperAdminProfileController::class, 'updateProfile'])->name('super-admin.profile.update');
        
        // Admin management routes
        Route::prefix('admins')->name('super-admin.admins.')->group(function () {
            Route::get('/', [SuperAdminController::class, 'showAdmins'])->name('index');
            Route::get('/create', [SuperAdminController::class, 'createAdmin'])->name('create');
            Route::post('/', [SuperAdminController::class, 'storeAdmin'])->name('store');
            Route::get('/{admin}/edit', [SuperAdminController::class, 'editAdmin'])->name('edit');
            Route::put('/{admin}', [SuperAdminController::class, 'updateAdmin'])->name('update');
            Route::post('/{admin}/toggle-status', [SuperAdminController::class, 'toggleAdminStatus'])->name('toggle-status');
            Route::delete('/{admin}', [SuperAdminController::class, 'destroyAdmin'])->name('destroy');
        });

        // Super Admin Management
        Route::prefix('super-admins')->name('super-admin.super-admins.')->group(function () {
            Route::get('/', [SuperAdminController::class, 'showSuperAdmins'])->name('index');
            Route::get('/create', [SuperAdminController::class, 'showCreateSuperAdmin'])->name('create');
            Route::post('/', [SuperAdminController::class, 'storeSuperAdmin'])->name('store');
            Route::get('/{id}/edit', [SuperAdminController::class, 'editSuperAdmin'])->name('edit');
            Route::put('/{id}', [SuperAdminController::class, 'updateSuperAdmin'])->name('update');
            Route::post('/{id}/toggle-status', [SuperAdminController::class, 'toggleSuperAdminStatus'])->name('toggle-status');
            Route::delete('/{id}', [SuperAdminController::class, 'destroySuperAdmin'])->name('destroy');
        });
    });
});

// ========================
// PUBLIC ADMIN ROUTES (Login/Logout - No auth required)
// ========================
Route::prefix('admin')->group(function () {
    // Authentication routes (public)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ========================
// ADMIN MANAGEMENT ROUTES - PROTECTED WITH ADMIN MIDDLEWARE
// ========================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/home', [AdminController::class, 'dashboard'])->name('admin.home');
    Route::redirect('/', '/admin/dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    // ✅ ADDED: Admin settings route
    Route::get('/profile/settings', [AdminController::class, 'settings'])->name('admin.profile.settings');

    // ========================
    // PRODUCTS MANAGEMENT - PROTECTED
    // ========================
    Route::prefix('products')->name('admin.products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminProductController::class, 'show'])->name('show');
        
        // ✅ ADDED: Edit and Update routes for products
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        
        // ✅ PRODUCT APPROVAL ROUTES
        Route::put('/{id}/approve', [AdminProductController::class, 'approve'])->name('approve');
        Route::put('/{id}/reject', [AdminProductController::class, 'reject'])->name('reject');
        Route::put('/{id}/activate', [AdminProductController::class, 'activate'])->name('activate');
        Route::put('/{id}/deactivate', [AdminProductController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
    });

    // ========================
    // AUCTIONS MANAGEMENT - PROTECTED  
    // ========================
    Route::prefix('auctions')->name('admin.auctions.')->group(function () {
        // ✅ AUCTION MONITORING ROUTES
        Route::get('/', [AdminAuctionController::class, 'index'])->name('index');
        Route::get('/pending', [AdminAuctionController::class, 'pendingAuctions'])->name('pending');
        Route::get('/active', [AdminAuctionController::class, 'activeAuctions'])->name('active');
        Route::get('/won', [AdminAuctionController::class, 'wonAuctions'])->name('won');
        
        // ✅ ESCROW PAYOUT MANAGEMENT ROUTES
        Route::get('/pending-payouts', [AdminAuctionController::class, 'pendingPayouts'])->name('pending-payouts');
        Route::get('/all-payouts', [AdminAuctionController::class, 'allPayouts'])->name('all-payouts');
        Route::post('/{id}/approve-payout', [AdminAuctionController::class, 'approvePayout'])->name('approve-payout');
        Route::post('/{id}/reject-payout', [AdminAuctionController::class, 'rejectPayout'])->name('reject-payout');
        Route::post('/bulk-approve-payouts', [AdminAuctionController::class, 'bulkApprovePayouts'])->name('bulk-approve-payouts');
        
        // ✅ AUCTION DETAILS & BIDDER MONITORING
        Route::get('/{id}', [AdminAuctionController::class, 'show'])->name('show');
        Route::get('/{id}/bidders', [AdminAuctionController::class, 'viewBidders'])->name('bidders');
        
        // ✅ AUCTION STATUS MANAGEMENT (Admin Actions)
        Route::post('/{id}/approve', [AdminAuctionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminAuctionController::class, 'reject'])->name('reject');
        Route::post('/{id}/activate', [AdminAuctionController::class, 'activate'])->name('activate');
        Route::post('/{id}/complete', [AdminAuctionController::class, 'complete'])->name('complete');
        Route::post('/{id}/determine-winner', [AdminAuctionController::class, 'determineWinner'])->name('determine-winner');
        
        // ✅ AUCTION DELETION (For inappropriate content)
        Route::delete('/{id}', [AdminAuctionController::class, 'destroy'])->name('destroy');
    });

    // ========================
    // TRADING MANAGEMENT - PROTECTED
    // ========================
    Route::prefix('trading-management')->name('admin.trading.')->group(function () {
        Route::get('/', [TradeController::class, 'adminTradingManagement'])->name('management');
        Route::get('/pending', [TradeController::class, 'pendingTrades'])->name('pending');
        Route::get('/approved', [TradeController::class, 'approvedTrades'])->name('approved');
        Route::get('/rejected', [TradeController::class, 'rejectedTrades'])->name('rejected');
        Route::get('/active', [TradeController::class, 'activeTrades'])->name('active');
        
        // Trade approval actions
        Route::post('/{trade}/approve', [TradeController::class, 'approveTrade'])->name('approve');
        Route::post('/{trade}/reject', [TradeController::class, 'rejectTrade'])->name('reject');
        Route::post('/{trade}/activate', [TradeController::class, 'activateTrade'])->name('activate');
        
        // Admin view trade details
        Route::get('/{id}/view', [TradeController::class, 'adminViewTrade'])->name('view');
    });

    // ========================
    // DIAMOND WALLET MANAGEMENT (Admin)
    // ========================
    Route::prefix('diamond-wallet')->name('admin.diamond-wallet.')->group(function () {
        Route::get('/', [CustomerCurrencyController::class, 'adminWalletDashboard'])->name('index');
        Route::get('/users', [CustomerCurrencyController::class, 'adminUserWallets'])->name('users');
        Route::get('/transactions', [CustomerCurrencyController::class, 'adminAllTransactions'])->name('transactions');
        Route::post('/add-diamonds', [CustomerCurrencyController::class, 'adminAddDiamonds'])->name('add-diamonds');
        Route::post('/adjust-balance', [CustomerCurrencyController::class, 'adminAdjustBalance'])->name('adjust-balance');
    });
});

// ========================
// ORDER ROUTES - ADDED TO FIX THE ERROR
// ========================
Route::prefix('admin')->group(function () {
    Route::get('/orders', function () {
        return view('admin.orders.index'); // You'll need to create this view
    })->name('order');
});

// ✅ FIXED: Changed ProductController to AdminProductController
Route::get('/auction', [AuctionController::class, 'auction'])->name('auction');
Route::get('/product', [AdminProductController::class, 'index'])->name('product');

/*
|--------------------------------------------------------------------------
| Frontend Routes (Auction & Trading)
|--------------------------------------------------------------------------
*/
Route::get('/auction-list', [AuctionController::class, 'auction'])->name('auction.list');
Route::get('/auction/{id}', [AuctionController::class, 'showDetail'])->name('frontend.auctions.detail');
Route::get('/trading', [TradeController::class, 'index'])->name('trading');
Route::get('/trading/create', [TradeController::class, 'create'])->name('trading.create');
Route::post('/trading', [TradeController::class, 'store'])->name('trading.store');
Route::get('/trading/{trade}', [TradeController::class, 'show'])->name('trading.show');
Route::get('/trading/{trade}/edit', [TradeController::class, 'edit'])->name('trading.edit');
Route::put('/trading/{trade}', [TradeController::class, 'update'])->name('trading.update');
Route::delete('/trading/{trade}', [TradeController::class, 'destroy'])->name('trading.destroy');

// AMAZON API ROUTES
Route::prefix('amazon-api')->group(function () {
    Route::get('/search', [AmazonProductController::class, 'search'])->name('amazon.search');
    Route::get('/product', [AmazonProductController::class, 'getProduct'])->name('amazon.product');
    Route::get('/search-figures', [AmazonProductController::class, 'searchFigures'])->name('amazon.search-figures');
});

// ✅ NEW: System automation routes for escrow management
Route::prefix('system')->group(function () {
    // Auto-end expired auctions (call this from cron)
    Route::post('/auto-end-auctions', [CustomerAuctionController::class, 'autoEndExpiredAuctions'])->name('system.auto-end-auctions');
    
    // Check for overdue seller replies and auto-refund (12-hour timeout)
    Route::post('/check-overdue-replies', [CustomerAuctionController::class, 'checkOverdueSellerReplies'])->name('system.check-overdue-replies');
    
    // Combined system checks (for scheduled tasks)
    Route::post('/schedule-auction-checks', [CustomerAuctionController::class, 'scheduleAutomaticChecks'])->name('system.schedule-auction-checks');
});

// ========================
// ✅ NEW: API Routes for Real-time Features
// ========================
Route::prefix('api')->middleware(['auth'])->group(function () {
    
    // Chat API endpoints
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [CustomerChatController::class, 'getConversations']);
        Route::get('/messages', [CustomerChatController::class, 'getMessages']);
        Route::post('/send-message', [CustomerChatController::class, 'sendMessage']);
        Route::post('/mark-payment-received', [CustomerChatController::class, 'markPaymentReceived']);
    });
    
    // Auction API endpoints
    Route::prefix('auctions')->group(function () {
        Route::get('/{id}/escrow-status', [CustomerAuctionController::class, 'getEscrowStatus']);
        Route::post('/{id}/mark-received', [CustomerAuctionController::class, 'markItemReceived']);
        Route::get('/expired/list', [CustomerAuctionController::class, 'getExpiredAuctions']);
        Route::post('/auto-end', [CustomerAuctionController::class, 'autoEndExpiredAuctions']);
    });
});

// ========================
// ✅ NEW: System maintenance routes (protected)
// ========================
Route::middleware(['auth'])->group(function () {
    Route::post('/system/auctions/auto-end-all', [CustomerAuctionController::class, 'autoEndExpiredAuctions'])->name('system.auctions.auto-end-all');
    Route::post('/system/auctions/check-overdue-all', [CustomerAuctionController::class, 'checkOverdueSellerReplies'])->name('system.auctions.check-overdue-all');
    Route::post('/system/schedule-all-checks', [CustomerAuctionController::class, 'scheduleAutomaticChecks'])->name('system.schedule-all-checks');
});

/*
|--------------------------------------------------------------------------
| 🔗 Include API Routes
|--------------------------------------------------------------------------
*/
if (File::exists(base_path('routes/api.php'))) {
    require base_path('routes/api.php');
}

// ========================
// ✅ FALLBACK ROUTE
// ========================
Route::fallback(function () {
    return view('errors.404');
});