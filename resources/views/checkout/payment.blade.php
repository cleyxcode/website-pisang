@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-credit-card"></i> Pilih Metode Pembayaran</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">Checkout</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Payment Methods -->
        <div class="col-md-8">
            <!-- Order Info -->
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>No. Pesanan:</strong> {{ $order->order_number }}<br>
                        <strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Nama:</strong> {{ $order->customer_name }}<br>
                        <strong>Email:</strong> {{ $order->customer_email }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-wallet2"></i> Pilih Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if($paymentMethods->count() > 0)
                        <form method="POST" action="{{ route('checkout.payment-method', $order->id) }}" id="paymentForm">
                            @csrf
                            <div class="row">
                                @foreach($paymentMethods as $method)
                                    <div class="col-md-6 mb-3">
                                        <div class="payment-method-card">
                                            <input type="radio" 
                                                   class="btn-check" 
                                                   name="payment_method_id" 
                                                   id="method_{{ $method->id }}" 
                                                   value="{{ $method->id }}"
                                                   required>
                                            <label class="btn btn-outline-primary w-100 h-100" 
                                                   for="method_{{ $method->id }}">
                                                <div class="payment-method-content p-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        @if($method->icon)
                                                            <img src="{{ asset('storage/' . $method->icon) }}" 
                                                                 class="me-3" 
                                                                 style="width: 40px; height: 40px; object-fit: contain;"
                                                                 alt="{{ $method->name }}">
                                                        @else
                                                            <i class="bi {{ $method->type === 'bank' ? 'bi-bank' : 'bi-wallet2' }} me-3" 
                                                               style="font-size: 24px;"></i>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-1">{{ $method->name }}</h6>
                                                            <span class="badge bg-{{ $method->type === 'bank' ? 'info' : 'success' }}">
                                                                {{ $method->type_label }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-start">
                                                        <small class="text-muted">
                                                            <strong>No. Rekening:</strong><br>
                                                            {{ $method->formatted_account_number }}<br>
                                                            <strong>a.n:</strong> {{ $method->account_name }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('checkout.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="selectBtn">
                                    <i class="bi bi-arrow-right"></i> Lanjutkan
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Tidak Ada Metode Pembayaran</h5>
                            <p class="text-muted">Saat ini tidak ada metode pembayaran yang tersedia. Silakan hubungi admin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span>Diskon</span>
                            <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </div>

                    <!-- Items Summary -->
                    <h6>Produk ({{ $order->items->sum('quantity') }} item)</h6>
                    <div class="small">
                        @foreach($order->items as $item)
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-truncate me-2">{{ $item->product_name }} ({{ $item->quantity }}x)</span>
                                <span>Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle text-info"></i> Informasi Pembayaran</h6>
                    <ul class="small text-muted mb-0">
                        <li>Transfer sesuai jumlah total yang tertera</li>
                        <li>Upload bukti transfer pada langkah berikutnya</li>
                        <li>Pesanan akan diproses setelah pembayaran terverifikasi</li>
                        <li>Pembayaran akan dikonfirmasi dalam 1x24 jam</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.payment-method-card {
    height: 100%;
}

.payment-method-content {
    min-height: 120px;
    text-align: left;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.btn-check:checked + .btn-outline-primary .badge {
    background-color: rgba(255,255,255,0.2) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#paymentForm').on('submit', function() {
        $('#selectBtn').prop('disabled', true)
                       .html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...');
    });
});
</script>
@endpush
@endsection