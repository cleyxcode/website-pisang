@extends('layouts.app')

@section('title', 'Reset Password - Toko Makanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="bi bi-shield-lock"></i> Reset Password</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="bi bi-clock"></i>
                        Kode OTP telah dikirim ke <strong>{{ $email }}</strong>. Kode berlaku selama 15 menit.
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <div class="mb-3">
                            <label for="otp" class="form-label">Kode OTP</label>
                            <input type="text" 
                                   id="otp"
                                   name="otp" 
                                   class="form-control @error('otp') is-invalid @enderror text-center" 
                                   value="{{ old('otp') }}" 
                                   required
                                   maxlength="6"
                                   style="font-size: 18px; letter-spacing: 5px;"
                                   autofocus>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan 6 digit kode OTP dari email</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" 
                                   id="password"
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required
                                   minlength="6">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" 
                                   id="password_confirmation"
                                   name="password_confirmation" 
                                   class="form-control" 
                                   required
                                   minlength="6">
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success" id="resetBtn">
                                <i class="bi bi-shield-check"></i> Update Password
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-2">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-clockwise"></i> Kirim Ulang Kode OTP
                            </a>
                        </p>
                        <p>
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Kembali ke Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Format input OTP (hanya angka)
    $('#otp').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Submit form handling
    $('#resetPasswordForm').on('submit', function() {
        $('#resetBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...');
    });
    
    // Validasi password confirmation
    $('#password_confirmation').on('keyup', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (password !== confirmation && confirmation.length > 0) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Password tidak cocok</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Auto focus ke password setelah OTP diisi
    $('#otp').on('input', function() {
        if ($(this).val().length === 6) {
            $('#password').focus();
        }
    });
});
</script>
@endpush