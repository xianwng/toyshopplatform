<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TradingManagementController extends Controller
{
    /**
     * Display trading management dashboard
     */
    public function index()
    {
        $trades = Trade::with(['user', 'proposals'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.trading.index', compact('trades'));
    }

    /**
     * Show trade details
     */
    public function show($id)
    {
        $trade = Trade::with(['user', 'proposals.user'])->findOrFail($id);
        return view('admin.trading.show', compact('trade'));
    }

    /**
     * Approve a trade
     */
    public function approve($id)
    {
        try {
            $trade = Trade::findOrFail($id);
            $trade->update(['status' => 'approved']);
            
            return redirect()->route('admin.trading.management')->with('success', 'Trade approved successfully!');
        } catch (\Exception $e) {
            Log::error('Trade approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve trade.');
        }
    }

    /**
     * Reject a trade
     */
    public function reject($id)
    {
        try {
            $trade = Trade::findOrFail($id);
            $trade->update(['status' => 'rejected']);
            
            return redirect()->route('admin.trading.management')->with('success', 'Trade rejected successfully!');
        } catch (\Exception $e) {
            Log::error('Trade rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject trade.');
        }
    }
}