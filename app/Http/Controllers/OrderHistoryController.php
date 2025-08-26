<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get orders with status filter
        $status = $request->get('status');
        
        $orders = Order::where('customer_email', $customer->email)
                     ->with(['items', 'paymentMethod', 'paymentProof'])
                     ->when($status, function ($query, $status) {
                         return $query->where('status', $status);
                     })
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);
        
        // Count orders by status for tabs
        $statusCounts = [
            'all' => Order::where('customer_email', $customer->email)->count(),
            'pending' => Order::where('customer_email', $customer->email)->where('status', 'pending')->count(),
            'paid' => Order::where('customer_email', $customer->email)->where('status', 'paid')->count(),
            'processing' => Order::where('customer_email', $customer->email)->where('status', 'processing')->count(),
            'shipped' => Order::where('customer_email', $customer->email)->where('status', 'shipped')->count(),
            'delivered' => Order::where('customer_email', $customer->email)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('customer_email', $customer->email)->where('status', 'cancelled')->count(),
        ];
        
        return view('orders.index', compact('orders', 'statusCounts', 'status'));
    }
    
    public function show($orderId)
    {
        $customer = Auth::guard('customer')->user();
        
        $order = Order::with(['items.product', 'paymentMethod', 'paymentProof'])
                     ->where('customer_email', $customer->email)
                     ->findOrFail($orderId);
        
        return view('orders.show', compact('order'));
    }
    
    public function cancel($orderId)
    {
        $customer = Auth::guard('customer')->user();
        
        $order = Order::where('customer_email', $customer->email)
                     ->where('status', 'pending')
                     ->findOrFail($orderId);
        
        $order->update(['status' => 'cancelled']);
        
        // Return stock to products
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
        
        return redirect()->route('orders.index')
                        ->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
    }
}