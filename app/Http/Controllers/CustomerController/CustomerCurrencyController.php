<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Diamond;

class CustomerCurrencyController extends Controller
{
    private function calculatePriceWithTax($basePrice)
    {
        $taxRate = 0.12; // 12% VAT in Philippines
        return round($basePrice * (1 + $taxRate), 2);
    }

    private function calculateTaxAmount($basePrice)
    {
        $taxRate = 0.12; // 12% VAT in Philippines
        return round($basePrice * $taxRate, 2);
    }

    public function showWallet()
    {
        Log::info('CustomerCurrencyController: showWallet method called');
        
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to access your wallet.');
            }

            $bundles = Diamond::where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->map(function ($bundle) {
                    // Calculate prices with tax
                    $bundle->original_price = $bundle->price;
                    $bundle->tax_amount = $this->calculateTaxAmount($bundle->price);
                    $bundle->final_price = $this->calculatePriceWithTax($bundle->price);
                    return $bundle;
                });

            Log::info('CustomerCurrencyController: Data loaded successfully', [
                'user_id' => $user->id,
                'bundles_count' => $bundles->count()
            ]);

            return view('customer.CustomerVirtualWallet.CustomerCurrency', compact('user', 'bundles'));

        } catch (\Exception $e) {
            Log::error('CustomerCurrencyController Error: ' . $e->getMessage());
            return response("Error loading wallet: " . $e->getMessage(), 500);
        }
    }

    public function showPaymentPage(Request $request)
    {
        try {
            // Debug the incoming request
            Log::info('Payment page request data:', $request->all());
            
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to make a purchase.');
            }

            $bundleId = $request->get('bundle_id');
            
            if (!$bundleId) {
                Log::error('No bundle_id provided in request');
                return redirect()->route('customer.wallet')->with('error', 'Please select a bundle first.');
            }

            $bundle = Diamond::find($bundleId);
            
            if (!$bundle) {
                Log::error('Bundle not found with ID: ' . $bundleId);
                return redirect()->route('customer.wallet')->with('error', 'Invalid bundle selection.');
            }

            // Calculate tax and final price
            $bundle->original_price = $bundle->price;
            $bundle->tax_amount = $this->calculateTaxAmount($bundle->price);
            $bundle->final_price = $this->calculatePriceWithTax($bundle->price);

            // Generate a unique transaction ID
            $transactionId = 'TXN_' . time() . '_' . rand(1000, 9999);

            Log::info('Showing payment page for bundle:', [
                'user_id' => $user->id,
                'bundle_id' => $bundle->id,
                'diamonds' => $bundle->diamond_amount,
                'original_price' => $bundle->original_price,
                'tax_amount' => $bundle->tax_amount,
                'final_price' => $bundle->final_price
            ]);

            return view('customer.CustomerVirtualWallet.CustomerCurrencyPayment', [
                'bundle' => $bundle,
                'user' => $user,
                'transactionId' => $transactionId
            ]);

        } catch (\Exception $e) {
            Log::error('Payment page error: ' . $e->getMessage());
            return redirect()->route('customer.wallet')->with('error', 'Error loading payment page.');
        }
    }

    public function processPayment(Request $request)
    {
        Log::info('Processing payment request:', $request->all());
        
        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            if (!$user) {
                Log::error('User not authenticated for payment processing');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            $request->validate([
                'bundle_id' => 'required|numeric',
                'transaction_id' => 'required',
                'payment_method' => 'required|in:gcash,paymaya'
            ]);

            $bundle = Diamond::find($request->bundle_id);
            
            if (!$bundle) {
                Log::error('Bundle not found with ID: ' . $request->bundle_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Bundle not found.'
                ], 404);
            }

            // Calculate tax and final price
            $originalPrice = $bundle->price;
            $taxAmount = $this->calculateTaxAmount($originalPrice);
            $finalPrice = $this->calculatePriceWithTax($originalPrice);

            Log::info('Processing payment for user:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'bundle_id' => $bundle->id,
                'diamond_amount' => $bundle->diamond_amount,
                'original_price' => $originalPrice,
                'tax_amount' => $taxAmount,
                'final_price' => $finalPrice,
                'payment_method' => $request->payment_method
            ]);

            // Get current balance first
            $currentUser = DB::table('users')->where('id', $user->id)->first();
            $oldBalance = $currentUser->diamond_balance;
            $newBalance = $oldBalance + $bundle->diamond_amount;

            // Create purchase record with user identification
            try {
                $purchaseData = [
                    'customer_id' => $user->id,
                    'customer_email' => $user->email,
                    'customer_name' => $user->first_name . ' ' . $user->last_name,
                    'bundle_id' => $bundle->id,
                    'diamond_amount' => $bundle->diamond_amount,
                    'original_price' => $originalPrice,
                    'tax_amount' => $taxAmount,
                    'final_price' => $finalPrice,
                    'payment_status' => 'completed',
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $purchaseId = DB::table('diamond_purchases')->insertGetId($purchaseData);
                Log::info('Purchase record created with ID: ' . $purchaseId);
            } catch (\Exception $e) {
                Log::error('Error creating purchase record: ' . $e->getMessage());
                // Continue without purchase record if there's an issue
            }

            // Create transaction record with user identification
            try {
                $transactionData = [
                    'customer_id' => $user->id,
                    'customer_email' => $user->email,
                    'customer_name' => $user->first_name . ' ' . $user->last_name,
                    'transaction_type' => 'purchase',
                    'amount' => $bundle->diamond_amount,
                    'description' => "Purchased {$bundle->diamond_amount} diamonds from {$bundle->name} via " . ucfirst($request->payment_method) . " (incl. 12% VAT)",
                    'reference_id' => $request->transaction_id,
                    'created_at' => now()
                ];
                
                DB::table('diamond_transactions')->insert($transactionData);
                Log::info('Transaction record created');
            } catch (\Exception $e) {
                Log::error('Error creating transaction record: ' . $e->getMessage());
                // Continue without transaction record if there's an issue
            }

            // Update user diamond balance using direct DB query
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'diamond_balance' => $newBalance,
                    'updated_at' => now()
                ]);

            Log::info('User diamond balance updated:', [
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'added_diamonds' => $bundle->diamond_amount
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Diamonds added to your wallet.',
                'new_balance' => $newBalance,
                'purchased_diamonds' => $bundle->diamond_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWalletBalance()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'balance' => 0
            ]);
        }
        
        // Get fresh balance from database
        $currentUser = DB::table('users')->where('id', $user->id)->first();
        
        return response()->json([
            'balance' => $currentUser->diamond_balance ?? 0
        ]);
    }

    public function getTransactionHistory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'transactions' => []
            ]);
        }

        $transactions = DB::table('diamond_transactions')
            ->where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }

    public function getPurchaseHistory()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'purchases' => []
            ]);
        }

        $purchases = DB::table('diamond_purchases')
            ->where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'purchases' => $purchases
        ]);
    }

    public function updateWalletBalance(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            $request->validate([
                'amount' => 'required|integer|min:1',
                'type' => 'required|in:add,subtract'
            ]);

            // Get current balance from database
            $currentUser = DB::table('users')->where('id', $user->id)->first();
            $oldBalance = $currentUser->diamond_balance;
            
            if ($request->type === 'add') {
                $newBalance = $oldBalance + $request->amount;
            } else {
                if ($oldBalance < $request->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient diamond balance.'
                    ], 400);
                }
                $newBalance = $oldBalance - $request->amount;
            }

            // Update user diamond balance using direct DB query
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'diamond_balance' => $newBalance,
                    'updated_at' => now()
                ]);

            // Create transaction record
            $transactionType = $request->type === 'add' ? 'bonus' : 'used';
            $description = $request->type === 'add' 
                ? "Added {$request->amount} diamonds" 
                : "Used {$request->amount} diamonds";

            DB::table('diamond_transactions')->insert([
                'customer_id' => $user->id,
                'customer_email' => $user->email,
                'customer_name' => $user->first_name . ' ' . $user->last_name,
                'transaction_type' => $transactionType,
                'amount' => $request->amount,
                'description' => $description,
                'reference_id' => 'SYS_' . time(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet balance updated successfully.',
                'new_balance' => $newBalance
            ]);

        } catch (\Exception $e) {
            Log::error('Update wallet balance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wallet balance.'
            ], 500);
        }
    }

    /**
     * Show transaction history page with all user transactions
     */
    public function showTransactionHistory()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to view transaction history.');
            }

            // Get all transactions for the user with pagination
            $transactions = DB::table('diamond_transactions')
                ->where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Calculate statistics
            $totalPurchased = DB::table('diamond_transactions')
                ->where('customer_id', $user->id)
                ->whereIn('transaction_type', ['purchase', 'bonus'])
                ->sum('amount');

            $totalUsed = DB::table('diamond_transactions')
                ->where('customer_id', $user->id)
                ->where('transaction_type', 'used')
                ->sum('amount');

            $totalTransactions = DB::table('diamond_transactions')
                ->where('customer_id', $user->id)
                ->count();

            Log::info('Transaction history loaded for user:', [
                'user_id' => $user->id,
                'total_transactions' => $totalTransactions,
                'total_purchased' => $totalPurchased,
                'total_used' => $totalUsed
            ]);

            return view('customer.CustomerVirtualWallet.CustomerCurrencyTransaction', compact(
                'transactions',
                'totalPurchased',
                'totalUsed',
                'totalTransactions'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading transaction history: ' . $e->getMessage());
            return redirect()->route('customer.wallet')->with('error', 'Error loading transaction history.');
        }
    }
}