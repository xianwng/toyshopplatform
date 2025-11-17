<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\ExchangeProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::with([
                'user:id,first_name,last_name,username,profile_picture'
            ])
            ->select([
                'id', 'user_id', 'name', 'brand', 'category', 'condition',
                'description', 'location', 'trade_preferences', 'status',
                'image', 'created_at'
            ])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('customer.CustomerTrade.trading', compact('trades'));
    }

    public function adminTradingManagement()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function pendingTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function approvedTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function rejectedTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function activeTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function completedTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.trade.trade', compact('trades'));
    }

    public function approveTrade(Request $request, Trade $trade)
    {
        try {
            DB::beginTransaction();

            $trade->approve();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trade approved successfully!',
                    'status' => $trade->status
                ]);
            }

            return redirect()->back()->with('success', 'Trade approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade approval error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error approving trade: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error approving trade: ' . $e->getMessage());
        }
    }

    public function rejectTrade(Request $request, Trade $trade)
    {
        try {
            DB::beginTransaction();

            $trade->reject();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trade rejected successfully!',
                    'status' => $trade->status
                ]);
            }

            return redirect()->back()->with('success', 'Trade rejected successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade rejection error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error rejecting trade: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error rejecting trade: ' . $e->getMessage());
        }
    }

    public function activateTrade(Request $request, Trade $trade)
    {
        try {
            if (Auth::id() !== $trade->user_id) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();

            $trade->activate();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trade activated successfully!',
                    'status' => $trade->status
                ]);
            }

            return redirect()->back()->with('success', 'Trade activated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade activation error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error activating trade: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error activating trade: ' . $e->getMessage());
        }
    }

    public function customerActivateTrade(Request $request, Trade $trade)
    {
        try {
            if (Auth::id() !== $trade->user_id) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();

            $trade->activate();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trade activated successfully!',
                    'status' => $trade->status
                ]);
            }

            return redirect()->back()->with('success', 'Trade activated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade activation error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error activating trade: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error activating trade: ' . $e->getMessage());
        }
    }

    // NEW: Complete Trade Method
    public function completeTrade(Request $request, Trade $trade)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $isTradeOwner = $trade->user_id === $user->id;
            
            // For non-owners, check if they have an accepted proposal for this trade
            $hasAcceptedProposal = false;
            if (!$isTradeOwner) {
                $hasAcceptedProposal = ExchangeProposal::where('receiver_trade_id', $trade->id)
                    ->where('sender_id', $user->id)
                    ->where('status', 'accepted')
                    ->exists();
            }

            if (!$isTradeOwner && !$hasAcceptedProposal) {
                abort(403, 'Unauthorized action. Only trade owner or proposal participant can complete trade.');
            }

            // Check if trade is active
            if (!$trade->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only active trades can be completed.'
                ], 422);
            }

            // Complete the trade
            $trade->complete();

            // Add system message to chat
            $this->addTradeCompleteMessage($trade, $user);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trade marked as completed successfully!',
                    'status' => $trade->status
                ]);
            }

            return redirect()->back()->with('success', 'Trade marked as completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade completion error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error completing trade: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error completing trade: ' . $e->getMessage());
        }
    }

    /**
     * Add trade complete message to chat
     */
    private function addTradeCompleteMessage($trade, $user)
    {
        try {
            // Instead of calling chat controller directly, we'll add the message to the chat storage
            $this->addMessageToChatStorage($trade, $user);
            
        } catch (\Exception $e) {
            Log::error('Error adding trade complete message: ' . $e->getMessage());
        }
    }

    /**
     * Add trade completion message directly to chat storage
     */
    private function addMessageToChatStorage($trade, $user)
    {
        try {
            $conversationId = 'trade_proposal_' . $trade->id . '_' . $user->id;
            $userName = $user->username ?? $user->first_name . ' ' . $user->last_name;
            
            $message = "✅ **Trade Completed!**\n\n";
            $message .= "**{$userName}** has marked the trade as completed.\n";
            $message .= "**Trade Item:** {$trade->name}\n";
            $message .= "**Status:** Trade has been ended\n\n";
            $message .= "This trade is now closed and no longer available for exchange.";

            // Load existing conversations
            $chatStorage = new \App\Services\ChatStorageService();
            $conversations = $chatStorage->loadConversations();
            
            if (isset($conversations[$conversationId])) {
                $messageData = [
                    'id' => uniqid('msg_'),
                    'sender_id' => $user->id,
                    'message' => $message,
                    'type' => 'trade_completed',
                    'timestamp' => now()->timestamp
                ];

                // Ensure messages array exists
                if (!isset($conversations[$conversationId]['messages'])) {
                    $conversations[$conversationId]['messages'] = [];
                }
                
                $conversations[$conversationId]['messages'][] = $messageData;
                $conversations[$conversationId]['updated_at'] = now()->timestamp;
                
                // Save back to storage
                $chatStorage->saveConversations($conversations);
                
                Log::info('Trade completion message added to chat:', [
                    'conversation_id' => $conversationId,
                    'trade_id' => $trade->id,
                    'user_id' => $user->id
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error adding message to chat storage: ' . $e->getMessage());
        }
    }

    public function adminViewTrade($id)
    {
        try {
            $trade = Trade::with([
                'user:id,first_name,last_name,username,email,profile_picture,contact_number'
            ])
            ->select([
                'id', 'user_id', 'name', 'brand', 'category', 'condition',
                'description', 'location', 'trade_preferences', 'status',
                'image', 'documents', 'created_at', 'updated_at'
            ])
            ->findOrFail($id);

            $trade->images_array = $this->parseImages($trade->image);
            $trade->documents_array = $this->parseDocuments($trade->documents);

            return view('frontend.trade.view_trade', compact('trade'));

        } catch (\Exception $e) {
            Log::error('Admin trade view error: ' . $e->getMessage());
            abort(404, 'Trade not found');
        }
    }

    public function create()
    {
        return view('frontend.trade.add_trade');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to upload a trade item.'
            ], 401);
        }

        $request->validate([
            'name'              => 'required|string|max:255',
            'brand'             => 'nullable|string|max:255',
            'category'          => 'required|string|max:255',
            'condition'         => 'required|string|max:255',
            'description'       => 'required|string',
            'location'          => 'required|string|max:255',
            'trade_preferences' => 'required|string|max:500',
            'images'            => 'required|array|min:1|max:10',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'documents'         => 'nullable|array',
            'documents.*'       => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name', 'brand', 'category', 'condition', 'description', 
                'location', 'trade_preferences'
            ]);

            $data['brand'] = $data['brand'] ?? 'Unknown';
            $data['user_id'] = Auth::id();

            if ($request->hasFile('images')) {
                $uploadedImages = $request->file('images');
                
                if (count($uploadedImages) > 10) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum 10 images allowed.'
                    ], 422);
                }
                
                $imagePaths = [];
                foreach ($uploadedImages as $image) {
                    if ($image->getSize() > 10 * 1024 * 1024) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Image size must be less than 10MB each.'
                        ], 422);
                    }

                    $filename = 'trade_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('models', $filename, 'public');
                    $imagePaths[] = $path;
                }
                
                $data['image'] = json_encode($imagePaths);
            }

            if ($request->hasFile('documents')) {
                $documentPaths = [];
                foreach ($request->file('documents') as $document) {
                    $docFilename = 'doc_' . time() . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
                    $docPath = $document->storeAs('trade_documents', $docFilename, 'public');
                    $documentPaths[] = $docPath;
                }
                $data['documents'] = json_encode($documentPaths);
            }

            $trade = Trade::create($data);
            Cache::forget('trades.active');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trade item submitted for admin approval. It will be visible to other users once approved and activated.',
                'trade_id' => $trade->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error uploading trade: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $trade = Trade::with([
                'user:id,first_name,last_name,username,email,profile_picture,contact_number'
            ])
            ->select([
                'id', 'user_id', 'name', 'brand', 'category', 'condition',
                'description', 'location', 'trade_preferences', 'status',
                'image', 'documents', 'created_at', 'updated_at'
            ])
            ->findOrFail($id);

            $trade->images_array = $this->parseImages($trade->image);
            $trade->documents_array = $this->parseDocuments($trade->documents);

            return view('customer.CustomerTrade.trade_details', compact('trade'));

        } catch (\Exception $e) {
            Log::error('Trade details error: ' . $e->getMessage());
            abort(404, 'Trade not found');
        }
    }

    public function myTrades()
    {
        $trades = Trade::with(['user:id,first_name,last_name,username'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('customer.CustomerTrade.my_trades', compact('trades'));
    }

    public function showProposal(Trade $trade)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to create an exchange proposal.');
        }

        try {
            if (!$trade->isActive()) {
                return redirect()->route('trading')->with('error', 'This trade is not available for exchange.');
            }
            
            $trade->load('user:id,first_name,last_name,username,profile_picture');
            $trade->images_array = $this->parseImages($trade->image);
            
            return view('customer.CustomerTrade.exchange_proposal', compact('trade'));
        } catch (\Exception $e) {
            Log::error('Exchange proposal error: ' . $e->getMessage());
            abort(404, 'Trade not found');
        }
    }

    public function storeProposal(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit an exchange proposal.'
            ], 401);
        }

        $request->validate([
            'receiver_trade_id' => 'required|exists:trades,id',
            'proposed_item_name' => 'required|string|max:255',
            'proposed_item_category' => 'required|string|max:255',
            'proposed_item_brand' => 'required|string|max:255',
            'proposed_item_location' => 'required|string|max:255',
            'proposed_item_condition' => 'required|string|max:255',
            'proposed_item_description' => 'required|string',
            'cash_amount' => 'nullable|numeric|min:0',
            'delivery_method' => 'required|string',
            'meetup_location' => 'nullable|string|max:500',
            'message' => 'nullable|string',
            'proposed_item_images' => 'required|array|min:1',
            'proposed_item_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'proposed_item_documents' => 'required|array|min:1',
            'proposed_item_documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $trade = Trade::with('user')->findOrFail($request->receiver_trade_id);
            $sender = Auth::user();
            $receiver = $trade->user;

            $imagePaths = [];
            if ($request->hasFile('proposed_item_images')) {
                foreach ($request->file('proposed_item_images') as $image) {
                    if ($image->isValid()) {
                        // FIXED: Store in models directory to match your file structure
                        $filename = 'proposal_' . time() . '_' . $sender->id . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('models', $filename, 'public');
                        $imagePaths[] = $path; // This will be 'models/filename.jpg'
                    }
                }
            }

            $documentPaths = [];
            if ($request->hasFile('proposed_item_documents')) {
                foreach ($request->file('proposed_item_documents') as $document) {
                    if ($document->isValid()) {
                        $docFilename = 'proposal_doc_' . time() . '_' . $sender->id . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
                        $docPath = $document->storeAs('trade_documents', $docFilename, 'public');
                        $documentPaths[] = $docPath;
                    }
                }
            }

            $proposal = ExchangeProposal::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'receiver_trade_id' => $request->receiver_trade_id,
                'proposed_item_name' => $request->proposed_item_name,
                'proposed_item_category' => $request->proposed_item_category,
                'proposed_item_brand' => $request->proposed_item_brand,
                'proposed_item_location' => $request->proposed_item_location,
                'proposed_item_condition' => $request->proposed_item_condition,
                'proposed_item_description' => $request->proposed_item_description,
                'proposed_item_images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
                'proposed_item_documents' => !empty($documentPaths) ? json_encode($documentPaths) : null,
                'cash_amount' => $request->cash_amount ?? 0,
                'delivery_method' => $request->delivery_method,
                'meetup_location' => $request->meetup_location,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            $chatController = new \App\Http\Controllers\CustomerController\CustomerChatController();
            $conversationId = $chatController->autoCreateTradeProposalChat(
                $trade->id, 
                $proposal->id, 
                $sender->id,
                $imagePaths
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exchange proposal submitted successfully! A chat has been started with the trade owner.',
                'proposal_id' => $proposal->id,
                'conversation_id' => $conversationId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exchange proposal store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error submitting proposal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myProposals()
    {
        $proposals = ExchangeProposal::with(['receiverTrade.user:id,first_name,last_name'])
                        ->select(['id', 'receiver_trade_id', 'proposed_item_name', 'status', 'created_at'])
                        ->where('sender_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('customer.CustomerTrade.my_proposals', compact('proposals'));
    }

    public function receivedProposals()
    {
        $proposals = ExchangeProposal::with(['sender:id,first_name,last_name,username'])
                        ->select(['id', 'sender_id', 'proposed_item_name', 'status', 'created_at'])
                        ->whereHas('receiverTrade', function($query) {
                            $query->where('user_id', Auth::id());
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('customer.CustomerTrade.received_proposals', compact('proposals'));
    }

    public function viewProposal($proposalId)
    {
        $proposal = ExchangeProposal::with([
                'sender:id,first_name,last_name,username,email',
                'receiverTrade.user:id,first_name,last_name'
            ])
            ->findOrFail($proposalId);
        
        if ($proposal->sender_id !== Auth::id() && $proposal->receiverTrade->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $proposal->images_array = $this->parseImages($proposal->proposed_item_images);
        
        return view('customer.CustomerTrade.view_proposal', compact('proposal'));
    }

    public function respondToProposal(Request $request, $proposalId)
    {
        $proposal = ExchangeProposal::findOrFail($proposalId);
        
        if ($proposal->receiverTrade->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'response_message' => 'nullable|string'
        ]);

        $proposal->update([
            'status' => $request->status,
            'response_message' => $request->response_message,
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposal ' . $request->status . ' successfully!'
        ]);
    }

    public function cancelProposal($proposalId)
    {
        $proposal = ExchangeProposal::findOrFail($proposalId);
        
        if ($proposal->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $proposal->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposal cancelled successfully!'
        ]);
    }

    public function edit(Trade $trade)
    {
        if (Auth::id() !== $trade->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $trade->images_array = $this->parseImages($trade->image);
        
        return view('frontend.trade.edit_trade', compact('trade'));
    }

    public function update(Request $request, Trade $trade)
    {
        if (Auth::id() !== $trade->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'              => 'required|string|max:255',
            'brand'             => 'nullable|string|max:255',
            'category'          => 'required|string|max:255',
            'condition'         => 'required|string|max:255',
            'description'       => 'required|string',
            'location'          => 'required|string|max:255',
            'trade_preferences' => 'required|string|max:500',
            'images'            => 'nullable|array|min:1|max:10',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'documents'         => 'nullable|array',
            'documents.*'       => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name', 'brand', 'category', 'condition', 'description', 
                'location', 'trade_preferences'
            ]);

            $data['brand'] = $data['brand'] ?? 'Unknown';
            $data['status'] = 'pending';

            if ($request->hasFile('images')) {
                $uploadedImages = $request->file('images');
                
                if (count($uploadedImages) > 10) {
                    return back()->with('error', 'Maximum 10 images allowed.');
                }
                
                if ($trade->image) {
                    $oldImages = $this->parseImages($trade->image);
                    foreach ($oldImages as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }

                $imagePaths = [];
                foreach ($uploadedImages as $image) {
                    if ($image->getSize() > 10 * 1024 * 1024) {
                        return back()->with('error', 'Image size must be less than 10MB each.');
                    }

                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('models', $filename, 'public');
                    $imagePaths[] = $path;
                }
                
                $data['image'] = json_encode($imagePaths);
            }

            if ($request->hasFile('documents')) {
                if ($trade->documents) {
                    $oldDocuments = $this->parseDocuments($trade->documents);
                    foreach ($oldDocuments as $oldDoc) {
                        if (is_string($oldDoc) && Storage::disk('public')->exists($oldDoc)) {
                            Storage::disk('public')->delete($oldDoc);
                        }
                    }
                }

                $documentPaths = [];
                foreach ($request->file('documents') as $document) {
                    $docFilename = time() . '_' . uniqid() . '_' . $document->getClientOriginalName();
                    $docPath = $document->storeAs('trade_documents', $docFilename, 'public');
                    $documentPaths[] = $docPath;
                }
                $data['documents'] = json_encode($documentPaths);
            }

            $trade->update($data);

            DB::commit();

            return redirect()->route('trading')->with('success', 'Trade item updated successfully and submitted for re-approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade update error: ' . $e->getMessage());
            
            return back()->with('error', 'Error updating trade: ' . $e->getMessage());
        }
    }

    public function destroy(Trade $trade)
    {
        if (Auth::id() !== $trade->user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            if ($trade->image) {
                $images = $this->parseImages($trade->image);
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            if ($trade->documents) {
                $documents = $this->parseDocuments($trade->documents);
                foreach ($documents as $document) {
                    if (is_string($document) && Storage::disk('public')->exists($document)) {
                        Storage::disk('public')->delete($document);
                    }
                }
            }

            $trade->delete();

            DB::commit();

            return redirect()->route('trading')->with('success', 'Trade item deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trade delete error: ' . $e->getMessage());
            
            return back()->with('error', 'Error deleting trade: ' . $e->getMessage());
        }
    }

    public function serveProposalImage($filename)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                abort(403, 'Unauthorized');
            }

            // FIXED: Look for images in models directory
            $path = 'models/' . $filename;
            
            if (!Storage::disk('public')->exists($path)) {
                abort(404);
            }

            $isAuthorized = ExchangeProposal::where(function($query) use ($user, $filename) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->where('proposed_item_images', 'like', '%' . $filename . '%')
            ->exists();

            if (!$isAuthorized) {
                abort(403, 'Unauthorized access to image');
            }

            $file = Storage::disk('public')->get($path);
            
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $type = $this->getMimeTypeFromExtension($extension);

            return response($file, 200)->header('Content-Type', $type);
            
        } catch (\Exception $e) {
            Log::error('Error serving proposal image: ' . $e->getMessage());
            abort(404);
        }
    }

    private function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'webp' => 'image/webp',
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    private function parseImages($images)
    {
        if (!$images) {
            return [];
        }

        if (is_string($images)) {
            $decoded = json_decode($images, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [$images];
        }

        if (is_array($images)) {
            return $images;
        }

        return [];
    }

    private function parseDocuments($documents)
    {
        if (!$documents) {
            return [];
        }

        if (is_string($documents)) {
            $decoded = json_decode($documents, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [$documents];
        }

        if (is_array($documents)) {
            return $documents;
        }

        return [];
    }
}