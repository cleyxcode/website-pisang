@extends('layouts.app')

@section('title', 'Edit Profil - Toko Makanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="mb-1 fw-bold">Edit Profil</h2>
                    <p class="text-muted mb-0">Perbarui informasi profil Anda</p>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Avatar Section -->
                        <div class="text-center mb-4">
                            <div class="avatar-upload-container position-relative d-inline-block">
                                <div class="current-avatar mb-3">
                                    @if($customer->avatar)
                                        <img src="{{ Storage::url($customer->avatar) }}" 
                                             alt="Avatar" 
                                             id="avatar-preview"
                                             class="rounded-circle border border-3 border-primary shadow"
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    @else
                                        <div id="avatar-preview" 
                                             class="rounded-circle border border-3 border-primary shadow d-flex align-items-center justify-content-center"
                                             style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-orange), var(--primary-dark)); color: white; font-size: 3rem; font-weight: bold;">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="avatar-upload-actions">
                                    <label for="avatar" class="btn btn-primary btn-sm me-2">
                                        <i class="bi bi-camera me-1"></i> Pilih Foto
                                    </label>
                                    @if($customer->avatar)
                                        <form action="{{ route('profile.avatar.delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('Hapus foto profil?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*">
                                    <small class="d-block text-muted mt-2">
                                        Format: JPG, PNG, GIF. Maksimal 2MB
                                    </small>
                                </div>
                            </div>
                            @error('avatar')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $customer->name) }}" 
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $customer->email) }}" 
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Nomor Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-telephone text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $customer->phone) }}"
                                           placeholder="Contoh: +62 821-xxxx-xxxx">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Alamat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                        <i class="bi bi-geo-alt text-muted"></i>
                                    </span>
                                    <textarea class="form-control border-start-0 @error('address') is-invalid @enderror" 
                                              id="address" 
                                              name="address" 
                                              rows="3"
                                              placeholder="Masukkan alamat lengkap Anda">{{ old('address', $customer->address) }}</textarea>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.form-control {
    border: 1px solid #dee2e6;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 0.2rem rgba(255, 87, 34, 0.25);
}

.avatar-upload-container {
    transition: var(--transition);
}

.avatar-upload-container:hover {
    transform: scale(1.02);
}

.card {
    transition: var(--transition);
}

.btn:hover {
    transform: translateY(-1px);
}
</style>

<script>
document.getElementById('avatar').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Avatar';
                img.id = 'avatar-preview';
                img.className = 'rounded-circle border border-3 border-primary shadow';
                img.style.cssText = 'width: 120px; height: 120px; object-fit: cover;';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection