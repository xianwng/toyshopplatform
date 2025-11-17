<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Auto-end expired auctions using controller method - runs every minute
        $schedule->call(function () {
            try {
                $controller = app(\App\Http\Controllers\CustomerController\CustomerAuctionController::class);
                $count = $controller->autoEndExpiredAuctions();
                
                // Log the result
                if ($count > 0) {
                    \Illuminate\Support\Facades\Log::info("Auto-ended {$count} expired auctions via scheduler - All payouts in escrow with 12-hour seller reply deadline");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to auto-end expired auctions: ' . $e->getMessage());
            }
        })->everyMinute()->name('auto-end-expired-auctions')->withoutOverlapping();

        // ✅ UPDATED: Check for overdue seller replies and auto-refund - runs every 5 minutes (12-hour timeout)
        $schedule->call(function () {
            try {
                $controller = app(\App\Http\Controllers\CustomerController\CustomerAuctionController::class);
                $refundedCount = $controller->checkOverdueSellerReplies();
                
                // Log the result
                if ($refundedCount > 0) {
                    \Illuminate\Support\Facades\Log::info("Auto-refunded {$refundedCount} auctions due to seller not replying within 12 hours");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to check overdue seller replies: ' . $e->getMessage());
            }
        })->everyFiveMinutes()->name('check-overdue-seller-replies')->withoutOverlapping();

        // ✅ NEW: Combined system check that runs every 10 minutes
        $schedule->call(function () {
            try {
                $controller = app(\App\Http\Controllers\CustomerController\CustomerAuctionController::class);
                $result = $controller->scheduleAutomaticChecks();
                
                if ($result) {
                    \Illuminate\Support\Facades\Log::info("Scheduled auction checks completed successfully");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to run scheduled auction checks: ' . $e->getMessage());
            }
        })->everyTenMinutes()->name('schedule-automatic-checks')->withoutOverlapping();

        // Optional: Add logging to verify scheduler is working
        $schedule->call(function () {
            \Illuminate\Support\Facades\Log::info('Auction scheduler is running at ' . now());
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}