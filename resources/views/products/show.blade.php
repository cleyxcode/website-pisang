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