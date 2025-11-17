<?php

namespace App\Http\Controllers\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Trade;
use App\Models\ExchangeProposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\ChatStorageService;

class CustomerChatController extends Controller
{
    protected $chatStorage;

    public function __construct()
    {
        $this->chatStorage = new ChatStorageService();
    }

    /**
     * Get conversations from JSON storage
     */
    private function getConversationsFromStorage()
    {
        return $this->chatStorage->loadConversations();
    }

    /**
     * Save conversations to JSON storage
     */
    private function saveConversationsToStorage($conversations)
    {
        return $this->chatStorage->saveConversations($conversations);
    }

    /**
     * NEW: Add trade completion message to chat
     */
    public function addTradeCompleteMessage($conversationId, $message, $senderId)
    {
        try {
            $privateConversations = $this->getConversationsFromStorage();
            
            if (isset($privateConversations[$conversationId])) {
                $messageData = [
                    'id' => uniqid('msg_'),
                    'sender_id' => $senderId,
                    'message' => $message,
                    'type' => 'trade_completed',
                    'timestamp' => now()->timestamp
                ];

                // Ensure messages array exists
                if (!isset($privateConversations[$conversationId]['messages'])) {
                    $privateConversations[$conversationId]['messages'] = [];
                }
                
                $privateConversations[$conversationId]['messages'][] = $messageData;
                $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
                
                $this->saveConversationsToStorage($privateConversations);
                
                Log::info('Trade completion message added to chat:', [
                    'conversation_id' => $conversationId,
                    'sender_id' => $senderId
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error adding trade complete message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * NEW: Add trade completion button to trade proposal chats
     */
    private function addTradeCompleteButton($conversationId, $tradeId, $userRole, $html)
    {
        try {
            $trade = Trade::find($tradeId);
            if (!$trade || !$trade->isActive()) {
                return $html;
            }

            // Only show complete button for trade proposal chats
            $conversations = $this->getConversationsFromStorage();
            if (!isset($conversations[$conversationId]) || $conversations[$conversationId]['chat_type'] !== 'trade_proposal') {
                return $html;
            }

            // Check if user is authorized to complete trade (trade owner or proposal participant)
            $user = Auth::user();
            $isTradeOwner = $trade->user_id === $user->id;
            
            $hasAcceptedProposal = false;
            if (!$isTradeOwner) {
                $hasAcceptedProposal = ExchangeProposal::where('receiver_trade_id', $trade->id)
                    ->where('sender_id', $user->id)
                    ->where('status', 'accepted')
                    ->exists();
            }

            if ($isTradeOwner || $hasAcceptedProposal) {
                $completeButton = '<div class="item-received-section">
                                    <div class="alert alert-info text-center">
                                        <h5><i class="fas fa-exchange-alt"></i> Complete Trade</h5>
                                        <p>Click below to mark this trade as completed and end the exchange.</p>
                                        <button class="btn btn-success btn-lg" onclick="completeTrade(\'' . e($conversationId) . '\', ' . $tradeId . ')">
                                            <i class="fas fa-check-circle"></i> Trade Complete - END EXCHANGE
                                        </button>
                                        <p class="small mt-2">This will change the trade status from "Active" to "Completed".</p>
                                    </div>
                                  </div>';
                
                // Insert the button at the beginning of the messages
                $html = $completeButton . $html;
            }
            
            return $html;
            
        } catch (\Exception $e) {
            Log::error('Error adding trade complete button: ' . $e->getMessage());
            return $html;
        }
    }

    /**
     * Store buy now data in session before redirecting to chat
     */
    public function storeBuyNowData(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login to make a purchase.']);
            }

            $productId = $request->get('product_id');
            $quantity = $request->get('quantity', 1);
            $totalPrice = $request->get('total_price');

            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found.']);
            }

            // Store buy now data in session
            Session::put('buy_now_quantity', $quantity);
            Session::put('buy_now_total_price', $totalPrice);
            Session::put('buy_now_product_id', $productId);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error storing buy now data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error processing purchase.']);
        }
    }

    /**
     * Start REGULAR chat with product seller (from Chat button)
     */
    public function startRegularChat($productId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to chat.');
            }

            $product = Product::with('user')->find($productId);
            
            if (!$product) {
                return redirect()->route('cproduct')->with('error', 'Product not found.');
            }
            
            if ($product->user_id === $user->id) {
                return redirect()->route('cproduct')->with('error', 'You cannot chat with yourself.');
            }
            
            // Generate unique conversation ID for product
            $conversationId = 'product_' . $user->id . '_' . $product->user_id . '_' . $product->id;
            
            // Store product info in session (keep this for immediate access)
            Session::put('chat_type', 'product');
            Session::put('chat_product_id', $product->id);
            Session::put('chat_product_name', $product->name);
            Session::put('chat_product_image', $product->model_file ? asset('storage/' . $product->model_file) : null);
            Session::put('chat_seller_id', $product->user_id);
            Session::put('chat_seller_name', $product->user->username ?? 'Seller');
            Session::put('chat_customer_name', $user->username ?? 'Customer');
            Session::put('current_conversation_id', $conversationId);
            Session::put('is_buy_now', false);
            Session::put('item_source', 'product_management');
            
            // Initialize or update private conversation storage
            $this->initializePrivateConversation($conversationId, $user, $product, false, 'product', 'product_management');
            
            // If it's a new conversation, add regular chat message
            if ($this->isNewPrivateConversation($conversationId)) {
                $this->addPrivateMessage($conversationId, $user->id, 'I want to ask more information about this item you are selling.', 'text');
            }
            
            return redirect()->route('customer.chat');
            
        } catch (\Exception $e) {
            Log::error('Error starting regular chat: ' . $e->getMessage());
            return redirect()->route('cproduct')->with('error', 'Error starting chat.');
        }
    }

    /**
     * Start AUCTION chat with auction seller (from Chat button on auction)
     */
    public function startAuctionChat($auctionId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to chat.');
            }

            $auction = Auction::with('user')->find($auctionId);
            
            if (!$auction) {
                return redirect()->route('customer.auctions.index')->with('error', 'Auction not found.');
            }
            
            if ($auction->user_id === $user->id) {
                return redirect()->route('customer.auctions.index')->with('error', 'You cannot chat with yourself.');
            }
            
            // Generate unique conversation ID for auction
            $conversationId = 'auction_' . $user->id . '_' . $auction->user_id . '_' . $auction->id;
            
            // Store auction info in session
            Session::put('chat_type', 'auction');
            Session::put('chat_auction_id', $auction->id);
            Session::put('chat_product_name', $auction->product_name);
            Session::put('chat_product_image', $auction->product_img ? (is_array($auction->product_img) && count($auction->product_img) > 0 ? asset('storage/' . $auction->product_img[0]) : asset('storage/' . $auction->product_img)) : null);
            Session::put('chat_seller_id', $auction->user_id);
            Session::put('chat_seller_name', $auction->user->username ?? 'Seller');
            Session::put('chat_customer_name', $user->username ?? 'Customer');
            Session::put('current_conversation_id', $conversationId);
            Session::put('is_buy_now', false);
            Session::put('item_source', 'auction_management');
            
