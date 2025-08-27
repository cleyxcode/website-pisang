@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Toko Makanan')

@section('content')
<div class="order-detail-page">
    <!-- Header Section -->
    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="page-title">
                        <i class="bi bi-receipt"></i>
                        Detail Pesanan #{{ $order->order_number }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Riwayat Pesanan</a></li>
                            <li class="breadcrumb-item active">Detail Pesanan</li>
                        </ol>
                    </nav>
                </div>
                <div class="header-status">
                    <span class="status-badge {{ $order->status_color }}">
                        <i class="bi bi-circle-fill"></i>
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-4">
                    <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Order Status Timeline -->
                @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                    <div class="timeline-card mb-4">
                        <div class="card-header">
                            <h5><i class="bi bi-truck"></i> Status Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <div class="order-timeline">
                                <div class="timeline-item {{ $order->created_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pesanan Dibuat</div>
                                        <div class="timeline-date">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ $order->paid_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pembayaran Dikonfirmasi</div>
                                        <div class="timeline-date">
                                            @if($order->paid_at)
                                                {{ $order->paid_at->format('d M Y, H:i') }}
                                            @else
                                                Menunggu konfirmasi pembayaran
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ $order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']) ? 'completed' : 'pending' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pesanan Sedang Diproses</div>
                                        <div class="timeline-date">
                                            @if($order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']))
                                                Pesanan sedang disiapkan
                                            @else
                                                Menunggu konfirmasi
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ $order->shipped_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pesanan Dikirim</div>
                                        <div class="timeline-date">
                                            @if($order->shipped_at)
                                                {{ $order->shipped_at->format('d M Y, H:i') }}
                                            @else
                                                Belum dikirim
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item {{ $order->delivered_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pesanan Selesai</div>
                                        <div class="timeline-date">
                                            @if($order->delivered_at)
                                                {{ $order->delivered_at->format('d M Y, H:i') }}
                                            @else
                                                Belum selesai
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Items -->
                <div class="products-card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-bag"></i> Produk yang Dipesan</h5>
                    </div>
                    <div class="card-body">
                        <div class="products-list">
                            @foreach($order->items as $item)
                                <div class="product-item">
                                    <div class="product-image">
                                        @if($item->product_image)
                                            <img src="{{ asset('storage/' . $item->product_image) }}" 
                                                 alt="{{ $item->product_name }}">
                                        @else
                                            <div class="image-placeholder">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-details">
                                        <div class="product-name">{{ $item->product_name }}</div>
                                        @if($item->product_sku)
                                            <div class="product-sku">SKU: {{ $item->product_sku }}</div>
                                        @endif
                                        <div class="product-price-info">
                                            Rp {{ number_format($item->product_price, 0, ',', '.') }} × {{ $item->quantity }}
                                        </div>
                                    </div>
                                    <div class="product-total">
                                        Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Ongkos Kirim:</span>
                                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="summary-row discount">
                                    <span>Diskon:</span>
                                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($order->paymentMethod)
                    <div class="payment-card mb-4">
                        <div class="card-header">
                            <h5><i class="bi bi-credit-card"></i> Informasi Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="payment-method-info">
                                        <h6>Metode Pembayaran</h6>
                                        <div class="method-details">
                                            <div class="method-name">{{ $order->paymentMethod->name }}</div>
                                            <div class="method-account">{{ $order->paymentMethod->account_number }}</div>
                                            <div class="method-owner">a.n. {{ $order->paymentMethod->account_name }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-status-info">
                                        <h6>Status Pembayaran</h6>
                                        @if($order->paymentProof)
                                            @if($order->paymentProof->status === 'verified')
                                                <div class="status-badge success">
                                                    <i class="bi bi-check-circle"></i>
                                                    Pembayaran Terverifikasi
                                                </div>
                                                <div class="status-note">
                                                    Diverifikasi pada {{ $order->paymentProof->verified_at?->format('d M Y, H:i') }}
                                                </div>
                                            @elseif($order->paymentProof->status === 'pending')
                                                <div class="status-badge warning">
                                                    <i class="bi bi-hourglass-split"></i>
                                                    Menunggu Verifikasi
                                                </div>
                                                <div class="status-note">
                                                    Bukti pembayaran sedang diverifikasi (1x24 jam)
                                                </div>
                                            @else
                                                <div class="status-badge danger">
                                                    <i class="bi bi-x-circle"></i>
                                                    Pembayaran Ditolak
                                                </div>
                                                @if($order->paymentProof->admin_notes)
                                                    <div class="status-note error">
                                                        Alasan: {{ $order->paymentProof->admin_notes }}
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            @if($order->status === 'pending')
                                                <div class="status-badge warning">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    Belum Dibayar
                                                </div>
                                                <div class="status-note">
                                                    Silakan lakukan pembayaran dan upload bukti transfer
                                                </div>
                                            @else
                                                <div class="status-badge success">
                                                    <i class="bi bi-check-circle"></i>
                                                    Sudah Dibayar
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($order->paymentProof)
                                <div class="payment-proof-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Detail Transfer</h6>
                                            <div class="transfer-details">
                                                <div class="detail-row">
                                                    <span>Jumlah:</span>
                                                    <span>Rp {{ number_format($order->paymentProof->transfer_amount, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span>Pengirim:</span>
                                                    <span>{{ $order->paymentProof->sender_name }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span>Tanggal:</span>
                                                    <span>{{ $order->paymentProof->transfer_date->format('d M Y, H:i') }}</span>
                                                </div>
                                                @if($order->paymentProof->sender_account)
                                                    <div class="detail-row">
                                                        <span>Dari Rekening:</span>
                                                        <span>{{ $order->paymentProof->sender_account }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Bukti Transfer</h6>
                                            <div class="proof-image-container">
                                                <img src="{{ asset('storage/' . $order->paymentProof->proof_image) }}" 
                                                     class="proof-image" 
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#proofModal"
                                                     alt="Bukti Transfer">
                                                <div class="image-overlay">
                                                    <i class="bi bi-zoom-in"></i>
                                                    <span>Klik untuk memperbesar</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($order->paymentProof->notes)
                                        <div class="transfer-notes">
                                            <h6>Catatan Transfer</h6>
                                            <p>{{ $order->paymentProof->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Customer Notes -->
                @if($order->notes)
                    <div class="notes-card mb-4">
                        <div class="card-header">
                            <h5><i class="bi bi-chat-text"></i> Catatan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Order Summary Card -->
                <div class="info-card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle"></i> Informasi Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="label">No. Pesanan:</span>
                                <span class="value">{{ $order->order_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Tanggal:</span>
                                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Status:</span>
                                <span class="value">
                                    <span class="status-badge {{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Total Item:</span>
                                <span class="value">{{ $order->items->sum('quantity') }} produk</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="label">Total Bayar:</span>
                                <span class="value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="customer-card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-person"></i> Informasi Penerima</h5>
                    </div>
                    <div class="card-body">
                        <div class="customer-info">
                            <div class="info-item">
                                <span class="label">Nama:</span>
                                <span class="value">{{ $order->customer_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Email:</span>
                                <span class="value">{{ $order->customer_email }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Telepon:</span>
                                <span class="value">{{ $order->customer_phone }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Alamat:</span>
                                <span class="value">{{ $order->customer_address }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="actions-card mb-4">
                    <div class="card-body">
                        <div class="action-buttons">
                            @if($order->status === 'pending' && !$order->has_payment_proof)
                                <a href="{{ route('checkout.payment', $order->id) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-credit-card"></i>
                                    Lanjutkan Pembayaran
                                </a>
                            @endif
                            
                            @if($order->status === 'pending')
                                <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="btn btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.')">
                                        <i class="bi bi-x-circle"></i>
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status === 'delivered')
                                <button class="btn btn-warning" onclick="alert('Fitur review akan segera tersedia!')">
                                    <i class="bi bi-star"></i>
                                    Beri Review
                                </button>
                            @endif
                            
                            <a href="{{ route('orders.index') }}" class="btn btn-outline">
                                <i class="bi bi-arrow-left"></i>
                                Kembali ke Riwayat
                            </a>
                            
                            <button class="btn btn-outline" onclick="window.print()">
                                <i class="bi bi-printer"></i>
                                Cetak Pesanan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="help-card">
                    <div class="card-body">
                        <div class="help-content">
                            <div class="help-icon">
                                <i class="bi bi-headset"></i>
                            </div>
                            <h6>Butuh Bantuan?</h6>
                            <p>Hubungi customer service untuk bantuan terkait pesanan Anda</p>
                            <div class="help-buttons">
                                <a href="https://wa.me/6281234567890?text=Halo, saya ingin bertanya tentang pesanan {{ $order->order_number }}" 
                                   class="btn btn-success" target="_blank">
                                    <i class="bi bi-whatsapp"></i>
                                    WhatsApp
                                </a>
                                <a href="mailto:info@tokomakanan.com?subject=Pertanyaan Pesanan {{ $order->order_number }}" 
                                   class="btn btn-outline">
                                    <i class="bi bi-envelope"></i>
                                    Email
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Proof Image Modal -->
@if($order->paymentProof)
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('storage/' . $order->paymentProof->proof_image) }}" 
                         class="img-fluid" 
                         alt="Bukti Transfer">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
:root {
    --shopee-orange: #ff5722;
    --shopee-dark-orange: #e64a19;
    --shopee-light-orange: #ffab91;
    --shopee-red: #d32f2f;
    --text-dark: #333333;
    --text-gray: #666666;
    --text-light: #999999;
    --border-light: #e0e0e0;
    --background-light: #f5f5f5;
    --white: #ffffff;
    --shadow: 0 2px 8px rgba(0,0,0,0.1);
    --shadow-hover: 0 4px 16px rgba(0,0,0,0.15);
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
    --info: #17a2b8;
    --primary: #007bff;
    --secondary: #6c757d;
}

.order-detail-page {
    background: var(--background-light);
    min-height: 100vh;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--shopee-orange) 0%, var(--shopee-red) 100%);
    color: white;
    padding: 2rem 0 1rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.page-title {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
    font-size: 0.9rem;
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.breadcrumb-item.active {
    color: white;
}

.header-status .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    background: rgba(255,255,255,0.2);
    color: white;
    backdrop-filter: blur(10px);
}

/* Card Styles */
.timeline-card,
.products-card,
.payment-card,
.notes-card,
.info-card,
.customer-card,
.actions-card,
.help-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-light);
    overflow: hidden;
}

.card-header {
    padding: 1.25rem;
    background: var(--background-light);
    border-bottom: 1px solid var(--border-light);
}

.card-header h5,
.card-header h6 {
    margin: 0;
    color: var(--text-dark);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-body {
    padding: 1.25rem;
}

/* Timeline */
.order-timeline {
    position: relative;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 2rem;
    bottom: -1.5rem;
    width: 2px;
    background: var(--border-light);
}

.timeline-item.completed:not(:last-child)::before {
    background: var(--success);
}

.timeline-dot {
    position: absolute;
    left: 0;
    top: 0.25rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: var(--border-light);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--border-light);
}

.timeline-item.completed .timeline-dot {
    background: var(--success);
    box-shadow: 0 0 0 2px var(--success);
}

.timeline-title {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.timeline-date {
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Products List */
.products-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--background-light);
    border-radius: 8px;
}

.product-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 1.5rem;
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-name {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.product-sku {
    color: var(--text-light);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.product-price-info {
    color: var(--text-gray);
    font-size: 0.9rem;
}

.product-total {
    font-weight: bold;
    color: var(--shopee-orange);
    font-size: 1.1rem;
}

/* Order Summary */
.order-summary {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--border-light);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.summary-row.discount {
    color: var(--success);
}

.summary-row.total {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--shopee-orange);
    padding-top: 0.75rem;
    border-top: 1px solid var(--border-light);
}

/* Payment Information */
.payment-method-info,
.payment-status-info {
    margin-bottom: 1.5rem;
}

.method-details {
    background: var(--background-light);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
}

.method-name {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.method-account {
    color: var(--text-gray);
    margin-bottom: 0.25rem;
}

.method-owner {
    color: var(--text-light);
    font-size: 0.9rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.status-badge.success {
    background: #d4edda;
    color: #155724;
}

.status-badge.warning {
    background: #fff3cd;
    color: #856404;
}

.status-badge.danger {
    background: #f8d7da;
    color: #721c24;
}

.status-note {
    font-size: 0.85rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.status-note.error {
    color: var(--danger);
}

/* Payment Proof */
.payment-proof-section {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-light);
}

.transfer-details {
    background: var(--background-light);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.detail-row span:first-child {
    color: var(--text-gray);
}

.detail-row span:last-child {
    color: var(--text-dark);
    font-weight: 500;
}

.proof-image-container {
    position: relative;
    display: inline-block;
    margin-top: 0.5rem;
}

.proof-image {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.proof-image:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-hover);
}

.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    padding: 0.5rem;
    border-radius: 0 0 8px 8px;
    font-size: 0.8rem;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.proof-image-container:hover .image-overlay {
    opacity: 1;
}

.transfer-notes {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

.transfer-notes p {
    margin: 0;
    color: var(--text-gray);
}

/* Sidebar Cards */
.info-list,
.customer-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-light);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item.highlight {
    background: var(--background-light);
    padding: 1rem;
    border-radius: 8px;
    border: none;
    margin-top: 0.5rem;
}

.info-item .label {
    color: var(--text-gray);
    font-size: 0.9rem;
    flex-shrink: 0;
}

.info-item .value {
    color: var(--text-dark);
    font-weight: 500;
    text-align: right;
    word-break: break-word;
}

.info-item.highlight .value {
    color: var(--shopee-orange);
    font-size: 1.1rem;
    font-weight: bold;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.btn-primary {
    background: var(--shopee-orange);
    color: white;
}

.btn-primary:hover {
    background: var(--shopee-dark-orange);
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.btn-outline {
    background: transparent;
    color: var(--shopee-orange);
    border: 2px solid var(--shopee-orange);
}

.btn-outline:hover {
    background: var(--shopee-orange);
    color: white;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.btn-warning {
    background: var(--warning);
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
}

/* Help Card */
.help-content {
    text-align: center;
}

.help-icon {
    font-size: 2.5rem;
    color: var(--shopee-orange);
    margin-bottom: 1rem;
}

.help-content h6 {
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.help-content p {
    color: var(--text-gray);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.help-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Modal */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: var(--shadow-hover);
}

.modal-header {
    background: var(--background-light);
    border-bottom: 1px solid var(--border-light);
}

.modal-title {
    color: var(--text-dark);
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-gray);
}

/* Print Styles */
@media print {
    .page-header,
    .actions-card,
    .help-card,
    .btn,
    .modal {
        display: none !important;
    }
    
    .order-detail-page {
        background: white !important;
    }
    
    .timeline-card,
    .products-card,
    .payment-card,
    .notes-card,
    .info-card,
    .customer-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
        margin-bottom: 1rem;
    }
    
    .container {
        max-width: none !important;
        padding: 0 !important;
    }
}

/* Responsive Design */
@media (max-width: 992px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .product-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
        gap: 0.75rem;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        align-self: center;
    }
    
    .product-details {
        text-align: center;
    }
    
    .product-total {
        align-self: center;
    }
    
    .summary-row {
        font-size: 0.9rem;
    }
    
    .timeline-item {
        padding-left: 2.5rem;
    }
    
    .payment-proof-section .row {
        flex-direction: column;
    }
    
    .proof-image {
        width: 100%;
        max-width: 300px;
        height: auto;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .info-item .value {
        text-align: left;
    }
    
    .help-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.3rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .order-timeline {
        font-size: 0.9rem;
    }
    
    .timeline-item {
        padding-left: 2rem;
        padding-bottom: 1rem;
    }
    
    .timeline-dot {
        width: 1rem;
        height: 1rem;
        left: 0;
    }
    
    .product-item {
        padding: 0.75rem;
    }
    
    .transfer-details,
    .method-details {
        padding: 0.75rem;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 0.25rem;
        margin-bottom: 0.75rem;
    }
    
    .detail-row span:last-child {
        font-weight: 600;
    }
    
    .btn {
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
    }
    
    .status-badge {
        padding: 0.625rem 1rem;
        font-size: 0.8rem;
    }
}

/* Status Colors */
.pending { background: #fff3cd; color: #856404; }
.paid { background: #d1ecf1; color: #0c5460; }
.processing { background: #cce5ff; color: #004085; }
.shipped { background: #e2e3e5; color: #383d41; }
.delivered { background: #d4edda; color: #155724; }
.cancelled { background: #f8d7da; color: #721c24; }

/* Animation for loading states */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.timeline-card,
.products-card,
.payment-card {
    animation: fadeIn 0.5s ease-out;
}

/* Smooth transitions */
* {
    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto refresh untuk pesanan yang masih dalam proses
    @if(in_array($order->status, ['pending', 'paid', 'processing']))
        // Refresh setiap 2 menit untuk update status
        setTimeout(function() {
            location.reload();
        }, 120000); // 2 minutes
    @endif
    
    // Smooth modal opening
    $('#proofModal').on('show.bs.modal', function () {
        $(this).find('.modal-content').css('transform', 'scale(0.8)');
    });
    
    $('#proofModal').on('shown.bs.modal', function () {
        $(this).find('.modal-content').css('transform', 'scale(1)');
    });
});
</script>
@endpush
@endsection