<?php

namespace App\Observers;

use App\Models\PaymentProof;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class PaymentProofObserver
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle the PaymentProof "updated" event.
     * Kirim notifikasi WhatsApp ketika status pembayaran berubah
     */
    public function updated(PaymentProof $paymentProof)
    {
        // // Cek jika status berubah
        // if ($paymentProof->wasChanged('status')) {
        //     $originalStatus = $paymentProof->getOriginal('status');
        //     $newStatus = $paymentProof->status;

        //     Log::info('Payment proof status changed', [
        //         'payment_proof_id' => $paymentProof->id,
        //         'order_id' => $paymentProof->order_id,
        //         'from' => $originalStatus,
        //         'to' => $newStatus
        //     ]);

        //     // Load relationships
        //     $paymentProof->load(['order.items', 'paymentMethod']);
        //     $order = $paymentProof->order;

        //     try {
        //         switch ($newStatus) {
        //             case 'verified':
        //                 // Update order status menjadi 'paid'
        //                 $order->update([
        //                     'status' => 'paid',
        //                     'paid_at' => now()
        //                 ]);

        //                 // Kirim notifikasi ke customer bahwa pembayaran terverifikasi
        //                 $this->whatsAppService->notifyPaymentVerified($order);
        //                 Log::info('Payment verified notification sent', ['order_id' => $order->id]);
        //                 break;

        //             case 'rejected':
        //                 // Kirim notifikasi ke customer bahwa pembayaran ditolak
        //                 $reason = $paymentProof->admin_notes ?: 'Bukti pembayaran tidak valid atau tidak sesuai';
        //                 $this->whatsAppService->notifyPaymentRejected($order, $reason);
        //                 Log::info('Payment rejected notification sent', ['order_id' => $order->id]);
        //                 break;
        //         }
        //     } catch (\Exception $e) {
        //         Log::error('Failed to send WhatsApp notification on status change', [
        //             'payment_proof_id' => $paymentProof->id,
        //             'order_id' => $order->id,
        //             'status' => $newStatus,
        //             'error' => $e->getMessage()
        //         ]);
        //     }
        // }
    }
}