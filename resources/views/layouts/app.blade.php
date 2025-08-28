<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Makanan')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-orange: #ff5722;
            --primary-dark: #e64a19;
            --primary-light: #ffab91;
            --secondary: #f8f9fa;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border: #dee2e6;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --white: #ffffff;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.15);
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: #f5f5f5;
        }

        /* Navbar Styles */
        .navbar {
            background: var(--white) !important;
            box-shadow: var(--shadow);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 700 !important;
            font-size: 1.5rem;
            color: var(--primary-orange) !important;
            transition: var(--transition);
        }

        .navbar-brand:hover {
            color: var(--primary-dark) !important;
            transform: scale(1.05);
        }

        .navbar-brand i {
            margin-right: 0.5rem;
            font-size: 1.6rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            padding: 0.75rem 1rem !important;
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
            margin: 0 0.25rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-orange) !important;
            background-color: rgba(255, 87, 34, 0.1);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-orange) !important;
            background-color: rgba(255, 87, 34, 0.1);
            font-weight: 600;
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background-color: var(--primary-orange);
            border-radius: 2px;
        }

        .navbar-nav .nav-link i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        /* Cart Badge */
        .cart-container {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, var(--primary-orange), var(--primary-dark));
            color: var(--white);
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(255, 87, 34, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-hover);
            border-radius: var(--border-radius);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background-color: rgba(255, 87, 34, 0.1);
            color: var(--primary-orange);
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            width: 16px;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange), var(--primary-dark));
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(255, 87, 34, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #d84315);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(255, 87, 34, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-orange);
            color: var(--primary-orange);
            font-weight: 600;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            transform: translateY(-2px);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            box-shadow: var(--shadow);
            border-left: 4px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left-color: var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left-color: var(--danger);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border-left-color: var(--warning);
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border-left-color: var(--info);
        }

        /* Product Card Styles */
        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        /* Mobile Navbar Toggle */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: var(--border-radius);
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: 2px solid var(--primary-orange);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: var(--white);
            margin-top: 4rem;
            padding: 3rem 0 1rem;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-orange), var(--primary-dark));
        }

        .footer h5 {
            color: var(--primary-orange);
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer h5 i {
            font-size: 1.5rem;
        }

        .footer h6 {
            color: var(--primary-light);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 0.5rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--primary-orange);
            text-decoration: underline;
        }

        .footer hr {
            border-color: rgba(255, 255, 255, 0.2);
            margin: 2rem 0 1rem;
        }

        .footer-bottom {
            background: rgba(0, 0, 0, 0.2);
            margin: 2rem -15px -1rem;
            padding: 1rem 15px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contact-item i {
            color: var(--primary-orange);
            width: 20px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.75rem 0;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .navbar-nav .nav-link {
                padding: 0.5rem 1rem !important;
                margin: 0.25rem 0;
            }

            .footer {
                margin-top: 2rem;
                padding: 2rem 0 1rem;
            }

            .footer h5 {
                font-size: 1.2rem;
            }

            .footer p {
                font-size: 0.9rem;
            }

            .contact-info {
                margin-top: 1rem;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .navbar-brand i {
                font-size: 1.3rem;
            }

            .footer {
                text-align: center;
            }

            .contact-item {
                justify-content: center;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-orange);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Focus States for Accessibility */
        .navbar-nav .nav-link:focus,
        .btn:focus {
            outline: 2px solid var(--primary-orange);
            outline-offset: 2px;
        }

        /* Animation for page transitions */
        main {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-shop"></i> Toko Makanan
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-fill"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-grid-fill"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.featured') ? 'active' : '' }}" href="{{ route('products.featured') }}">
                            <i class="bi bi-star-fill"></i> Produk Unggulan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                            <i class="bi bi-clock-history"></i> Pesanan Saya
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth('customer')
                        <li class="nav-item">
                            <a class="nav-link cart-container" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3"></i> Keranjang
                                <span class="cart-badge" id="cart-count" style="display: none;">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ Auth::guard('customer')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-person"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="bi bi-bag"></i> Pesanan Saya
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Daftar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show m-0" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show m-0" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="bi bi-shop"></i> Toko Makanan</h5>
                    <p>Menyediakan berbagai macam makanan ringan berkualitas dengan harga terjangkau. Kepuasan pelanggan adalah prioritas utama kami.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Menu</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('products.index') }}">Produk</a></li>
                        <li><a href="{{ route('products.featured') }}">Produk Unggulan</a></li>
                        @auth('customer')
                            <li><a href="{{ route('orders.index') }}">Pesanan Saya</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6>Informasi</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6>Hubungi Kami</h6>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="bi bi-whatsapp"></i>
                            <span>+62 821-9857-9298</span>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-envelope"></i>
                            <span>info@tokomakanan.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-geo-alt"></i>
                            <span>Ambon, Maluku, Indonesia</span>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-clock"></i>
                            <span>24/7 Customer Service</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <small>&copy; {{ date('Y') }} Toko Makanan. All rights reserved.</small>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <small>Made with <i class="bi bi-heart-fill text-danger"></i> in Indonesia</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Update cart count on page load (only for authenticated customers)
        $(document).ready(function() {
            @auth('customer')
                updateCartCount();
            @endauth
            
            // Auto hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        function updateCartCount() {
            @auth('customer')
                $.get('{{ route("cart.count") }}', function(response) {
                    const count = response.count;
                    const badge = $('#cart-count');
                    
                    if (count > 0) {
                        badge.text(count).show();
                    } else {
                        badge.hide();
                    }
                }).fail(function() {
                    console.log('Failed to update cart count');
                });
            @endauth
        }

        // Add to cart function with loading state
        function addToCart(productId, quantity = 1) {
            @guest('customer')
                showAlert('warning', 'Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang!');
                setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
                return;
            @endguest
            
            @auth('customer')
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="loading-spinner"></span> Menambahkan...';
                button.disabled = true;
                
                $.post('{{ route("cart.add") }}', {
                    product_id: productId,
                    quantity: quantity
                })
                .done(function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        updateCartCount();
                    } else {
                        showAlert('danger', response.message);
                    }
                })
                .fail(function(xhr) {
                    let message = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                })
                .always(function() {
                    // Restore button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            @endauth
        }

        // Enhanced alert function with icons
        function showAlert(type, message) {
            const icons = {
                'success': 'bi-check-circle',
                'danger': 'bi-exclamation-triangle', 
                'warning': 'bi-exclamation-circle',
                'info': 'bi-info-circle'
            };
            
            const alert = `
                <div class="alert alert-${type} alert-dismissible fade show m-0" role="alert">
                    <i class="bi ${icons[type]} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert after navbar
            $('nav').after(alert);
            
            // Auto hide alert after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        }

        // Handle protected action clicks
        function requireLogin(url) {
            @guest('customer')
                showAlert('warning', 'Anda harus login terlebih dahulu untuk mengakses fitur ini!');
                setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
                return false;
            @endguest
            
            @auth('customer')
                window.location.href = url;
            @endauth
        }

        // Smooth scroll to top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Add scroll to top button
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                if (!$('#scroll-top').length) {
                    $('body').append(`
                        <button id="scroll-top" onclick="scrollToTop()" 
                                style="position: fixed; bottom: 20px; right: 20px; 
                                       background: var(--primary-orange); color: white; 
                                       border: none; border-radius: 50%; width: 50px; height: 50px; 
                                       box-shadow: var(--shadow); cursor: pointer; z-index: 1000;
                                       transition: var(--transition);">
                            <i class="bi bi-arrow-up"></i>
                        </button>
                    `);
                }
            } else {
                $('#scroll-top').remove();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>