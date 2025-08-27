@extends('layouts.app')

@section('title', 'Beranda - Toko Makanan')

@section('content')
<!-- Hero Banner Section -->
<section class="hero-banner">
    <div class="container-fluid">
        <div class="banner-carousel">
            <div class="banner-slide active">
                <div class="banner-content">
                    <div class="banner-text">
                        <h1 class="banner-title">Selamat Datang di <span class="highlight">Toko Makanan</span></h1>
                        <p class="banner-subtitle">Temukan berbagai macam makanan ringan berkualitas dengan harga terjangkau</p>
                        <a href="{{ route('products.index') }}" class="btn-shop-now">
                            <i class="bi bi-grid"></i> Belanja Sekarang
                        </a>
                    </div>
                    <div class="banner-visual">
                        <div class="promo-badge">
                            <span class="discount">DISKON</span>
                            <span class="percentage">15%</span>
                        </div>
                        <i class="bi bi-shop banner-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Categories -->
<section class="quick-categories">
    <div class="container">
        <div class="categories-grid">
            @if($categories->count() > 0)
                @foreach($categories->take(8) as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-item">
                    <div class="category-icon">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                        @else
                            <i class="bi bi-basket"></i>
                        @endif
                    </div>
                    <span class="category-name">{{ $category->name }}</span>
                </a>
                @endforeach
            @else
                <div class="category-item">
                    <div class="category-icon"><i class="bi bi-basket"></i></div>
                    <span class="category-name">Semua Produk</span>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Flash Sale Section -->
@if($featuredProducts->count() > 0)
<section class="flash-sale">
    <div class="container">
        <div class="section-header-orange">
            <div class="section-title-wrapper">
                <h2 class="section-title">
                    <i class="bi bi-lightning-fill"></i>
                    FLASH SALE
                </h2>
                <div class="timer">
                    <span class="timer-label">Berakhir dalam</span>
                    <div class="timer-display">
                        <span class="timer-unit">23</span>:
                        <span class="timer-unit">59</span>:
                        <span class="timer-unit">45</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('products.featured') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div class="products-slider">
            <div class="products-row">
                @foreach($featuredProducts as $product)
                <div class="product-item">
                    <div class="product-image">
                        @if($product->main_image)
                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                        <div class="discount-badge">-15%</div>
                        @if($product->stock <= 5)
                            <div class="stock-warning">Tersisa {{ $product->stock }}</div>
                        @endif
                    </div>
                    <div class="product-info">
                        <div class="product-price">
                            <span class="current-price">{{ $product->formatted_price }}</span>
                        </div>
                        <div class="product-sold">Terjual {{ rand(10, 100) }}</div>
                        <div class="product-actions">
                            @if($product->stock > 0)
                                @auth
                                    <button class="add-to-cart" onclick="addToCart({{ $product->id }})">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                @else
                                    <button class="add-to-cart" onclick="requireLogin('{{ route('login') }}')">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                @endauth
                            @else
                                <button class="add-to-cart disabled" disabled>
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Best Sellers -->
@if($latestProducts->count() > 0)
<section class="best-sellers">
    <div class="container">
        <div class="section-header-white">
            <h2 class="section-title">PRODUK TERLARIS</h2>
            <a href="{{ route('products.index') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div class="products-grid">
            @foreach($latestProducts as $product)
            <div class="product-card-modern">
                <div class="product-image-wrapper">
                    @if($product->main_image)
                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-placeholder">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif
                    <div class="product-overlay">
                        <div class="overlay-actions">
                            @auth
                                <a href="{{ route('products.show', $product->slug) }}" class="btn-view">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @else
                                <button class="btn-view" onclick="requireLogin('{{ route('login') }}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            @endauth
                            @if($product->stock > 0)
                                @auth
                                    <button class="btn-cart" onclick="addToCart({{ $product->id }})">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                @else
                                    <button class="btn-cart" onclick="requireLogin('{{ route('login') }}')">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                @endauth
                            @endif
                        </div>
                    </div>
                    <div class="product-badges">
                        <span class="badge-new">BARU</span>
                    </div>
                </div>
                <div class="product-details">
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                        </div>
                        <span class="rating-count">({{ rand(10, 200) }})</span>
                    </div>
                    <div class="product-price-section">
                        <span class="price">{{ $product->formatted_price }}</span>
                        <span class="location">{{ $product->category->name }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Mall Brands Section -->
<section class="mall-brands">
    <div class="container">
        <div class="section-header-white">
            <h2 class="section-title">KATEGORI PILIHAN</h2>
        </div>
        @if($categories->count() > 0)
        <div class="brands-grid">
            @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="brand-card">
                <div class="brand-image">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                    @else
                        <div class="brand-placeholder">
                            <i class="bi bi-basket"></i>
                        </div>
                    @endif
                </div>
                <div class="brand-info">
                    <h3 class="brand-name">{{ $category->name }}</h3>
                    <p class="brand-description">{{ $category->products_count }} produk tersedia</p>
                    <div class="brand-cta">Belanja Sekarang</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Call to Action -->
<section class="bottom-cta">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h3>Bergabunglah dengan Jutaan Pembeli!</h3>
                <p>Dapatkan penawaran terbaik dan pengalaman belanja yang menyenangkan</p>
            </div>
            <div class="cta-buttons">
                @auth
                    <a href="{{ route('products.index') }}" class="btn-primary-cta">
                        <i class="bi bi-grid"></i> Mulai Belanja
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary-cta">
                        <i class="bi bi-person-plus"></i> Daftar Gratis
                    </a>
                    <a href="{{ route('products.index') }}" class="btn-secondary-cta">
                        <i class="bi bi-grid"></i> Lihat Produk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

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

/* Global Styles */
* {
    box-sizing: border-box;
}

.container-fluid {
    max-width: 100%;
    padding: 0;
}

/* Hero Banner */
.hero-banner {
    background: linear-gradient(135deg, var(--shopee-orange) 0%, var(--shopee-red) 100%);
    padding: 2rem 0;
    color: white;
}

.banner-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.banner-text {
    flex: 1;
    max-width: 50%;
}

.banner-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.highlight {
    color: #fff200;
}

.banner-subtitle {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.btn-shop-now {
    background: white;
    color: var(--shopee-orange);
    padding: 12px 24px;
    border-radius: 4px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-shop-now:hover {
    background: #f0f0f0;
    color: var(--shopee-orange);
    transform: translateY(-2px);
}

.banner-visual {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.banner-icon {
    font-size: 120px;
    opacity: 0.3;
}

.promo-badge {
    position: absolute;
    top: -20px;
    right: 20px;
    background: #fff200;
    color: var(--shopee-orange);
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    text-align: center;
    transform: rotate(15deg);
    animation: bounce 2s infinite;
}

.discount {
    display: block;
    font-size: 0.8rem;
}

.percentage {
    display: block;
    font-size: 1.2rem;
}

@keyframes bounce {
    0%, 100% { transform: rotate(15deg) translateY(0px); }
    50% { transform: rotate(15deg) translateY(-10px); }
}

/* Quick Categories */
.quick-categories {
    background: white;
    padding: 2rem 0;
    border-bottom: 1px solid var(--border-light);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    max-width: 1000px;
    margin: 0 auto;
}

.category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: var(--text-dark);
    padding: 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.category-item:hover {
    background: var(--background-light);
    color: var(--shopee-orange);
    transform: translateY(-2px);
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.category-icon img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
}

.category-icon i {
    font-size: 24px;
    color: var(--shopee-orange);
}

.category-name {
    font-size: 0.9rem;
    text-align: center;
    font-weight: 500;
}

/* Flash Sale */
.flash-sale {
    background: var(--shopee-orange);
    padding: 2rem 0;
    color: white;
}

.section-header-orange {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title-wrapper {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.timer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.timer-display {
    display: flex;
    gap: 2px;
}

.timer-unit {
    background: white;
    color: var(--shopee-orange);
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    min-width: 24px;
    text-align: center;
}

.view-all {
    color: white;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px solid white;
}

.products-slider {
    overflow-x: auto;
}

.products-row {
    display: flex;
    gap: 1rem;
    padding-bottom: 1rem;
}

.product-item {
    flex: 0 0 200px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease;
}

.product-item:hover {
    transform: translateY(-4px);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image img,
.product-placeholder {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder {
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 2rem;
}

.discount-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: var(--shopee-red);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
}

.stock-warning {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
}

.product-info {
    padding: 1rem;
    color: var(--text-dark);
}

.current-price {
    color: var(--shopee-orange);
    font-size: 1.1rem;
    font-weight: bold;
}

.product-sold {
    font-size: 0.8rem;
    color: var(--text-light);
    margin: 0.5rem 0;
}

.add-to-cart {
    background: var(--shopee-orange);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.add-to-cart:hover {
    background: var(--shopee-dark-orange);
}

.add-to-cart.disabled {
    background: var(--text-light);
    cursor: not-allowed;
}

/* Best Sellers */
.best-sellers {
    background: white;
    padding: 3rem 0;
}

.section-header-white {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-header-white .section-title {
    color: var(--text-dark);
    font-size: 1.5rem;
    font-weight: bold;
}

.section-header-white .view-all {
    color: var(--shopee-orange);
    text-decoration: none;
    font-weight: 500;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.product-card-modern {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border: 1px solid var(--border-light);
}

.product-card-modern:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-2px);
}

.product-image-wrapper {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card-modern:hover .product-image-wrapper img {
    transform: scale(1.05);
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

.product-card-modern:hover .product-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 1rem;
}

.btn-view,
.btn-cart {
    background: white;
    color: var(--shopee-orange);
    border: none;
    padding: 12px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.btn-view:hover,
.btn-cart:hover {
    background: var(--shopee-orange);
    color: white;
}

.product-badges {
    position: absolute;
    top: 12px;
    left: 12px;
}

.badge-new {
    background: var(--shopee-red);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: bold;
}

.product-details {
    padding: 1.5rem;
}

.product-name {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
    line-height: 1.4;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars {
    color: #ffc107;
}

.rating-count {
    color: var(--text-light);
    font-size: 0.8rem;
}

.product-price-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price {
    color: var(--shopee-orange);
    font-size: 1.1rem;
    font-weight: bold;
}

.location {
    color: var(--text-light);
    font-size: 0.8rem;
}

/* Mall Brands */
.mall-brands {
    background: var(--background-light);
    padding: 3rem 0;
}

.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.brand-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    text-decoration: none;
    color: var(--text-dark);
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.brand-card:hover {
    color: var(--text-dark);
    box-shadow: var(--shadow-hover);
    transform: translateY(-4px);
}

.brand-image {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
}

.brand-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.brand-placeholder {
    width: 100%;
    height: 100%;
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--shopee-orange);
    font-size: 2rem;
}

.brand-name {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.brand-description {
    color: var(--text-gray);
    margin-bottom: 1rem;
}

.brand-cta {
    color: var(--shopee-orange);
    font-weight: 500;
}

/* Bottom CTA */
.bottom-cta {
    background: linear-gradient(135deg, var(--shopee-orange) 0%, var(--shopee-red) 100%);
    color: white;
    padding: 3rem 0;
    text-align: center;
}

.cta-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.cta-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-primary-cta,
.btn-secondary-cta {
    padding: 12px 24px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary-cta {
    background: white;
    color: var(--shopee-orange);
}

.btn-primary-cta:hover {
    background: #f0f0f0;
    color: var(--shopee-orange);
    transform: translateY(-2px);
}

.btn-secondary-cta {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-secondary-cta:hover {
    background: white;
    color: var(--shopee-orange);
}

/* Responsive Design */
@media (max-width: 768px) {
    .banner-content {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }
    
    .banner-text {
        max-width: 100%;
    }
    
    .banner-title {
        font-size: 2rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }
    
    .category-item {
        padding: 0.5rem;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
    }
    
    .category-name {
        font-size: 0.8rem;
    }
    
    .section-header-orange,
    .section-header-white {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .section-title-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .brands-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-primary-cta,
    .btn-secondary-cta {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .product-item {
        flex: 0 0 160px;
    }
    
    .product-image {
        height: 160px;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-image-wrapper {
        height: 200px;
    }
    
    .overlay-actions {
        gap: 0.5rem;
    }
    
    .btn-view,
    .btn-cart {
        padding: 8px;
        font-size: 1rem;
    }
}
</style>
@endsection