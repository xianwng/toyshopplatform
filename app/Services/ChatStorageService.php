<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatStorageService
{
    protected $storagePath = 'chat_data/conversations.json';
    protected $backupEnabled = true;

    public function loadConversations()
    {
        try {
            if (!Storage::exists($this->storagePath)) {
                Log::info('Chat storage file does not exist, creating empty array');
                return [];
            }

            $content = Storage::get($this->storagePath);
            $conversations = json_decode($content, true) ?? [];

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error decoding chat JSON: ' . json_last_error_msg());
                return [];
            }

            Log::info('Loaded conversations from storage', ['count' => count($conversations)]);
            return $conversations;

        } catch (\Exception $e) {
            Log::error('Error loading conversations from storage: ' . $e->getMessage());
            return [];
        }
    }

    public function saveConversations($conversations)
    {
        try {
            // Create directory if it doesn't exist
            $directory = dirname($this->storagePath);
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            $jsonData = json_encode($conversations, JSON_PRETTY_PRINT);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON encode error: ' . json_last_error_msg());
            }

            Storage::put($this->storagePath, $jsonData);
            
            Log::info('Saved conversations to storage', [
                'count' => count($conversations),
                'file_size' => strlen($jsonData)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error saving conversations to storage: ' . $e->getMessage());
            return false;
        }
    }

    public function createBackup()
    {
        try {
            $conversations = $this->loadConversations();
            if (empty($conversations)) return false;

            $backupPath = 'chat_data/backups/conversations_' . date('Y-m-d_H-i-s') . '.json';
            $backupDir = dirname($backupPath);
            
            if (!Storage::exists($backupDir)) {
                Storage::makeDirectory($backupDir);
            }

            Storage::put($backupPath, json_encode($conversations, JSON_PRETTY_PRINT));
            Log::info('Created chat backup: ' . $backupPath);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Error creating chat backup: ' . $e->getMessage());
            return false;
        }
    }

    public function getStorageInfo()
    {
        try {
            $exists = Storage::exists($this->storagePath);
            $size = $exists ? Storage::size($this->storagePath) : 0;
            $conversations = $this->loadConversations();
            
            return [
                'file_exists' => $exists,
                'file_size' => $size,
                'conversations_count' => count($conversations),
                'storage_path' => Storage::path($this->storagePath)
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ADD THESE MISSING METHODS:

    /**
     * Restore conversations from the latest backup
     */
    public function restoreFromBackup()
    {
        try {
            $backupDir = 'chat_data/backups/';
            if (!Storage::exists($backupDir)) {
                return null;
            }

            $files = Storage::files($backupDir);
            $backupFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'json' && 
                       strpos($file, 'conversations_') === 0;
            });

            if (empty($backupFiles)) {
                return null;
            }

            // Sort by filename (which includes timestamp) to get the latest
            rsort($backupFiles);
            $latestBackup = $backupFiles[0];

            $content = Storage::get($latestBackup);
            $conversations = json_decode($content, true) ?? [];

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error decoding backup JSON: ' . json_last_error_msg());
                return null;
            }

            Log::info('Restored conversations from backup: ' . $latestBackup);
            return $conversations;

        } catch (\Exception $e) {
            Log::error('Error restoring from backup: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate the structure of conversations data
     */
    public function validateConversationsStructure($conversations)
    {
        if (!is_array($conversations)) {
            return false;
        }

        foreach ($conversations as $conversationId => $conversation) {
            if (!is_array($conversation)) {
                return false;
            }

            // Check for required fields
            $requiredFields = ['customer_id', 'seller_id', 'customer_name', 'seller_name', 'chat_type'];
            foreach ($requiredFields as $field) {
                if (!isset($conversation[$field])) {
                    Log::warning("Missing required field in conversation: {$field}");
                    return false;
                }
            }

            // Validate messages structure if they exist
            if (isset($conversation['messages']) && is_array($conversation['messages'])) {
                foreach ($conversation['messages'] as $message) {
                    if (!is_array($message) || !isset($message['sender_id']) || !isset($message['timestamp'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats()
    {
        $conversations = $this->loadConversations();
        $totalMessages = 0;
        $totalImages = 0;

        foreach ($conversations as $conversation) {
            $messages = $conversation['messages'] ?? [];
            $totalMessages += count($messages);
            
            foreach ($messages as $message) {
                if (($message['type'] ?? 'text') === 'image') {
                    $totalImages++;
                }
            }
        }

        return [
            'total_conversations' => count($conversations),
            'total_messages' => $totalMessages,
            'total_images' => $totalImages,
            'storage_size' => Storage::exists($this->storagePath) ? Storage::size($this->storagePath) : 0,
            'last_updated' => Storage::exists($this->storagePath) ? Storage::lastModified($this->storagePath) : null
        ];
    }

    /**
     * Clean up old backups (keep only last 7 days)
     */
    public function cleanupOldBackups($daysToKeep = 7)
    {
        try {
            $backupDir = 'chat_data/backups/';
            if (!Storage::exists($backupDir)) {
                return 0;
            }

            $files = Storage::files($backupDir);
            $backupFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'json' && 
                       strpos($file, 'conversations_') === 0;
            });

            $cutoffTime = now()->subDays($daysToKeep)->timestamp;
            $deletedCount = 0;

            foreach ($backupFiles as $file) {
                $fileTime = Storage::lastModified($file);
                if ($fileTime < $cutoffTime) {
                    Storage::delete($file);
                    $deletedCount++;
                    Log::info('Deleted old backup: ' . $file);
                }
            }

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Error cleaning up old backups: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Emergency repair - try to fix corrupted data
     */
    public function emergencyRepair()
    {
        try {
            $conversations = $this->loadConversations();
            $repairedCount = 0;

            foreach ($conversations as $conversationId => &$conversation) {
                // Ensure required fields exist
                if (!isset($conversation['customer_id'])) {
                    $conversation['customer_id'] = 0;
                    $repairedCount++;
                }
                if (!isset($conversation['seller_id'])) {
                    $conversation['seller_id'] = 0;
                    $repairedCount++;
                }
                if (!isset($conversation['updated_at'])) {
                    $conversation['updated_at'] = time();
                    $repairedCount++;
                }

                // Ensure messages is an array
                if (!isset($conversation['messages']) || !is_array($conversation['messages'])) {
                    $conversation['messages'] = [];
                    $repairedCount++;
                }

                // Clean up invalid messages
                $validMessages = [];
                foreach ($conversation['messages'] as $message) {
                    if (is_array($message) && isset($message['sender_id']) && isset($message['timestamp'])) {
                        $validMessages[] = $message;
                    } else {
                        $repairedCount++;
                    }
                }
                $conversation['messages'] = $validMessages;
            }

            if ($repairedCount > 0) {
                $this->saveConversations($conversations);
                Log::warning("Emergency repair performed. Fixed {$repairedCount} issues.");
            }

            return $repairedCount;

        } catch (\Exception $e) {
            Log::error('Error during emergency repair: ' . $e->getMessage());
            return 0;
        }
    }
}