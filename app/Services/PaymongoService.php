<?php

namespace App\Services;

use Paymongo\PaymongoClient;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymongoService
{
    protected $client;

    public function __construct()
    {
        $this->client = new PaymongoClient(config('services.paymongo.secret_key'));
    }

    /**
     * Generate GCash-compatible QR code data (for scanner detection only)
     */
    public function generateGCashQRData($amount, $orderId, $merchantName = "Toyspace")
    {
        try {
            Log::info('Generating GCash QR data for scanner detection', [
                'amount' => $amount,
                'order_id' => $orderId,
                'merchant_name' => $merchantName
            ]);

            // GCash QR PH format (simplified for demo - will be detected but won't process payment)
            $qrData = [
                '00' => '01', // Payload Format Indicator
                '01' => '11', // Point of Initiation Method (11 = static QR)
                '26' => [
                    '00' => 'ph.gcash', // GUI
                    '01' => $merchantName, // Merchant Name
                    '02' => 'DEMO' . $orderId, // Merchant ID (demo prefix)
                ],
                '52' => '7999', // Merchant Category Code
                '53' => '608', // Currency (PHP)
                '54' => number_format($amount, 2), // Transaction Amount
                '58' => 'PH', // Country Code
                '62' => [
                    '01' => 'DEMO TRANSACTION', // Bill number
                    '05' => 'Toyspace Demo' // Store label
                ]
            ];

            // Convert to EMVCo QR string format
            $qrString = $this->buildEMVCoQRString($qrData);
            
            Log::info('GCash QR data generated successfully', [
                'order_id' => $orderId,
                'qr_string_length' => strlen($qrString)
            ]);

            return [
                'success' => true,
                'qr_data' => $qrString,
                'amount' => $amount,
                'order_id' => $orderId,
                'merchant_name' => $merchantName
            ];
        } catch (Exception $e) {
            Log::error('GCash QR generation failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build EMVCo QR string from data array
     */
    private function buildEMVCoQRString($data)
    {
        $qrString = '';
        
        foreach ($data as $id => $value) {
            if (is_array($value)) {
                $subString = '';
                foreach ($value as $subId => $subValue) {
                    $subString .= sprintf('%02d', $subId) . sprintf('%02d', strlen($subValue)) . $subValue;
                }
                $qrString .= sprintf('%02d', $id) . sprintf('%02d', strlen($subString)) . $subString;
            } else {
                $qrString .= sprintf('%02d', $id) . sprintf('%02d', strlen($value)) . $value;
            }
        }
        
        return $qrString;
    }

    /**
     * Generate simple demo QR data (alternative method)
     */
    public function generateDemoQRData($amount, $orderId)
    {
        try {
            // Simple demo QR content that GCash will recognize but not process
            $demoData = "GCASH|Toyspace Demo|{$amount}|ORDER{$orderId}|DEMO";
            
            return [
                'success' => true,
                'qr_data' => $demoData,
                'amount' => $amount,
                'order_id' => $orderId,
                'type' => 'demo'
            ];
        } catch (Exception $e) {
            Log::error('Demo QR generation failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create PayMongo source (for real payments)
     */
    public function createPaymentSource($amount, $type = 'gcash', $redirectSuccess, $redirectFailed)
    {
        try {
            Log::info('Creating PayMongo payment source', [
                'amount' => $amount,
                'type' => $type,
                'redirect_success' => $redirectSuccess,
                'redirect_failed' => $redirectFailed
            ]);

            $source = $this->client->sources->create([
                'amount' => $amount * 100,
                'currency' => 'PHP',
                'type' => $type,
                'redirect' => [
                    'success' => $redirectSuccess,
                    'failed' => $redirectFailed
                ]
            ]);

            // Debug: Log the entire source object to see its structure
            Log::info('PayMongo source created', [
                'source_id' => $source->id,
                'source_class' => get_class($source),
                'source_methods' => get_class_methods($source),
                'source_vars' => get_object_vars($source)
            ]);

            // Try different ways to access the checkout URL
            $checkoutUrl = null;
            
            // Method 1: Check if attributes property exists
            if (property_exists($source, 'attributes') && isset($source->attributes['redirect']['checkout_url'])) {
                $checkoutUrl = $source->attributes['redirect']['checkout_url'];
            }
            // Method 2: Check if there's a getAttributes method
            elseif (method_exists($source, 'getAttributes')) {
                $attributes = $source->getAttributes();
                $checkoutUrl = $attributes['redirect']['checkout_url'] ?? null;
            }
            // Method 3: Check if redirect is a direct property
            elseif (property_exists($source, 'redirect') && isset($source->redirect['checkout_url'])) {
                $checkoutUrl = $source->redirect['checkout_url'];
            }
            // Method 4: Try to access as array
            elseif (isset($source['attributes']['redirect']['checkout_url'])) {
                $checkoutUrl = $source['attributes']['redirect']['checkout_url'];
            }
            // Method 5: Use reflection to inspect the object
            else {
                $checkoutUrl = $this->inspectSourceObject($source);
            }

            if (!$checkoutUrl) {
                // Fallback to direct HTTP method
                Log::warning('Could not retrieve checkout URL from library, falling back to direct HTTP');
                return $this->createPaymentSourceDirect($amount, $type, $redirectSuccess, $redirectFailed);
            }

            Log::info('PayMongo source created successfully', [
                'source_id' => $source->id,
                'checkout_url' => $checkoutUrl
            ]);

            return [
                'success' => true,
                'source_id' => $source->id,
                'checkout_url' => $checkoutUrl,
                'status' => 'pending'
            ];
        } catch (Exception $e) {
            Log::error('PayMongo source creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to direct HTTP method on exception
            Log::info('Falling back to direct HTTP method due to exception');
            return $this->createPaymentSourceDirect($amount, $type, $redirectSuccess, $redirectFailed);
        }
    }

    public function retrieveSource($sourceId)
    {
        try {
            Log::info('Retrieving PayMongo source', ['source_id' => $sourceId]);

            $source = $this->client->sources->retrieve($sourceId);

            // Debug: Log the retrieved source object
            Log::info('PayMongo source retrieved', [
                'source_id' => $source->id,
                'source_class' => get_class($source),
                'source_methods' => get_class_methods($source)
            ]);

            // Try different ways to access the status
            $status = null;
            $checkoutUrl = null;

            // Method 1: Check if attributes property exists
            if (property_exists($source, 'attributes')) {
                if (isset($source->attributes['status'])) {
                    $status = $source->attributes['status'];
                }
                if (isset($source->attributes['redirect']['checkout_url'])) {
                    $checkoutUrl = $source->attributes['redirect']['checkout_url'];
                }
            }
            // Method 2: Check if there's a getAttributes method
            elseif (method_exists($source, 'getAttributes')) {
                $attributes = $source->getAttributes();
                $status = $attributes['status'] ?? null;
                $checkoutUrl = $attributes['redirect']['checkout_url'] ?? null;
            }
            // Method 3: Check if status is a direct property
            elseif (property_exists($source, 'status')) {
                $status = $source->status;
            }

            if (!$status) {
                $status = 'unknown';
            }

            Log::info('PayMongo source retrieved successfully', [
                'source_id' => $sourceId,
                'status' => $status,
                'checkout_url' => $checkoutUrl
            ]);

            return [
                'success' => true,
                'source' => $source,
                'status' => $status,
                'checkout_url' => $checkoutUrl
            ];
        } catch (Exception $e) {
            Log::error('PayMongo source retrieval failed', [
                'source_id' => $sourceId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Inspect the source object using reflection to find the checkout URL
     */
    private function inspectSourceObject($source)
    {
        try {
            Log::info('Inspecting PayMongo source object structure');
            
            // Method 1: Try to serialize and check
            $serialized = serialize($source);
            if (strpos($serialized, 'checkout_url') !== false) {
                preg_match('/checkout_url";s:\d+:"([^"]+)"/', $serialized, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            }

            // Method 2: Try to convert to array
            $sourceArray = json_decode(json_encode($source), true);
            if (isset($sourceArray['attributes']['redirect']['checkout_url'])) {
                return $sourceArray['attributes']['redirect']['checkout_url'];
            }
            if (isset($sourceArray['redirect']['checkout_url'])) {
                return $sourceArray['redirect']['checkout_url'];
            }

            // Method 3: Use reflection to get all properties
            $reflection = new \ReflectionClass($source);
            $properties = $reflection->getProperties();
            
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($source);
                
                if (is_array($value) && isset($value['redirect']['checkout_url'])) {
                    return $value['redirect']['checkout_url'];
                }
                
                // Check if value is an object that might contain the URL
                if (is_object($value)) {
                    $valueArray = json_decode(json_encode($value), true);
                    if (isset($valueArray['redirect']['checkout_url'])) {
                        return $valueArray['redirect']['checkout_url'];
                    }
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error('Error inspecting source object', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Alternative method using direct HTTP calls if the library has issues
     */
    public function createPaymentSourceDirect($amount, $type = 'gcash', $redirectSuccess, $redirectFailed)
    {
        try {
            Log::info('Creating PayMongo payment source via direct HTTP', [
                'amount' => $amount,
                'type' => $type,
                'redirect_success' => $redirectSuccess,
                'redirect_failed' => $redirectFailed
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret_key') . ':'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paymongo.com/v1/sources', [
                'data' => [
                    'attributes' => [
                        'type' => $type,
                        'amount' => $amount * 100,
                        'currency' => 'PHP',
                        'redirect' => [
                            'success' => $redirectSuccess,
                            'failed' => $redirectFailed
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $source = $data['data'];
                
                Log::info('PayMongo direct source creation successful', [
                    'source_id' => $source['id'],
                    'checkout_url' => $source['attributes']['redirect']['checkout_url']
                ]);

                return [
                    'success' => true,
                    'source_id' => $source['id'],
                    'checkout_url' => $source['attributes']['redirect']['checkout_url'],
                    'status' => $source['attributes']['status']
                ];
            } else {
                $errorMessage = 'HTTP Error: ' . $response->status() . ' - ' . $response->body();
                Log::error('PayMongo direct HTTP request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception($errorMessage);
            }
        } catch (Exception $e) {
            Log::error('PayMongo direct source creation failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code image from data (for frontend display)
     */
    public function generateQRCodeImage($qrData, $size = 250)
    {
        try {
            // This would generate an actual QR code image
            // For now, we'll return the data for frontend to handle
            return [
                'success' => true,
                'qr_data' => $qrData,
                'size' => $size,
                'message' => 'Use frontend QR library to generate image from this data'
            ];
        } catch (Exception $e) {
            Log::error('QR code image generation failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}