@extends('layouts.app')

@section('title', 'Lupa Password - Toko Makanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="bi bi-key"></i> Lupa Password</h4>
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

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Masukkan alamat email Anda dan kami akan mengirimkan kode OTP untuk reset password.
                    </div>

                    <form method="POST" action="{{ route('password.send-otp') }}" id="forgotPasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   id="email"
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   required
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan email yang terdaftar di akun Anda</small>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary" id="sendOtpBtn">
                                <i class="bi bi-envelope"></i> Kirim Kode OTP
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
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
    $('#forgotPasswordForm').on('submit', function() {
        $('#sendOtpBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-2"></i>Mengirim...');
    });
});
</script>
@endpush