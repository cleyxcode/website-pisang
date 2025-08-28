<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->email
        ]);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cari customer berdasarkan email
        $customer = Customer::where('email', $request->email)->first();
        
        if (!$customer) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar.']
            ]);
        }

        if (!$customer->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah dinonaktifkan. Silakan hubungi admin.']
            ]);
        }

        // Coba login dengan guard customer
        if (Auth::guard('customer')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true
        ], $request->boolean('remember'))) {
            
            $request->session()->regenerate();
            
            return redirect()->intended(route('home'))
                ->with('success', 'Login berhasil! Selamat datang, ' . $customer->name);
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.']
        ]);
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // Buat customer baru
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Akan di-hash otomatis
                'is_active' => true
            ]);

            // Login otomatis setelah register
            Auth::guard('customer')->login($customer);

            return redirect()->route('home')
                ->with('success', 'Registrasi berhasil! Selamat datang, ' . $customer->name);

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        try {
            $customer = Customer::where('email', $request->email)->first();
            
            if (!$customer->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Akun Anda telah dinonaktifkan. Silakan hubungi admin.']
                ]);
            }

            // Generate OTP
            $otp = $customer->generatePasswordResetToken();

            // Kirim email OTP
            Mail::to($customer->email)->send(new PasswordResetOtpMail($otp, $customer->name));

            return redirect()->route('password.reset', ['email' => $customer->email])
                ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox/spam folder.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal mengirim kode OTP. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
            'otp.size' => 'Kode OTP harus 6 digit.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        try {
            $customer = Customer::where('email', $request->email)->first();

            // Verifikasi OTP
            if (!$customer->verifyPasswordResetToken($request->otp)) {
                throw ValidationException::withMessages([
                    'otp' => ['Kode OTP tidak valid atau sudah kadaluarsa.']
                ]);
            }

            // Update password
            $customer->update([
                'password' => $request->password
            ]);

            // Clear token reset password
            $customer->clearPasswordResetToken();

            return redirect()->route('login')
                ->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal mengubah password. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        $customerName = Auth::guard('customer')->user()->name;
        
        Auth::guard('customer')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')
            ->with('success', 'Sampai jumpa lagi, ' . $customerName . '!');
    }
}