<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FreeAmazonScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $amazonService;

    public function __construct(FreeAmazonScraper $amazonService)
    {
        $this->amazonService = $amazonService;
    }

    /**
     * Show all products (Admin) - WITH AUTO-SYNC and status filtering
     */
    public function index()
    {
        $products = Product::with('user')->get();
        
        // Auto-sync all products in the list (only if stale)
        foreach ($products as $product) {
            $this->autoSyncProductIfStale($product);
        }
        
        // Reload products with updated data and user relationship
        $products = Product::with('user')->get();
        
        return view('frontend.product.product', compact('products')); 
    }

    /**
     * Show single product details - WITH AUTO-SYNC
     */
    public function show($id)
    {
        $product = Product::with('user')->findOrFail($id);
        
        // Auto-sync when viewing product details
        $this->autoSyncProductIfStale($product);
        
        // Reload product with updated data
        $product = Product::with('user')->findOrFail($id);
        
        return view('frontend.product.view_product', compact('product'));
    }

    /**
     * Approve a product (admin only) - Sets to approved but not active
     */
    public function approve($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);
            
            Log::info("Product {$id} approved by admin");
            return redirect()->back()->with('success', 'Product approved successfully. Seller can now activate it for public viewing.');
            
        } catch (\Exception $e) {
            Log::error('Product approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve product.');
        }
    }

    /**
     * Reject a product (admin only)
     */
    public function reject($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'status' => 'rejected',
                'updated_at' => now()
            ]);
            
            Log::info("Product {$id} rejected by admin");
            return redirect()->back()->with('success', 'Product rejected successfully.');
            
        } catch (\Exception $e) {
            Log::error('Product rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject product.');
        }
    }

    /**
     * Activate a product (admin override - for emergency cases)
     */
    public function activate($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'status' => 'active',
                'updated_at' => now()
            ]);
            
            Log::info("Product {$id} activated by admin");
            return redirect()->back()->with('success', 'Product activated successfully. It is now publicly visible.');
            
        } catch (\Exception $e) {
            Log::error('Product activation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to activate product.');
        }
    }

    /**
     * Deactivate a product (admin only)
     */
    public function deactivate($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);
            
            Log::info("Product {$id} deactivated by admin");
            return redirect()->back()->with('success', 'Product deactivated successfully. It is no longer publicly visible.');
            
        } catch (\Exception $e) {
            Log::error('Product deactivation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to deactivate product.');
        }
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->model_file && Storage::disk('public')->exists($product->model_file)) {
                Storage::disk('public')->delete($product->model_file);
            }

            $product->delete();
            Log::info('Product deleted successfully: ' . $product->name);

        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete product.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Auto-sync product if data is stale (older than 1 hour) - FIXED VERSION
     */
    private function autoSyncProductIfStale($product)
    {
        // Only sync if data is older than 1 hour or never synced
        if (!$product->last_synced_at || $product->last_synced_at->lt(now()->subHour())) {
            try {
                Log::info("Auto-syncing product {$product->id} - data is stale");
                
                if (!empty($product->asin)) {
                    $amazonData = $this->amazonService->getProductPriceAndStock($product->asin);
                    
                    if ($amazonData) {
                        // Convert Amazon stock string to integer
                        $stockInteger = $this->convertStockToInteger($amazonData['stock']);
                        
                        // Update product with fresh data
                        $updateData = [
                            'amazon_price' => $amazonData['original_usd_price'] ?? $product->amazon_price,
                            'price' => $amazonData['price'] ?? $product->price,
                            'amazon_stock' => $stockInteger,
                            'stock' => $stockInteger,
                            'exchange_rate' => $amazonData['exchange_rate'] ?? $product->exchange_rate,
                            'discount_applied' => $amazonData['discount_applied'] ?? $product->discount_applied,
                            'last_synced_at' => now(),
                        ];
                        
                        // Update the product in database
                        $product->update($updateData);
                        
                        // CRITICAL FIX: Refresh the product object to get updated data
                        $product->refresh();
                        
                        Log::info("Auto-sync completed for product {$product->id}");
                        Log::info("New prices - USD: $" . ($product->amazon_price ?? 'N/A') . ", PHP: ₱" . ($product->price ?? 'N/A'));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Auto-sync failed: ' . $e->getMessage());
                // Continue with existing product data if sync fails
            }
        } else {
            Log::info("Product {$product->id} data is fresh (synced: {$product->last_synced_at})");
        }
    }

    /**
     * Convert Amazon stock string to integer
     */
    private function convertStockToInteger($stockString)
    {
        if (is_string($stockString)) {
            $stockString = strtolower($stockString);
            
            // Handle "Only X left" pattern
            if (preg_match('/(\d+)\s+left/', $stockString, $matches)) {
                return (int) $matches[1];
            }
            
            // Handle "In Stock" - assume available
            if (strpos($stockString, 'in stock') !== false) {
                return 10; // Default stock for "In Stock"
            }
            
            // Handle "Out of Stock"
            if (strpos($stockString, 'out of stock') !== false || 
                strpos($stockString, 'unavailable') !== false) {
                return 0;
            }
            
            // Extract any numbers from the string
            if (preg_match('/(\d+)/', $stockString, $matches)) {
                return (int) $matches[1];
            }
        }
        
        // Default fallback
        return is_numeric($stockString) ? (int) $stockString : 1;
    }
}