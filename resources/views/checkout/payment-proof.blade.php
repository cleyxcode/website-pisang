@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-cloud-upload"></i> Upload Bukti Pembayaran</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">Checkout</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('checkout.payment', $order->id) }}">Pembayaran</a></li>
                    <li class="breadcrumb-item active">Bukti Pembayaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Payment Proof Form -->
        <div class="col-md-8">
            <!-- Payment Instructions -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Instruksi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-wallet2"></i> {{ $order->paymentMethod->name }}</h6>
                            <p class="mb-1"><strong>No. Rekening:</strong> {{ $order->paymentMethod->account_number }}</p>
                            <p class="mb-1"><strong>Atas Nama:</strong> {{ $order->paymentMethod->account_name }}</p>
                            <p class="mb-0"><strong>Jenis:</strong> 
                                <span class="badge bg-{{ $order->paymentMethod->type === 'bank' ? 'info' : 'success' }}">
                                    {{ $order->paymentMethod->type_label }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-cash"></i> Jumlah Transfer</h6>
                            <h4 class="text-primary mb-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h4>
                            <small class="text-muted">Transfer tepat sesuai jumlah di atas</small>
                        </div>
                    </div>
                    
                    @if($order->paymentMethod->instructions)
                        <hr>
                        <div class="alert alert-light">
                            <h6><i class="bi bi-list-check"></i> Petunjuk Khusus:</h6>
                            {!! nl2br(e($order->paymentMethod->instructions)) !!}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-upload"></i> Upload Bukti Transfer</h5>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ route('checkout.store-payment-proof', $order->id) }}" 
                          enctype="multipart/form-data" 
                          id="paymentProofForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="transfer_amount" class="form-label">Jumlah Transfer *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('transfer_amount') is-invalid @enderror" 
                                           id="transfer_amount" 
                                           name="transfer_amount" 
                                           value="{{ old('transfer_amount', $order->total_amount) }}" 
                                           min="0"
                                           step="1"
                                           required>
                                </div>
                                @error('transfer_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Masukkan jumlah yang Anda transfer</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="transfer_date" class="form-label">Tanggal Transfer *</label>
                                <input type="datetime-local" 
                                       class="form-control @error('transfer_date') is-invalid @enderror" 
                                       id="transfer_date" 
                                       name="transfer_date" 
                                       value="{{ old('transfer_date', date('Y-m-d\TH:i')) }}"
                                       max="{{ date('Y-m-d\TH:i') }}"
                                       required>
                                @error('transfer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sender_name" class="form-label">Nama Pengirim *</label>
                                <input type="text" 
                                       class="form-control @error('sender_name') is-invalid @enderror" 
                                       id="sender_name" 
                                       name="sender_name" 
                                       value="{{ old('sender_name', $order->customer_name) }}" 
                                       placeholder="Nama sesuai rekening pengirim"
                                       required>
                                @error('sender_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="sender_account" class="form-label">Rekening/No. HP Pengirim</label>
                                <input type="text" 
                                       class="form-control @error('sender_account') is-invalid @enderror" 
                                       id="sender_account" 
                                       name="sender_account" 
                                       value="{{ old('sender_account') }}" 
                                       placeholder="Nomor rekening atau HP pengirim">
                                @error('sender_account')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional, untuk memudahkan verifikasi</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="proof_image" class="form-label">Bukti Transfer *</label>
                            <input type="file" 
                                   class="form-control @error('proof_image') is-invalid @enderror" 
                                   id="proof_image" 
                                   name="proof_image" 
                                   accept="image/*"
                                   required>
                            @error('proof_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB</small>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Catatan tambahan terkait pembayaran (opsional)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('checkout.payment', $order->id) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success" id="uploadBtn">
                                <i class="bi bi-cloud-upload"></i> Upload Bukti Pembayaran
                            </button>
                        </div>
                    </form>
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
                    <div class="mb-3">
                        <strong>No. Pesanan:</strong> {{ $order->order_number }}<br>
                        <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}
                    </div>
                    
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
                </div>
            </div>

            <!-- Tips -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Tips Upload Bukti Transfer</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Pastikan foto bukti transfer jelas dan mudah dibaca</li>
                        <li>Terlihat tanggal, jam, jumlah, dan nomor rekening tujuan</li>
                        <li>Format gambar yang didukung: JPG, JPEG, PNG</li>
                        <li>Ukuran maksimal 2MB</li>
                        <li>Jangan edit atau crop foto berlebihan</li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <i class="bi bi-headset text-primary" style="font-size: 2rem;"></i>
                    <h6 class="mt-2">Butuh Bantuan?</h6>
                    <p class="text-muted small">Hubungi customer service kami</p>
                    <a href="https://wa.me/6281234567890" class="btn btn-outline-success btn-sm" target="_blank">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Image preview
    $('#proof_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
    
    // Form submission
    $('#paymentProofForm').on('submit', function() {
        $('#uploadBtn').prop('disabled', true)
                       .html('<i class="spinner-border spinner-border-sm me-2"></i>Mengupload...');
    });
    
    // Format amount input
    $('#transfer_amount').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });
    
    // Set max date to today
    const now = new Date();
    const maxDate = now.toISOString().slice(0, 16);
    $('#transfer_date').attr('max', maxDate);
});
</script>
@endpush
@endsection