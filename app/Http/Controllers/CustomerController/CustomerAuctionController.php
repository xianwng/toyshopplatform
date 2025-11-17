<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ChatStorageService;

class CustomerAuctionController extends Controller
{
    protected $chatStorage;

    public function __construct()
    {
        $this->chatStorage = new ChatStorageService();
    }

    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view your auctions.');
        }
        
        try {
            // User's auctions - ONLY show auctions created by the current user
            $userAuctions = Auction::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10, ['*'], 'my_auctions_page');

            // FIXED: Show ALL active auctions from other users (remove status filter to include all active auctions)
            $publicAuctions = Auction::where('status', 'active')
                                   ->where('end_time', '>', Carbon::now())
                                   ->where('user_id', '!=', $user->id)
                                   ->with('bids')
                                   ->withCount('bids')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(12, ['*'], 'public_auctions_page');

            return view('customer.CustomerAuction.cauction', compact('userAuctions', 'publicAuctions'));
            
        } catch (\Exception $e) {
            Log::error('Error loading auctions: ' . $e->getMessage());
            
            // Fallback with same fixes
            $userAuctions = Auction::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10, ['*'], 'my_auctions_page');

            $publicAuctions = Auction::where('status', 'active')
                                   ->where('end_time', '>', Carbon::now())
                                   ->where('user_id', '!=', $user->id)
                                   ->with('bids')
                                   ->withCount('bids')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(12, ['*'], 'public_auctions_page');

            return view('customer.CustomerAuction.cauction', compact('userAuctions', 'publicAuctions'));
        }
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to create an auction.');
        }
        
        return view('customer.CustomerAuction.cadd_auction');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to create an auction.');
        }

        // Validation
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_img' => 'required|array|min:1|max:6',
            'product_img.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'starting_price' => 'required|integer|min:1',
            'buyout_bid' => 'nullable|integer|min:1',
            'end_time' => 'required|date|after:+5 days',
            'minimum_market_value' => 'required|numeric|min:1',
            'owner_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'market_value_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'terms_accepted' => 'required|accepted',
            'reference_links' => 'nullable|string',
        ]);

        // Validate end time
        $endTime = Carbon::parse($validated['end_time']);
        $minEndTime = Carbon::now()->addDays(5);
        
        if ($endTime->lt($minEndTime)) {
            return back()->withErrors(['end_time' => 'Auction must run for at least 5 days.'])->withInput();
        }

        if ($request->filled('buyout_bid') && $request->buyout_bid <= $request->starting_price) {
            return back()->withErrors(['buyout_bid' => 'Buyout price must be higher than starting price.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $user, $request) {
                // ✅ FIXED: Handle multiple product image uploads - STORE ALL IMAGES AS JSON ARRAY
                $allImagePaths = [];
                
                if ($request->hasFile('product_img')) {
                    foreach ($request->file('product_img') as $imageFile) {
                        $filename = time() . '_' . uniqid() . '_product_' . preg_replace('/\s+/', '_', $imageFile->getClientOriginalName());
                        $imagePath = 'auctions/' . $filename;
                        $imageFile->storeAs('auctions', $filename, 'public');
                        
                        // Store all image paths in array
                        $allImagePaths[] = $imagePath;
                    }
                }

                // Handle owner proof upload
                $ownerProofPath = null;
                if ($request->hasFile('owner_proof')) {
                    $proofFile = $request->file('owner_proof');
                    $filename = time() . '_owner_proof_' . preg_replace('/\s+/', '_', $proofFile->getClientOriginalName());
                    $ownerProofPath = 'proofs/' . $filename;
                    $proofFile->storeAs('proofs', $filename, 'public');
                }

                // Handle market value proof upload
                $marketValueProofPath = null;
                if ($request->hasFile('market_value_proof')) {
                    $proofFile = $request->file('market_value_proof');
                    $filename = time() . '_market_proof_' . preg_replace('/\s+/', '_', $proofFile->getClientOriginalName());
                    $marketValueProofPath = 'proofs/' . $filename;
                    $proofFile->storeAs('proofs', $filename, 'public');
                }

                // ✅ FIXED: Create the auction - STORE ALL IMAGES AS JSON ARRAY
                $auctionData = [
                    'product_name' => $validated['product_name'],
                    'brand' => $validated['brand'],
                    'condition' => $validated['condition'],
                    'category' => $validated['category'],
                    'description' => $validated['description'] ?? null,
                    'product_img' => $allImagePaths, // ✅ Store ALL images as JSON array
                    'starting_price' => $validated['starting_price'],
                    'buyout_bid' => $validated['buyout_bid'] ?? null,
                    'end_time' => $validated['end_time'],
                    'minimum_market_value' => $validated['minimum_market_value'],
                    'owner_proof' => $ownerProofPath,
                    'market_value_proof' => $marketValueProofPath,
                    'reference_links' => $validated['reference_links'] ?? null,
                    'terms_accepted' => true,
                    'status' => 'pending',
                    'current_bid' => 0,
                    'user_id' => $user->id,
                    'payout_status' => 'pending',
                    'payout_amount' => 0.00,
                    'delivery_method' => 'seller_delivery',
                    'delivery_cost' => 0.00,
                ];

                $auction = Auction::create($auctionData);
                
                Log::info("Auction {$auction->id} created with multiple images. Total images: " . count($allImagePaths));
            });

            return redirect()->route('customer.auctions.index')->with('success', 'Auction created successfully and submitted for verification!');
        } catch (\Exception $e) {
            Log::error('Auction creation failed: ' . $e->getMessage());
            return back()->withErrors('Failed to create auction: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view auction details.');
        }
        
        try {
            $auction = Auction::with(['bids.user', 'user'])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error loading auction: ' . $e->getMessage());
            $auction = Auction::with(['bids.user', 'user'])->findOrFail($id);
        }
        
        if ($auction->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('customer.CustomerAuction.cauction_details', compact('auction'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to edit auctions.');
        }
        
        try {
            $auction = Auction::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error loading auction for edit: ' . $e->getMessage());
            $auction = Auction::findOrFail($id);
        }
        
        if ($auction->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($auction->status !== 'pending' || $auction->bids()->count() > 0) {
            return redirect()->route('customer.auctions.index')->with('error', 'Cannot edit auction that has bids or is already approved.');
        }
        
        return view('customer.CustomerAuction.cedit_auction', compact('auction'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to update auctions.');
        }
        
        $auction = Auction::findOrFail($id);
        
        if ($auction->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($auction->status !== 'pending' || $auction->bids()->count() > 0) {
            return redirect()->route('customer.auctions.index')->with('error', 'Cannot update auction that has bids or is already approved.');
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_img' => 'nullable|array|min:1|max:6',
            'product_img.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'starting_price' => 'required|integer|min:1',
            'buyout_bid' => 'nullable|integer|min:1',
            'end_time' => 'required|date|after:+5 days',
            'minimum_market_value' => 'required|numeric|min:1',
            'owner_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'market_value_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'reference_links' => 'nullable|string',
        ]);

        // Validate end time
        $endTime = Carbon::parse($validated['end_time']);
        $minEndTime = Carbon::now()->addDays(5);
        
        if ($endTime->lt($minEndTime)) {
            return back()->withErrors(['end_time' => 'Auction must run for at least 5 days.'])->withInput();
        }

        if ($request->filled('buyout_bid') && $request->buyout_bid <= $request->starting_price) {
            return back()->withErrors(['buyout_bid' => 'Buyout price must be higher than starting price.'])->withInput();
        }

        try {
            $updateData = [
                'product_name' => $validated['product_name'],
                'brand' => $validated['brand'],
                'condition' => $validated['condition'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'starting_price' => $validated['starting_price'],
                'buyout_bid' => $validated['buyout_bid'] ?? null,
                'end_time' => $validated['end_time'],
                'minimum_market_value' => $validated['minimum_market_value'],
                'reference_links' => $validated['reference_links'] ?? null,
            ];

            // ✅ FIXED: Handle multiple product image updates - STORE ALL IMAGES AS JSON ARRAY
            if ($request->hasFile('product_img')) {
                // Delete old files if they exist
                $oldImages = $auction->product_img ?? [];
                if (is_array($oldImages) && !empty($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }

                $allImagePaths = [];
                foreach ($request->file('product_img') as $imageFile) {
                    $filename = time() . '_' . uniqid() . '_product_' . preg_replace('/\s+/', '_', $imageFile->getClientOriginalName());
                    $imagePath = 'auctions/' . $filename;
                    $imageFile->storeAs('auctions', $filename, 'public');
                    
                    // Store all image paths in array
                    $allImagePaths[] = $imagePath;
                }

                $updateData['product_img'] = $allImagePaths; // ✅ Store ALL images as JSON array
            }

            // Handle owner proof update
            if ($request->hasFile('owner_proof')) {
                if ($auction->owner_proof && Storage::disk('public')->exists($auction->owner_proof)) {
                    Storage::disk('public')->delete($auction->owner_proof);
                }

                $proofFile = $request->file('owner_proof');
                $filename = time() . '_owner_proof_' . preg_replace('/\s+/', '_', $proofFile->getClientOriginalName());
                $updateData['owner_proof'] = 'proofs/' . $filename;
                $proofFile->storeAs('proofs', $filename, 'public');
            }

            // Handle market value proof update
            if ($request->hasFile('market_value_proof')) {
                if ($auction->market_value_proof && Storage::disk('public')->exists($auction->market_value_proof)) {
                    Storage::disk('public')->delete($auction->market_value_proof);
                }

                $proofFile = $request->file('market_value_proof');
                $filename = time() . '_market_proof_' . preg_replace('/\s+/', '_', $proofFile->getClientOriginalName());
                $updateData['market_value_proof'] = 'proofs/' . $filename;
                $proofFile->storeAs('proofs', $filename, 'public');
            }

            // Update auction
            $auction->update($updateData);

            return redirect()->route('customer.auctions.index')->with('success', 'Auction updated successfully!');
        } catch (\Exception $e) {
            Log::error('Auction update failed: ' . $e->getMessage());
            return back()->withErrors('Failed to update auction: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to delete auctions.');
            }
            
            $auction = Auction::findOrFail($id);
            
            if ($auction->user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }

            if ($auction->status !== 'pending' || $auction->bids()->count() > 0) {
                return redirect()->route('customer.auctions.index')->with('error', 'Cannot delete auction that has bids or is already approved.');
            }

            // Use transaction for data consistency
            DB::transaction(function () use ($auction) {
                // ✅ FIXED: Delete all product images (multiple files)
                $productImages = $auction->product_img ?? [];
                if (is_array($productImages) && !empty($productImages)) {
                    foreach ($productImages as $image) {
                        if ($image && Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                } elseif (is_string($productImages) && !empty($productImages)) {
                    // Handle legacy single image format
                    if (Storage::disk('public')->exists($productImages)) {
                        Storage::disk('public')->delete($productImages);
                    }
                }
                
                // Delete other files
                $files = [
                    $auction->owner_proof,
                    $auction->market_value_proof
                ];
                
                foreach ($files as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
                
                // Delete auction
                $auction->delete();
            });

            return redirect()->route('customer.auctions.index')->with('success', 'Auction deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Auction deletion failed: ' . $e->getMessage());
            return back()->withErrors('Failed to delete auction: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Allow public viewing of auction details
     */
    public function detail($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view auction details.');
        }
        
        try {
            $auction = Auction::with(['bids.user', 'user'])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error loading auction details: ' . $e->getMessage());
            $auction = Auction::with(['bids.user', 'user'])->findOrFail($id);
        }
        
        return view('customer.CustomerAuction.cauction_details', compact('auction'));
    }

    public function myAuctions()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view your auctions.');
        }
        
        try {
            $auctions = Auction::where('user_id', $user->id)
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error loading my auctions: ' . $e->getMessage());
            $auctions = Auction::where('user_id', $user->id)
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
        }

        return view('customer.CustomerAuction.cauction', compact('auctions'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to search auctions.');
        }
        
        $query = Auction::where('user_id', $user->id);

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        try {
            $auctions = $query->orderBy('created_at', 'desc')->paginate(10);
        } catch (\Exception $e) {
            Log::error('Error searching auctions: ' . $e->getMessage());
            $auctions = $query->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('customer.CustomerAuction.cauction', compact('auctions'));
    }

    /**
     * Display public auctions for bidding - FIXED: Now shows only active auctions
     */
    public function publicAuctions()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to browse auctions.');
        }
        
        try {
            // FIXED: Get only active auctions from OTHER users that haven't ended yet
            $publicAuctions = Auction::where('status', 'active')
                                   ->where('end_time', '>', Carbon::now())
                                   ->where('user_id', '!=', $user->id)
                                   ->with('bids')
                                   ->withCount('bids')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(12, ['*'], 'public_auctions_page');

            $userAuctions = collect();

        } catch (\Exception $e) {
            Log::error('Error loading public auctions: ' . $e->getMessage());
            $publicAuctions = Auction::where('status', 'active')
                                   ->where('end_time', '>', Carbon::now())
                                   ->where('user_id', '!=', $user->id)
                                   ->with('bids')
                                   ->withCount('bids')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(12, ['*'], 'public_auctions_page');
            $userAuctions = collect();
        }

        return view('customer.CustomerAuction.cauction', compact('userAuctions', 'publicAuctions'));
    }

    /**
     * Display auction details for public viewing
     */
    public function publicShow($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view auction details.');
        }
        
        try {
            $auction = Auction::with(['user', 'bids' => function($query) {
                $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
            }])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error loading public auction: ' . $e->getMessage());
            $auction = Auction::with(['user', 'bids' => function($query) {
                $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
            }])->findOrFail($id);
        }

        // Allow viewing of ended auctions for winners and always allow owners to view
        $allowedStatuses = ['active', 'approved', 'ended', 'completed'];
        
        // Always allow auction owner to view their auction
        if ($auction->user_id === $user->id) {
            return view('customer.CustomerAuction.cauction_details', compact('auction'));
        }
        
        // Allow winners to view ended auctions they won
        if ($auction->status === 'ended' && $auction->winner_id === $user->id) {
            return view('customer.CustomerAuction.cauction_details', compact('auction'));
        }
        
        // For other users, only allow viewing active/approved auctions
        if (!in_array($auction->status, $allowedStatuses) || $auction->status === 'ended') {
            abort(404, 'Auction not found or not available for viewing.');
        }

        return view('customer.CustomerAuction.cauction_details', compact('auction'));
    }

    /**
     * Search public auctions - FIXED: Now includes only active auctions
     */
    public function searchPublicAuctions(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to search auctions.');
        }
        
        $query = Auction::where('status', 'active')
                       ->where('end_time', '>', Carbon::now())
                       ->where('user_id', '!=', $user->id)
                       ->with('bids')
                       ->withCount('bids');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        try {
            $publicAuctions = $query->orderBy('created_at', 'desc')->paginate(12, ['*'], 'public_auctions_page');
            $userAuctions = collect();
        } catch (\Exception $e) {
            Log::error('Error searching public auctions: ' . $e->getMessage());
            $publicAuctions = $query->orderBy('created_at', 'desc')->paginate(12, ['*'], 'public_auctions_page');
            $userAuctions = collect();
        }

        return view('customer.CustomerAuction.cauction', compact('userAuctions', 'publicAuctions'));
    }

    /**
     * Place a bid on an auction using diamonds - FIXED BUYOUT BID LOGIC WITH PROPER CHAT CREATION
     */
    public function placeBid(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to place a bid.');
        }

        $auction = Auction::with(['bids' => function($query) {
            $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        // Check if auction is active and hasn't ended
        if ($auction->status !== 'active' || $auction->end_time <= Carbon::now()) {
            return back()->with('error', 'This auction is not available for bidding.');
        }

        // Check if user is trying to bid on their own auction
        if ($auction->user_id === $user->id) {
            return back()->with('error', 'You cannot bid on your own auction.');
        }

        $minBid = $auction->current_bid > 0 ? $auction->current_bid + 1 : $auction->starting_price;
        
        // Check if this is a buyout bid attempt
        $isBuyoutAttempt = $auction->buyout_bid && $request->bid_amount >= $auction->buyout_bid;
        
        // For buyout bids, require EXACT buyout price
        if ($isBuyoutAttempt && $request->bid_amount != $auction->buyout_bid) {
            return back()->with('error', 'Buyout bid must be exactly 💎' . number_format($auction->buyout_bid, 0) . '.');
        }

        // Set validation rules
        $validationRules = [
            'bid_amount' => 'required|integer|min:' . $minBid
        ];

        // For regular bids, set maximum limit to buyout price minus 1 (if buyout exists)
        if ($auction->buyout_bid && !$isBuyoutAttempt) {
            $validationRules['bid_amount'] .= '|max:' . ($auction->buyout_bid - 1);
        }

        $validated = $request->validate($validationRules);

        $bidAmount = $validated['bid_amount'];

        // Check if user has enough diamonds
        if ($user->diamond_balance < $bidAmount) {
            return back()->with('error', 'Insufficient diamonds. You need 💎' . $bidAmount . ' but only have 💎' . $user->diamond_balance);
        }

        // Check if bid meets buyout price exactly
        $isBuyoutTriggered = $auction->buyout_bid && $bidAmount == $auction->buyout_bid;

        try {
            // Use database transaction for data consistency
            DB::transaction(function () use ($auction, $user, $bidAmount, $isBuyoutTriggered) {
                // Get the current highest bidder (if any)
                $currentHighestBid = $auction->bids->first();
                $previousHighestBidder = null;
                $previousBidAmount = 0;

                if ($currentHighestBid && $currentHighestBid->user_id !== $user->id) {
                    $previousHighestBidder = $currentHighestBid->user;
                    $previousBidAmount = $currentHighestBid->amount;
                }

                // Create the new bid
                $newBid = Bid::create([
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'amount' => $bidAmount,
                    'is_buyout' => $isBuyoutTriggered
                ]);

                // Update auction current bid
                $auction->update([
                    'current_bid' => $bidAmount
                ]);

                // If buyout was triggered, end the auction immediately and set winner with ESCROW (12-hour deadline)
                if ($isBuyoutTriggered) {
                    $auction->update([
                        'status' => 'ended',
                        'winner_id' => $user->id,
                        'payout_status' => 'pending',
                        'payout_amount' => $bidAmount,
                        'escrow_held_at' => now(),
                        'seller_reply_deadline' => now()->addHours(12),
                        'chat_created_at' => now()
                    ]);

                    // ✅ FIXED: Create winner chat using our own method (not CustomerChatController)
                    $conversationId = $this->createWinnerChat($auction->id, $user->id);
                    
                    Log::info("Auction {$auction->id} ended via buyout bid by user {$user->id} for 💎{$bidAmount} - Chat created: " . ($conversationId ? 'Yes' : 'No'));

                    // ✅ FIXED: Store proper session data for auto-opening chat
                    session([
                        'current_conversation_id' => $conversationId,
                        'force_open_chat' => true,
                        'chat_type' => 'auction_winner',
                        'chat_auction_id' => $auction->id,
                        'chat_product_name' => $auction->product_name,
                        'chat_seller_id' => $auction->user_id,
                        'chat_seller_name' => $auction->user->username ?? 'Seller',
                        'chat_customer_name' => $user->username ?? 'Customer',
                        'is_buy_now' => true,
                        'item_source' => 'auction_management'
                    ]);
                }

                // Deduct diamonds from the new bidder
                User::where('id', $user->id)->decrement('diamond_balance', $bidAmount);

                // Return diamonds to the previous highest bidder (if they were outbid)
                if ($previousHighestBidder) {
                    User::where('id', $previousHighestBidder->id)->increment('diamond_balance', $previousBidAmount);
                    
                    Log::info("Refunded 💎{$previousBidAmount} to user {$previousHighestBidder->id} (outbid by user {$user->id})");
                }

                // Refund all other bidders if buyout was triggered
                if ($isBuyoutTriggered) {
                    foreach ($auction->bids as $bid) {
                        if ($bid->id !== $newBid->id && $bid->user_id !== $user->id) {
                            User::where('id', $bid->user_id)->increment('diamond_balance', $bid->amount);
                            Log::info("Refunded 💎{$bid->amount} to user {$bid->user_id} (auction bought out by user {$user->id})");
                        }
                    }
                }
            });

            // If buyout was triggered, redirect to chat page with instructions
            if ($isBuyoutTriggered) {
                return redirect()->route('customer.chat')->with([
                    'success' => 'Auction bought out successfully! You won the auction via buyout. 💎' . $bidAmount . ' deducted from your balance. Payout is in escrow pending seller reply within 12 hours. Please use the chat system to contact the seller.',
                    'auto_open_chat' => true,
                    'auction_id' => $auction->id
                ]);
            } else {
                return redirect()->route('auction_details', $auction->id)->with('success', 'Bid placed successfully! 💎' . $bidAmount . ' deducted from your balance.');
            }
        } catch (\Exception $e) {
            Log::error('Bid placement failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to place bid: ' . $e->getMessage());
        }
    }

    /**
     * FIXED: Create winner chat with proper structure matching CustomerChatController
     */
    private function createWinnerChat($auctionId, $winnerId)
    {
        try {
            $auction = Auction::with(['user'])->find($auctionId);
            if (!$auction) {
                Log::error("Auction not found for chat creation: {$auctionId}");
                return null;
            }

            $sellerId = $auction->user_id;
            $winner = User::find($winnerId);
            
            if (!$winner) {
                Log::error("Winner not found for chat creation: {$winnerId}");
                return null;
            }

            // ✅ FIXED: Use EXACT same conversation ID format as CustomerChatController
            $conversationId = 'auction_winner_' . $winnerId . '_' . $sellerId . '_' . $auctionId;

            // Load existing conversations
            $privateConversations = $this->chatStorage->loadConversations();

            // Check if conversation already exists
            if (!isset($privateConversations[$conversationId])) {
                // ✅ FIXED: Use EXACT same data structure as CustomerChatController
                $conversationData = [
                    'id' => $conversationId,
                    'customer_id' => $winnerId,
                    'seller_id' => $sellerId,
                    'customer_name' => $winner->username ?? 'Customer',
                    'seller_name' => $auction->user->username ?? 'Seller',
                    'chat_type' => 'auction_winner',
                    'is_buy_now' => true,
                    'payment_received' => false,
                    'item_source' => 'auction_management',
                    'auction_id' => $auctionId,
                    'product_name' => $auction->product_name,
                    'product_image' => $auction->first_image_url, // ✅ Use first image URL from the updated model
                    'product_price' => $auction->starting_price,
                    'buy_now_quantity' => 1,
                    'buy_now_total_price' => $auction->current_bid,
                    'messages' => [],
                    'created_at' => now()->timestamp,
                    'updated_at' => now()->timestamp,
                ];

                // ✅ FIXED: Add proper winner message with escrow information
                $winnerName = $winner->username ?? 'Customer';
                $winMethod = 'buyout';
                
                $message = "🏆 **{$winnerName}** won your auction!\n\n";
                $message .= "**Auction Item:** {$auction->product_name}\n";
                $message .= "**Winning Bid:** 💎" . number_format($auction->current_bid, 0) . "\n";
                $message .= "**Win Method:** {$winMethod}\n\n";
                
                // Add escrow information
                $message .= "💰 **Payment Status:** 💎" . number_format($auction->payout_amount, 0) . " held in escrow\n";
                $message .= "⏰ **Seller Reply Deadline:** " . ($auction->seller_reply_deadline ? $auction->seller_reply_deadline->format('M j, Y g:i A') : 'Not set') . "\n\n";
                
                if ($auction->is_seller_reply_overdue) {
                    $message .= "⚠️ **Status:** Seller reply OVERDUE - Eligible for automatic refund\n\n";
                } else {
                    $message .= "✅ **Status:** Funds secured in escrow - Seller has " . ($auction->seller_reply_deadline ? $auction->seller_reply_deadline->diffForHumans() : '12 hours') . " to reply\n\n";
                }
                
                $message .= "Please coordinate for item delivery and payment confirmation.";

                $messageData = [
                    'id' => uniqid('msg_'),
                    'sender_id' => $winnerId,
                    'message' => $message,
                    'type' => 'auction_win',
                    'timestamp' => now()->timestamp
                ];

                $conversationData['messages'][] = $messageData;
                $privateConversations[$conversationId] = $conversationData;

                // Save conversations
                $this->chatStorage->saveConversations($privateConversations);

                Log::info("Winner chat created successfully with proper structure: {$conversationId}");
            } else {
                Log::info("Winner chat already exists: {$conversationId}");
            }

            return $conversationId;

        } catch (\Exception $e) {
            Log::error('Failed to create winner chat: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * End expired auction and handle diamond distribution (for system use) - UPDATED: Escrow system with 12-hour deadline
     */
    public function endExpiredAuction($id)
    {
        try {
            $auction = Auction::with(['bids' => function($query) {
                $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
            }, 'user'])->findOrFail($id);

            // Check if auction is active and has ended
            if ($auction->status !== 'active') {
                return response()->json(['error' => 'Auction is not active'], 400);
            }

            $winningBid = null;
            
            DB::transaction(function () use ($auction, &$winningBid) {
                $winningBid = $auction->bids->first();

                if ($winningBid) {
                    // Set payout in ESCROW for admin approval with 12-hour deadline
                    $auction->update([
                        'winner_id' => $winningBid->user_id,
                        'status' => 'ended',
                        'current_bid' => $winningBid->amount,
                        'payout_status' => 'pending',
                        'payout_amount' => $winningBid->amount,
                        'escrow_held_at' => now(),
                        'seller_reply_deadline' => now()->addHours(12),
                        'chat_created_at' => now()
                    ]);

                    // ✅ FIXED: Automatically create winner chat for expired auctions too
                    $conversationId = $this->createWinnerChat($auction->id, $winningBid->user_id);

                    Log::info("Expired auction {$auction->id} ended. Payout 💎{$winningBid->amount} in escrow pending seller reply within 12 hours. Chat created: " . ($conversationId ? 'Yes' : 'No'));

                    // Refund all losing bidders (except the winner)
                    foreach ($auction->bids as $bid) {
                        if ($bid->id !== $winningBid->id && $bid->user_id !== $winningBid->user_id) {
                            User::where('id', $bid->user_id)->increment('diamond_balance', $bid->amount);
                            Log::info("Refunded 💎{$bid->amount} to user {$bid->user_id} (expired auction {$auction->id})");
                        }
                    }
                } else {
                    // No bids, just end the auction
                    $auction->update([
                        'status' => 'ended',
                        'current_bid' => $auction->starting_price,
                        'payout_status' => 'pending',
                        'payout_amount' => 0
                    ]);

                    Log::info("Expired auction {$auction->id} ended with no bids");
                }
            });

            return response()->json(['success' => 'Auction ended successfully. Payout in escrow pending seller reply within 12 hours.'], 200);
        } catch (\Exception $e) {
            Log::error('Expired auction ending failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to end auction: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Auto-end expired auctions (call this from a scheduled task) - UPDATED: Escrow system with 12-hour deadline
     */
    public function autoEndExpiredAuctions()
    {
        $expiredAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', Carbon::now())
            ->get();

        $processedCount = 0;

        foreach ($expiredAuctions as $auction) {
            try {
                $this->endExpiredAuction($auction->id);
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-end auction {$auction->id}: " . $e->getMessage());
            }
        }

        Log::info("Auto-ended {$processedCount} expired auctions - All payouts in escrow with 12-hour seller reply deadline");
        
        return $processedCount;
    }

    /**
     * Get expired auctions for processing
     */
    public function getExpiredAuctions()
    {
        $expiredAuctions = Auction::where('status', 'active')
            ->where('end_time', '<=', Carbon::now())
            ->with(['bids' => function($query) {
                $query->orderBy('amount', 'desc')->orderBy('created_at', 'asc');
            }, 'user'])
            ->get();

        return response()->json([
            'count' => $expiredAuctions->count(),
            'auctions' => $expiredAuctions->map(function($auction) {
                return [
                    'id' => $auction->id,
                    'product_name' => $auction->product_name,
                    'end_time' => $auction->end_time,
                    'bids_count' => $auction->bids->count(),
                    'current_bid' => $auction->current_bid,
                    'starting_price' => $auction->starting_price
                ];
            })
        ]);
    }

    /**
     * Check seller's pending and available diamond balances
     */
    public function getSellerBalance($sellerId)
    {
        try {
            $seller = User::findOrFail($sellerId);
            
            // Calculate pending payouts in escrow
            $pendingPayouts = Auction::where('user_id', $sellerId)
                ->where('payout_status', 'pending')
                ->where('payout_amount', '>', 0)
                ->sum('payout_amount');

            // Calculate approved but not yet released payouts
            $approvedPayouts = Auction::where('user_id', $sellerId)
                ->where('payout_status', 'approved')
                ->where('payout_amount', '>', 0)
                ->sum('payout_amount');

            return response()->json([
                'available_balance' => $seller->diamond_balance,
                'pending_escrow_balance' => $pendingPayouts,
                'approved_payouts_balance' => $approvedPayouts,
                'total_earnings' => $seller->diamond_balance + $pendingPayouts + $approvedPayouts
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get seller balance: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve balance'], 500);
        }
    }

    /**
     * Mark item as received by buyer and AUTOMATICALLY release escrow to seller
     */
    public function markItemReceived(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Please log in to mark item as received.'], 401);
            }

            $auction = Auction::findOrFail($id);

            // Check if current user is the winner of this auction
            if ($auction->winner_id !== $user->id) {
                return response()->json(['error' => 'You are not the winner of this auction.'], 403);
            }

            // Check if item is already marked as received
            if ($auction->item_received_at) {
                return response()->json(['error' => 'Item has already been marked as received.'], 400);
            }

            // Check if payout is in escrow
            if (!$auction->isPayoutInEscrow() && !$auction->isPayoutApproved()) {
                return response()->json(['error' => 'Payout is not in escrow status.'], 400);
            }

            DB::transaction(function () use ($auction, $user) {
                // Mark item as received
                $auction->update([
                    'item_received_at' => now()
                ]);

                // AUTOMATICALLY release escrow to seller - NO ADMIN APPROVAL NEEDED
                if ($auction->isPayoutInEscrow()) {
                    // First approve the payout automatically
                    $auction->update([
                        'payout_status' => 'approved',
                        'payout_approved_at' => now(),
                        'payout_approved_by' => $user->id
                    ]);
                }

                // AUTOMATICALLY release diamonds to seller
                User::where('id', $auction->user_id)->increment('diamond_balance', $auction->payout_amount);

                // Mark payout as released automatically
                $auction->update([
                    'payout_status' => 'released',
                    'escrow_released_at' => now()
                ]);

                Log::info("Item marked as received for auction {$auction->id}. 💎{$auction->payout_amount} AUTOMATICALLY released to seller {$auction->user_id} without admin approval.");
            });

            return response()->json([
                'success' => 'Item marked as received successfully! 💎' . number_format($auction->payout_amount, 0) . ' has been AUTOMATICALLY released to the seller.',
                'payout_status' => $auction->payout_status
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to mark item as received: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark item as received: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check for overdue seller replies and auto-refund - UPDATED: 12-hour automatic refund
     */
    public function checkOverdueSellerReplies()
    {
        try {
            $overdueAuctions = Auction::where('payout_status', 'pending')
                ->where('seller_reply_deadline', '<=', now())
                ->whereNull('item_received_at')
                ->get();

            $refundedCount = 0;

            foreach ($overdueAuctions as $auction) {
                // Check if seller hasn't replied within 12 hours
                $hasSellerReplied = $this->checkSellerHasRepliedInChat($auction->id);
                
                if (!$hasSellerReplied) {
                    DB::transaction(function () use ($auction) {
                        // Refund diamonds to buyer
                        User::where('id', $auction->winner_id)->increment('diamond_balance', $auction->payout_amount);
                        
                        // Mark payout as refunded
                        $auction->update([
                            'payout_status' => 'refunded',
                            'escrow_released_at' => now()
                        ]);

                        Log::info("AUTO-REFUNDED 💎{$auction->payout_amount} to buyer {$auction->winner_id} for auction {$auction->id} (seller didn't reply within 12 hours)");
                    });

                    $refundedCount++;
                }
            }

            Log::info("Auto-refunded {$refundedCount} auctions due to seller not replying within 12 hours");
            return $refundedCount;

        } catch (\Exception $e) {
            Log::error('Failed to check overdue seller replies: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if seller has replied in the chat
     */
    private function checkSellerHasRepliedInChat($auctionId)
    {
        try {
            $auction = Auction::find($auctionId);
            if (!$auction) {
                return false;
            }

            // Get the conversation using chat storage
            $conversationId = 'auction_winner_' . $auction->winner_id . '_' . $auction->user_id . '_' . $auction->id;
            
            $privateConversations = $this->chatStorage->loadConversations();
            
            if (!isset($privateConversations[$conversationId])) {
                Log::info("No conversation found for auction {$auctionId}");
                return false;
            }

            $conversation = $privateConversations[$conversationId];
            $messages = $conversation['messages'] ?? [];

            // Check if seller (auction owner) has sent any messages
            foreach ($messages as $message) {
                if ($message['sender_id'] === $auction->user_id) {
                    Log::info("Seller has replied in chat for auction {$auctionId}");
                    return true;
                }
            }

            Log::info("Seller has NOT replied in chat for auction {$auctionId}");
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error checking seller reply in chat: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get auction escrow status for monitoring
     */
    public function getEscrowStatus($id)
    {
        try {
            $auction = Auction::with(['user', 'winner'])->findOrFail($id);
            
            return response()->json([
                'payout_status' => $auction->payout_status,
                'payout_amount' => $auction->payout_amount,
                'escrow_held_at' => $auction->escrow_held_at,
                'seller_reply_deadline' => $auction->seller_reply_deadline,
                'item_received_at' => $auction->item_received_at,
                'is_seller_reply_overdue' => $auction->is_seller_reply_overdue,
                'has_seller_replied' => $this->checkSellerHasRepliedInChat($id),
                'seller_username' => $auction->user->username ?? 'Unknown',
                'winner_username' => $auction->winner->username ?? 'Unknown',
                'hours_until_deadline' => $auction->seller_reply_deadline ? now()->diffInHours($auction->seller_reply_deadline, false) : null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get escrow status: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve escrow status'], 500);
        }
    }

    /**
     * Schedule automatic checks (call this from Laravel scheduler)
     */
    public function scheduleAutomaticChecks()
    {
        try {
            // Check for expired auctions
            $expiredCount = $this->autoEndExpiredAuctions();
            
            // Check for overdue seller replies (12-hour timeout)
            $refundedCount = $this->checkOverdueSellerReplies();
            
            Log::info("Scheduled checks completed: Ended {$expiredCount} expired auctions, refunded {$refundedCount} overdue auctions");
            
            return response()->json([
                'success' => true,
                'expired_ended' => $expiredCount,
                'overdue_refunded' => $refundedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Scheduled automatic checks failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}