@extends('layouts.app')

@section('title', 'Checkout - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-credit-card"></i> Checkout</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
        @csrf
        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Penerima</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Lengkap *</label>
                                <input type="text" 
                                       class="form-control @error('customer_name') is-invalid @enderror" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       value="{{ old('customer_name', $customer->name) }}" 
                                       required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Email *</label>
                                <input type="email" 
                                       class="form-control @error('customer_email') is-invalid @enderror" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       value="{{ old('customer_email', $customer->email) }}" 
                                       required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">No. Telepon *</label>
                                <input type="text" 
                                       class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       value="{{ old('customer_phone', $customer->phone) }}" 
                                       placeholder="08123456789"
                                       required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Alamat Lengkap *</label>
                            <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                      id="customer_address" 
                                      name="customer_address" 
                                      rows="3" 
                                      placeholder="Masukkan alamat lengkap termasuk kecamatan, kota, provinsi, dan kode pos"
                                      required>{{ old('customer_address', $customer->address) }}</textarea>
                            @error('customer_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="2" 
                                      placeholder="Catatan khusus untuk pesanan (opsional)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-bag"></i> Detail Pesanan</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cart as $id => $item)
                            <div class="border-bottom p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                                 class="img-fluid rounded" 
                                                 alt="{{ $item['name'] }}"
                                                 style="height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-1">{{ $item['name'] }}</h6>
                                        <p class="text-muted small mb-0">Rp {{ number_format($item['price'], 0, ',', '.') }} × {{ $item['quantity'] }}</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <strong>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <!-- Voucher Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Kode Voucher</h5>
                    </div>
                    <div class="card-body">
                        @if($appliedVoucher)
                            <!-- Applied Voucher Display -->
                            <div class="alert alert-success" id="appliedVoucherAlert">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="alert-heading mb-1"><i class="bi bi-check-circle"></i> Voucher Aktif</h6>
                                        <strong>{{ $appliedVoucher['code'] }}</strong><br>
                                        <small class="text-muted">{{ $appliedVoucher['name'] }}</small><br>
                                        @if($appliedVoucher['free_shipping'])
                                            <span class="badge bg-info">Gratis Ongkir</span>
                                        @else
                                            <span class="badge bg-success">Diskon Rp {{ number_format($appliedVoucher['discount_amount'], 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn-close" onclick="removeVoucher()"></button>
                                </div>
                            </div>
                        @else
                            <!-- Voucher Input Form -->
                            <div id="voucherInputForm">
                                <div class="input-group mb-2">
                                    <input type="text" 
                                           class="form-control" 
                                           id="voucherCode" 
                                           placeholder="Masukkan kode voucher"
                                           maxlength="50">
                                    <button class="btn btn-outline-primary" 
                                            type="button" 
                                            onclick="applyVoucher()">
                                        <i class="bi bi-plus-circle"></i> Gunakan
                                    </button>
                                </div>
                                <div id="voucherMessage" class="small text-muted"></div>
                            </div>
                        @endif
                        
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Masukkan kode voucher untuk mendapatkan diskon atau gratis ongkir
                        </small>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span id="shippingDisplay" class="{{ $finalShippingCost == 0 ? 'text-success' : '' }}">
                                @if($finalShippingCost == 0 && $appliedVoucher && $appliedVoucher['free_shipping'])
                                    <del class="text-muted">Rp {{ number_format($shippingCost, 0, ',', '.') }}</del>
                                    <span class="text-success">GRATIS</span>
                                @else
                                    Rp {{ number_format($finalShippingCost, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        @if($discountAmount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success" id="discountRow">
                                <span><i class="bi bi-ticket-perforated"></i> Diskon Voucher</span>
                                <span id="discountDisplay">-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Pembayaran</strong>
                            <strong class="text-primary" id="totalDisplay">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <small>Pembayaran menggunakan transfer manual. Anda akan diarahkan ke halaman pembayaran setelah konfirmasi pesanan.</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="checkoutBtn">
                                <i class="bi bi-check-circle"></i> Konfirmasi Pesanan
                            </button>
                        </div>
                        
                        <div class="d-grid mt-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Transaksi Aman</h6>
                        <p class="text-muted small">Data Anda dilindungi dengan enkripsi SSL dan tidak akan dibagikan kepada pihak ketiga.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#checkoutForm').on('submit', function() {
        $('#checkoutBtn').prop('disabled', true)
                         .html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...');
    });
    
    // Phone number formatting
    $('#customer_phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });
    
    // Voucher code uppercase and validation on input
    $('#voucherCode').on('input', function() {
        this.value = this.value.toUpperCase();
        $('#voucherMessage').html('').removeClass('text-success text-danger');
    });
    
    // Allow Enter key to apply voucher
    $('#voucherCode').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            applyVoucher();
        }
    });
});

function applyVoucher() {
    const voucherCode = $('#voucherCode').val().trim();
    const messageDiv = $('#voucherMessage');
    const applyBtn = $('.btn-outline-primary');
    
    if (!voucherCode) {
        messageDiv.html('<i class="bi bi-exclamation-circle"></i> Masukkan kode voucher').addClass('text-danger');
        return;
    }
    
    // Show loading
    applyBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Validasi...');
    messageDiv.html('<i class="bi bi-clock"></i> Memvalidasi voucher...').removeClass('text-success text-danger').addClass('text-info');
    
    $.ajax({
        url: '{{ route("vouchers.apply") }}',
        method: 'POST',
        data: {
            voucher_code: voucherCode,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update totals
                updateOrderSummary(response.totals);
                
                // Show success message and hide form
                messageDiv.html('<i class="bi bi-check-circle"></i> ' + response.message).addClass('text-success');
                
                // Replace form with success display
                setTimeout(function() {
                    location.reload(); // Reload to show the applied voucher properly
                }, 1000);
            } else {
                messageDiv.html('<i class="bi bi-exclamation-circle"></i> ' + response.message).addClass('text-danger');
            }
        },
        error: function(xhr) {
            let message = 'Terjadi kesalahan saat memvalidasi voucher';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            messageDiv.html('<i class="bi bi-exclamation-circle"></i> ' + message).addClass('text-danger');
        },
        complete: function() {
            applyBtn.prop('disabled', false).html('<i class="bi bi-plus-circle"></i> Gunakan');
        }
    });
}

function removeVoucher() {
    if (!confirm('Yakin ingin menghapus voucher yang sudah diterapkan?')) {
        return;
    }
    
    $.ajax({
        url: '{{ route("vouchers.remove") }}',
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update totals
                updateOrderSummary(response.totals);
                
                // Reload page to reset the voucher section
                setTimeout(function() {
                    location.reload();
                }, 500);
                
                showAlert('success', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat menghapus voucher');
        }
    });
}

function updateOrderSummary(totals) {
    $('#subtotalDisplay').text('Rp ' + totals.subtotal);
    $('#shippingDisplay').html('Rp ' + totals.shipping_cost);
    $('#totalDisplay').text('Rp ' + totals.total);
    
    if (totals.discount_amount && totals.discount_amount !== '0') {
        if ($('#discountRow').length === 0) {
            $('#shippingDisplay').parent().after(`
                <div class="d-flex justify-content-between mb-2 text-success" id="discountRow">
                    <span><i class="bi bi-ticket-perforated"></i> Diskon Voucher</span>
                    <span id="discountDisplay">-Rp ${totals.discount_amount}</span>
                </div>
            `);
        } else {
            $('#discountDisplay').text('-Rp ' + totals.discount_amount);
        }
    } else {
        $('#discountRow').remove();
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').prepend(alertHtml);
    
    // Auto dismiss after 3 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 3000);
}
</script>
@endpush
@endsection