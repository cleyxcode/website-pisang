@extends('layouts.app')

@section('title', 'Semua Produk - Toko Makanan')

@section('content')
<div class="products-page">
    <!-- Header Section -->
    <div class="page-header">
        <div class="container">
            <div class="header-content">
                <div class="header-text">
                    <h1 class="page-title">Semua Produk</h1>
                    <p class="product-count">{{ $products->total() }} produk ditemukan</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('home') }}" class="btn-back">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Login Info untuk Guest -->
        @guest
        <div class="guest-notice">
            <div class="notice-content">
                <i class="bi bi-info-circle"></i>
                <div class="notice-text">
                    <strong>Info:</strong> Untuk melihat detail produk dan berbelanja, silakan 
                    <a href="{{ route('login') }}" class="login-link">login</a> atau 
                    <a href="{{ route('register') }}" class="register-link">daftar</a> terlebih dahulu.
                </div>
            </div>
        </div>
        @endguest

        <!-- Modern Filters -->
        <div class="filters-section">
            <div class="filters-card">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <div class="search-wrapper">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" 
                                   name="search" 
                                   class="search-input" 
                                   placeholder="Cari produk..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <select name="category" class="category-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" 
                                        {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-search">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('products.index') }}" class="btn-reset">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                <div class="product-card">
                    <div class="product-image-wrapper">
                        @if($product->main_image)
                            <img src="{{ asset('storage/' . $product->main_image) }}" 
                                 class="product-image" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="product-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                        
                        <!-- Featured Badge -->
                        @if($product->is_featured)
                            <div class="featured-badge">
                                <i class="bi bi-star"></i> Unggulan
                            </div>
                        @endif

                        <!-- Quick Actions Overlay -->
                        <div class="product-overlay">
                            <div class="overlay-actions">
                                @auth
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="btn-quick-view">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @else
                                    <button class="btn-quick-view" onclick="requireLogin('{{ route('login') }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                @endauth
                                
                                @if($product->stock > 0)
                                    @auth
                                        <button class="btn-quick-cart" onclick="addToCart({{ $product->id }})">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    @else
                                        <button class="btn-quick-cart" onclick="requireLogin('{{ route('login') }}')">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-category">
                            <i class="bi bi-tag"></i> {{ $product->category->name }}
                        </div>
                        <h3 class="product-name">{{ $product->name }}</h3>
                        
                        @if($product->description)
                            <p class="product-description">
                                {{ Str::limit(strip_tags($product->description), 60) }}
                            </p>
                        @endif
                        
                        <div class="product-footer">
                            <div class="price-stock">
                                <div class="product-price">{{ $product->formatted_price }}</div>
                                <div class="stock-info">
                                    @if($product->stock > 0)
                                        <span class="stock-available">Stok: {{ $product->stock }}</span>
                                    @else
                                        <span class="stock-empty">Stok Habis</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                @if($product->stock > 0)
                                    @auth
                                        <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    @else
                                        <button class="btn-add-cart" onclick="requireLogin('{{ route('login') }}')">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    @endauth
                                @else
                                    <button class="btn-add-cart disabled" disabled>
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Modern Pagination -->
            <div class="pagination-wrapper">
                {{ $products->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="empty-title">Produk tidak ditemukan</h3>
                <p class="empty-description">Coba ubah kata kunci pencarian atau filter kategori</p>
                <a href="{{ route('products.index') }}" class="btn-empty-action">
                    <i class="bi bi-arrow-clockwise"></i> Lihat Semua Produk
                </a>
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --shopee-orange: #ff5722;
    --shopee-dark-orange: #e64a19;
    --shopee-light-orange: #ffab91;
    --shopee-red: #d32f2f;
    --text-dark: #333333;
    --text-gray: #666666;
    --text-light: #999999;
    --border-light: #e0e0e0;
    --background-light: #f5f5f5;
    --white: #ffffff;
    --shadow: 0 2px 8px rgba(0,0,0,0.1);
    --shadow-hover: 0 4px 16px rgba(0,0,0,0.15);
}

.products-page {
    background: var(--background-light);
    min-height: 100vh;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--shopee-orange) 0%, var(--shopee-red) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.product-count {
    opacity: 0.9;
    margin: 0;
}

.btn-back {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-back:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    transform: translateY(-1px);
}

/* Guest Notice */
.guest-notice {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 12px;
    margin-bottom: 2rem;
    overflow: hidden;
}

.notice-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    color: #1976d2;
}

.notice-content i {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.login-link, .register-link {
    color: var(--shopee-orange);
    font-weight: 600;
    text-decoration: none;
}

.login-link:hover, .register-link:hover {
    color: var(--shopee-dark-orange);
    text-decoration: underline;
}

/* Modern Filters */
.filters-section {
    margin-bottom: 2rem;
}

.filters-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.filters-form {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 1rem;
    padding: 1.5rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.search-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 1.1rem;
}

.search-input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 2px solid var(--border-light);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fafafa;
}

.search-input:focus {
    outline: none;
    border-color: var(--shopee-orange);
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
}

.category-select {
    padding: 12px 16px;
    border: 2px solid var(--border-light);
    border-radius: 8px;
    font-size: 1rem;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-select:focus {
    outline: none;
    border-color: var(--shopee-orange);
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-search {
    background: var(--shopee-orange);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-search:hover {
    background: var(--shopee-dark-orange);
    transform: translateY(-1px);
}

.btn-reset {
    background: #f5f5f5;
    color: var(--text-gray);
    border: 2px solid var(--border-light);
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-reset:hover {
    background: #e0e0e0;
    color: var(--text-dark);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.product-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-4px);
    border-color: var(--shopee-orange);
}

.product-image-wrapper {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 3rem;
}

.featured-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 1rem;
}

.btn-quick-view,
.btn-quick-cart {
    background: white;
    color: var(--shopee-orange);
    border: none;
    padding: 12px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-quick-view:hover,
.btn-quick-cart:hover {
    background: var(--shopee-orange);
    color: white;
    transform: scale(1.1);
}

/* Product Info */
.product-info {
    padding: 1.5rem;
}

.product-category {
    color: var(--text-light);
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    color: var(--text-gray);
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1rem;
}

.price-stock {
    flex: 1;
}

.product-price {
    color: var(--shopee-orange);
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stock-info {
    font-size: 0.8rem;
}

.stock-available {
    color: #4caf50;
    font-weight: 500;
}

.stock-empty {
    color: var(--shopee-red);
    font-weight: 500;
}

.btn-add-cart {
    background: var(--shopee-orange);
    color: white;
    border: none;
    padding: 8px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btn-add-cart:hover {
    background: var(--shopee-dark-orange);
    transform: scale(1.1);
}

.btn-add-cart.disabled {
    background: var(--text-light);
    cursor: not-allowed;
    transform: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.empty-icon {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.empty-title {
    color: var(--text-dark);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: var(--text-gray);
    margin-bottom: 2rem;
}

.btn-empty-action {
    background: var(--shopee-orange);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-empty-action:hover {
    background: var(--shopee-dark-orange);
    color: white;
    transform: translateY(-2px);
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem 0;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .filters-form {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-actions {
        justify-content: center;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .product-card {
        border-radius: 8px;
    }
    
    .product-image-wrapper {
        height: 160px;
    }
    
    .product-info {
        padding: 1rem;
    }
    
    .product-name {
        font-size: 1rem;
    }
    
    .product-price {
        font-size: 1.1rem;
    }
    
    .overlay-actions {
        gap: 0.5rem;
    }
    
    .btn-quick-view,
    .btn-quick-cart {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .product-card {
        max-width: 100%;
    }
    
    .product-image-wrapper {
        height: 200px;
    }
    
    .notice-content {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .filters-form {
        padding: 1rem;
    }
    
    .filter-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-search,
    .btn-reset {
        justify-content: center;
    }
}
</style>
@endsection