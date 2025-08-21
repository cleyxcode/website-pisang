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
                        <h5 class="mb-0">Item dalam Keranjang ({{ array_sum(array_column($cart, 'quantity')) }} item)</h5>
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
                                        <p class="text-muted small mb-0">Stok tersedia: {{ $item['stock'] }}</p>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    type="button" 
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" 
                                                   class="form-control form-control-sm text-center" 
                                                   value="{{ $item['quantity'] }}" 
                                                   min="1" 
                                                   max="{{ $item['stock'] }}"
                                                   onchange="updateQuantity({{ $id }}, this.value)">
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    type="button" 
                                                    onclick="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Item Total & Actions -->
                                    <div class="col-md-3 text-end">
                                        <div class="fw-bold text-primary mb-2 item-total">
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
                
                <!-- Promo Info -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <i class="bi bi-gift text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Ada Voucher?</h6>
                        <p class="text-muted small">Anda bisa memasukkan kode voucher saat checkout untuk mendapatkan diskon!</p>
                    </div>
                </div>
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
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: 'PUT',
        data: {
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update item total
                $(`[data-product-id="${productId}"] .item-total`).text('Rp ' + response.item_total);
                
                // Update cart total
                $('.cart-total').text('Rp ' + response.total);
                
                // Update cart count
                updateCartCount();
            } else {
                showAlert('danger', response.message);
                // Reload page to reset quantities
                setTimeout(() => location.reload(), 2000);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat mengupdate keranjang');
        }
    });
}

function removeFromCart(productId) {
    if (!confirm('Yakin ingin menghapus item ini dari keranjang?')) {
        return;
    }
    
    $.ajax({
        url: '{{ route("cart.remove") }}',
        method: 'DELETE',
        data: {
            product_id: productId
        },
        success: function(response) {
            if (response.success) {
                // Remove item from view
                $(`[data-product-id="${productId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        location.reload();
                    }
                });
                
                // Update totals
                $('.cart-total').text('Rp ' + response.total);
                updateCartCount();
                
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat menghapus item');
        }
    });
}

function clearCart() {
    if (!confirm('Yakin ingin mengosongkan seluruh keranjang?')) {
        return;
    }
    
    $.ajax({
        url: '{{ route("cart.clear") }}',
        method: 'DELETE',
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: function() {
            showAlert('danger', 'Terjadi kesalahan saat mengosongkan keranjang');
        }
    });
}
</script>
@endpush
@endsection