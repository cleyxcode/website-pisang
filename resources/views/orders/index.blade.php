@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Toko Makanan')

@section('content')
<div class="orders-page">
    <!-- Header Section -->
    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <div class="header-info">
                    <h1 class="page-title">
                        <i class="bi bi-clock-history"></i>
                        Riwayat Pesanan
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item active">Riwayat Pesanan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-4">
        <!-- Status Filter Pills -->
        <div class="status-filter-card mb-4">
            <div class="filter-pills-container">
                <div class="filter-pills">
                    <a href="{{ route('orders.index') }}" 
                       class="filter-pill {{ !request('status') ? 'active' : '' }}">
                        <span class="pill-text">Semua</span>
                        <span class="pill-badge">{{ $statusCounts['all'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'pending']) }}" 
                       class="filter-pill {{ request('status') == 'pending' ? 'active' : '' }}">
                        <span class="pill-text">Menunggu Bayar</span>
                        <span class="pill-badge warning">{{ $statusCounts['pending'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'paid']) }}" 
                       class="filter-pill {{ request('status') == 'paid' ? 'active' : '' }}">
                        <span class="pill-text">Sudah Dibayar</span>
                        <span class="pill-badge info">{{ $statusCounts['paid'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'processing']) }}" 
                       class="filter-pill {{ request('status') == 'processing' ? 'active' : '' }}">
                        <span class="pill-text">Diproses</span>
                        <span class="pill-badge primary">{{ $statusCounts['processing'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'shipped']) }}" 
                       class="filter-pill {{ request('status') == 'shipped' ? 'active' : '' }}">
                        <span class="pill-text">Dikirim</span>
                        <span class="pill-badge secondary">{{ $statusCounts['shipped'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'delivered']) }}" 
                       class="filter-pill {{ request('status') == 'delivered' ? 'active' : '' }}">
                        <span class="pill-text">Selesai</span>
                        <span class="pill-badge success">{{ $statusCounts['delivered'] }}</span>
                    </a>
                    <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" 
                       class="filter-pill {{ request('status') == 'cancelled' ? 'active' : '' }}">
                        <span class="pill-text">Dibatalkan</span>
                        <span class="pill-badge danger">{{ $statusCounts['cancelled'] }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->count() > 0)
            <div class="orders-grid">
                @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">
                                    <i class="bi bi-receipt"></i>
                                    #{{ $order->order_number }}
                                </div>
                                <div class="order-date">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="order-status">
                                <span class="status-badge {{ $order->status_color }}">
                                    <i class="bi bi-circle-fill"></i>
                                    {{ $order->status_label }}
                                </span>
                                @if($order->has_payment_proof)
                                    <div class="payment-indicator" title="Sudah Upload Bukti Pembayaran">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="order-body">
                            <!-- Products Preview -->
                            <div class="products-preview">
                                @foreach($order->items->take(3) as $item)
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
                                            <div class="product-quantity">{{ $item->quantity }}x</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <div class="more-products">
                                        +{{ $order->items->count() - 3 }} lainnya
                                    </div>
                                @endif
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="total-amount">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                                <div class="total-items">
                                    {{ $order->items->sum('quantity') }} item
                                </div>
                            </div>

                            <!-- Payment Method -->
                            @if($order->paymentMethod)
                                <div class="payment-method">
                                    <i class="bi bi-credit-card"></i>
                                    {{ $order->paymentMethod->name }}
                                </div>
                            @endif

                            <!-- Progress Bar for Active Orders -->
                            @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                <div class="order-progress">
                                    <div class="progress-bar">
                                        @php
                                            $progress = match($order->status) {
                                                'paid' => 25,
                                                'processing' => 50,
                                                'shipped' => 75,
                                                'delivered' => 100,
                                                default => 0
                                            };
                                        @endphp
                                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="progress-steps">
                                        <div class="step {{ $order->paid_at ? 'completed' : '' }}">
                                            <i class="bi bi-check-circle"></i>
                                            <span>Dibayar</span>
                                        </div>
                                        <div class="step {{ $order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']) ? 'completed' : '' }}">
                                            <i class="bi bi-gear"></i>
                                            <span>Diproses</span>
                                        </div>
                                        <div class="step {{ $order->shipped_at ? 'completed' : '' }}">
                                            <i class="bi bi-truck"></i>
                                            <span>Dikirim</span>
                                        </div>
                                        <div class="step {{ $order->delivered_at ? 'completed' : '' }}">
                                            <i class="bi bi-house-check"></i>
                                            <span>Selesai</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Status Message -->
                            <div class="status-message">
                                @if($order->status === 'pending')
                                    <div class="message warning">
                                        <i class="bi bi-clock"></i>
                                        Menunggu pembayaran
                                    </div>
                                @elseif($order->status === 'paid')
                                    <div class="message success">
                                        <i class="bi bi-check-circle"></i>
                                        Pembayaran terverifikasi
                                    </div>
                                @elseif($order->paymentProof && $order->paymentProof->status === 'pending')
                                    <div class="message warning">
                                        <i class="bi bi-hourglass-split"></i>
                                        Menunggu verifikasi pembayaran
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="order-actions">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline">
                                <i class="bi bi-eye"></i>
                                Detail
                            </a>
                            
                            @if($order->status === 'pending' && !$order->has_payment_proof)
                                <a href="{{ route('checkout.payment', $order->id) }}" class="btn btn-primary">
                                    <i class="bi bi-credit-card"></i>
                                    Bayar
                                </a>
                            @endif
                            
                            @if($order->status === 'pending')
                                <form method="POST" 
                                      action="{{ route('orders.cancel', $order->id) }}" 
                                      class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="btn btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                        <i class="bi bi-x-circle"></i>
                                        <span class="btn-text">Batalkan</span>
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status === 'delivered')
                                <button class="btn btn-outline" title="Beri Ulasan">
                                    <i class="bi bi-star"></i>
                                    Review
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-bag-x"></i>
                </div>
                <div class="empty-content">
                    <h3>
                        @if(request('status'))
                            Tidak Ada Pesanan dengan Status 
                            @switch(request('status'))
                                @case('pending')
                                    "Menunggu Pembayaran"
                                    @break
                                @case('paid')
                                    "Sudah Dibayar"
                                    @break
                                @case('processing')
                                    "Sedang Diproses"
                                    @break
                                @case('shipped')
                                    "Sedang Dikirim"
                                    @break
                                @case('delivered')
                                    "Selesai"
                                    @break
                                @case('cancelled')
                                    "Dibatalkan"
                                    @break
                            @endswitch
                        @else
                            Belum Ada Pesanan
                        @endif
                    </h3>
                    <p>
                        @if(request('status'))
                            Coba lihat pesanan dengan status lain atau mulai berbelanja sekarang.
                        @else
                            Mulai berbelanja dan buat pesanan pertama Anda!
                        @endif
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="bi bi-grid"></i>
                        Mulai Belanja
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

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

