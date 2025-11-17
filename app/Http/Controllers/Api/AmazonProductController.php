<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FreeAmazonScraper;
use Illuminate\Http\Request;
use App\Models\Product;

class AmazonProductController extends Controller
{
    private $amazonService;

    public function __construct(FreeAmazonScraper $amazonService)
    {
        $this->amazonService = $amazonService;
    }

    /**
     * Search Amazon for products
     */
    public function search(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|min:2',
            'domain' => 'sometimes|string|size:2'
        ]);

        $keywords = $request->input('keywords');
        $domain = $request->input('domain', 'com');

        $results = $this->amazonService->searchProducts($keywords, $domain);

        if (!$results) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from Amazon API'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get product details by ASIN
     */
    public function getProduct(Request $request)
    {
        $request->validate([
            'asin' => 'required|string',
            'domain' => 'sometimes|string|size:2'
        ]);

        $asin = $request->input('asin');
        $domain = $request->input('domain', 'com');

        $productData = $this->amazonService->getProductPriceAndStock($asin, $domain);

        if (!$productData) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or API error'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $productData
        ]);
    }

    /**
     * Search specifically for figure products
     */
    public function searchFigures(Request $request)
    {
        $request->validate([
            'figure_name' => 'required|string|min:2',
            'brand' => 'sometimes|string'
        ]);

        $figureName = $request->input('figure_name');
        $brand = $request->input('brand');

        $results = $this->amazonService->searchFigureProducts($figureName, $brand);

        if (!$results) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch figure data from Amazon API'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Sync Amazon data with existing product
     */
    public function syncProduct(Request $request, $productId)
    {
        $request->validate([
            'asin' => 'required|string',
            'domain' => 'sometimes|string|size:2'
        ]);

        $product = Product::findOrFail($productId);
        $asin = $request->input('asin');
        $domain = $request->input('domain', 'com');

        $amazonData = $this->amazonService->getProductPriceAndStock($asin, $domain);

        if (!$amazonData) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Amazon data for this ASIN'
            ], 404);
        }

        // Update product with Amazon data
        $updates = [];
        
        if ($amazonData['price']) {
            $updates['amazon_price'] = $amazonData['price'];
        }

        if ($amazonData['stock'] !== null) {
            $updates['amazon_stock'] = $amazonData['stock'];
        }

        if (!empty($updates)) {
            $updates['last_synced_at'] = now();
            $product->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product synced with Amazon data',
            'data' => [
                'product' => $product,
                'amazon_data' => $amazonData
            ]
        ]);
    }

    /**
     * Sync product using existing ASIN (no ASIN needed in request)
     */
    public function syncProductByAsin($productId)
    {
        $product = Product::findOrFail($productId);
        
        if (empty($product->asin)) {
            return response()->json([
                'success' => false,
                'message' => 'No ASIN found for this product'
            ], 400);
        }

        $amazonData = $this->amazonService->getProductPriceAndStock($product->asin);

        if (!$amazonData) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Amazon data for ASIN: ' . $product->asin
            ], 404);
        }

        // Update product with Amazon data
        $updates = [];
        
        if ($amazonData['price']) {
            $updates['amazon_price'] = $amazonData['price'];
        }

        if ($amazonData['stock'] !== null) {
            $updates['amazon_stock'] = $amazonData['stock'];
        }

        if (!empty($updates)) {
            $updates['last_synced_at'] = now();
            $product->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product synced with Amazon data',
            'data' => [
                'product' => $product,
                'amazon_data' => $amazonData
            ]
        ]);
    }

    /**
     * Auto-sync all products that have ASIN stored
     */
    public function syncAllProducts(Request $request)
    {
        $products = Product::whereNotNull('asin')->get();
        
        $results = [];
        $synced = 0;
        $failed = 0;

        foreach ($products as $product) {
            $amazonData = $this->amazonService->getProductPriceAndStock($product->asin);

            if ($amazonData) {
                $updates = [];
                
                if ($amazonData['price']) {
                    $updates['amazon_price'] = $amazonData['price'];
                }

                if ($amazonData['stock'] !== null) {
                    $updates['amazon_stock'] = $amazonData['stock'];
                }

                if (!empty($updates)) {
                    $updates['last_synced_at'] = now();
                    $product->update($updates);
                    $synced++;
                }
            } else {
                $failed++;
            }

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'asin' => $product->asin,
                'success' => !empty($amazonData),
                'amazon_data' => $amazonData
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Synced {$synced} products, {$failed} failed",
            'data' => $results
        ]);
    }

    /**
     * Sync products that need updating (never synced or older than 24 hours)
     */
    public function syncNeededProducts(Request $request)
    {
        $products = Product::needsSync()->get();
        
        $results = [];
        $synced = 0;
        $failed = 0;

        foreach ($products as $product) {
            $amazonData = $this->amazonService->getProductPriceAndStock($product->asin);

            if ($amazonData) {
                $updates = [];
                
                if ($amazonData['price']) {
                    $updates['amazon_price'] = $amazonData['price'];
                }

                if ($amazonData['stock'] !== null) {
                    $updates['amazon_stock'] = $amazonData['stock'];
                }

                if (!empty($updates)) {
                    $updates['last_synced_at'] = now();
                    $product->update($updates);
                    $synced++;
                }
            } else {
                $failed++;
            }

            $results[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'asin' => $product->asin,
                'last_synced' => $product->last_synced_at,
                'success' => !empty($amazonData),
                'amazon_data' => $amazonData
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Synced {$synced} products needing update, {$failed} failed",
            'data' => $results
        ]);
    }

    /**
     * Get sync status for all products
     */
    public function getSyncStatus(Request $request)
    {
        $totalProducts = Product::count();
        $productsWithAsin = Product::withAsin()->count();
        $productsWithoutAsin = Product::withoutAsin()->count();
        $productsNeedingSync = Product::needsSync()->count();
        $recentlySynced = Product::withAsin()->where('last_synced_at', '>=', now()->subHours(24))->count();

        $products = Product::withAsin()->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'asin' => $product->asin,
                'amazon_price' => $product->amazon_price,
                'amazon_stock' => $product->amazon_stock,
                'last_synced_at' => $product->last_synced_at,
                'sync_status' => $product->sync_status_text,
                'needs_sync' => $product->needsSync,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_products' => $totalProducts,
                    'with_asin' => $productsWithAsin,
                    'without_asin' => $productsWithoutAsin,
                    'needing_sync' => $productsNeedingSync,
                    'recently_synced' => $recentlySynced,
                ],
                'products' => $products
            ]
        ]);
    }
}