<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\PaymentProof;
use App\Models\Voucher;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong');
        }
        
        // Validate cart items and stock
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product || !$product->is_active || $product->stock < $item['quantity']) {
                unset($cart[$id]);
            }
        }
        
        if (empty($cart)) {
            session()->put('cart', []);
            return redirect()->route('cart.index')->with('error', 'Beberapa produk tidak tersedia atau stok habis');
        }
        
        $subtotal = $this->calculateSubtotal($cart);
        $shippingCost = 15000; // Fixed shipping cost
        
        // Get applied voucher from session
        $appliedVoucher = session()->get('applied_voucher');
        $discountAmount = 0;
        $freeShipping = false;
        
        if ($appliedVoucher) {
            $voucher = Voucher::find($appliedVoucher['id']);
            if ($voucher && $voucher->isUsable()) {
                if ($voucher->discount_type === 'free_shipping') {
                    $freeShipping = true;
                } else {
                    $discountAmount = $voucher->calculateDiscount($subtotal);
                }
            } else {
                // Remove invalid voucher
                session()->forget('applied_voucher');
                $appliedVoucher = null;
            }
        }
        
        $finalShippingCost = $freeShipping ? 0 : $shippingCost;
        $total = $subtotal + $finalShippingCost - $discountAmount;
        
        // Get customer info
        $customer = Auth::guard('customer')->user();
        
        return view('checkout.index', compact(
            'cart', 
            'subtotal', 
            'shippingCost',
            'finalShippingCost', 
            'discountAmount',
            'total', 
            'customer', 
            'appliedVoucher'
        ));
    }
    
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string|max:50'
        ]);
        
        $voucherCode = strtoupper(trim($request->voucher_code));
        
        // Find voucher
        $voucher = Voucher::where('code', $voucherCode)->first();
        
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak ditemukan'
            ]);
        }
        
        if (!$voucher->isUsable()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak dapat digunakan: ' . $voucher->status
            ]);
        }
        
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);
        
        // Check minimum amount
        if ($voucher->minimum_amount && $subtotal < $voucher->minimum_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum pembelian untuk voucher ini adalah Rp ' . number_format($voucher->minimum_amount, 0, ',', '.')
            ]);
        }
        
        // Calculate discount
        $discountAmount = 0;
        $freeShipping = false;
        
        if ($voucher->discount_type === 'free_shipping') {
            $freeShipping = true;
        } else {
            $discountAmount = $voucher->calculateDiscount($subtotal);
        }
        
        // Store voucher in session
        session()->put('applied_voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount_type' => $voucher->discount_type,
            'discount_amount' => $discountAmount,
            'free_shipping' => $freeShipping
        ]);
        
        $shippingCost = 15000;
        $finalShippingCost = $freeShipping ? 0 : $shippingCost;
        $newTotal = $subtotal + $finalShippingCost - $discountAmount;
        
        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'voucher' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_amount' => $discountAmount,
                'free_shipping' => $freeShipping,
                'formatted_discount' => $voucher->formatted_discount
            ],
            'totals' => [
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'shipping_cost' => number_format($finalShippingCost, 0, ',', '.'),
                'discount_amount' => number_format($discountAmount, 0, ',', '.'),
                'total' => number_format($newTotal, 0, ',', '.')
            ]
        ]);
    }
    
    public function removeVoucher()
    {
        session()->forget('applied_voucher');
        
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);
        $shippingCost = 15000;
        $total = $subtotal + $shippingCost;
        
        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus',
            'totals' => [
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'shipping_cost' => number_format($shippingCost, 0, ',', '.'),
                'discount_amount' => '0',
                'total' => number_format($total, 0, ',', '.')
            ]
        ]);
    }
    
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string|max:50'
        ]);
        
        $voucherCode = strtoupper(trim($request->voucher_code));
        $voucher = Voucher::where('code', $voucherCode)->first();
        
        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak ditemukan'
            ]);
        }
        
        if (!$voucher->isUsable()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak dapat digunakan: ' . $voucher->status
            ]);
        }
        
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);
        
        if ($voucher->minimum_amount && $subtotal < $voucher->minimum_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimum pembelian Rp ' . number_format($voucher->minimum_amount, 0, ',', '.')
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'voucher' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'description' => $voucher->description,
                'formatted_discount' => $voucher->formatted_discount,
                'minimum_amount' => $voucher->minimum_amount,
                'expires_at' => $voucher->expires_at ? $voucher->expires_at->format('d M Y') : null
            ]
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong');
        }
        
        DB::beginTransaction();
        
        try {
            // Validate stock again
            foreach ($cart as $id => $item) {
                $product = Product::lockForUpdate()->find($id);
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$item['name']} tidak mencukupi");
                }
            }
            
            $subtotal = $this->calculateSubtotal($cart);
            $shippingCost = 15000;
            
            // Process voucher
            $appliedVoucher = session()->get('applied_voucher');
            $discountAmount = 0;
            $finalShippingCost = $shippingCost;
            $voucherId = null;
            $voucherCode = null;
            
            if ($appliedVoucher) {
                $voucher = Voucher::find($appliedVoucher['id']);
                if ($voucher && $voucher->isUsable()) {
                    $voucherId = $voucher->id;
                    $voucherCode = $voucher->code;
                    
                    if ($voucher->discount_type === 'free_shipping') {
                        $finalShippingCost = 0;
                    } else {
                        $discountAmount = $voucher->calculateDiscount($subtotal);
                    }
                    
                    // Increment voucher usage
                    $voucher->incrementUsage();
                }
            }
            
            $total = $subtotal + $finalShippingCost - $discountAmount;
            
            // Create order
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'subtotal' => $subtotal,
                'shipping_cost' => $finalShippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'voucher_id' => $voucherId,
                'voucher_code' => $voucherCode,
                'payment_method' => 'manual',
                'status' => 'pending',
                'notes' => $request->notes,
                'has_payment_proof' => false,
            ]);
            
            // Create order items and reduce stock
            foreach ($cart as $id => $item) {
                $product = Product::find($id);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? '',
                    'product_price' => $product->price,
                    'product_image' => $product->images ? $product->images[0] : null,
                    'quantity' => $item['quantity'],
                    'total_price' => $product->price * $item['quantity'],
                ]);
                
                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
            
            // Send WhatsApp notifications
            try {
                // Load order with relationships for WhatsApp service
                $orderWithRelations = Order::with(['items'])->find($order->id);
                
                // Send notification to admin about new order
                $this->whatsAppService->notifyAdminNewOrder($orderWithRelations);
                
                // Send confirmation to customer
                $this->whatsAppService->notifyCustomerOrderConfirmation($orderWithRelations);
                
            } catch (\Exception $e) {
               
            }
            
            // Clear cart and voucher session
            session()->forget(['cart', 'applied_voucher']);
            
            return redirect()->route('checkout.payment', $order->id)
                           ->with('success', 'Pesanan berhasil dibuat! Silakan pilih metode pembayaran.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function payment($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Check if user owns this order (using session or email check)
        $customer = Auth::guard('customer')->user();
        if ($customer && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized');
        }
        
        if ($order->status !== 'pending') {
            return redirect()->route('home')->with('info', 'Pesanan ini sudah diproses');
        }
        
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return view('checkout.payment', compact('order', 'paymentMethods'));
    }
    
    public function paymentMethod(Request $request, $orderId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);
        
        $order = Order::findOrFail($orderId);
        
        // Check if user owns this order
        $customer = Auth::guard('customer')->user();
        if ($customer && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized');
        }
        
        if ($order->status !== 'pending') {
            return redirect()->route('home')->with('error', 'Pesanan sudah diproses');
        }
        
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
        
        $order->update([
            'payment_method_id' => $paymentMethod->id
        ]);
        
        return redirect()->route('checkout.payment-proof', $order->id);
    }
    
    public function paymentProof($orderId)
    {
        $order = Order::with('paymentMethod')->findOrFail($orderId);
        
        // Check if user owns this order
        $customer = Auth::guard('customer')->user();
        if ($customer && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized');
        }
        
        if (!$order->payment_method_id) {
            return redirect()->route('checkout.payment', $order->id)
                           ->with('error', 'Silakan pilih metode pembayaran terlebih dahulu');
        }
        
        if ($order->has_payment_proof) {
            return redirect()->route('checkout.success', $order->id)
                           ->with('info', 'Bukti pembayaran sudah pernah diupload');
        }
        
        return view('checkout.payment-proof', compact('order'));
    }
    
    public function storePaymentProof(Request $request, $orderId)
    {
        $request->validate([
            'transfer_amount' => 'required|numeric|min:0',
            'transfer_date' => 'required|date|before_or_equal:now',
            'sender_name' => 'required|string|max:255',
            'sender_account' => 'nullable|string|max:100',
            'proof_image' => 'required|image|max:2048|mimes:jpeg,jpg,png',
            'notes' => 'nullable|string|max:500',
        ], [
            'proof_image.required' => 'Bukti transfer wajib diupload',
            'proof_image.image' => 'File harus berupa gambar',
            'proof_image.max' => 'Ukuran file maksimal 2MB',
            'transfer_date.before_or_equal' => 'Tanggal transfer tidak boleh lebih dari hari ini',
        ]);
        
        $order = Order::with(['items', 'paymentMethod'])->findOrFail($orderId);
        
        // Check if user owns this order
        $customer = Auth::guard('customer')->user();
        if ($customer && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized');
        }
        
        if ($order->has_payment_proof) {
            return back()->with('error', 'Bukti pembayaran sudah pernah diupload');
        }
        
        if (!$order->payment_method_id) {
            return back()->with('error', 'Metode pembayaran belum dipilih');
        }
        
        DB::beginTransaction();
        
        try {
            // Store proof image
            $proofPath = $request->file('proof_image')->store('payment-proofs', 'public');
            
            // Create payment proof
            $paymentProof = PaymentProof::create([
                'order_id' => $order->id,
                'payment_method_id' => $order->payment_method_id,
                'transfer_amount' => $request->transfer_amount,
                'transfer_date' => $request->transfer_date,
                'sender_name' => $request->sender_name,
                'sender_account' => $request->sender_account,
                'proof_image' => $proofPath,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);
            
            // Update order
            $order->update([
                'has_payment_proof' => true
            ]);
            
            DB::commit();
            
            // Send WhatsApp notifications
            try {
                // Load payment proof with relationships
                $paymentProofWithRelations = PaymentProof::with(['order', 'paymentMethod'])->find($paymentProof->id);
                
                // Send notification to admin with payment proof image
                $this->whatsAppService->notifyAdminPaymentProof($order, $paymentProofWithRelations);
                
                // Send confirmation to customer that payment proof was received
                $this->whatsAppService->notifyCustomerPaymentReceived($order);
                
            } catch (\Exception $e) {
                // Log error but don't fail the transaction
               
            }
            
            return redirect()->route('checkout.success', $order->id)
                           ->with('success', 'Bukti pembayaran berhasil diupload! Pesanan Anda sedang dalam proses verifikasi.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function success($orderId)
    {
        $order = Order::with(['items', 'paymentMethod', 'paymentProof', 'voucher'])
                     ->findOrFail($orderId);
        
        // Check if user owns this order
        $customer = Auth::guard('customer')->user();
        if ($customer && $order->customer_email !== $customer->email) {
            abort(403, 'Unauthorized');
        }
        
        return view('checkout.success', compact('order'));
    }
    
    /**
     * Customer order tracking
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email'
        ]);
        
        $order = Order::with(['items', 'paymentMethod', 'paymentProof'])
                     ->where('order_number', $request->order_number)
                     ->where('customer_email', $request->email)
                     ->first();
        
        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan atau email tidak sesuai');
        }
        
        return view('orders.track', compact('order'));
    }
    
    /**
     * Resend payment proof upload page
     */
    public function resendPaymentProof(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email'
        ]);
        
        $order = Order::with('paymentMethod')
                     ->where('order_number', $request->order_number)
                     ->where('customer_email', $request->email)
                     ->first();
        
        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan atau email tidak sesuai');
        }
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan ini sudah diproses');
        }
        
        if (!$order->payment_method_id) {
            return back()->with('error', 'Metode pembayaran belum dipilih');
        }
        
        return view('checkout.payment-proof', compact('order'));
    }
    
    private function calculateSubtotal($cart)
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }
}