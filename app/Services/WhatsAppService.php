<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $token;
    private $adminPhone;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fonnte.com/send';
        $this->token = config('services.fonnte.token', '3WnnMDMG1aWLsF1Um1gq');
        $this->adminPhone = config('services.fonnte.admin_phone', '081234567890'); // Ganti dengan nomor admin
    }

    /**
     * Send WhatsApp message using Fonnte API
     */
    public function sendMessage($target, $message, $options = [])
    {
        try {
            $payload = array_merge([
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Indonesia country code
            ], $options);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('WhatsApp message sent successfully', [
                    'target' => $target,
                    'response' => $result
                ]);
                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Pesan berhasil dikirim'
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'target' => $target,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'message' => 'Gagal mengirim pesan WhatsApp'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Notify admin about new payment proof
     */
    public function notifyAdminPaymentProof($order, $paymentProof)
    {
        $message = $this->formatPaymentProofMessage($order, $paymentProof);
        return $this->sendMessage($this->adminPhone, $message);
    }

    /**
     * Format payment proof notification message
     */
    private function formatPaymentProofMessage($order, $paymentProof)
    {
        $message = "🔔 *BUKTI PEMBAYARAN BARU*\n\n";
        $message .= "📋 *Detail Pesanan:*\n";
        $message .= "• No. Pesanan: {$order->order_number}\n";
        $message .= "• Customer: {$order->customer_name}\n";
        $message .= "• Email: {$order->customer_email}\n";
        $message .= "• Telepon: {$order->customer_phone}\n";
        $message .= "• Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n\n";

        $message .= "💳 *Detail Pembayaran:*\n";
        $message .= "• Metode: {$order->paymentMethod->name}\n";
        $message .= "• Jumlah Transfer: Rp " . number_format($paymentProof->transfer_amount, 0, ',', '.') . "\n";
        $message .= "• Pengirim: {$paymentProof->sender_name}\n";
        $message .= "• Tanggal: " . $paymentProof->transfer_date->format('d M Y H:i') . "\n";
        
        if ($paymentProof->sender_account) {
            $message .= "• Rekening Pengirim: {$paymentProof->sender_account}\n";
        }
        
        if ($paymentProof->notes) {
            $message .= "• Catatan: {$paymentProof->notes}\n";
        }

        $message .= "\n📦 *Produk:*\n";
        foreach ($order->items as $item) {
            $message .= "• {$item->product_name} ({$item->quantity}x) - Rp " . number_format($item->total_price, 0, ',', '.') . "\n";
        }

        $message .= "\n📍 *Alamat Pengiriman:*\n";
        $message .= $order->customer_address . "\n\n";

        $message .= "⚠️ Silakan verifikasi pembayaran melalui admin panel.\n";
        $message .= "🕐 Waktu upload: " . now()->format('d M Y H:i') . "\n\n";
        $message .= "---\n";
        $message .= "Pesan otomatis dari sistem Toko Makanan";

        return $message;
    }

    /**
     * Notify admin about new order (without payment)
     */
    public function notifyAdminNewOrder($order)
    {
        $message = "🛒 *PESANAN BARU*\n\n";
        $message .= "📋 *Detail:*\n";
        $message .= "• No. Pesanan: {$order->order_number}\n";
        $message .= "• Customer: {$order->customer_name}\n";
        $message .= "• Email: {$order->customer_email}\n";
        $message .= "• Telepon: {$order->customer_phone}\n";
        $message .= "• Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n";
        $message .= "• Status: Menunggu Pembayaran\n\n";

        $message .= "📦 *Produk:*\n";
        foreach ($order->items as $item) {
            $message .= "• {$item->product_name} ({$item->quantity}x)\n";
        }

        $message .= "\n📍 *Alamat:*\n";
        $message .= $order->customer_address . "\n\n";

        if ($order->notes) {
            $message .= "📝 *Catatan:* {$order->notes}\n\n";
        }

        $message .= "🕐 " . $order->created_at->format('d M Y H:i');

        return $this->sendMessage($this->adminPhone, $message);
    }

    /**
     * Send message to customer
     */
    public function notifyCustomer($phone, $message)
    {
        // Clean phone number (remove non-numeric characters except +)
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        
        // Add country code if not present
        if (!str_starts_with($cleanPhone, '+62') && !str_starts_with($cleanPhone, '62')) {
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            } else {
                $cleanPhone = '62' . $cleanPhone;
            }
        }
        
        return $this->sendMessage($cleanPhone, $message);
    }

    /**
     * Notify customer when payment is verified
     */
    public function notifyPaymentVerified($order)
    {
        $message = "✅ *PEMBAYARAN TERVERIFIKASI*\n\n";
        $message .= "Halo {$order->customer_name},\n\n";
        $message .= "Pembayaran untuk pesanan #{$order->order_number} telah berhasil diverifikasi.\n\n";
        $message .= "💰 Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n";
        $message .= "📦 Status: Pesanan sedang diproses\n\n";
        $message .= "Pesanan Anda akan segera diproses dan dikirim dalam 1-3 hari kerja.\n\n";
        $message .= "Terima kasih telah berbelanja di Toko Makanan! 🙏";

        return $this->notifyCustomer($order->customer_phone, $message);
    }

    /**
     * Notify customer when payment is rejected
     */
    public function notifyPaymentRejected($order, $reason = null)
    {
        $message = "❌ *PEMBAYARAN DITOLAK*\n\n";
        $message .= "Halo {$order->customer_name},\n\n";
        $message .= "Maaf, pembayaran untuk pesanan #{$order->order_number} tidak dapat diverifikasi.\n\n";
        
        if ($reason) {
            $message .= "📝 Alasan: {$reason}\n\n";
        }
        
        $message .= "Silakan hubungi customer service untuk informasi lebih lanjut atau upload ulang bukti pembayaran yang benar.\n\n";
        $message .= "📞 WhatsApp: {$this->adminPhone}\n";
        $message .= "📧 Email: info@tokomakanan.com\n\n";
        $message .= "Terima kasih atas pengertiannya.";

        return $this->notifyCustomer($order->customer_phone, $message);
    }
}