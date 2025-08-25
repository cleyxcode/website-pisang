<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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