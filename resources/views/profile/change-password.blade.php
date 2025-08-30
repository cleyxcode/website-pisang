@extends('layouts.app')

@section('title', 'Ubah Password - Toko Makanan')

@push('styles')
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

.card {
    transition: var(--transition);
}

.btn:hover {
    transform: translateY(-1px);
}

.strength-bar {
    width: 100%;
}

.password-strength.weak .strength-fill {
    width: 33%;
    background-color: #dc3545;
}

.password-strength.medium .strength-fill {
    width: 66%;
    background-color: #ffc107;
}

.password-strength.strong .strength-fill {
    width: 100%;
    background-color: #28a745;
}

.match-text.match {
    color: #28a745;
}

.match-text.no-match {
    color: #dc3545;
}
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="mb-1 fw-bold">Ubah Password</h2>
                    <p class="text-muted mb-0">Pastikan password Anda kuat dan aman</p>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock text-warning me-2"></i>
                        Keamanan Password
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">
                                Password Saat Ini *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                <button type="button" class="btn btn-outline-secondary border-start-0" 
                                        onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Password Baru *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-key text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required
                                       onkeyup="checkPasswordStrength()">
                                <button type="button" class="btn btn-outline-secondary border-start-0" 
                                        onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="password_icon"></i>
                                </button>
                            </div>
                            
                            <!-- Password Strength Indicator -->
                            <div class="password-strength mt-2" id="password_strength" style="display: none;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="strength-bar bg-light rounded" style="height: 4px; flex: 1;">
                                        <div class="strength-fill rounded" style="height: 100%; transition: width 0.3s, background-color 0.3s;"></div>
                                    </div>
                                    <small class="strength-text text-muted"></small>
                                </div>
                            </div>
                            
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            
                            <!-- Password Requirements -->
                            <small class="text-muted">
                                Password harus minimal 8 karakter dan mengandung kombinasi huruf, angka, dan simbol.
                            </small>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Konfirmasi Password Baru *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-check-circle text-muted"></i>
                                </span>
                                <input type="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       onkeyup="checkPasswordMatch()">
                                <button type="button" class="btn btn-outline-secondary border-start-0" 
                                        onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                            <div id="password_match" class="mt-1" style="display: none;">
                                <small class="match-text"></small>
                            </div>
                        </div>

                        <!-- Security Tips -->
                        <div class="alert alert-info border-0 bg-light">
                            <h6 class="alert-heading">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Tips Keamanan
                            </h6>
                            <ul class="mb-0 small">
                                <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                <li>Hindari menggunakan informasi pribadi dalam password</li>
                                <li>Gunakan password yang berbeda untuk setiap akun</li>
                                <li>Jangan membagikan password Anda kepada siapapun</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning" id="submit_btn" disabled>
                                <i class="bi bi-shield-check me-1"></i> Ubah Password
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

.card {
    transition: var(--transition);
}

.btn:hover {
    transform: translateY(-1px);
}

.strength-bar {
    width: 100%;
}

.password-strength.weak .strength-fill {
    width: 33%;
    background-color: #dc3545;
}

.password-strength.medium .strength-fill {
    width: 66%;
    background-color: #ffc107;
}

.password-strength.strong .strength-fill {
    width: 100%;
    background-color: #28a745;
}

.match-text.match {
    color: #28a745;
}

.match-text.no-match {
    color: #dc3545;
}
</style>

@push('scripts')
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthDiv = document.getElementById('password_strength');
    const strengthText = strengthDiv.querySelector('.strength-text');
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }
    
    strengthDiv.style.display = 'block';
    
    let strength = 0;
    let feedback = '';
    
    // Length check
    if (password.length >= 8) strength++;
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength++;
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength++;
    
    // Number check
    if (/\d/.test(password)) strength++;
    
    // Special character check
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    
    // Remove previous strength classes
    strengthDiv.classList.remove('weak', 'medium', 'strong');
    
    if (strength < 3) {
        strengthDiv.classList.add('weak');
        feedback = 'Lemah';
        strengthText.className = 'strength-text text-danger';
    } else if (strength < 5) {
        strengthDiv.classList.add('medium');
        feedback = 'Sedang';
        strengthText.className = 'strength-text text-warning';
    } else {
        strengthDiv.classList.add('strong');
        feedback = 'Kuat';
        strengthText.className = 'strength-text text-success';
    }
    
    strengthText.textContent = feedback;
    checkFormValidity();
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('password_match');
    const matchText = matchDiv.querySelector('.match-text');
    
    if (confirmPassword.length === 0) {
        matchDiv.style.display = 'none';
        return;
    }
    
    matchDiv.style.display = 'block';
    
    if (password === confirmPassword) {
        matchText.textContent = '✓ Password cocok';
        matchText.className = 'match-text match';
    } else {
        matchText.textContent = '✗ Password tidak cocok';
        matchText.className = 'match-text no-match';
    }
    
    checkFormValidity();
}

function checkFormValidity() {
    const currentPassword = document.getElementById('current_password').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const submitBtn = document.getElementById('submit_btn');
    
    const isPasswordStrong = password.length >= 8 && 
                           /[a-z]/.test(password) && 
                           /[A-Z]/.test(password) && 
                           /\d/.test(password);
    
    const isPasswordMatch = password === confirmPassword;
    
    if (currentPassword && password && confirmPassword && isPasswordStrong && isPasswordMatch) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}

// Add event listeners
document.getElementById('current_password').addEventListener('keyup', checkFormValidity);
document.getElementById('password').addEventListener('keyup', function() {
    checkPasswordStrength();
    checkPasswordMatch();
});
document.getElementById('password_confirmation').addEventListener('keyup', checkPasswordMatch);
</script>
@endpush
@endsection