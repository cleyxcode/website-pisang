@extends('layouts.app')

@section('title', 'Pesanan Berhasil - Toko Makanan')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Success Header -->
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h2 class="text-success">Pesanan Berhasil Dibuat!</h2>
                <p class="text-muted">Terima kasih atas pesanan Anda. Berikut adalah detail pesanan Anda:</p>
            </div>

            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Pesanan</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>No. Pesanan:</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @if($order->voucher_code)
                                    <tr>
                                        <td><strong>Voucher:</strong></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $order->voucher_code }}</span>
                                            @if($order->voucher)
                                                <br><small class="text-muted">{{ $order->voucher->name }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td><strong class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Penerima</h6>
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
                </div>
            </div>

            <!-- Voucher Savings Info -->
            @if($order->discount_amount > 0 || $order->shipping_cost == 0)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Penghematan dengan Voucher</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @if($order->discount_amount > 0)
                                <div class="col-md-6">
                                    <i class="bi bi-percent text-success" style="font-size: 2rem;"></i>
                                    <h5 class="text-success">Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</h5>
                                    <p class="text-muted small mb-0">Diskon Produk</p>
                                </div>
                            @endif
                            @if($order->shipping_cost == 0 && $order->voucher && $order->voucher->discount_type === 'free_shipping')
                                <div class="col-md-6">
                                    <i class="bi bi-truck text-info" style="font-size: 2rem;"></i>
                                    <h5 class="text-info">GRATIS</h5>
                                    <p class="text-muted small mb-0">Ongkos Kirim</p>
                                </div>
                            @endif
                        </div>
                        @php
                            $totalSavings = $order->discount_amount + ($order->shipping_cost == 0 ? 15000 : 0);
                        @endphp
                        @if($totalSavings > 0)
                            <hr>
                            <div class="text-center">
                                <strong class="text-success">Total Penghematan: Rp {{ number_format($totalSavings, 0, ',', '.') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Payment Status -->
            @if($order->paymentProof)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Status Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        @if($order->status === 'processing')
                            <div class="alert alert-success">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="alert-heading"><i class="bi bi-check-circle"></i> Pembayaran Terverifikasi</h6>
                                        <p class="mb-0">
                                            Pembayaran Anda telah berhasil diverifikasi dan pesanan sedang dalam proses pengerjaan. 
                                            Kami akan segera memproses pesanan Anda.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span class="badge bg-primary fs-6 p-2">
                                            <i class="bi bi-cog-6-tooth"></i> Sedang Diproses
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'paid')
                            <div class="alert alert-info">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="alert-heading"><i class="bi bi-check-circle"></i> Pembayaran Terverifikasi</h6>
                                        <p class="mb-0">
                                            Pembayaran Anda telah berhasil diverifikasi. 
                                            Pesanan akan segera diproses oleh tim kami.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span class="badge bg-info fs-6 p-2">
                                            <i class="bi bi-check-circle"></i> Sudah Dibayar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Bukti Pembayaran Berhasil Diupload</h6>
                                        <p class="mb-0">
                                            Bukti transfer Anda telah berhasil diupload dan sedang dalam proses verifikasi. 
                                            Tim kami akan memverifikasi pembayaran dalam waktu 1x24 jam.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <span class="badge bg-warning fs-6 p-2">
                                            <i class="bi bi-clock"></i> Menunggu Verifikasi
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <h6>Detail Pembayaran</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Metode:</strong></td>
                                        <td>{{ $order->paymentMethod->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jumlah:</strong></td>
                                        <td>Rp {{ number_format($order->paymentProof->transfer_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pengirim:</strong></td>
                                        <td>{{ $order->paymentProof->sender_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Transfer:</strong></td>
                                        <td>{{ $order->paymentProof->transfer_date->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <strong>Bukti Transfer:</strong><br>
                                <img src="{{ asset('storage/' . $order->paymentProof->proof_image) }}" 
                                     class="img-thumbnail mt-2" 
                                     style="max-width: 200px; max-height: 150px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#proofModal"
                                     alt="Bukti Transfer">
                                <br><small class="text-muted">Klik untuk memperbesar</small>
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
                                             style="height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="height: 60px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $item->product_name }}</h6>
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
                    
                    <!-- Totals -->
                    <div class="p-3 bg-light">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal:</span>
                                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Ongkos Kirim:</span>
                                    <span class="{{ $order->shipping_cost == 0 ? 'text-success' : '' }}">
                                        @if($order->shipping_cost == 0 && $order->voucher && $order->voucher->discount_type === 'free_shipping')
                                            <del class="text-muted">Rp 15.000</del> <strong>GRATIS</strong>
                                        @else
                                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-1 text-success">
                                        <span><i class="bi bi-ticket-perforated"></i> Diskon Voucher:</span>
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

            <!-- Next Steps -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Status Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Step 1: Order Created -->
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Pesanan Dibuat</h6>
                                <p class="text-muted small mb-0">{{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <!-- Step 2: Payment Proof Uploaded -->
                        @if($order->paymentProof)
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Bukti Pembayaran Diupload</h6>
                                    <p class="text-muted small mb-0">{{ $order->paymentProof->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Step 3: Payment Verification -->
                        <div class="timeline-item {{ in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']) ? 'completed' : 'pending' }}">
                            <div class="timeline-marker bg-{{ in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']) ? 'success' : 'warning' }}"></div>
                            <div class="timeline-content">
                                <h6>Verifikasi Pembayaran</h6>
                                <p class="text-muted small mb-0">
                                    @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                        Pembayaran terverifikasi {{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '' }}
                                    @else
                                        Menunggu verifikasi (1x24 jam)
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Step 4: Order Processing -->
                        <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : 'pending' }}">
                            <div class="timeline-marker bg-{{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'success' : 'light' }}"></div>
                            <div class="timeline-content">
                                <h6>Pesanan Diproses</h6>
                                <p class="text-muted small mb-0">
                                    @if($order->status === 'processing' || in_array($order->status, ['shipped', 'delivered']))
                                        Pesanan sedang diproses {{ $order->processing_at ? $order->processing_at->format('d M Y H:i') : '' }}
                                    @elseif($order->status === 'paid')
                                        Menunggu diproses
                                    @else
                                        Setelah pembayaran terverifikasi
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Step 5: Order Shipped -->
                        <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : 'pending' }}">
                            <div class="timeline-marker bg-{{ in_array($order->status, ['shipped', 'delivered']) ? 'success' : 'light' }}"></div>
                            <div class="timeline-content">
                                <h6>Pesanan Dikirim</h6>
                                <p class="text-muted small mb-0">
                                    @if(in_array($order->status, ['shipped', 'delivered']))
                                        Dikirim {{ $order->shipped_at ? $order->shipped_at->format('d M Y H:i') : '' }}
                                    @else
                                        Estimasi 1-3 hari kerja setelah diproses
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Step 6: Order Delivered -->
                        <div class="timeline-item {{ $order->status === 'delivered' ? 'completed' : 'pending' }}">
                            <div class="timeline-marker bg-{{ $order->status === 'delivered' ? 'success' : 'light' }}"></div>
                            <div class="timeline-content">
                                <h6>Pesanan Selesai</h6>
                                <p class="text-muted small mb-0">
                                    @if($order->status === 'delivered')
                                        Selesai {{ $order->delivered_at ? $order->delivered_at->format('d M Y H:i') : '' }}
                                    @else
                                        Estimasi setelah pesanan dikirim
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-grid"></i> Lanjut Belanja
                </a>
            </div>

            <!-- Contact Info -->
            <div class="alert alert-light text-center mt-4">
                <h6><i class="bi bi-headset"></i> Butuh Bantuan?</h6>
                <p class="mb-2">Jika ada pertanyaan mengenai pesanan Anda, jangan ragu untuk menghubungi kami:</p>
                <div>
                    <a href="https://wa.me/6281234567890?text=Halo, saya ingin bertanya tentang pesanan {{ $order->order_number }}" 
                       class="btn btn-success me-2" 
                       target="_blank">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="mailto:info@tokomakanan.com?subject=Pertanyaan Pesanan {{ $order->order_number }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-envelope"></i> Email
                    </a>
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
</style>
@endpush
@endsection