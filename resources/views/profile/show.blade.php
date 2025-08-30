@extends('layouts.app')

@section('title', 'Profil Saya - Toko Makanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Profile Header -->
            <div class="profile-header text-center mb-4">
                <div class="avatar-container position-relative d-inline-block mb-3">
                    @if($customer->avatar)
                        <img src="{{ Storage::url($customer->avatar) }}" 
                             alt="Avatar" 
                             class="avatar rounded-circle border border-3 border-white shadow"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="avatar rounded-circle border border-3 border-white shadow d-flex align-items-center justify-content-center"
                             style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-orange), var(--primary-dark)); color: white; font-size: 3rem; font-weight: bold;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ $customer->name }}</h2>
                <p class="text-muted mb-0">
                    <i class="bi bi-envelope me-1"></i> {{ $customer->email }}
                </p>
                @if($customer->phone)
                    <p class="text-muted mb-0">
                        <i class="bi bi-telephone me-1"></i> {{ $customer->phone }}
                    </p>
                @endif
            </div>

            <!-- Profile Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            Informasi Profil
                        </h5>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit Profil
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nama Lengkap</label>
                            <div class="info-item">
                                <i class="bi bi-person text-primary me-2"></i>
                                {{ $customer->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Email</label>
                            <div class="info-item">
                                <i class="bi bi-envelope text-primary me-2"></i>
                                {{ $customer->email }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Nomor Telepon</label>
                            <div class="info-item">
                                <i class="bi bi-telephone text-primary me-2"></i>
                                {{ $customer->phone ?: 'Belum diisi' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-semibold">Bergabung Sejak</label>
                            <div class="info-item">
                                <i class="bi bi-calendar text-primary me-2"></i>
                                {{ $customer->created_at->format('d M Y') }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold">Alamat</label>
                            <div class="info-item">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                {{ $customer->address ?: 'Belum diisi' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-shield-lock text-warning me-2"></i>
                            Keamanan Akun
                        </h5>
                        <a href="{{ route('profile.password.edit') }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-key me-1"></i> Ubah Password
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="security-icon me-3">
                            <i class="bi bi-lock-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Password</h6>
                            <small class="text-muted">Password terakhir diubah pada {{ $customer->updated_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-lightning text-info me-2"></i>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-bag me-2"></i>
                                Lihat Pesanan Saya
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-cart3 me-2"></i>
                                Keranjang Belanja
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-grid me-2"></i>
                                Jelajahi Produk
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('products.featured') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-star me-2"></i>
                                Produk Unggulan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    background: var(--secondary);
    padding: 0.75rem;
    border-radius: var(--border-radius);
    border-left: 3px solid var(--primary-orange);
    font-weight: 500;
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-orange), var(--primary-dark));
    color: white;
    padding: 3rem 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem !important;
}

.card {
    transition: var(--transition);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover) !important;
}

.security-icon {
    width: 50px;
    height: 50px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection