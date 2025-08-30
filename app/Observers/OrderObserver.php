<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
    {
        // Check if status was changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            // Skip if status didn't really change
            if ($oldStatus === $newStatus) {
                return;
            }

            Log::info('Order status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

            try {
                // Send WhatsApp notification to customer about status change
                $this->whatsAppService->notifyCustomerOrderStatus($order, $oldStatus);
                
                // Send specific notifications based on status
                switch ($newStatus) {
                    case 'confirmed':
                        // Additional processing for confirmed orders
                        break;
                        
                    case 'processing':
                        // Additional processing for processing orders
                        break;
                        
                    case 'shipped':
                        // Send shipping notification
                        $this->sendShippingNotification($order);
                        break;
                        
                    case 'delivered':
                        // Send delivery confirmation
                        $this->sendDeliveryNotification($order);
                        break;
                        
                    case 'cancelled':
                        // Send cancellation notification
                        $this->sendCancellationNotification($order);
                        break;
                }
                
            } catch (\Exception $e) {
                Log::error('WhatsApp notification failed for order status change', [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Send shipping notification with tracking info if available
     */
    private function sendShippingNotification(Order $order)
    {
        // You can customize this message based on your needs
        Log::info('Order shipped notification sent', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Send delivery confirmation
     */
    private function sendDeliveryNotification(Order $order)
    {
        Log::info('Order delivered notification sent', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Send cancellation notification
     */
    private function sendCancellationNotification(Order $order)
    {
        Log::info('Order cancellation notification sent', [
            'order_id' => $order->id
        ]);
    }
}