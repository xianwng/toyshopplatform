<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use App\Models\User;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessExpiredAuctions extends Command
{
    protected $signature = 'auctions:process-expired';
    protected $description = 'Process expired auctions and handle diamond distribution';

    public function handle()
    {
        $this->info('Starting expired auctions processing...');
        
        $expiredAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', Carbon::now())
            ->with(['bids' => function($query) {
                $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
            }, 'user'])
            ->get();

        $processedCount = 0;
        $refundedTotal = 0;

        foreach ($expiredAuctions as $auction) {
            try {
                DB::transaction(function () use ($auction, &$processedCount, &$refundedTotal) {
                    $winningBid = $auction->bids->first();
                    $seller = $auction->user;

                    if ($winningBid) {
                        $winner = $winningBid->user;
                        
                        // Transfer diamonds to seller
                        User::where('id', $seller->id)->increment('diamond_balance', $winningBid->amount);
                        
                        // Update auction winner and status
                        $auction->update([
                            'winner_id' => $winningBid->user_id,
                            'status' => 'ended',
                            'current_bid' => $winningBid->amount
                        ]);

                        $this->info("Auction #{$auction->id} ('{$auction->product_name}') - Winner: {$winner->username} with 💎{$winningBid->amount}");
                        Log::info("Expired auction {$auction->id} processed. 💎{$winningBid->amount} transferred to seller {$seller->id}");

                        // Refund all losing bidders (except the winner)
                        foreach ($auction->bids as $bid) {
                            if ($bid->id !== $winningBid->id && $bid->user_id !== $winningBid->user_id) {
                                User::where('id', $bid->user_id)->increment('diamond_balance', $bid->amount);
                                $refundedTotal += $bid->amount;
                                Log::info("Refunded 💎{$bid->amount} to user {$bid->user_id} (expired auction {$auction->id})");
                                $this->info("  Refunded 💎{$bid->amount} to user {$bid->user_id}");
                            }
                        }
                    } else {
                        // No bids, just end the auction
                        $auction->update([
                            'status' => 'ended',
                            'current_bid' => $auction->starting_price
                        ]);

                        $this->info("Auction #{$auction->id} ('{$auction->product_name}') - Ended with no bids");
                        Log::info("Expired auction {$auction->id} ended with no bids");
                    }

                    $processedCount++;
                });
            } catch (\Exception $e) {
                $this->error("Failed to process auction #{$auction->id}: " . $e->getMessage());
                Log::error("Failed to process expired auction {$auction->id}: " . $e->getMessage());
            }
        }

        if ($processedCount > 0) {
            $this->info("Successfully processed {$processedCount} expired auctions.");
            if ($refundedTotal > 0) {
                $this->info("Total diamonds refunded: 💎{$refundedTotal}");
            }
        } else {
            $this->info("No expired auctions to process.");
        }

        // Also process auctions that should be active but aren't (safety check)
        $this->processPendingActiveAuctions();
    }

    /**
     * Safety check: Process auctions that should be active but aren't
     */
    private function processPendingActiveAuctions()
    {
        $pendingActiveAuctions = Auction::where('status', 'approved')
            ->where('start_time', '<=', Carbon::now())
            ->where('end_time', '>', Carbon::now())
            ->get();

        foreach ($pendingActiveAuctions as $auction) {
            try {
                $auction->update(['status' => 'active']);
                $this->info("Activated auction #{$auction->id} ('{$auction->product_name}')");
                Log::info("Auto-activated auction {$auction->id}");
            } catch (\Exception $e) {
                $this->error("Failed to activate auction #{$auction->id}: " . $e->getMessage());
                Log::error("Failed to activate auction {$auction->id}: " . $e->getMessage());
            }
        }
    }
}