            // Initialize or update private conversation storage
            $this->initializePrivateConversation($conversationId, $user, $auction, false, 'auction', 'auction_management');
            
            // If it's a new conversation, add regular chat message
            if ($this->isNewPrivateConversation($conversationId)) {
                $this->addPrivateMessage($conversationId, $user->id, 'I want to ask more information about this auction item.', 'text');
            }
            
            return redirect()->route('customer.chat');
            
        } catch (\Exception $e) {
            Log::error('Error starting auction chat: ' . $e->getMessage());
            return redirect()->route('customer.auctions.index')->with('error', 'Error starting chat.');
        }
    }

    /**
     * Start AUCTION WINNER chat (when auction ends and winner wants to chat with seller) - UPDATED WITH ESCROW INFO
     */
    public function startAuctionWinnerChat($auctionId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to chat.');
            }

            $auction = Auction::with('user')->find($auctionId);
            
            if (!$auction) {
                return redirect()->route('customer.auctions.index')->with('error', 'Auction not found.');
            }
            
            // Check if user is the winner OR the seller
            $isWinner = $auction->winner_id === $user->id;
            $isSeller = $auction->user_id === $user->id;
            
            if (!$isWinner && !$isSeller) {
                return redirect()->route('customer.auctions.index')->with('error', 'Only the auction winner or seller can access this chat.');
            }
            
            // Generate unique conversation ID for auction winner
            $conversationId = 'auction_winner_' . $user->id . '_' . $auction->user_id . '_' . $auction->id;
            
            // Store auction info in session
            Session::put('chat_type', 'auction_winner');
            Session::put('chat_auction_id', $auction->id);
            Session::put('chat_product_name', $auction->product_name);
            Session::put('chat_product_image', $auction->product_img ? (is_array($auction->product_img) && count($auction->product_img) > 0 ? asset('storage/' . $auction->product_img[0]) : asset('storage/' . $auction->product_img)) : null);
            Session::put('chat_seller_id', $auction->user_id);
            Session::put('chat_seller_name', $auction->user->username ?? 'Seller');
            Session::put('chat_customer_name', $user->username ?? 'Customer');
            Session::put('current_conversation_id', $conversationId);
            Session::put('is_buy_now', true);
            Session::put('buy_now_quantity', 1);
            Session::put('buy_now_total_price', $auction->current_bid);
            Session::put('item_source', 'auction_management');
            
            // Initialize or update private conversation storage
            $this->initializePrivateConversation($conversationId, $user, $auction, true, 'auction_winner', 'auction_management', 1, $auction->current_bid);
            
            // Check if this is a new conversation to add winner message
            if ($this->isNewPrivateConversation($conversationId)) {
                $this->addAuctionWinMessage($conversationId, $auction, $user);
            }
            
            return redirect()->route('customer.chat');
            
        } catch (\Exception $e) {
            Log::error('Error starting auction winner chat: ' . $e->getMessage());
            return redirect()->route('customer.auctions.index')->with('error', 'Error starting winner chat.');
        }
    }

    /**
     * Start TRADE PROPOSAL chat (when someone submits an exchange proposal)
     */
    public function startTradeProposalChat($tradeId, $proposalId = null)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to chat.');
            }

            $trade = Trade::with('user')->find($tradeId);
            
            if (!$trade) {
                return redirect()->route('customer.trading')->with('error', 'Trade not found.');
            }
            
            // Check if user is the sender OR the receiver
            $isSender = false;
            $isReceiver = false;
            
            if ($proposalId) {
                $proposal = ExchangeProposal::find($proposalId);
                if ($proposal) {
                    $isSender = $proposal->sender_id === $user->id;
                    $isReceiver = $proposal->receiver_id === $user->id;
                }
            }
            
            if (!$isSender && !$isReceiver && $trade->user_id !== $user->id) {
                return redirect()->route('customer.trading')->with('error', 'Only trade participants can access this chat.');
            }
            
            // Generate unique conversation ID for trade proposal
            $conversationId = 'trade_proposal_' . $trade->id . '_' . $user->id;
            
            // Store trade info in session
            Session::put('chat_type', 'trade_proposal');
            Session::put('chat_trade_id', $trade->id);
            Session::put('chat_proposal_id', $proposalId);
            Session::put('chat_product_name', $trade->name);
            Session::put('chat_product_image', $trade->image ? (is_array($trade->image) && count($trade->image) > 0 ? asset('storage/' . $trade->image[0]) : asset('storage/' . $trade->image)) : null);
            Session::put('chat_seller_id', $trade->user_id);
            Session::put('chat_seller_name', $trade->user->username ?? 'Trade Owner');
            Session::put('chat_customer_name', $user->username ?? 'Customer');
            Session::put('current_conversation_id', $conversationId);
            Session::put('is_buy_now', false);
            Session::put('item_source', 'trade_management');
            
            // Initialize or update private conversation storage
            $this->initializeTradeConversation($conversationId, $user, $trade, $proposalId);
            
            return redirect()->route('customer.chat');
            
        } catch (\Exception $e) {
            Log::error('Error starting trade proposal chat: ' . $e->getMessage());
            return redirect()->route('customer.trading')->with('error', 'Error starting trade chat.');
        }
    }

    /**
     * Initialize trade conversation storage
     */
    private function initializeTradeConversation($conversationId, $user, $trade, $proposalId = null)
    {
        $privateConversations = $this->getConversationsFromStorage();
        
        if (!isset($privateConversations[$conversationId])) {
            $conversationData = [
                'id' => $conversationId,
                'customer_id' => $user->id,
                'seller_id' => $trade->user_id,
                'customer_name' => $user->username ?? 'Customer',
                'seller_name' => $trade->user->username ?? 'Trade Owner',
                'chat_type' => 'trade_proposal',
                'is_buy_now' => false,
                'payment_received' => false,
                'item_source' => 'trade_management',
                'messages' => [],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
                'trade_id' => $trade->id,
                'proposal_id' => $proposalId,
                'product_name' => $trade->name,
                'product_image' => $trade->image ? (is_array($trade->image) && count($trade->image) > 0 ? asset('storage/' . $trade->image[0]) : asset('storage/' . $trade->image)) : null,
            ];

            $privateConversations[$conversationId] = $conversationData;
        }
        
        $this->saveConversationsToStorage($privateConversations);
        
        Log::info('Initialized trade conversation:', [
            'conversation_id' => $conversationId,
            'trade_id' => $trade->id,
            'proposal_id' => $proposalId
        ]);
    }

    /**
     * Auto create trade proposal chat with image support
     */
    public function autoCreateTradeProposalChat($tradeId, $proposalId, $senderId, $imagePaths = [])
    {
        try {
            $trade = Trade::with('user')->find($tradeId);
            $sender = User::find($senderId);
            $proposal = ExchangeProposal::find($proposalId);
            
            if (!$trade || !$sender || !$proposal) {
                Log::error('Auto create trade proposal chat failed: Trade, sender or proposal not found', [
                    'trade_id' => $tradeId, 
                    'sender_id' => $senderId,
                    'proposal_id' => $proposalId
                ]);
                return false;
            }
            
            // Generate unique conversation ID for trade proposal
            $conversationId = 'trade_proposal_' . $trade->id . '_' . $sender->id;
            
            // Initialize private conversation storage
            $this->initializeTradeConversation($conversationId, $sender, $trade, $proposalId);
            
            // Add trade proposal message with images
            $this->addTradeProposalMessage($conversationId, $proposal, $trade, $sender, $imagePaths);
            
            Log::info('Auto created trade proposal chat', [
                'trade_id' => $tradeId, 
                'sender_id' => $senderId,
                'proposal_id' => $proposalId,
                'conversation_id' => $conversationId,
                'image_count' => count($imagePaths)
            ]);
            
            return $conversationId;
            
        } catch (\Exception $e) {
            Log::error('Error auto creating trade proposal chat: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enhanced trade proposal message with image support
     */
    private function addTradeProposalMessage($conversationId, $proposal, $trade, $sender, $imagePaths = [])
    {
        $senderName = $sender->username ?? 'Customer';
        $cashAmount = $proposal->cash_amount > 0 ? 
            "➕ **Cash Amount:** ₱" . number_format($proposal->cash_amount, 2) . "\n" : 
            "";
        
        $personalMessage = $proposal->message ? 
            "💬 **Personal Message:** " . e($proposal->message) . "\n" : 
            "";
        
        $meetupInfo = $proposal->delivery_method === 'meetupOnly' && $proposal->meetup_location ? 
            "📍 **Meet-up Location:** " . e($proposal->meetup_location) . "\n" : 
            "";

        // Main proposal message
        $message = "🔄 **{$senderName}** sent you an exchange proposal!\n\n";
        $message .= "**Trade Item You're Offering:** {$trade->name}\n";
        $message .= "**Brand:** {$trade->brand} | **Condition:** " . ucfirst($trade->condition) . "\n\n";
        
        $message .= "**Proposed Exchange Item:** {$proposal->proposed_item_name}\n";
        $message .= "**Brand:** {$proposal->proposed_item_brand}\n";
        $message .= "**Category:** " . ucfirst(str_replace('-', ' ', $proposal->proposed_item_category)) . "\n";
        $message .= "**Condition:** " . ucfirst($proposal->proposed_item_condition) . "\n";
        $message .= "**Location:** {$proposal->proposed_item_location}\n";
        $message .= "**Description:** {$proposal->proposed_item_description}\n\n";
        
        $message .= "**Delivery Method:** " . ($proposal->delivery_method === 'cashOnDelivery' ? 'Cash On Delivery' : 'Meet-up Only') . "\n";
        $message .= $meetupInfo;
        $message .= $cashAmount;
        $message .= $personalMessage;
        
        $message .= "\n📸 **Item Photos:** " . (count($imagePaths) > 0 ? count($imagePaths) . " photos attached" : "No photos attached");
        $message .= "\n💡 You can respond to this proposal in your trade management panel or continue chatting here.";

        // Add the main proposal message
        $this->addPrivateMessage($conversationId, $sender->id, $message, 'trade_proposal');

        // Add image messages for each uploaded photo
        foreach ($imagePaths as $imagePath) {
            $this->addTradeProposalImageMessage($conversationId, $sender->id, $imagePath);
        }
    }

    /**
     * Add trade proposal image message
     */
    private function addTradeProposalImageMessage($conversationId, $senderId, $imagePath)
    {
        try {
            // Generate full URL for the image
            $imageUrl = asset('storage/' . $imagePath);
            
            $messageData = [
                'id' => uniqid('msg_'),
                'sender_id' => $senderId,
                'message' => '📷 Proposed Item Photo',
                'type' => 'image',
                'image_url' => $imageUrl,
                'timestamp' => now()->timestamp,
                'is_trade_proposal_image' => true // Flag to identify trade proposal images
            ];

            $privateConversations = $this->getConversationsFromStorage();
            
            if (isset($privateConversations[$conversationId])) {
                // Ensure messages array exists
                if (!isset($privateConversations[$conversationId]['messages'])) {
                    $privateConversations[$conversationId]['messages'] = [];
                }
                
                $privateConversations[$conversationId]['messages'][] = $messageData;
                $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
                
                $this->saveConversationsToStorage($privateConversations);
                
                Log::info('Added trade proposal image message:', [
                    'conversation_id' => $conversationId,
                    'sender_id' => $senderId,
                    'image_url' => $imageUrl
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error adding trade proposal image message: ' . $e->getMessage());
        }
    }

    /**
     * Automatically create winner chat when auction ends (for system use) - UPDATED WITH ESCROW INFO
     */
    public function autoCreateWinnerChat($auctionId, $winnerId)
    {
        try {
            $auction = Auction::with('user')->find($auctionId);
            $winner = User::find($winnerId);
            
            if (!$auction || !$winner) {
                Log::error('Auto create winner chat failed: Auction or winner not found', ['auction_id' => $auctionId, 'winner_id' => $winnerId]);
                return false;
            }
            
            // Generate unique conversation ID for auction winner
            $conversationId = 'auction_winner_' . $winner->id . '_' . $auction->user_id . '_' . $auction->id;
            
            // Initialize private conversation storage
            $this->initializePrivateConversation($conversationId, $winner, $auction, true, 'auction_winner', 'auction_management', 1, $auction->current_bid);
            
            // Add winner message with escrow information
            $this->addAuctionWinMessage($conversationId, $auction, $winner);
            
            Log::info('Auto created winner chat', ['auction_id' => $auctionId, 'winner_id' => $winnerId, 'conversation_id' => $conversationId]);
            
            return $conversationId;
            
        } catch (\Exception $e) {
            Log::error('Error auto creating winner chat: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add auction win message with proper formatting
     */
    private function addAuctionWinMessage($conversationId, $auction, $winner)
    {
        $winnerName = $winner->username ?? 'Customer';
        $winMethod = 'highest bid';
        
        // Check if there was a buyout bid
        $buyoutBid = $auction->bids()->where('is_buyout', true)->first();
        if ($buyoutBid) {
            $winMethod = 'buyout';
        }
        
        $message = "🏆 **{$winnerName}** won your auction!\n\n";
        $message .= "**Auction Item:** {$auction->product_name}\n";
        $message .= "**Winning Bid:** 💎" . number_format($auction->current_bid, 0) . "\n";
        $message .= "**Win Method:** {$winMethod}\n\n";
        
        // Add escrow information
        if ($auction->payout_status === 'pending') {
            $message .= "💰 **Payment Status:** 💎" . number_format($auction->payout_amount, 0) . " held in escrow\n";
            $message .= "⏰ **Seller Reply Deadline:** " . ($auction->seller_reply_deadline ? $auction->seller_reply_deadline->format('M j, Y g:i A') : 'Not set') . "\n\n";
            
            if ($auction->is_seller_reply_overdue) {
                $message .= "⚠️ **Status:** Seller reply OVERDUE - Eligible for automatic refund\n\n";
            } else {
                $message .= "✅ **Status:** Funds secured in escrow - Seller has " . ($auction->seller_reply_deadline ? $auction->seller_reply_deadline->diffForHumans() : '12 hours') . " to reply\n\n";
            }
        } elseif ($auction->payout_status === 'approved') {
            $message .= "✅ **Payment Status:** Payout approved - Ready for release\n\n";
        } elseif ($auction->payout_status === 'released') {
            $message .= "✅ **Payment Status:** Payout completed - Funds released to seller\n\n";
        } elseif ($auction->payout_status === 'rejected') {
            $message .= "❌ **Payment Status:** Payout rejected - Buyer refunded\n\n";
        } elseif ($auction->payout_status === 'refunded') {
            $message .= "🔄 **Payment Status:** Payout refunded - Funds returned to buyer\n\n";
        }
        
        $message .= "Please coordinate for item delivery and payment confirmation.";
        
        $this->addPrivateMessage($conversationId, $winner->id, $message, 'auction_win');
    }

    /**
     * FIXED: Point to existing method for backward compatibility
     */
    public function startRegularChatWithProduct($productId)
    {
        return $this->startRegularChat($productId);
    }

    /**
     * Start BUY NOW chat with product seller (from Buy Now button) - FIXED VERSION
     */
    public function startBuyNowChat($productId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to make a purchase.');
            }

            $product = Product::with('user')->find($productId);
            
            if (!$product) {
                return redirect()->route('cproduct')->with('error', 'Product not found.');
            }
            
            if ($product->user_id === $user->id) {
                return redirect()->route('cproduct')->with('error', 'You cannot purchase your own products.');
            }
            
            // Generate unique conversation ID (same as regular chat)
            $conversationId = 'product_' . $user->id . '_' . $product->user_id . '_' . $product->id;
            
            // Get buy now data from session
            $quantity = Session::get('buy_now_quantity', 1);
            $totalPrice = Session::get('buy_now_total_price', $product->price * $quantity);
            
            // Store product info in session
            Session::put('chat_type', 'product');
            Session::put('chat_product_id', $product->id);
            Session::put('chat_product_name', $product->name);
            Session::put('chat_product_image', $product->model_file ? asset('storage/' . $product->model_file) : null);
            Session::put('chat_seller_id', $product->user_id);
            Session::put('chat_seller_name', $product->user->username ?? 'Seller');
            Session::put('chat_customer_name', $user->username ?? 'Customer');
            Session::put('current_conversation_id', $conversationId);
            Session::put('is_buy_now', true);
            Session::put('buy_now_quantity', $quantity);
            Session::put('buy_now_total_price', $totalPrice);
            Session::put('item_source', 'product_management');
            
            // Initialize or update private conversation storage with buy now data
            $this->initializePrivateConversation($conversationId, $user, $product, true, 'product', 'product_management', $quantity, $totalPrice);
            
            // ✅ FIXED: ALWAYS add buy now message when coming from Buy Now button
            $buyerName = $user->username ?? 'Customer';
            $message = "🛒 **{$buyerName}** is interested in buying your item!\n\n";
            $message .= "**Product:** {$product->name}\n";
            $message .= "**Quantity:** {$quantity}\n";
            $message .= "**Unit Price:** ₱" . number_format($product->price, 2) . "\n";
            $message .= "**Total Price:** ₱" . number_format($totalPrice, 2) . "\n\n";
            $message .= "Please confirm when you receive the payment.";
            
            $this->addPrivateMessage($conversationId, $user->id, $message, 'buy_now');
            
            // Clear buy now session data
            Session::forget('buy_now_quantity');
            Session::forget('buy_now_total_price');
            Session::forget('buy_now_product_id');
            
            return redirect()->route('customer.chat');
            
        } catch (\Exception $e) {
            Log::error('Error starting buy now chat: ' . $e->getMessage());
            return redirect()->route('cproduct')->with('error', 'Error processing purchase.');
        }
    }

    /**
     * Chat main page - UPDATED: Handle force open chat
     */
    public function chatPage()
    {
        // Check if we need to force open a specific conversation
        $forceOpenChat = session('force_open_chat', false);
        $currentConversationId = session('current_conversation_id');
        
        if ($forceOpenChat && $currentConversationId) {
            // Clear the force flag so it doesn't persist
            session()->forget('force_open_chat');
        }
        
        return view('customer.CustomerChat.customer_chat', [
            'forceOpenChat' => $forceOpenChat,
            'currentConversationId' => $currentConversationId
        ]);
    }

    /**
     * Get user's private conversations - FIXED VERSION WITH BADGES
     */
    public function getConversations()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['html' => '<div class="empty-conversations"><i class="fa fa-comments"></i><h4>Please login to view conversations</h4></div>']);
            }

            $privateConversations = $this->getConversationsFromStorage();
            
            Log::info('Current conversations in storage:', ['count' => count($privateConversations), 'conversations' => array_keys($privateConversations)]);
            
            $userConversations = [];
            
            foreach ($privateConversations as $conversationId => $conversation) {
                // ✅ FIXED: Handle conversations created by CustomerAuctionController
                if (!isset($conversation['customer_id']) || !isset($conversation['seller_id'])) {
                    Log::warning('Invalid conversation structure:', ['conversation_id' => $conversationId]);
                    continue;
                }
                
                // Only show conversations where user is either customer or seller
                if ($conversation['customer_id'] === $user->id || $conversation['seller_id'] === $user->id) {
                    $userConversations[$conversationId] = $conversation;
                }
            }
            
            // Sort conversations by updated_at timestamp (newest first)
            uasort($userConversations, function($a, $b) {
                $timeA = $a['updated_at'] ?? 0;
                $timeB = $b['updated_at'] ?? 0;
                return $timeB <=> $timeA;
            });
            
            $html = '';
            
            if (empty($userConversations)) {
                $html = '<div class="empty-conversations">
                            <i class="fa fa-comments fa-3x mb-3 text-muted"></i>
                            <h4 class="text-muted">No conversations yet</h4>
                            <p class="text-muted">Start a conversation by clicking "Chat" on a product or auction</p>
                        </div>';
            } else {
                foreach ($userConversations as $conversationId => $conversation) {
                    // ✅ FIXED: Determine display names based on user role with proper fallbacks
                    if ($conversation['seller_id'] === $user->id) {
                        // User is seller - show customer's username
                        $displayName = $conversation['customer_name'] ?? 'Customer';
                        $otherPersonId = $conversation['customer_id'] ?? 0;
                        $otherPersonName = $conversation['customer_name'] ?? 'Customer';
                        $userRole = 'seller';
                    } else {
                        // User is customer - show seller's username
                        $displayName = $conversation['seller_name'] ?? 'Seller';
                        $otherPersonId = $conversation['seller_id'] ?? 0;
                        $otherPersonName = $conversation['seller_name'] ?? 'Seller';
                        $userRole = 'customer';
                    }
                    
                    $productName = $conversation['product_name'] ?? 'Product';
                    $chatType = $conversation['chat_type'] ?? 'product';
                    $itemSource = $conversation['item_source'] ?? 'product_management';
                    
                    // ✅ FIXED: Get type badge based on item source
                    $typeBadge = $this->getItemSourceBadge($itemSource, $chatType);
                    
                    // Get latest message for preview
                    $messages = $conversation['messages'] ?? [];
                    $latestMessage = end($messages);
                    
                    if ($latestMessage && is_array($latestMessage)) {
                        if (($latestMessage['type'] ?? 'text') === 'image') {
                            $preview = '📷 Image';
                        } elseif (($latestMessage['type'] ?? 'text') === 'buy_now') {
                            $preview = '🛒 Purchase Intent';
                        } elseif (($latestMessage['type'] ?? 'text') === 'auction_win') {
                            $preview = '🏆 Auction Win';
                        } elseif (($latestMessage['type'] ?? 'text') === 'payment_confirmed') {
                            $preview = '✅ Payment Confirmed';
                        } elseif (($latestMessage['type'] ?? 'text') === 'trade_proposal') {
                            $preview = '🔄 Exchange Proposal';
                        } elseif (($latestMessage['type'] ?? 'text') === 'trade_completed') {
                            $preview = '✅ Trade Completed';
                        } else {
                            $preview = $this->truncateMessage($latestMessage['message'] ?? 'No message', 35);
                        }
                        $time = $this->formatTime($latestMessage['timestamp'] ?? time());
                    } else {
                        $preview = 'No messages yet';
                        $time = 'Just now';
                    }
                    
                    $isActive = Session::get('current_conversation_id') === $conversationId;
                    
                    $html .= '<div class="conversation-item ' . ($isActive ? 'active' : '') . '" 
                                data-conversation="' . e($conversationId) . '" 
                                data-person-id="' . e($otherPersonId) . '"
                                data-person-name="' . e($otherPersonName) . '" 
                                data-product-name="' . e($productName) . '"
                                data-user-role="' . e($userRole) . '"
                                data-chat-type="' . e($chatType) . '"
                                data-item-source="' . e($itemSource) . '"
                                data-is-buy-now="' . ($conversation['is_buy_now'] ?? false ? '1' : '0') . '">
                                <div class="conversation-avatar">' . strtoupper(substr($displayName, 0, 1)) . '</div>
                                <div class="conversation-content">
                                    <div class="conversation-header">
                                        <div class="conversation-seller">' . $typeBadge . e($displayName) . '</div>
                                        <div class="conversation-time">' . e($time) . '</div>
                                    </div>
                                    <div class="conversation-product">' . e($productName) . '</div>
                                    <div class="conversation-preview">' . e($preview) . '</div>
                                </div>
                              </div>';
                }
            }

            Log::info('Generated conversations HTML for user: ' . $user->id);
            return response()->json(['html' => $html]);
            
        } catch (\Exception $e) {
            Log::error('Error loading conversations: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json(['html' => '<div class="empty-conversations"><i class="fa fa-exclamation-triangle text-danger"></i><h4 class="text-danger">Error loading conversations</h4><p class="text-muted">Please try refreshing the page</p></div>']);
        }
    }

    /**
     * Get messages for a conversation - UPDATED: Handle trade proposal images and trade complete button
     */
    public function getMessages(Request $request)
    {
        try {
            $conversationId = $request->get('conversation_id');
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['html' => '<div class="text-center text-muted p-3">Please login to view messages</div>']);
            }

            if (!$conversationId) {
                return response()->json(['html' => '<div class="text-center text-muted p-3">No conversation selected</div>']);
            }

            $privateConversations = $this->getConversationsFromStorage();
            
            if (!isset($privateConversations[$conversationId])) {
                Log::warning('Conversation not found in storage:', ['conversation_id' => $conversationId]);
                return response()->json(['html' => '<div class="text-center text-muted p-3>Conversation not found</div>']);
            }

            $conversation = $privateConversations[$conversationId];
            
            // ✅ FIXED: Handle conversations created by CustomerAuctionController
            if (!isset($conversation['customer_id']) || !isset($conversation['seller_id'])) {
                Log::error('Invalid conversation structure:', $conversation);
                return response()->json(['html' => '<div class="text-center text-danger p-3">Invalid conversation data</div>']);
            }
            
            if ($conversation['customer_id'] !== $user->id && $conversation['seller_id'] !== $user->id) {
                return response()->json(['html' => '<div class="text-center text-muted p-3">Access denied</div>']);
            }

            $messages = $conversation['messages'] ?? [];
            $isBuyNow = $conversation['is_buy_now'] ?? false;
            $chatType = $conversation['chat_type'] ?? 'product';
            $userRole = ($conversation['seller_id'] === $user->id) ? 'seller' : 'customer';
            $paymentReceived = $conversation['payment_received'] ?? false;
            $itemSource = $conversation['item_source'] ?? 'product_management';
            $auctionId = $conversation['auction_id'] ?? null;
            $tradeId = $conversation['trade_id'] ?? null;

            $html = '';
            
            // ✅ FIXED: Show item source badge in chat header
            $itemSourceBadge = $this->getItemSourceBadge($itemSource, $chatType, true);
            $html .= '<div class="item-source-indicator">' . $itemSourceBadge . '</div>';
            
            // ✅ NEW: Add Trade Complete button for trade proposal chats
            if ($chatType === 'trade_proposal' && $tradeId) {
                $html = $this->addTradeCompleteButton($conversationId, $tradeId, $userRole, $html);
            }
            
            // ✅ FIXED: Show "Payment Received" button for PRODUCT sellers and "Item Received" button for AUCTION winners
            if (!$paymentReceived) {
                // For PRODUCT sellers - show Payment Received button
                if ($chatType === 'product' && $userRole === 'seller' && $isBuyNow) {
                    $html .= '<div class="item-received-section">
                                <div class="alert alert-info text-center">
                                    <h5><i class="fas fa-money-bill-wave"></i> Confirm Payment Received</h5>
                                    <p>Click below once you receive the payment to complete the transaction and update stock.</p>
                                    <button class="btn btn-success btn-lg" onclick="markPaymentReceived(\'' . e($conversationId) . '\')">
                                        <i class="fas fa-check-circle"></i> I Have Received The Payment - COMPLETE TRANSACTION
                                    </button>
                                    <p class="small mt-2">This will deduct stock and mark the transaction as completed.</p>
                                </div>
                              </div>';
                }
                // For AUCTION winners (customers) - show Item Received button
                elseif ($chatType === 'auction_winner' && $userRole === 'customer' && $auctionId) {
                    $auction = Auction::find($auctionId);
                    if ($auction && $auction->winner_id === $user->id && !$auction->item_received_at) {
                        $html .= '<div class="item-received-section">
                                    <div class="alert alert-info text-center">
                                        <h5><i class="fas fa-box-open"></i> Confirm Item Received</h5>
                                        <p>Click below once you receive the item to AUTOMATICALLY release 💎' . number_format($auction->payout_amount, 0) . ' to the seller.</p>
                                        <button class="btn btn-success btn-lg" onclick="markItemReceived(\'' . e($conversationId) . '\', ' . $auctionId . ')">
                                            <i class="fas fa-check-circle"></i> I Have Received The Item - RELEASE PAYMENT
                                        </button>
                                        <p class="small mt-2">This will automatically release the escrow funds to the seller without admin approval.</p>
                                    </div>
                                  </div>';
                    }
                }
            }

            foreach ($messages as $message) {
                if (!is_array($message) || !isset($message['sender_id'])) {
                    Log::warning('Invalid message structure:', ['message' => $message]);
                    continue;
                }
                
                // Determine message class based on current user
                $isCurrentUser = $message['sender_id'] === $user->id;
                $messageClass = $isCurrentUser ? 'customer' : 'shop';
                $time = $this->formatTime($message['timestamp'] ?? time());
                
                // Special styling for system messages
                if (in_array($message['type'] ?? 'text', ['buy_now', 'payment_confirmed', 'auction_win', 'trade_proposal', 'trade_completed'])) {
                    $messageClass = 'system';
                }
                
                // Handle different message types
                if (($message['type'] ?? 'text') === 'image') {
                    if (isset($message['image_url'])) {
                        $imageUrl = $message['image_url'];
                        $messageContent = '<div class="message-image">
                                            <img src="' . e($imageUrl) . '" alt="Shared image" onclick="openImageModal(this.src)" style="max-width: 300px; border-radius: 12px; cursor: pointer;">
                                          </div>';
                        // Add caption for trade proposal images
                        if (isset($message['is_trade_proposal_image']) && $message['is_trade_proposal_image']) {
                            $messageContent = '<div class="trade-proposal-image">' . $messageContent . '</div>';
                        }
                    } else {
                        $messageContent = '<div class="message-text">📷 Image (Unable to load)</div>';
                    }
                } else {
                    $messageContent = '<div class="message-text">' . nl2br(e($message['message'] ?? 'No message content')) . '</div>';
                }
                
                $html .= '<div class="message ' . $messageClass . '">
                            ' . $messageContent . '
                            <div class="message-time">' . $time . '</div>
                          </div>';
            }

            // If no messages yet, show appropriate message
            if (empty($messages)) {
                $otherPersonId = ($conversation['customer_id'] === $user->id) ? $conversation['seller_id'] : $conversation['customer_id'];
                $otherPersonName = ($conversation['customer_id'] === $user->id) ? 
                    ($conversation['seller_name'] ?? 'Seller') : 
                    ($conversation['customer_name'] ?? 'Customer');
                
                $chatTypeText = ($conversation['chat_type'] ?? 'product') === 'auction' ? 'auction item' : 'product';
                
                $html = '<div class="text-center text-muted p-4">
                            <div class="conversation-start-info">
                                <i class="fa fa-comments fa-2x mb-3"></i>
                                <h5>Conversation with ' . e($otherPersonName) . '</h5>
                                <p>You\'re discussing: <strong>' . e($conversation['product_name'] ?? 'Item') . '</strong></p>
                                <p class="small">Start the conversation by sending a message!</p>
                            </div>
                        </div>' . $html;
            }

            return response()->json(['html' => $html]);
            
        } catch (\Exception $e) {
            Log::error('Error loading messages: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json(['html' => '<div class="text-center text-danger p-3">Error loading messages</div>']);
        }
    }

    /**
     * Send message to private storage - FIXED VERSION
     */
    public function sendMessage(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login to send messages']);
            }

            $conversationId = $request->get('conversation_id');
            $messageText = $request->get('message');
            $image = $request->file('image');

            if (!$conversationId) {
                return response()->json(['success' => false, 'message' => 'No conversation selected']);
            }

            // Get private conversations
            $privateConversations = $this->getConversationsFromStorage();
            
            if (!isset($privateConversations[$conversationId])) {
                return response()->json(['success' => false, 'message' => 'Conversation not found']);
            }

            $conversation = $privateConversations[$conversationId];
            
            // ✅ FIXED: Verify user has access to this conversation
            if (!isset($conversation['customer_id']) || !isset($conversation['seller_id'])) {
                return response()->json(['success' => false, 'message' => 'Invalid conversation data']);
            }
            
            if ($conversation['customer_id'] !== $user->id && $conversation['seller_id'] !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Access denied']);
            }

            $messageData = [
                'id' => uniqid('msg_'),
                'sender_id' => $user->id,
                'timestamp' => now()->timestamp
            ];

            // Handle image upload
            if ($image && $image->isValid()) {
                try {
                    // Validate image
                    $request->validate([
                        'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'
                    ]);

                    // Create directory in public folder
                    $directory = public_path('chat_images');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }

                    // Store image with unique name
                    $imageName = 'chat_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    
                    // Move the file to public directory
                    $image->move($directory, $imageName);
                    
                    // Generate URL - use asset() for proper URL generation
                    $imageUrl = asset('chat_images/' . $imageName);
                    
                    Log::info('Image uploaded successfully:', [
                        'filename' => $imageName,
                        'url' => $imageUrl,
                        'full_path' => $directory . '/' . $imageName
                    ]);
                    
                    $messageData['type'] = 'image';
                    $messageData['image_url'] = $imageUrl;
                    $messageData['message'] = '📷 Image';
                    
                } catch (\Exception $e) {
                    Log::error('Image upload error: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()]);
                }
            } 
            // Handle text message
            else if ($messageText && trim($messageText) !== '') {
                $messageData['type'] = 'text';
                $messageData['message'] = trim($messageText);
            } else {
                return response()->json(['success' => false, 'message' => 'No message or image provided']);
            }

            // ✅ FIXED: Ensure messages array exists
            if (!isset($privateConversations[$conversationId]['messages'])) {
                $privateConversations[$conversationId]['messages'] = [];
            }

            // Add message to private storage
            $privateConversations[$conversationId]['messages'][] = $messageData;
            $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
            
            // ✅ FIXED: Save back to JSON storage
            $this->saveConversationsToStorage($privateConversations);
            Session::put('current_conversation_id', $conversationId);

            Log::info('Message sent successfully:', [
                'conversation_id' => $conversationId,
                'sender_id' => $user->id,
                'message_type' => $messageData['type']
            ]);

            return response()->json([
                'success' => true, 
                'message_id' => $messageData['id']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error sending message: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark payment as received and deduct stock - UPDATED WITH ESCROW RELEASE
     */
    public function markPaymentReceived(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login to confirm payment']);
            }

            $conversationId = $request->get('conversation_id');
            
            $privateConversations = $this->getConversationsFromStorage();
            
            if (!isset($privateConversations[$conversationId])) {
                return response()->json(['success' => false, 'message' => 'Conversation not found']);
            }

            $conversation = $privateConversations[$conversationId];
            
            // Verify user is the seller
            if ($conversation['seller_id'] !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Only the seller can confirm payment']);
            }

            // Check if payment already received
            if ($conversation['payment_received'] ?? false) {
                return response()->json(['success' => false, 'message' => 'Payment already confirmed']);
            }

            $chatType = $conversation['chat_type'] ?? 'product';
            
            if ($chatType === 'product' || $chatType === 'auction_winner') {
                // Handle product or auction winner payment confirmation
                if ($chatType === 'product') {
                    // Get product and quantity
                    $productId = $conversation['product_id'];
                    $quantity = $conversation['buy_now_quantity'] ?? 1;
                    
                    $product = Product::find($productId);
                    if (!$product) {
                        return response()->json(['success' => false, 'message' => 'Product not found']);
                    }

                    // Check stock availability
                    if ($product->stock < $quantity) {
                        return response()->json(['success' => false, 'message' => 'Not enough stock available']);
                    }

                    // Deduct stock
                    $product->stock -= $quantity;
                    $product->save();
                } else if ($chatType === 'auction_winner') {
                    // NEW: Handle auction winner payment confirmation with escrow
                    $auctionId = $conversation['auction_id'];
                    $auction = Auction::find($auctionId);
                    
                    if (!$auction) {
                        return response()->json(['success' => false, 'message' => 'Auction not found']);
                    }
                    
                    // Check if payout is in escrow
                    if ($auction->payout_status === 'pending') {
                        // Automatically release escrow to seller
                        $auction->update([
                            'payout_status' => 'approved',
                            'payout_approved_at' => now(),
                            'payout_approved_by' => $user->id
                        ]);
                        
                        // Release diamonds to seller
                        User::where('id', $auction->user_id)->increment('diamond_balance', $auction->payout_amount);
                        
                        // Mark payout as released
                        $auction->update([
                            'payout_status' => 'released',
                            'escrow_released_at' => now()
                        ]);
                        
                        Log::info("Escrow released for auction {$auction->id}. 💎{$auction->payout_amount} sent to seller {$auction->user_id}");
                    }
                }

                // Update conversation with payment confirmation
                $privateConversations[$conversationId]['payment_received'] = true;
                $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
                
                // Add payment confirmation message
                $sellerName = $conversation['seller_name'] ?? 'Seller';
                $confirmationMessage = "✅ **{$sellerName}** has confirmed " . ($chatType === 'auction_winner' ? 'delivery!' : 'receiving the payment!') . "\n\n";
                $confirmationMessage .= "**Item:** {$conversation['product_name']}\n";
                
                if ($chatType === 'product') {
                    $confirmationMessage .= "**Quantity:** {$quantity}\n";
                    $confirmationMessage .= "**Total Amount:** ₱" . number_format($conversation['buy_now_total_price'], 2) . "\n\n";
                    $confirmationMessage .= "Stock has been updated. Thank you for your purchase!";
                } else {
                    $confirmationMessage .= "**Winning Bid:** 💎" . number_format($conversation['buy_now_total_price'], 0) . "\n\n";
                    
                    // NEW: Add escrow release information
                    if ($auction->payout_status === 'released') {
                        $confirmationMessage .= "💰 **Escrow Status:** 💎" . number_format($auction->payout_amount, 0) . " released to seller\n\n";
                    }
                    
                    $confirmationMessage .= "Auction completed successfully. Thank you!";
                }
                
                $messageData = [
                    'id' => uniqid('msg_'),
                    'sender_id' => $user->id,
                    'message' => $confirmationMessage,
                    'type' => 'payment_confirmed',
                    'timestamp' => now()->timestamp
                ];
                
                $privateConversations[$conversationId]['messages'][] = $messageData;
                
                $this->saveConversationsToStorage($privateConversations);

                return response()->json([
                    'success' => true,
                    'message' => ($chatType === 'auction_winner' ? 'Delivery confirmed successfully! Escrow released to seller.' : 'Payment confirmed successfully! Stock has been updated.')
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Invalid chat type for payment confirmation']);
            
        } catch (\Exception $e) {
            Log::error('Error confirming payment: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error confirming payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Find or create conversation
     */
    public function findOrCreateConversation(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login']);
            }

            $conversationId = $request->get('conversation_id');
            
            if ($conversationId) {
                Session::put('current_conversation_id', $conversationId);
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'No conversation ID provided']);
            
        } catch (\Exception $e) {
            Log::error('Error finding conversation: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error finding conversation']);
        }
    }

    /**
     * Clear auto message
     */
    public function clearAutoMessage(Request $request)
    {
        try {
            Session::forget('auto_message_sent');
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error clearing auto message']);
        }
    }

    /**
     * Clear chat session (UPDATED - only clears session, not JSON storage)
     */
    public function clearChatSession()
    {
        try {
            // Only clear session data, NOT the JSON file storage
            Session::forget('chat_type');
            Session::forget('chat_product_id');
            Session::forget('chat_auction_id');
            Session::forget('chat_trade_id');
            Session::forget('chat_proposal_id');
            Session::forget('chat_product_name');
            Session::forget('chat_product_image');
            Session::forget('chat_seller_id');
            Session::forget('chat_seller_name');
            Session::forget('chat_customer_name');
            Session::forget('current_conversation_id');
            Session::forget('is_buy_now');
            Session::forget('buy_now_quantity');
            Session::forget('buy_now_total_price');
            Session::forget('buy_now_product_id');
            Session::forget('item_source');
            Session::forget('force_open_chat');
            
            return response()->json(['success' => true, 'message' => 'Chat session cleared (conversations are permanently saved in storage)']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error clearing session']);
        }
    }

    /**
     * NEW: Clear ALL chat data from JSON storage (DANGEROUS - use with caution)
     */
    public function clearAllChatData()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login']);
            }

            // Only allow admins or in development
            if (app()->environment('production') && !$user->is_admin) {
                return response()->json(['success' => false, 'message' => 'Not authorized']);
            }

            // Create backup before clearing
            $this->chatStorage->createBackup();
            
            // Clear all conversations
            $this->saveConversationsToStorage([]);
            
            Log::warning('All chat data cleared by user: ' . $user->id);
            
            return response()->json(['success' => true, 'message' => 'All chat data cleared from storage']);
        } catch (\Exception $e) {
            Log::error('Error clearing all chat data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error clearing chat data']);
        }
    }

    /**
     * NEW: Get chat storage statistics
     */
    public function getChatStats()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login']);
            }

            $stats = $this->chatStorage->getStorageStats();
            return response()->json(['success' => true, 'stats' => $stats]);
            
        } catch (\Exception $e) {
            Log::error('Error getting chat stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error getting statistics']);
        }
    }

    /**
     * NEW: Check if seller has replied in chat conversation
     */
    private function checkSellerHasRepliedInChat($auctionId)
    {
        try {
            $auction = Auction::find($auctionId);
            if (!$auction) {
                return false;
            }

            // Get the conversation using our own storage method
            $conversationId = 'auction_winner_' . $auction->winner_id . '_' . $auction->user_id . '_' . $auction->id;
            
            $privateConversations = $this->getConversationsFromStorage();
            
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
     * Initialize private conversation storage
     */
    private function initializePrivateConversation($conversationId, $user, $item, $isBuyNow = false, $chatType = 'product', $itemSource = 'product_management', $quantity = 1, $totalPrice = null)
    {
        $privateConversations = $this->getConversationsFromStorage();
        
        if (!isset($privateConversations[$conversationId])) {
            $conversationData = [
                'id' => $conversationId,
                'customer_id' => $user->id,
                'seller_id' => $item->user_id,
                'customer_name' => $user->username ?? 'Customer',
                'seller_name' => $item->user->username ?? 'Seller',
                'chat_type' => $chatType,
                'is_buy_now' => $isBuyNow,
                'payment_received' => false,
                'item_source' => $itemSource,
                'messages' => [],
                'created_at' => now()->timestamp,
                'updated_at' => now()->timestamp,
            ];

            // Add item-specific data
            if ($chatType === 'product') {
                $conversationData['product_id'] = $item->id;
                $conversationData['product_name'] = $item->name;
                $conversationData['product_image'] = $item->model_file ? asset('storage/' . $item->model_file) : null;
                $conversationData['product_price'] = $item->price;
                if ($isBuyNow) {
                    $conversationData['buy_now_quantity'] = $quantity;
                    $conversationData['buy_now_total_price'] = $totalPrice ?? $item->price * $quantity;
                }
            } else {
                // Auction or auction winner
                $conversationData['auction_id'] = $item->id;
                $conversationData['product_name'] = $item->product_name;
                // Handle both array and string product_img formats
                if (is_array($item->product_img) && count($item->product_img) > 0) {
                    $conversationData['product_image'] = asset('storage/' . $item->product_img[0]);
                } else {
                    $conversationData['product_image'] = $item->product_img ? asset('storage/' . $item->product_img) : null;
                }
                $conversationData['product_price'] = $item->starting_price;
                if ($isBuyNow || $chatType === 'auction_winner') {
                    $conversationData['buy_now_quantity'] = $quantity;
                    $conversationData['buy_now_total_price'] = $totalPrice ?? $item->current_bid;
                }
            }

            $privateConversations[$conversationId] = $conversationData;
        } else {
            // Update existing conversation with new data if applicable
            if ($isBuyNow) {
                $privateConversations[$conversationId]['is_buy_now'] = true;
                $privateConversations[$conversationId]['buy_now_quantity'] = $quantity;
                $privateConversations[$conversationId]['buy_now_total_price'] = $totalPrice ?? 
                    (($chatType === 'product') ? $item->price * $quantity : $item->current_bid);
                $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
            }
            
            // Update item source if not set
            if (!isset($privateConversations[$conversationId]['item_source'])) {
                $privateConversations[$conversationId]['item_source'] = $itemSource;
            }
        }
        
        $this->saveConversationsToStorage($privateConversations);
        
        Log::info('Initialized conversation:', [
            'conversation_id' => $conversationId,
            'chat_type' => $chatType,
            'is_buy_now' => $isBuyNow,
            'item_source' => $itemSource,
            'quantity' => $quantity
        ]);
    }

    /**
     * Check if conversation is new in private storage
     */
    private function isNewPrivateConversation($conversationId)
    {
        $privateConversations = $this->getConversationsFromStorage();
        return !isset($privateConversations[$conversationId]) || empty($privateConversations[$conversationId]['messages']);
    }

    /**
     * Add message to private storage
     */
    private function addPrivateMessage($conversationId, $senderId, $message, $type = 'text')
    {
        $privateConversations = $this->getConversationsFromStorage();
        
        if (isset($privateConversations[$conversationId])) {
            $messageData = [
                'id' => uniqid('msg_'),
                'sender_id' => $senderId,
                'message' => $message,
                'type' => $type,
                'timestamp' => now()->timestamp
            ];
            
            // Ensure messages array exists
            if (!isset($privateConversations[$conversationId]['messages'])) {
                $privateConversations[$conversationId]['messages'] = [];
            }
            
            $privateConversations[$conversationId]['messages'][] = $messageData;
            $privateConversations[$conversationId]['updated_at'] = now()->timestamp;
            
            $this->saveConversationsToStorage($privateConversations);
            
            Log::info('Added private message:', [
                'conversation_id' => $conversationId,
                'sender_id' => $senderId,
                'type' => $type
            ]);
            
            return $messageData;
        }
        
        return null;
    }

    /**
     * NEW: Get item source badge HTML - UPDATED WITH BLUE PRODUCT BADGE
     */
    private function getItemSourceBadge($itemSource, $chatType = null, $large = false)
    {
        $badgeClass = $large ? 'badge-lg' : 'badge-sm';
        $icon = '';
        $text = '';
        $color = '';
        
        switch ($itemSource) {
            case 'product_management':
                $icon = 'fa-box';
                $text = 'Product';
                $color = 'primary';
                break;
            case 'auction_management':
                $icon = 'fa-gavel';
                $text = 'Auction';
                $color = 'warning';
                break;
            case 'trade_management':
                $icon = 'fa-exchange-alt';
                $text = 'Trade';
                $color = 'info';
                break;
            default:
                $icon = 'fa-question-circle';
                $text = 'Unknown';
                $color = 'secondary';
                break;
        }
        
        // Special cases for auction types
        if ($chatType === 'auction_winner') {
            $icon = 'fa-trophy';
            $text = 'Auction Win';
            $color = 'success';
        } elseif ($chatType === 'buy_now') {
            $icon = 'fa-bolt';
            $text = 'Instant Buy';
            $color = 'danger';
        }
        
        return '<span class="badge bg-' . $color . ' ' . $badgeClass . ' me-2"><i class="fa ' . $icon . ' me-1"></i>' . $text . '</span>';
    }

    /**
     * Format timestamp for display
     */
    private function formatTime($timestamp)
    {
        try {
            $time = \Carbon\Carbon::createFromTimestamp($timestamp);
            $now = \Carbon\Carbon::now();
            
            if ($time->isToday()) {
                return $time->format('h:i A');
            } elseif ($time->isYesterday()) {
                return 'Yesterday ' . $time->format('h:i A');
            } else {
                return $time->format('M j, h:i A');
            }
        } catch (\Exception $e) {
            return 'Recently';
        }
    }

    /**
     * Truncate message for preview
     */
    private function truncateMessage($message, $length = 50)
    {
        if (strlen($message) <= $length) {
            return $message;
        }
        return substr($message, 0, $length) . '...';
    }
}