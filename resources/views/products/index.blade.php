@extends('layouts.app')

@section('title', 'Semua Produk - Toko Makanan')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Semua Produk</h2>
            <p class="text-muted">{{ $products->total() }} produk ditemukan</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-house"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Produk</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Nama produk..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" 
                                    {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    @if($product->main_image)
                        <img src="{{ asset('storage/' . $product->main_image) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                        </div>
                    @endif
                    
                    <!-- Featured Badge -->
                    @if($product->is_featured)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-warning">
                                <i class="bi bi-star"></i> Unggulan
                            </span>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag"></i> {{ $product->category->name }}
                        </p>
                        
                        <!-- Description (truncated) -->
                        @if($product->description)
                            <p class="card-text small text-muted">
                                {{ Str::limit(strip_tags($product->description), 80) }}
                            </p>
                        @endif
                        
                        <div class="mt-auto">
                            <!-- Price and Stock -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-primary h6 mb-0">
                                    {{ $product->formatted_price }}
                                </span>
                                <small class="text-muted">
                                    Stok: {{ $product->stock }}
                                </small>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                @if($product->stock > 0)
                                    <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="bi bi-x-circle"></i> Stok Habis
                                    </button>
                                @endif
                                
                                <a href="{{ route('products.show', $product->slug) }}" 
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

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-search" style="font-size: 64px; color: #ccc;"></i>
            <h4 class="mt-3">Produk tidak ditemukan</h4>
            <p class="text-muted">Coba ubah kata kunci pencarian atau filter kategori</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-clockwise"></i> Lihat Semua Produk
            </a>
        </div>
    @endif
</div>
@endsection