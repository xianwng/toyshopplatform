<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CustomerProductController extends Controller
{
    /**
     * Display products for cproduct page - FIXED QUERY LOGIC
     */
    public function cproduct()
    {
        // FIXED: Correct query logic for product visibility
        // "All Products" section should ONLY show active products (approved by admin)
        $query = Product::where('status', 'active')
                       ->where('stock', '>', 0);

        // Handle search
        if (request()->has('query') && !empty(request('query'))) {
            $searchQuery = request('query');
            $query->where(function($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('brand', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('description', 'LIKE', "%{$searchQuery}%");
            });
        }

        // Handle category filter
        if (request()->has('category') && !empty(request('category'))) {
            $query->where('category', request('category'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Count only public products for total
        $totalProducts = Product::where('status', 'active')
                               ->where('stock', '>', 0)
                               ->count();

        // Get ALL user's products (all statuses) for "My Products" section
        $userProducts = Auth::check() ? Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        return view('customer.CustomerProduct.cproduct', compact('products', 'totalProducts', 'userProducts'));
    }

    /**
     * Show the form for creating a new product for customers.
     */
    public function create()
    {
        return view('customer.CustomerProduct.cadd_product');
    }

    /**
     * Show the form for creating a new product for customers (cadd route).
     */
    public function cadd()
    {
        return view('customer.CustomerProduct.cadd_product');
    }

    /**
     * Store a newly created product in storage - FIXED: Removed original_price field
     */
    public function store(Request $request)
    {
        Log::info('=== CUSTOMER PRODUCT STORE METHOD CALLED ===');
        Log::info('All request data:', $request->all());
        Log::info('Images received: ' . ($request->hasFile('product_images') ? 'YES - ' . count($request->file('product_images')) . ' files' : 'NO'));

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'asin' => 'required|string|max:255',
            'product_images' => [
                'required',
                'array',
                'min:1',
                'max:6'
            ],
            'product_images.*' => [
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:5120',
            ],
            'description' => 'required|string|min:10|max:2000',
            'contact_number' => 'required|string|max:20',
            'home_address' => 'required|string|max:500',
            'shipping_method' => 'required|string|in:lalamove,lbc,jnt',
            'certificate' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'market_value_proof' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
        ], [
            'description.required' => 'Please provide a detailed product description.',
            'description.min' => 'Description must be at least 10 characters long.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);

        Log::info('Validation passed:', $validated);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Handle multiple image uploads - FIXED: Store in models directory consistently
            $imagePaths = [];
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $index => $image) {
                    // Generate unique filename
                    $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    
                    // Store in storage/app/public/models directory
                    $imagePath = $image->storeAs('models', $imageName, 'public');
                    
                    // Store the FULL path including 'models/' directory
                    $imagePaths[] = $imagePath;
                    
                    Log::info('Image stored: ' . $imagePath . ' (Index: ' . $index . ')');
                    Log::info('Full storage path: ' . storage_path('app/public/' . $imagePath));
                    Log::info('Public URL will be: ' . asset('storage/' . $imagePath));
                    
                    // Verify the file actually exists
                    $fullPath = storage_path('app/public/' . $imagePath);
                    if (file_exists($fullPath)) {
                        Log::info('✅ Image file verified at: ' . $fullPath);
                        Log::info('✅ File size: ' . filesize($fullPath) . ' bytes');
                    } else {
                        Log::error('❌ Image file NOT FOUND at: ' . $fullPath);
                        throw new \Exception('Image file was not saved correctly: ' . $imagePath);
                    }
                }
                
                // Log the order of images
                Log::info('Image paths stored: ', $imagePaths);
            } else {
                throw new \Exception('No product images uploaded.');
            }

            // Handle certificate upload
            $certificatePath = null;
            if ($request->hasFile('certificate')) {
                $certificateFile = $request->file('certificate');
                $certificateName = time() . '_certificate_' . $certificateFile->getClientOriginalName();
                $certificatePath = $certificateFile->storeAs('products/certificates', $certificateName, 'public');
                Log::info('Certificate stored: ' . $certificatePath);
            }

            // Handle market value proof upload
            $marketValueProofPath = null;
            if ($request->hasFile('market_value_proof')) {
                $marketValueFile = $request->file('market_value_proof');
                $marketValueName = time() . '_market_value_' . $marketValueFile->getClientOriginalName();
                $marketValueProofPath = $marketValueFile->storeAs('products/market_proofs', $marketValueName, 'public');
                Log::info('Market value proof stored: ' . $marketValueProofPath);
            }

            // Calculate adjusted price based on condition
            $originalPrice = $validated['price'];
            $adjustedPrice = $this->calculateAdjustedPrice($originalPrice, $validated['condition']);

            Log::info("Price adjustment - Original: {$originalPrice}, Condition: {$validated['condition']}, Adjusted: {$adjustedPrice}");

            // Update user's contact_number and home_address using direct DB update to avoid model issues
            $userId = Auth::id();
            if ($userId) {
                try {
                    $updateData = [
                        'contact_number' => $validated['contact_number'],
                        'home_address' => $validated['home_address'],
                        'updated_at' => now()
                    ];

                    $affectedRows = DB::table('users')
                        ->where('id', $userId)
                        ->update($updateData);

                    if ($affectedRows > 0) {
                        Log::info('User contact information updated successfully via DB query');
                    } else {
                        Log::warning('No rows affected when updating user information');
                    }
                } catch (\Exception $userUpdateError) {
                    Log::error('Error updating user information via DB: ' . $userUpdateError->getMessage());
                    // Continue with product creation even if user update fails
                }
            }

            // Store shipping method as array to work with the model's array cast
            $shippingMethod = [$validated['shipping_method']];

            // FIXED: Create the product WITHOUT original_price field
            $product = Product::create([
                'user_id' => $userId,
                'name' => $validated['name'],
                'brand' => $validated['brand'],
                'category' => $validated['category'],
                'price' => $adjustedPrice, // Use the adjusted price
                'stock' => $validated['stock'],
                'condition' => $validated['condition'],
                'asin' => $validated['asin'],
                'product_images' => $imagePaths,
                'description' => $validated['description'],
                'shipping_methods' => $shippingMethod,
                'status' => 'pending',
                'certificate_path' => $certificatePath,
                'market_value_proof' => $marketValueProofPath,
            ]);

            Log::info('Product created successfully with ID: ' . $product->id);
            Log::info('Product images saved to database: ' . json_encode($imagePaths));
            Log::info('First image path: ' . ($imagePaths[0] ?? 'No image'));
            
            // Test the image URL generation immediately
            $testImageUrl = $product->first_image_url;
            Log::info('First image URL should be: ' . $testImageUrl);
            Log::info('Shipping method saved to database: ' . json_encode($shippingMethod));

            // Test if the URL is accessible
            if ($testImageUrl && $testImageUrl !== $this->getDefaultImageUrl()) {
                Log::info('✅ Image URL generation successful!');
            } else {
                Log::warning('⚠️ Image URL generation returned default image');
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('cproduct')->with('success', 'Product created successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('Error creating product: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error creating product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get default image URL (helper method)
     */
    private function getDefaultImageUrl()
    {
        return asset('images/default-product.png');
    }

    /**
     * Calculate adjusted price based on condition
     */
    private function calculateAdjustedPrice($originalPrice, $condition)
    {
        $adjustmentRates = [
            'sealed' => 0.00,
            'bib' => 0.15,
            'loose' => 0.20,
        ];

        // Default to sealed if condition not found
        $discountRate = $adjustmentRates[$condition] ?? 0.00;
        
        $adjustedPrice = $originalPrice * (1 - $discountRate);
        
        // Round to 2 decimal places
        return round($adjustedPrice, 2);
    }

    /**
     * Display the specified product for customers.
     */
    public function show(Product $product)
    {
        // Only show public products or user's own products
        if (!$product->isPublic() && $product->user_id !== Auth::id()) {
            return redirect()->route('cproduct')
                           ->with('error', 'This product is not available.');
        }

        // Debug: Log the shipping methods from database
        Log::info('Product shipping methods from DB:', ['shipping_methods' => $product->shipping_methods]);
        
        // Use the model's array cast - shipping_methods is now automatically an array
        $shippingMethods = $product->shipping_methods ?? [];
        
        // Ensure we have a valid array
        if (!is_array($shippingMethods)) {
            $shippingMethods = [];
        }
        
        Log::info('Final decoded shipping methods: ', $shippingMethods);

        // Get seller information
        $seller = User::find($product->user_id);

        return view('customer.CustomerProduct.view_cproduct', compact('product', 'seller', 'shippingMethods'));
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this product.'
            ], 403);
        }

        try {
            // Delete the product
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product cancelled successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate a product (customer action)
     */
    public function activate(Product $product)
    {
        // Check if user owns the product
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to activate this product.'
            ], 403);
        }

        if ($product->activate()) {
            return response()->json([
                'success' => true,
                'message' => 'Product activated successfully! It is now visible to the public.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product cannot be activated. It must be approved first.'
        ], 400);
    }

    /**
     * Deactivate a product (customer action)
     */
    public function deactivate(Product $product)
    {
        // Check if user owns the product
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to deactivate this product.'
            ], 403);
        }

        if ($product->deactivate()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deactivated successfully! It is now hidden from the public.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product cannot be deactivated.'
        ], 400);
    }

    /**
     * Search products based on query.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        
        // FIXED: Only show active products in search results
        $products = Product::where('status', 'active')
                          ->where('stock', '>', 0)
                          ->where(function($q) use ($query, $category) {
                              if ($query) {
                                  $q->where('name', 'LIKE', "%{$query}%")
                                    ->orWhere('brand', 'LIKE', "%{$query}%")
                                    ->orWhere('description', 'LIKE', "%{$query}%");
                              }
                              if ($category) {
                                  $q->where('category', $category);
                              }
                          })
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);

        $totalProducts = $products->total();

        // Get ALL user's products (all statuses) for "My Products" section
        $userProducts = Auth::check() ? Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        return view('customer.CustomerProduct.cproduct', compact('products', 'query', 'category', 'totalProducts', 'userProducts'));
    }

    /**
     * Filter products by category.
     */
    public function category($category)
    {
        // FIXED: Only show active products in category results
        $products = Product::where('status', 'active')
                          ->where('stock', '>', 0)
                          ->where('category', $category)
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);

        $totalProducts = Product::where('status', 'active')
                               ->where('stock', '>', 0)
                               ->where('category', $category)
                               ->count();

        // Get ALL user's products (all statuses) for "My Products" section
        $userProducts = Auth::check() ? Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        return view('customer.CustomerProduct.cproduct', compact('products', 'category', 'totalProducts', 'userProducts'));
    }

    /**
     * Filter products by rarity.
     */
    public function rarity($rarity)
    {
        // FIXED: Only show active products in rarity results
        $products = Product::where('status', 'active')
                          ->where('stock', '>', 0)
                          ->where('rarity', $rarity)
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);

        $totalProducts = Product::where('status', 'active')
                               ->where('stock', '>', 0)
                               ->where('rarity', $rarity)
                               ->count();

        // Get ALL user's products (all statuses) for "My Products" section
        $userProducts = Auth::check() ? Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        return view('customer.CustomerProduct.cproduct', compact('products', 'rarity', 'totalProducts', 'userProducts'));
    }

    /**
     * Sort products by various criteria.
     */
    public function sort(Request $request)
    {
        $sortBy = $request->input('sort_by', 'newest');
        
        // FIXED: Only show active products in sort results
        $productQuery = Product::where('status', 'active')
                              ->where('stock', '>', 0);

        switch ($sortBy) {
            case 'price_low':
                $productQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productQuery->orderBy('price', 'desc');
                break;
            case 'name':
                $productQuery->orderBy('name', 'asc');
                break;
            case 'stock':
                $productQuery->orderBy('stock', 'desc');
                break;
            default:
                $productQuery->orderBy('created_at', 'desc');
                break;
        }

        $products = $productQuery->paginate(12);
        $totalProducts = Product::where('status', 'active')
                               ->where('stock', '>', 0)
                               ->count();

        // Get ALL user's products (all statuses) for "My Products" section
        $userProducts = Auth::check() ? Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : collect();

        return view('customer.CustomerProduct.cproduct', compact('products', 'sortBy', 'totalProducts', 'userProducts'));
    }

    /**
     * NEW: Handle Buy Now request and redirect to chat - FIXED ROUTE
     */
    public function buyNow(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to make a purchase.');
            }

            $productId = $request->input('product_id');
            $quantity = $request->input('quantity', 1);

            $product = Product::with('user')->find($productId);
            
            if (!$product) {
                return redirect()->route('cproduct')->with('error', 'Product not found.');
            }
            
            if ($product->user_id === $user->id) {
                return redirect()->route('cproduct')->with('error', 'You cannot purchase your own products.');
            }
            
            if ($product->stock < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }

            // Store buy now data in session for the chat controller to use
            Session::put('buy_now_quantity', $quantity);
            Session::put('buy_now_total_price', $product->price * $quantity);
            Session::put('buy_now_product_id', $productId);

            Log::info('Buy Now processed:', [
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity
            ]);

            // ✅ FIXED: Redirect to the correct buy now chat route
            return redirect()->route('customer.chat.buy-now-chat', ['productId' => $productId]);
            
        } catch (\Exception $e) {
            Log::error('Error processing Buy Now: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error processing purchase: ' . $e->getMessage());
        }
    }
}