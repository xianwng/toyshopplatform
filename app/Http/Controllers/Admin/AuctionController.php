<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuctionController extends Controller
{
    /**
     * Display all auctions for monitoring (Admin Dashboard)
     */
    public function index()
    {
        $auctions = Auction::withCount('bids')
            ->with(['user', 'bids' => function($query) {
                $query->orderBy('amount', 'desc')->take(5);
            }])
            ->orderByRaw("
                CASE 
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'active' THEN 2 
                    WHEN status = 'ended' THEN 3
                    WHEN status = 'completed' THEN 4
                    WHEN status = 'rejected' THEN 5
                    ELSE 6 
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistics for the view
        $totalAuctions = Auction::count();
        $pendingCount = Auction::where('status', 'pending')->count();
        $activeCount = Auction::where('status', 'active')->count();
        $endedCount = Auction::where('status', 'ended')->count();
        $completedCount = Auction::where('status', 'completed')->count();
        $rejectedCount = Auction::where('status', 'rejected')->count();

        return view('admin.auctions.index', compact(
            'auctions', 
            'totalAuctions',
            'pendingCount',
            'activeCount', 
            'endedCount',
            'completedCount',
            'rejectedCount'
        ));
    }

    /**
     * Display auction details
     */
    public function show($id)
    {
        $auction = Auction::with(['user', 'winner', 'bids.user'])
            ->withCount('bids')
            ->findOrFail($id);

        return view('frontend.auction.view_auction', compact('auction'));
    }

    /**
     * Approve pending auction
     */
    public function approve($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'approved']);

        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction approved successfully.');
    }

    /**
     * Reject auction
     */
    public function reject($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'rejected']);

        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction rejected successfully.');
    }

    /**
     * Activate approved auction
     */
    public function activate($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Auction activated successfully.');
    }

    /**
     * Complete ended auction
     */
    public function complete($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Auction marked as completed.');
    }

    /**
     * View bidders for an auction
     */
    public function viewBidders($id)
    {
        $auction = Auction::with(['bids.user'])->findOrFail($id);
        $bidders = $auction->bids->groupBy('user_id');

        return view('admin.auctions.bidders', compact('auction', 'bidders'));
    }

    /**
     * Display pending auctions
     */
    public function pendingAuctions()
    {
        $auctions = Auction::where('status', 'pending')
            ->with(['user'])
            ->withCount('bids')
            ->latest()
            ->paginate(10);

        return view('admin.auctions.pending', compact('auctions'));
    }

    /**
     * Display active auctions
     */
    public function activeAuctions()
    {
        $auctions = Auction::where('status', 'active')
            ->with(['user'])
            ->withCount('bids')
            ->latest()
            ->paginate(10);

        return view('admin.auctions.active', compact('auctions'));
    }

    /**
     * Display completed auctions
     */
    public function wonAuctions()
    {
        $auctions = Auction::where('status', 'completed')
            ->with(['user', 'winner'])
            ->latest()
            ->paginate(10);

        return view('admin.auctions.won', compact('auctions'));
    }

    /**
     * Display pending payouts
     */
    public function pendingPayouts()
    {
        $auctions = Auction::where('payout_status', 'pending')
            ->with(['user', 'winner'])
            ->latest()
            ->paginate(10);

        return view('admin.auctions.pending-payouts', compact('auctions'));
    }

    /**
     * Display all payouts
     */
    public function allPayouts()
    {
        $auctions = Auction::whereIn('payout_status', ['pending', 'approved', 'released', 'rejected'])
            ->with(['user', 'winner'])
            ->latest()
            ->paginate(20);

        return view('admin.auctions.all-payouts', compact('auctions'));
    }

    /**
     * Approve payout
     */
    public function approvePayout($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update([
            'payout_status' => 'approved',
            'payout_approved_at' => now(),
        ]);

        return redirect()->route('admin.auctions.pending-payouts')
            ->with('success', 'Payout approved successfully.');
    }

    /**
     * Reject payout
     */
    public function rejectPayout($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update([
            'payout_status' => 'rejected',
            'payout_approved_at' => now(),
        ]);

        return redirect()->route('admin.auctions.pending-payouts')
            ->with('success', 'Payout rejected successfully.');
    }

    /**
     * Determine auction winner
     */
    public function determineWinner($id)
    {
        $auction = Auction::with(['bids' => function($query) {
            $query->orderBy('amount', 'desc');
        }])->findOrFail($id);
        
        $highestBid = $auction->bids->first();
        
        if ($highestBid) {
            $highestBid->update(['is_winner' => true]);
            $auction->update([
                'status' => 'completed',
                'winner_id' => $highestBid->user_id
            ]);
            
            return redirect()->back()->with('success', 'Winner determined successfully.');
        }
        
        return redirect()->back()->with('error', 'No bids found for this auction.');
    }

    /**
     * Delete auction
     */
    public function destroy($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->delete();

        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction deleted successfully.');
    }

    /**
     * Display ended auctions
     */
    public function endedAuctions()
    {
        $auctions = Auction::where('status', 'ended')
            ->with(['user', 'winner'])
            ->withCount('bids')
            ->latest()
            ->paginate(10);

        return view('admin.auctions.ended', compact('auctions'));
    }

    /**
     * Force end an auction
     */
    public function forceEnd($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'ended']);

        return redirect()->back()->with('success', 'Auction ended successfully.');
    }

    /**
     * Display rejected auctions
     */
    public function rejectedAuctions()
    {
        $auctions = Auction::where('status', 'rejected')
            ->with(['user'])
            ->withCount('bids')
            ->latest()
            ->paginate(10);

        return view('admin.auctions.rejected', compact('auctions'));
    }

    /**
     * Restore rejected auction
     */
    public function restore($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Auction restored to pending status.');
    }

    /**
     * Bulk actions for auctions
     */
    public function bulkActions(Request $request)
    {
        $action = $request->input('action');
        $auctionIds = $request->input('auction_ids', []);

        if (empty($auctionIds)) {
            return redirect()->back()->with('error', 'No auctions selected.');
        }

        $count = 0;
        foreach ($auctionIds as $id) {
            $auction = Auction::find($id);
            if ($auction) {
                switch ($action) {
                    case 'approve':
                        if ($auction->status === 'pending') {
                            $auction->update(['status' => 'approved']);
                            $count++;
                        }
                        break;
                    case 'activate':
                        if ($auction->status === 'approved') {
                            $auction->update(['status' => 'active']);
                            $count++;
                        }
                        break;
                    case 'reject':
                        if (in_array($auction->status, ['pending', 'approved'])) {
                            $auction->update(['status' => 'rejected']);
                            $count++;
                        }
                        break;
                    case 'complete':
                        if ($auction->status === 'ended') {
                            $auction->update(['status' => 'completed']);
                            $count++;
                        }
                        break;
                    case 'delete':
                        if ($auction->canBeDeleted()) {
                            $auction->delete();
                            $count++;
                        }
                        break;
                }
            }
        }

        return redirect()->back()->with('success', "{$count} auctions processed successfully.");
    }

    /**
     * Export auctions to CSV
     */
    public function export(Request $request)
    {
        $status = $request->input('status', 'all');
        
        $query = Auction::with(['user', 'winner']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $auctions = $query->get();
        
        $fileName = 'auctions_' . $status . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($auctions) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Product Name',
                'Seller',
                'Starting Price',
                'Current Bid',
                'Status',
                'Start Time',
                'End Time',
                'Bids Count',
                'Winner',
                'Created At'
            ]);
            
            // Add data rows
            foreach ($auctions as $auction) {
                fputcsv($file, [
                    $auction->id,
                    $auction->product_name,
                    $auction->user ? $auction->user->name : 'N/A',
                    $auction->starting_price,
                    $auction->current_bid,
                    $auction->status,
                    $auction->start_time ? $auction->start_time->format('Y-m-d H:i') : 'N/A',
                    $auction->end_time ? $auction->end_time->format('Y-m-d H:i') : 'N/A',
                    $auction->bids_count,
                    $auction->winner ? $auction->winner->name : 'N/A',
                    $auction->created_at->format('Y-m-d H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get auction statistics for dashboard
     */
    public function getStatistics()
    {
        $totalAuctions = Auction::count();
        $pendingAuctions = Auction::where('status', 'pending')->count();
        $activeAuctions = Auction::where('status', 'active')->count();
        $completedAuctions = Auction::where('status', 'completed')->count();
        $totalRevenue = Auction::where('status', 'completed')->sum('current_bid');
        
        return response()->json([
            'total_auctions' => $totalAuctions,
            'pending_auctions' => $pendingAuctions,
            'active_auctions' => $activeAuctions,
            'completed_auctions' => $completedAuctions,
            'total_revenue' => $totalRevenue
        ]);
    }
}