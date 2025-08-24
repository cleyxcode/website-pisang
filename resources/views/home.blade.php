@extends('layouts.app')

@section('title', 'Beranda - Toko Makanan')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">Selamat Datang di Toko Makanan</h1>
                <p class="lead mb-4">Temukan berbagai macam makanan ringan berkualitas dengan harga terjangkau. Dari keripik pisang hingga snack manis, semuanya ada di sini!</p>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-grid"></i> Lihat Semua Produk
                </a>
            </div>
            <div class="col-md-6 text-center">
                <i class="bi bi-shop" style="font-size: 200px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Kategori Produk</h2>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover;" 
                             alt="{{ $category->name }}">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                             style="height: 200px;">
                            <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text text-muted">{{ $category->products_count }} produk tersedia</p>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                           class="btn btn-outline-primary">
                            Lihat Produk
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if($featuredProducts->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Produk Unggulan</h2>
                <p class="text-muted">Produk terpilih dengan kualitas terbaik</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('products.featured') }}" class="btn btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    @if($product->main_image)
                        <img src="{{ asset('storage/' . $product->main_image) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary">{{ $product->formatted_price }}</span>
                                <small class="text-muted">Stok: {{ $product->stock }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                @if($product->stock > 0)
                                    @auth
                                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    @else
                                        <button class="btn btn-primary btn-sm" onclick="requireLogin('{{ route('login') }}')">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    @endauth
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="bi bi-x-circle"></i> Stok Habis
                                    </button>
                                @endif
                                
                                @auth
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" onclick="requireLogin('{{ route('login') }}')">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Products Section -->
@if($latestProducts->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Produk Terbaru</h2>
                <p class="text-muted">Produk-produk terbaru yang baru saja ditambahkan</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row">
            @foreach($latestProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    @if($product->main_image)
                        <img src="{{ asset('storage/' . $product->main_image) }}" 
                             class="card-img-top product-image" 
                             alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary">{{ $product->formatted_price }}</span>
                                <small class="text-muted">Stok: {{ $product->stock }}</small>
                            </div>
                            <div class="d-grid gap-2">
                                @if($product->stock > 0)
                                    @auth
                                        <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    @else
                                        <button class="btn btn-primary btn-sm" onclick="requireLogin('{{ route('login') }}')">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    @endauth
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="bi bi-x-circle"></i> Stok Habis
                                    </button>
                                @endif
                                
                                @auth
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" onclick="requireLogin('{{ route('login') }}')">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h3>Siap Berbelanja?</h3>
        <p class="lead">Jelajahi koleksi lengkap makanan ringan kami dan temukan favorit baru Anda!</p>
        @auth
            <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                <i class="bi bi-grid"></i> Mulai Belanja Sekarang
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">
                <i class="bi bi-person-plus"></i> Daftar Sekarang
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-grid"></i> Lihat Produk
            </a>
        @endauth
    </div>
</section>
@endsection