.orders-page {
    background: var(--background-light);
    min-height: 100vh;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--shopee-orange) 0%, var(--shopee-red) 100%);
    color: white;
    padding: 2rem 0 1rem;
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

/* Status Filter */
.status-filter-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: var(--shadow);
}

.filter-pills-container {
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.filter-pills {
    display: flex;
    gap: 0.75rem;
    min-width: max-content;
}

.filter-pill {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--background-light);
    color: var(--text-gray);
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
}

.filter-pill:hover {
    background: var(--shopee-light-orange);
    color: var(--shopee-dark-orange);
    transform: translateY(-1px);
}

.filter-pill.active {
    background: var(--shopee-orange);
    color: white;
}

.pill-badge {
    background: rgba(255,255,255,0.8);
    color: var(--text-dark);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    min-width: 20px;
    text-align: center;
}

.filter-pill.active .pill-badge {
    background: rgba(255,255,255,0.2);
    color: white;
}

.pill-badge.warning { background-color: var(--warning); color: white; }
.pill-badge.info { background-color: var(--info); color: white; }
.pill-badge.primary { background-color: var(--primary); color: white; }
.pill-badge.secondary { background-color: var(--secondary); color: white; }
.pill-badge.success { background-color: var(--success); color: white; }
.pill-badge.danger { background-color: var(--danger); color: white; }

