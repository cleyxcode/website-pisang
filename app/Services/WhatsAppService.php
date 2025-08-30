<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private $token;
    private $adminPhone;
    private $apiUrl;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->adminPhone = config('services.fonnte.admin_phone');
        $this->apiUrl = config('services.fonnte.api_url');
    }

    /**
     * Send WhatsApp message using Fonnte API
     */
    private function sendMessage(array $data)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->token
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if (curl_errno($curl)) {
                $error = curl_error($curl);
                curl_close($curl);
                throw new \Exception("cURL Error: " . $error);
            }
            
            curl_close($curl);

            Log::info('WhatsApp API Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'data' => $data
            ]);

            $responseData = json_decode($response, true);
            
            if ($httpCode !== 200) {
                throw new \Exception("API Error: HTTP {$httpCode} - " . ($responseData['message'] ?? 'Unknown error'));
            }

            return $responseData;

        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Send message with media file
     */
    private function sendMessageWithMedia(string $target, string $message, string $filePath = null, string $fileName = null)
    {
        $data = [
            'target' => $this->formatPhoneNumber($target),
            'message' => $message
        ];

        if ($filePath && file_exists($filePath)) {
            $data['file'] = new \CURLFile($filePath, '', $fileName ?? basename($filePath));
        }

        return $this->sendMessage($data);
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with '0', replace with '62'
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with '62', add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Notify admin about new order
     */
    public function notifyAdminNewOrder(Order $order)
    {
        try {
            $message = $this->buildNewOrderMessage($order);
            
            $data = [
                'target' => $this->formatPhoneNumber($this->adminPhone),
                'message' => $message
            ];

            return $this->sendMessage($data);

        } catch (\Exception $e) {
            Log::error('Failed to notify admin about new order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify customer about order confirmation
     */
    public function notifyCustomerOrderConfirmation(Order $order)
    {
        try {
            $message = $this->buildOrderConfirmationMessage($order);
            
            $data = [
                'target' => $this->formatPhoneNumber($order->customer_phone),
                'message' => $message
            ];

            return $this->sendMessage($data);

        } catch (\Exception $e) {
            Log::error('Failed to notify customer about order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify admin about payment proof with image
     */
    public function notifyAdminPaymentProof(Order $order, PaymentProof $paymentProof)
    {
        try {
            $message = $this->buildPaymentProofMessage($order, $paymentProof);
            
            // Get the full path to the payment proof image
            $imagePath = storage_path('app/public/' . $paymentProof->proof_image);
            
            if (file_exists($imagePath)) {
                // Send message with payment proof image
                return $this->sendMessageWithMedia(
                    $this->adminPhone,
                    $message,
                    $imagePath,
                    'bukti_pembayaran_' . $order->order_number . '.jpg'
                );
            } else {
                // Send message without image if file not found
                $data = [
                    'target' => $this->formatPhoneNumber($this->adminPhone),
                    'message' => $message . "\n\n⚠️ Gambar bukti pembayaran tidak ditemukan"
                ];
                
                return $this->sendMessage($data);
            }

        } catch (\Exception $e) {
            Log::error('Failed to notify admin about payment proof', [
                'order_id' => $order->id,
                'payment_proof_id' => $paymentProof->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify customer about payment confirmation
     */
    public function notifyCustomerPaymentReceived(Order $order)
    {
        try {
            $message = $this->buildPaymentReceivedMessage($order);
            
            $data = [
                'target' => $this->formatPhoneNumber($order->customer_phone),
                'message' => $message
            ];

            return $this->sendMessage($data);

        } catch (\Exception $e) {
            Log::error('Failed to notify customer about payment received', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Notify customer about order status update
     */
    public function notifyCustomerOrderStatus(Order $order, string $oldStatus)
    {
        try {
            $message = $this->buildOrderStatusMessage($order, $oldStatus);
            
            $data = [
                'target' => $this->formatPhoneNumber($order->customer_phone),
                'message' => $message
            ];

            return $this->sendMessage($data);

        } catch (\Exception $e) {
            Log::error('Failed to notify customer about order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build new order message for admin
     */
    private function buildNewOrderMessage(Order $order): string
    {
        $items = $order->items->map(function ($item) {
            return "• {$item->product_name} x{$item->quantity} = Rp " . number_format($item->total_price, 0, ',', '.');
        })->implode("\n");

        return "🛒 *PESANAN BARU*\n\n" .
               "📋 *Detail Pesanan:*\n" .
               "No. Pesanan: {$order->order_number}\n" .
               "Tanggal: " . $order->created_at->format('d M Y H:i') . "\n\n" .
               "👤 *Data Customer:*\n" .
               "Nama: {$order->customer_name}\n" .
               "Email: {$order->customer_email}\n" .
               "No. HP: {$order->customer_phone}\n" .
               "Alamat: {$order->customer_address}\n\n" .
               "🛍️ *Produk:*\n{$items}\n\n" .
               "💰 *Ringkasan:*\n" .
               "Subtotal: Rp " . number_format($order->subtotal, 0, ',', '.') . "\n" .
               "Ongkir: Rp " . number_format($order->shipping_cost, 0, ',', '.') . "\n" .
               ($order->discount_amount > 0 ? "Diskon: -Rp " . number_format($order->discount_amount, 0, ',', '.') . "\n" : "") .
               "Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n" .
               ($order->notes ? "📝 *Catatan:* {$order->notes}\n\n" : "") .
               "Status: *{$order->status_label}*\n\n" .
               "Silakan cek dashboard admin untuk detail lebih lanjut.";
    }

    /**
     * Build order confirmation message for customer
     */
    private function buildOrderConfirmationMessage(Order $order): string
    {
        $items = $order->items->map(function ($item) {
            return "• {$item->product_name} x{$item->quantity}";
        })->implode("\n");

        return "✅ *PESANAN BERHASIL DIBUAT*\n\n" .
               "Halo {$order->customer_name},\n\n" .
               "Terima kasih telah berbelanja di toko kami! 🛍️\n\n" .
               "📋 *Detail Pesanan:*\n" .
               "No. Pesanan: *{$order->order_number}*\n" .
               "Tanggal: " . $order->created_at->format('d M Y H:i') . "\n\n" .
               "🛍️ *Produk yang dipesan:*\n{$items}\n\n" .
               "💰 *Total Pembayaran:*\n" .
               "Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n" .
               "📱 Untuk melanjutkan pembayaran, silakan klik link berikut:\n" .
               route('checkout.payment', $order->id) . "\n\n" .
               "📞 Jika ada pertanyaan, hubungi kami di nomor ini.\n\n" .
               "Terima kasih! 🙏";
    }

    /**
     * Build payment proof message for admin
     */
    private function buildPaymentProofMessage(Order $order, PaymentProof $paymentProof): string
    {
        return "💳 *BUKTI PEMBAYARAN BARU*\n\n" .
               "📋 *Detail Pesanan:*\n" .
               "No. Pesanan: {$order->order_number}\n" .
               "Customer: {$order->customer_name}\n" .
               "Total Pesanan: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n" .
               "💰 *Detail Pembayaran:*\n" .
               "Metode: {$paymentProof->paymentMethod->name}\n" .
               "Jumlah Transfer: Rp " . number_format($paymentProof->transfer_amount, 0, ',', '.') . "\n" .
               "Tanggal Transfer: " . $paymentProof->transfer_date->format('d M Y H:i') . "\n" .
               "Pengirim: {$paymentProof->sender_name}\n" .
               ($paymentProof->sender_account ? "Rekening Pengirim: {$paymentProof->sender_account}\n" : "") .
               ($paymentProof->notes ? "Catatan: {$paymentProof->notes}\n" : "") . "\n" .
               "✅ Silakan verifikasi pembayaran ini di dashboard admin.";
    }

    /**
     * Build payment received message for customer
     */
    private function buildPaymentReceivedMessage(Order $order): string
    {
        return "✅ *PEMBAYARAN DIKONFIRMASI*\n\n" .
               "Halo {$order->customer_name},\n\n" .
               "Pembayaran untuk pesanan *{$order->order_number}* telah kami terima dan dikonfirmasi! 🎉\n\n" .
               "📋 *Status Pesanan:*\n" .
               "Status: *{$order->status_label}*\n" .
               "Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n" .
               "📦 Pesanan Anda sedang diproses dan akan segera dikirim.\n\n" .
               "📞 Jika ada pertanyaan, hubungi kami di nomor ini.\n\n" .
               "Terima kasih telah berbelanja! 🙏";
    }

    /**
     * Build order status message for customer
     */
    private function buildOrderStatusMessage(Order $order, string $oldStatus): string
    {
        $statusMessages = [
            'confirmed' => "✅ Pesanan Anda telah dikonfirmasi dan sedang diproses.",
            'processing' => "📦 Pesanan Anda sedang disiapkan untuk pengiriman.",
            'shipped' => "🚚 Pesanan Anda telah dikirim dan dalam perjalanan.",
            'delivered' => "🎉 Pesanan Anda telah sampai tujuan. Terima kasih!",
            'cancelled' => "❌ Pesanan Anda telah dibatalkan."
        ];

        $statusMessage = $statusMessages[$order->status] ?? "Status pesanan Anda telah diupdate.";

        return "📋 *UPDATE PESANAN*\n\n" .
               "Halo {$order->customer_name},\n\n" .
               "Ada update untuk pesanan *{$order->order_number}*:\n\n" .
               "Status: *{$order->status_label}*\n\n" .
               $statusMessage . "\n\n" .
               "📞 Jika ada pertanyaan, hubungi kami di nomor ini.\n\n" .
               "Terima kasih! 🙏";
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkMessages(array $messages, int $delay = 2)
    {
        try {
            $data = json_encode(array_map(function ($message, $index) use ($delay) {
                return [
                    'target' => $this->formatPhoneNumber($message['target']),
                    'message' => $message['message'],
                    'delay' => $index * $delay // Progressive delay
                ];
            }, $messages, array_keys($messages)));

            $requestData = [
                'data' => $data
            ];

            return $this->sendMessage($requestData);

        } catch (\Exception $e) {
            Log::error('Failed to send bulk messages', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);
            throw $e;
        }
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(string $phoneNumber = null): bool
    {
        try {
            $target = $phoneNumber ?? $this->adminPhone;
            
            $data = [
                'target' => $this->formatPhoneNumber($target),
                'message' => '🧪 *TEST MESSAGE*\n\nIni adalah pesan test dari sistem WhatsApp.\n\nJika Anda menerima pesan ini, berarti koneksi berhasil! ✅'
            ];

            $response = $this->sendMessage($data);
            
            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp connection test failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber ?? $this->adminPhone
            ]);
            return false;
        }
    }
}