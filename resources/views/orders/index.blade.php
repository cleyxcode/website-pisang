@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-clock-history"></i> Riwayat Pesanan</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item active">Riwayat Pesanan</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-pills" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ !request('status') ? 'active' : '' }}" 
                       href="{{ route('orders.index') }}">
                        Semua 
                        <span class="badge bg-secondary ms-1">{{ $statusCounts['all'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'pending']) }}">
                        Menunggu Pembayaran 
                        <span class="badge bg-warning ms-1">{{ $statusCounts['pending'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'paid' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'paid']) }}">
                        Sudah Dibayar 
                        <span class="badge bg-info ms-1">{{ $statusCounts['paid'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'processing' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'processing']) }}">
                        Sedang Diproses 
                        <span class="badge bg-primary ms-1">{{ $statusCounts['processing'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'shipped' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'shipped']) }}">
                        Sedang Dikirim 
                        <span class="badge bg-secondary ms-1">{{ $statusCounts['shipped'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'delivered']) }}">
                        Selesai 
                        <span class="badge bg-success ms-1">{{ $statusCounts['delivered'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" 
                       href="{{ route('orders.index', ['status' => 'cancelled']) }}">
                        Dibatalkan 
                        <span class="badge bg-danger ms-1">{{ $statusCounts['cancelled'] }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <i class="bi bi-receipt"></i> 
                                    Pesanan #{{ $order->order_number }}
                                </h6>
                                <small class="text-muted">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $order->status_color }} me-2">
                                    {{ $order->status_label }}
                                </span>
                                @if($order->has_payment_proof)
                                    <i class="bi bi-check-circle-fill text-success" title="Sudah Upload Bukti Pembayaran"></i>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Order Items Preview -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="d-flex flex-wrap">
                                        @foreach($order->items->take(3) as $item)
                                            <div class="me-3 mb-2 d-flex align-items-center">
                                                @if($item->product_image)
                                                    <img src="{{ asset('storage/' . $item->product_image) }}" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted small"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <small class="fw-bold">{{ $item->product_name }}</small>
                                                    <br><small class="text-muted">{{ $item->quantity }}x</small>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <div class="align-self-center">
                                                <small class="text-muted">+{{ $order->items->count() - 3 }} produk lainnya</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="fw-bold text-primary">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">{{ $order->items->sum('quantity') }} item</small>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            @if($order->paymentMethod)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="bi bi-credit-card"></i> 
                                        {{ $order->paymentMethod->name }}
                                    </small>
                                </div>
                            @endif
                            
                            <!-- Order Status Progress (untuk status tertentu) -->
                            @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                <div class="progress mb-3" style="height: 6px;">
                                    @php
                                        $progress = match($order->status) {
                                            'paid' => 25,
                                            'processing' => 50,
                                            'shipped' => 75,
                                            'delivered' => 100,
                                            default => 0
                                        };
                                    @endphp
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="row text-center small text-muted">
                                    <div class="col-3">
                                        <i class="bi bi-check-circle {{ $order->paid_at ? 'text-success' : '' }}"></i>
                                        <br>Dibayar
                                    </div>
                                    <div class="col-3">
                                        <i class="bi bi-gear {{ $order->status === 'processing' ? 'text-success' : '' }}"></i>
                                        <br>Diproses
                                    </div>
                                    <div class="col-3">
                                        <i class="bi bi-truck {{ $order->shipped_at ? 'text-success' : '' }}"></i>
                                        <br>Dikirim
                                    </div>
                                    <div class="col-3">
                                        <i class="bi bi-house-check {{ $order->delivered_at ? 'text-success' : '' }}"></i>
                                        <br>Selesai
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    @if($order->status === 'pending')
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> 
                                            Menunggu pembayaran
                                        </small>
                                    @elseif($order->status === 'paid')
                                        <small class="text-success">
                                            <i class="bi bi-check-circle"></i> 
                                            Pembayaran terverifikasi
                                        </small>
                                    @elseif($order->paymentProof && $order->paymentProof->status === 'pending')
                                        <small class="text-warning">
                                            <i class="bi bi-hourglass-split"></i> 
                                            Menunggu verifikasi pembayaran
                                        </small>
                                    @endif
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    
                                    @if($order->status === 'pending' && !$order->has_payment_proof)
                                        <a href="{{ route('checkout.payment', $order->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-credit-card"></i> Bayar
                                        </a>
                                    @endif
                                    
                                    @if($order->status === 'pending')
                                        <form method="POST" 
                                              action="{{ route('orders.cancel', $order->id) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                <i class="bi bi-x-circle"></i> Batalkan
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($order->status === 'delivered')
                                        <button class="btn btn-outline-warning btn-sm" 
                                                title="Beri Ulasan">
                                            <i class="bi bi-star"></i> Review
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-muted">
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
            </h4>
            <p class="text-muted mb-4">
                @if(request('status'))
                    Coba lihat pesanan dengan status lain atau mulai berbelanja sekarang.
                @else
                    Mulai berbelanja dan buat pesanan pertama Anda!
                @endif
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="bi bi-grid"></i> Mulai Belanja
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
.nav-pills .nav-link {
    color: #6c757d;
    border-radius: 20px;
    margin-right: 5px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.nav-pills .nav-link.active {
    background-color: #28a745;
    color: white;
}

.nav-pills .nav-link.active .badge {
    background-color: rgba(255,255,255,0.3) !important;
}

.card {
    border: 1px solid #e9ecef;
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.progress-bar {
    transition: width 0.6s ease;
}

.btn-group .btn {
    border-radius: 4px;
    margin-left: 2px;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

@media (max-width: 768px) {
    .nav-pills {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .nav-pills .nav-item {
        flex: 0 0 auto;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin: 1px 0;
        border-radius: 4px !important;
    }
}
</style>
@endpush

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
});
</script>
@endpush
@endsection