/* Orders Grid */
.orders-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
}

.order-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.order-header {
    padding: 1.25rem;
    background: var(--background-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-light);
}

.order-number {
    font-weight: bold;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
}

.order-date {
    color: var(--text-light);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.order-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-badge.pending { background: #fff3cd; color: #856404; }
.status-badge.paid { background: #d1ecf1; color: #0c5460; }
.status-badge.processing { background: #cce5ff; color: #004085; }
.status-badge.shipped { background: #e2e3e5; color: #383d41; }
.status-badge.delivered { background: #d4edda; color: #155724; }
.status-badge.cancelled { background: #f8d7da; color: #721c24; }

.payment-indicator {
    color: var(--success);
    font-size: 1.2rem;
}

/* Order Body */
.order-body {
    padding: 1.25rem;
}

.products-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    align-items: center;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 200px;
}

.product-image {
    width: 50px;
    height: 50px;
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
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 1.2rem;
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-name {
    font-weight: 500;
    color: var(--text-dark);
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-quantity {
    color: var(--text-light);
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.more-products {
    color: var(--text-light);
    font-size: 0.85rem;
    font-style: italic;
    padding: 0.5rem;
    background: var(--background-light);
    border-radius: 6px;
}

.order-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--background-light);
    border-radius: 8px;
}

.total-amount {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--shopee-orange);
}

.total-items {
    color: var(--text-light);
    font-size: 0.9rem;
}

.payment-method {
    color: var(--text-gray);
    font-size: 0.85rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Progress Bar */
.order-progress {
    margin: 1rem 0;
}

.progress-bar {
    height: 4px;
    background: var(--border-light);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success) 0%, var(--info) 100%);
    transition: width 0.6s ease;
}

.progress-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
}

.step {
    text-align: center;
    font-size: 0.75rem;
    color: var(--text-light);
}

.step.completed {
    color: var(--success);
}

.step i {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

/* Status Messages */
.status-message {
    margin-bottom: 1rem;
}

.message {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* Action Buttons */
.order-actions {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.25rem;
    background: var(--background-light);
    border-top: 1px solid var(--border-light);
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
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
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    color: var(--shopee-orange);
    border: 1px solid var(--shopee-orange);
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
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.empty-content h3 {
    color: var(--text-gray);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.empty-content p {
    color: var(--text-light);
    margin-bottom: 2rem;
    font-size: 1rem;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .order-header {
        padding: 1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .order-body {
        padding: 1rem;
    }
    
    .products-preview {
        flex-direction: column;
        align-items: stretch;
    }
    
    .product-item {
        min-width: auto;
        flex: none;
    }
    
    .order-summary {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .progress-steps {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .order-actions {
        padding: 1rem;
        justify-content: center;
    }
    
    .btn {
        flex: 1;
        justify-content: center;
        min-width: 100px;
    }
    
    .btn-text {
        display: none;
    }
}

@media (max-width: 576px) {
    .filter-pills {
        padding: 0 1rem;
    }
    
    .filter-pill {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .product-name {
        font-size: 0.85rem;
    }
    
    .order-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .btn-text {
        display: inline;
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto refresh untuk pesanan yang masih pending
    const hasPendingOrders = {{ $orders->where('status', 'pending')->count() > 0 ? 'true' : 'false' }};
    
    if (hasPendingOrders) {
        // Refresh setiap 5 menit untuk update status
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutes
    }
    
    // Smooth scroll untuk filter pills di mobile
    $('.filter-pills-container').on('scroll', function() {
        $(this).addClass('scrolling');
        clearTimeout($(this).data('scrollTimeout'));
        $(this).data('scrollTimeout', setTimeout(function() {
            $('.filter-pills-container').removeClass('scrolling');
        }, 150));
    });
});
</script>
@endpush
@endsection