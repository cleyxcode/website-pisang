@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-receipt"></i> Detail Pesanan #{{ $order->order_number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Riwayat Pesanan</a></li>
                    <li class="breadcrumb-item active">Detail Pesanan</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Order Status Timeline -->
            @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Status Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item {{ $order->created_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker bg-{{ $order->created_at ? 'success' : 'light' }}"></div>
                                <div class="timeline-content">
                                    <h6>Pesanan Dibuat</h6>
                                    <p class="text-muted small mb-0">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $order->paid_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker bg-{{ $order->paid_at ? 'success' : 'warning' }}"></div>
                                <div class="timeline-content">
                                    <h6>Pembayaran Dikonfirmasi</h6>
                                    <p class="text-muted small mb-0">
                                        @if($order->paid_at)
                                            {{ $order->paid_at->format('d M Y, H:i') }}
                                        @else
                                            Menunggu konfirmasi pembayaran
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']) ? 'completed' : 'pending' }}">
                                <div class="timeline-marker bg-{{ $order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']) ? 'success' : 'light' }}"></div>
                                <div class="timeline-content">
                                    <h6>Pesanan Sedang Diproses</h6>
                                    <p class="text-muted small mb-0">
                                        @if($order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']))
                                            Pesanan sedang disiapkan
                                        @else
                                            Menunggu konfirmasi
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $order->shipped_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker bg-{{ $order->shipped_at ? 'success' : 'light' }}"></div>
                                <div class="timeline-content">
                                    <h6>Pesanan Dikirim</h6>
                                    <p class="text-muted small mb-0">
                                        @if($order->shipped_at)
                                            {{ $order->shipped_at->format('d M Y, H:i') }}
                                        @else
                                            Belum dikirim
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $order->delivered_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker bg-{{ $order->delivered_at ? 'success' : 'light' }}"></div>
                                <div class="timeline-content">
                                    <h6>Pesanan Selesai</h6>
                                    <p class="text-muted small mb-0">
                                        @if($order->delivered_at)
                                            {{ $order->delivered_at->format('d M Y, H:i') }}
                                        @else
                                            Belum selesai
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bag"></i> Produk yang Dipesan</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                        <div class="border-bottom p-3">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($item->product_image)
                                        <img src="{{ asset('storage/' . $item->product_image) }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $item->product_name }}"
                                             style="height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="height: 80px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $item->product_name }}</h6>
                                    @if($item->product_sku)
                                        <small class="text-muted">SKU: {{ $item->product_sku }}</small><br>
                                    @endif
                                    <p class="text-muted small mb-0">
                                        Rp {{ number_format($item->product_price, 0, ',', '.') }} × {{ $item->quantity }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Order Summary -->
                    <div class="p-3 bg-light">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal:</span>
                                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Ongkos Kirim:</span>
                                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-1 text-success">
                                        <span>Diskon:</span>
                                        <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->paymentMethod)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Informasi Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Metode Pembayaran</h6>
                                <p class="mb-1"><strong>{{ $order->paymentMethod->name }}</strong></p>
                                <p class="mb-1">{{ $order->paymentMethod->account_number }}</p>
                                <p class="mb-0 small text-muted">a.n. {{ $order->paymentMethod->account_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Status Pembayaran</h6>
                                @if($order->paymentProof)
                                    @if($order->paymentProof->status === 'verified')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Pembayaran Terverifikasi
                                        </span>
                                        <p class="small text-muted mb-0 mt-1">
                                            Diverifikasi pada {{ $order->paymentProof->verified_at?->format('d M Y, H:i') }}
                                        </p>
                                    @elseif($order->paymentProof->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                        </span>
                                        <p class="small text-muted mb-0 mt-1">
                                            Bukti pembayaran sedang diverifikasi (1x24 jam)
                                        </p>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Pembayaran Ditolak
                                        </span>
                                        @if($order->paymentProof->admin_notes)
                                            <p class="small text-danger mb-0 mt-1">
                                                Alasan: {{ $order->paymentProof->admin_notes }}
                                            </p>
                                        @endif
                                    @endif
                                @else
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Belum Dibayar
                                        </span>
                                        <p class="small text-muted mb-0 mt-1">
                                            Silakan lakukan pembayaran dan upload bukti transfer
                                        </p>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Sudah Dibayar
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        @if($order->paymentProof)
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Detail Transfer</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td>Jumlah:</td>
                                            <td>Rp {{ number_format($order->paymentProof->transfer_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pengirim:</td>
                                            <td>{{ $order->paymentProof->sender_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal:</td>
                                            <td>{{ $order->paymentProof->transfer_date->format('d M Y, H:i') }}</td>
                                        </tr>
                                        @if($order->paymentProof->sender_account)
                                            <tr>
                                                <td>Dari Rekening:</td>
                                                <td>{{ $order->paymentProof->sender_account }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Bukti Transfer</h6>
                                    <img src="{{ asset('storage/' . $order->paymentProof->proof_image) }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 150px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#proofModal"
                                         alt="Bukti Transfer">
                                    <br><small class="text-muted">Klik untuk memperbesar</small>
                                </div>
                            </div>
                            
                            @if($order->paymentProof->notes)
                                <div class="mt-3">
                                    <h6>Catatan Transfer</h6>
                                    <p class="mb-0">{{ $order->paymentProof->notes }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <!-- Customer Notes -->
            @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-chat-text"></i> Catatan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Order Summary Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>No. Pesanan:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal:</strong></td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Total Item:</strong></td>
                            <td>{{ $order->items->sum('quantity') }} produk</td>
                        </tr>
                        <tr>
                            <td><strong>Total Bayar:</strong></td>
                            <td class="text-primary"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Penerima</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $order->customer_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $order->customer_email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Telepon:</strong></td>
                            <td>{{ $order->customer_phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td>{{ $order->customer_address }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->status === 'pending' && !$order->has_payment_proof)
                            <a href="{{ route('checkout.payment', $order->id) }}" 
                               class="btn btn-primary">
                                <i class="bi bi-credit-card"></i> Lanjutkan Pembayaran
                            </a>
                        @endif
                        
                        @if($order->status === 'pending')
                            <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                        class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.')">
                                    <i class="bi bi-x-circle"></i> Batalkan Pesanan
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status === 'delivered')
                            <button class="btn btn-outline-warning" onclick="alert('Fitur review akan segera tersedia!')">
                                <i class="bi bi-star"></i> Beri Review
                            </button>
                        @endif
                        
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                        </a>
                        
                        <button class="btn btn-outline-info" onclick="window.print()">
                            <i class="bi bi-printer"></i> Cetak Pesanan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-headset text-primary" style="font-size: 2rem;"></i>
                    <h6 class="mt-2">Butuh Bantuan?</h6>
                    <p class="text-muted small">Hubungi customer service untuk bantuan terkait pesanan Anda</p>
                    <div class="d-grid gap-1">
                        <a href="https://wa.me/6281234567890?text=Halo, saya ingin bertanya tentang pesanan {{ $order->order_number }}" 
                           class="btn btn-success btn-sm" target="_blank">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:info@tokomakanan.com?subject=Pertanyaan Pesanan {{ $order->order_number }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope"></i> Email
                        </a>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.completed .timeline-marker {
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-item.pending .timeline-marker {
    background: #f8f9fa;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    color: #495057;
}

@media print {
    .btn, .card-header, .modal { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .container { max-width: none !important; }
}

@media (max-width: 768px) {
    .d-grid.gap-2 > * {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

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
});
</script>
@endpush
@endsection