<?php

namespace App\Http\Controllers\CustomerController;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Added for authentication
use App\Models\User; // Added for user data

class FrontController extends Controller
{
    public function index()
    {
        return view('frontend.index');
    }

    public function login()
    {
        return view('frontend.login');
    }

    public function home()
    {
       
        $products = Product::where('stock', '>', 0)
                          ->orderBy('created_at', 'desc')
                          ->paginate(8);
        
        return view('frontend.chome', compact('products'));
    }

    public function products()
    {
        
        $products = Product::where('stock', '>', 0)
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);
        
        
        return view('frontend.CustomerProduct.cproduct', compact('products'));
    }


    public function yourCart()
    {
        return redirect()->route('your_cart');
    }

    public function productView()
    {
        // FIXED: Changed from non-existent 'frontend.product' to actual existing view
        // If you have a specific product view, use it. Otherwise redirect to products page.
        return redirect()->route('cproduct');
    }

    public function checkout()
    {
        return redirect()->route('your_cart');
    }

    public function orderHistory()
    {
        return view('frontend.order_history');
    }

    public function myProfile()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your profile.');
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Return the Security profile view with user data
        return view('Security.profile', compact('user'));
    }

    public function auction()
    {
        return view('frontend.cusauction');
    }

    public function auctionDetails()
    {
        return view('frontend.auction_details');
    }

    public function orderStatus()
    {
        return view('customer.CustomerOrder.order_status');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        
        $products = Product::where('stock', '>', 0)
                          ->when($query, function($q) use ($query) {
                              $q->where(function($q) use ($query) {
                                  $q->where('name', 'LIKE', "%{$query}%")
                                    ->orWhere('brand', 'LIKE', "%{$query}%")
                                    ->orWhere('description', 'LIKE', "%{$query}%")
                                    ->orWhere('category', 'LIKE', "%{$query}%");
                              });
                          })
                          ->when($category, function($q) use ($category) {
                              $q->where('category', $category);
                          })
                          ->orderBy('created_at', 'desc')
                          ->paginate(8);

        return view('frontend.chome', compact('products', 'query', 'category'));
    }
}