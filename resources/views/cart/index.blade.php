@extends('layouts.app')

@section('title', 'Keranjang Belanja - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-cart3"></i> Keranjang Belanja</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Lanjut Belanja
            </a>
        </div>
    </div>

    @if(count($cart) > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Item dalam Keranjang (<span id="total-items">{{ array_sum(array_column($cart, 'quantity')) }}</span> item)</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cart as $id => $item)
                            <div class="cart-item border-bottom p-3" data-product-id="{{ $id }}">
                                <div class="row align-items-center">
                                    <!-- Product Image -->
                                    <div class="col-md-2">
                                        @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                                 class="img-fluid rounded" 
                                                 alt="{{ $item['name'] }}"
                                                 style="height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="height: 80px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="col-md-4">
                                        <h6 class="mb-1">{{ $item['name'] }}</h6>
                                        <p class="text-muted small mb-1">Harga: Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        <p class="text-muted small mb-0">Stok tersedia: <span class="stock-info" data-stock="{{ $item['stock'] }}">{{ $item['stock'] }}</span></p>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary btn-sm decrease-btn" 
                                                    type="button" 
                                                    data-product-id="{{ $id }}">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control form-control-sm text-center quantity-input" 
                                                   value="{{ $item['quantity'] }}" 
                                                   min="1" 
                                                   max="{{ $item['stock'] }}"
                                                   data-product-id="{{ $id }}"
                                                   data-price="{{ $item['price'] }}">
                                            <button class="btn btn-outline-secondary btn-sm increase-btn" 
                                                    type="button" 
                                                    data-product-id="{{ $id }}"
                                                    data-max-stock="{{ $item['stock'] }}">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Item Total & Actions -->
                                    <div class="col-md-3 text-end">
                                        <div class="fw-bold text-primary mb-2 item-total" data-product-id="{{ $id }}">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </div>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="removeFromCart({{ $id }})">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-outline-warning" onclick="clearCart()">
                            <i class="bi bi-trash"></i> Kosongkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="cart-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Ongkos Kirim</span>
                            <span class="text-muted">Dihitung saat checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary cart-total">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        
                        <!-- Checkout Button -->
                        <div class="d-grid">
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-credit-card"></i> Lanjut ke Checkout
                            </a>
                        </div>
                        
                        <!-- Continue Shopping -->
                        <div class="d-grid mt-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Available Vouchers -->
                @if(isset($availableVouchers) && $availableVouchers->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-ticket-perforated text-primary"></i> Voucher Tersedia</h6>
                        </div>
                        <div class="card-body">
                            @foreach($availableVouchers as $voucher)
                                <div class="border rounded p-2 mb-2 small">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-primary">{{ $voucher->code }}</strong>
                                            <br>
                                            <span class="text-muted">{{ Str::limit($voucher->name, 40) }}</span>
                                            <br>
                                            <span class="badge bg-{{ $voucher->discount_type === 'percentage' ? 'success' : ($voucher->discount_type === 'fixed' ? 'warning' : 'info') }} badge-sm">
                                                {{ $voucher->formatted_discount }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            @if($voucher->minimum_amount && $total < $voucher->minimum_amount)
                                                <small class="text-muted">
                                                    Min. Rp {{ number_format($voucher->minimum_amount, 0, ',', '.') }}
                                                </small>
                                            @else
                                                <button class="btn btn-outline-primary btn-sm" 
                                                        onclick="copyVoucherCode('{{ $voucher->code }}')"
                                                        title="Salin kode">
                                                    <i class="bi bi-copy"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @if($voucher->expires_at)
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-clock"></i> Berlaku hingga {{ $voucher->expires_at->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            <div class="text-center mt-2">
                                <small class="text-muted">Gunakan kode voucher saat checkout untuk mendapatkan diskon!</small>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Fallback Promo Info when no vouchers available -->
                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <i class="bi bi-gift text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Ada Voucher?</h6>
                            <p class="text-muted small">Anda bisa memasukkan kode voucher saat checkout untuk mendapatkan diskon!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size: 64px; color: #ccc;"></i>
            <h4 class="mt-3">Keranjang Belanja Kosong</h4>
            <p class="text-muted">Anda belum menambahkan produk apapun ke keranjang</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-grid"></i> Mulai Belanja
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Prevent double clicks
let isUpdating = false;

$(document).ready(function() {
    // Handle increase button
    $('.increase-btn').on('click', function() {
        if (isUpdating) return;
        
        const productId = $(this).data('product-id');
        const maxStock = $(this).data('max-stock');
        const input = $(`.quantity-input[data-product-id="${productId}"]`);
        const currentQty = parseInt(input.val());
        
        if (currentQty < maxStock) {
            updateQuantityFromButton(productId, currentQty + 1);
        } else {
            showAlert('warning', `Maksimal ${maxStock} item untuk produk ini`);
        }
    });
    
    // Handle decrease button
    $('.decrease-btn').on('click', function() {
        if (isUpdating) return;
        
        const productId = $(this).data('product-id');
        const input = $(`.quantity-input[data-product-id="${productId}"]`);
        const currentQty = parseInt(input.val());
        
        if (currentQty > 1) {
            updateQuantityFromButton(productId, currentQty - 1);
        } else {
            removeFromCart(productId);
        }
    });
    
    // Handle direct input change
    $('.quantity-input').on('change', function() {
        if (isUpdating) return;
        
        const productId = $(this).data('product-id');
        const newQty = parseInt($(this).val());
        const maxStock = parseInt($(this).attr('max'));
        
        if (newQty < 1) {
            removeFromCart(productId);
        } else if (newQty > maxStock) {
            showAlert('warning', `Maksimal ${maxStock} item untuk produk ini`);
            $(this).val(maxStock);
            updateQuantityFromButton(productId, maxStock);
        } else {
            updateQuantityFromButton(productId, newQty);
        }
    });
    
    // Handle enter key on quantity input
    $('.quantity-input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).blur(); // Trigger change event
        }
    });
});

function updateQuantityFromButton(productId, newQuantity) {
    if (isUpdating) return;
    
    isUpdating = true;
    
    // Disable buttons to prevent multiple clicks
    $(`.increase-btn[data-product-id="${productId}"], .decrease-btn[data-product-id="${productId}"]`).prop('disabled', true);
    
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            product_id: productId,
            quantity: newQuantity
        },
        success: function(response) {
            if (response.success) {
                // Update quantity input value
                $(`.quantity-input[data-product-id="${productId}"]`).val(newQuantity);
                
                // Update item total
                $(`.item-total[data-product-id="${productId}"]`).text('Rp ' + response.item_total);
                
                // Update cart totals
                $('.cart-total').text('Rp ' + response.total);
                
                // Update total items count
                updateTotalItems();
                
                // Update cart count in navbar
                updateCartCount();
                
                showAlert('success', 'Keranjang berhasil diperbarui');
            } else {
                showAlert('danger', response.message);
                // Reset input value on error
                location.reload();
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            showAlert('danger', 'Terjadi kesalahan saat mengupdate keranjang');
            // Reset input value on error
            location.reload();
        },
        complete: function() {
            isUpdating = false;
            // Re-enable buttons
            $(`.increase-btn[data-product-id="${productId}"], .decrease-btn[data-product-id="${productId}"]`).prop('disabled', false);
        }
    });
}

