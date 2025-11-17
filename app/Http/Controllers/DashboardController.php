<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Trade;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real data from database
        $totalProducts = Product::count();
        
        // Get low stock products (stock <= 10)
        $lowStockProducts = Product::where('stock', '<=', 10)->count();
        
        // Get active auctions
        $activeAuctions = Auction::where('status', 'active')->count();
        
        // Get auctions ending soon (within 24 hours)
        $endingSoonAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', now()->addHours(24))
            ->count();
            
        // Get active trades
        $activeTrades = Trade::where('status', 'active')->count();
        
        // Get new trades (created within last 7 days)
        $newTrades = Trade::where('status', 'active')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Get pending and completed orders
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Stock overview with real data
        $stockOverview = Product::select('name', 'stock')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Active auctions list
        $activeAuctionsList = Auction::where('status', 'active')
            ->orderBy('end_time', 'asc')
            ->get();

        // Active trades list
        $activeTradesList = Trade::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate stock distribution counts
        $lowStockCount = Product::where('stock', '<=', 10)->count();
        $mediumStockCount = Product::where('stock', '>', 10)->where('stock', '<=', 20)->count();
        $goodStockCount = Product::where('stock', '>', 20)->count();

        return view('frontend.dashboard', [
            'totalProducts' => $totalProducts,
            'lowStockProducts' => $lowStockProducts,
            'activeAuctions' => $activeAuctions,
            'endingSoonAuctions' => $endingSoonAuctions,
            'activeTrades' => $activeTrades,
            'newTrades' => $newTrades,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'stockOverview' => $stockOverview,
            'activeAuctionsList' => $activeAuctionsList,
            'activeTradesList' => $activeTradesList,
            'lowStockCount' => $lowStockCount,
            'mediumStockCount' => $mediumStockCount,
            'goodStockCount' => $goodStockCount,
        ]);
    }
}