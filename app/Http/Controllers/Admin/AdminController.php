<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Trade;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get real data from database
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();
        $activeAuctions = Auction::where('status', 'active')->count();
        $endingSoonAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', now()->addHours(24))
            ->count();
        $activeTrades = Trade::where('status', 'active')->count();
        $newTrades = Trade::where('status', 'active')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Stock overview
        $lowStockCount = Product::where('stock', '<=', 10)->count();
        $mediumStockCount = Product::where('stock', '>', 10)->where('stock', '<=', 20)->count();
        $goodStockCount = Product::where('stock', '>', 20)->count();

        return view('admin.dashboard', compact(
            'user',
            'totalProducts',
            'lowStockProducts',
            'activeAuctions',
            'endingSoonAuctions',
            'activeTrades',
            'newTrades',
            'pendingOrders',
            'completedOrders',
            'totalCustomers',
            'lowStockCount',
            'mediumStockCount',
            'goodStockCount'
        ));
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
}