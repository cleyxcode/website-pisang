@extends('layouts.app')

@section('title', $product->name . ' - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.index') }}">Produk</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Detail -->
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            @if($product->images && count($product->images) > 0)
                <!-- Main Image -->
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $product->images[0]) }}" 
                         class="img-fluid rounded" 
                         alt="{{ $product->name }}"
                         id="main-image"
                         style="width: 100%; height: 400px; object-fit: cover;">
                </div>
                
                <!-- Thumbnail Images -->
                @if(count($product->images) > 1)
                    <div class="row">
                        @foreach($product->images as $index => $image)
                            <div class="col-3">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     class="img-fluid rounded thumbnail-image {{ $index === 0 ? 'active' : '' }}" 
                                     alt="{{ $product->name }}"
                                     onclick="changeMainImage('{{ asset('storage/' . $image) }}')"
                                     style="height: 80px; object-fit: cover; cursor: pointer; border: 2px solid transparent;">
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                     style="height: 400px;">
                    <i class="bi bi-image" style="font-size: 64px; color: #ccc;"></i>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <div class="product-info">
                <!-- Featured Badge -->
                @if($product->is_featured)
                    <span class="badge bg-warning mb-2">
                        <i class="bi bi-star"></i> Produk Unggulan
                    </span>
                @endif

                <h1 class="h3 mb-3">{{ $product->name }}</h1>
                
                <!-- Category -->
                <p class="text-muted mb-2">
                    <i class="bi bi-tag"></i> 
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" 
                       class="text-decoration-none">
                        {{ $product->category->name }}
                    </a>
                </p>

                <!-- SKU -->
                <p class="text-muted small mb-3">SKU: {{ $product->sku }}</p>

                <!-- Price -->
                <div class="price-section mb-4">
                    <span class="h4 text-primary fw-bold">{{ $product->formatted_price }}</span>
                </div>

                <!-- Stock Info -->
                <div class="stock-info mb-4">
                    @if($product->stock > 0)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle"></i> Stok Tersedia: {{ $product->stock }} pcs
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="bi bi-x-circle"></i> Stok Habis
                        </span>
                    @endif
                </div>

                <!-- Quantity and Add to Cart -->
                @if($product->stock > 0)
                    <div class="add-to-cart-section mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah:</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->stock }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-primary btn-lg" onclick="addToCartWithQuantity()">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- WhatsApp Contact Section -->
                @if($product->whatsapp_contact)
                    <div class="alert alert-success border-success mb-4" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="alert-heading mb-2">
                                    <i class="bi bi-whatsapp"></i> Pesan via WhatsApp
                                </h6>
                                <p class="mb-0 small">
                                    Ingin pesan langsung? Hubungi kami via WhatsApp untuk pemesanan manual
                                </p>
                            </div>
                            <div>
                                <a href="{{ $product->whatsapp_link }}" 
                                   target="_blank" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-whatsapp"></i> Chat Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alternative: WhatsApp Button Style 2 (Uncomment jika mau pakai style ini) -->
                {{-- @if($product->whatsapp_contact)
                    <div class="mb-4">
                        <a href="{{ $product->whatsapp_link }}" 
                           target="_blank" 
                           class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-whatsapp"></i> Pesan via WhatsApp
                            <small class="d-block" style="font-size: 0.75rem;">
                                Hubungi: +{{ $product->formatted_whatsapp }}
                            </small>
                        </a>
                    </div>
                @endif --}}

                <!-- Product Weight -->
                @if($product->weight)
                    <div class="weight-info mb-3">
                        <small class="text-muted">
                            <i class="bi bi-weight"></i> Berat: {{ $product->weight }}g
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Description -->
    @if($product->description)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Deskripsi Produk</h5>
                    </div>
                    <div class="card-body">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Contact Information Card (Alternative Style) -->
    @if($product->whatsapp_contact)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-2">
                                    <i class="bi bi-headset text-success"></i> Butuh Bantuan atau Pemesanan Khusus?
                                </h5>
                                <p class="card-text mb-0">
                                    Hubungi kami langsung via WhatsApp untuk konsultasi produk, pemesanan dalam jumlah besar, 
                                    atau pertanyaan lainnya. Tim kami siap membantu Anda!
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ $product->whatsapp_link }}" 
                                   target="_blank" 
                                   class="btn btn-success btn-lg pulse-button">
                                    <i class="bi bi-whatsapp fs-5"></i> 
                                    <span class="ms-2">Chat WhatsApp</span>
                                </a>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-telephone"></i> +{{ $product->formatted_whatsapp }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">Produk Terkait</h4>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-3 mb-4">
                            <div class="card product-card h-100">
                                @if($relatedProduct->main_image)
                                    <img src="{{ asset('storage/' . $relatedProduct->main_image) }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $relatedProduct->name }}">
                                @else
                                    <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-primary">{{ $relatedProduct->formatted_price }}</span>
                                            <small class="text-muted">Stok: {{ $relatedProduct->stock }}</small>
                                        </div>
                                        <div class="d-grid gap-2">
                                            @if($relatedProduct->stock > 0)
                                                <button class="btn btn-primary btn-sm" onclick="addToCart({{ $relatedProduct->id }})">
                                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="bi bi-x-circle"></i> Stok Habis
                                                </button>
                                            @endif
                                            <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .thumbnail-image.active {
        border-color: #28a745 !important;
    }
    
    .thumbnail-image:hover {
        border-color: #28a745 !important;
        opacity: 0.8;
    }

    /* WhatsApp Button Animation */
    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        transition: all 0.3s ease;
    }

    /* WhatsApp Alert Style */
    .alert-success {
        background-color: #d1f4dd;
        border-left: 4px solid #25D366;
    }

    .alert-success .btn-success {
        background-color: #25D366;
        border-color: #25D366;
    }

    .alert-success .btn-success:hover {
        background-color: #128C7E;
        border-color: #128C7E;
    }
</style>
@endpush

@push('scripts')
<script>
    function changeMainImage(src) {
        document.getElementById('main-image').src = src;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail-image').forEach(img => {
            img.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    function increaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.max);
        
        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
        }
    }

    function decreaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    }

    function addToCartWithQuantity() {
        const quantity = parseInt(document.getElementById('quantity').value);
        addToCart({{ $product->id }}, quantity);
    }
</script>
@endpush
@endsection