<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function register(Request $request)
    {
        // USERNAME/EMAIL BISA SENSITIF & TANPA FORMAT @ WAKTU REGISTRASI
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'subscription' => 'required|in:siswa_basic,siswa_premium,siswa_khusus,instansi_basic,instansi_premium',
        ]);

        $role = str_contains($request->subscription, 'instansi') ? 'admin' : 'peserta';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => $role,
            'subscription' => $request->subscription,
            'account_status' => 'pending',
            'instansi_id' => null
        ]);

        $namaPaket = str_replace('_', ' ', strtoupper($request->subscription));
        $textMessage = "Halo Admin Pusat, saya ingin mengonfirmasi pengajuan registrasi akun di learningeveryday.\n\n"
                     . "Nama / Instansi: " . $request->name . "\n"
                     . "Email / Username: " . $request->email . "\n"
                     . "Paket Langganan: " . $namaPaket . "\n\n"
                     . "Mohon untuk segera diperiksa berkas administrasi dan diaktifkan. Terima kasih!";

        return redirect()->back()->with([
            'success' => 'Pengajuan akun berhasil dikirim! Silakan lakukan aktivasi dan pemeriksaan berkas administrasi sebelum melakukan login.',
            'wa_text' => urlencode($textMessage)
        ]);
    }

    public function login(Request $request)
    {
        // DUKUNG LOGIN DENGAN USERNAME TANPA @ (SENSITIF HURUFU BESAR/KECIL)
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        // Cek pencocokan username/email secara persis
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->account_status === 'pending') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Akun Anda belum aktif. Mohon tunggu proses verifikasi/approval dari Super Admin.']);
            }
            if ($user->account_status === 'rejected') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Pengajuan registrasi akun Anda ditolak oleh Super Admin.']);
            }
        }

        // Gunakan kredensial pencocokan email/username
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'super_admin' || Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard.utama');
            }
            return redirect()->intended('/peserta/dashboard');
        }

        return redirect()->back()->withInput()->withErrors(['email' => 'Kombinasi Username/Email dan password tidak cocok.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}