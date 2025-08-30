<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
class ProfileController extends Controller
{
    /**
     * Display the customer's profile
     */
    public function show()
    {
        $customer = Auth::guard('customer')->user();
        return view('profile.show', compact('customer'));
    }

    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $customer = Auth::guard('customer')->user();
        return view('profile.edit', compact('customer'));
    }

    /**
     * Update the customer's profile information
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $customer->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Show the form for changing password
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the customer's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:customer'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $customer = Auth::guard('customer')->user();
        $customer->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.show')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar()
    {
        $customer = Auth::guard('customer')->user();
        
        if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
            Storage::disk('public')->delete($customer->avatar);
            $customer->update(['avatar' => null]);
        }

        return redirect()->back()->with('success', 'Foto profile berhasil dihapus!');
    }
}