function updateQuantity(productId, quantity) {
    // This function is kept for backward compatibility but redirects to the new function
    updateQuantityFromButton(productId, quantity);
}

function removeFromCart(productId) {
    if (!confirm('Yakin ingin menghapus item ini dari keranjang?')) {
        return;
    }
    
    if (isUpdating) return;
    isUpdating = true;
    
    $.ajax({
        url: '{{ route("cart.remove") }}',
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            product_id: productId
        },
        success: function(response) {
            if (response.success) {
                // Remove item from view with animation
                $(`[data-product-id="${productId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Update totals
                    $('.cart-total').text('Rp ' + response.total);
                    updateTotalItems();
                    updateCartCount();
                    
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        setTimeout(() => location.reload(), 500);
                    }
                });
                
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat menghapus item');
        },
        complete: function() {
            isUpdating = false;
        }
    });
}

function clearCart() {
    if (!confirm('Yakin ingin mengosongkan seluruh keranjang?')) {
        return;
    }
    
    if (isUpdating) return;
    isUpdating = true;
    
    $.ajax({
        url: '{{ route("cart.clear") }}',
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat mengosongkan keranjang');
        },
        complete: function() {
            isUpdating = false;
        }
    });
}

// Update total items count
function updateTotalItems() {
    let totalItems = 0;
    $('.quantity-input').each(function() {
        totalItems += parseInt($(this).val()) || 0;
    });
    $('#total-items').text(totalItems);
}

// Voucher functions
function copyVoucherCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        showAlert('success', `Kode voucher "${code}" berhasil disalin! Gunakan saat checkout.`);
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        showAlert('success', `Kode voucher "${code}" berhasil disalin! Gunakan saat checkout.`);
    });
}

// Update cart count in navbar
function updateCartCount() {
    $.ajax({
        url: '{{ route("cart.count") }}',
        method: 'GET',
        success: function(response) {
            // Update cart badge in navbar if exists
            $('.cart-count, .cart-badge').text(response.count);
        }
    });
}

function showAlert(type, message) {
    // Remove existing alerts first
    $('.alert-floating').remove();
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show alert-floating" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'info-circle')}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    // Auto dismiss after 4 seconds
    setTimeout(function() {
        $('.alert-floating').fadeOut(300);
    }, 4000);
}
</script>
@endpush
@endsection