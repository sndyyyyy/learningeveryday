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

    // Memproses data inputan Sign Up pendaftar baru
// Memproses data inputan Sign Up pendaftar baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'subscription' => 'required|in:siswa_basic,siswa_premium,siswa_khusus,instansi_basic,instansi_premium',
        ]);

        // Atur Role dasar secara otomatis berdasarkan pilihan jenis paket langganan
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

        // Membuat format teks draf pesan WhatsApp agar rapi (menggunakan urlencode)
        $namaPaket = str_replace('_', ' ', strtoupper($request->subscription));
        $textMessage = "Halo Admin Pusat, saya ingin mengonfirmasi pengajuan registrasi akun di learningeveryday.\n\n"
                     . "Nama / Instansi: " . $request->name . "\n"
                     . "Email / Username: " . $request->email . "\n"
                     . "Paket Langganan: " . $namaPaket . "\n\n"
                     . "Mohon untuk segera diperiksa berkas administrasi dan diaktifkan. Terima kasih!";

        // Kembalikan ke halaman sebelumnya dengan sukses teks pesan WA
        return redirect()->back()->with([
            'success' => 'Pengajuan akun berhasil dikirim! Silakan lakukan aktivasi dan pemeriksaan berkas administrasi sebelum melakukan login.',
            'wa_text' => urlencode($textMessage)
        ]);
    }

    // Penyesuaian Interseptor Login Eksisting
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari tahu apakah user terdaftar
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // CEGAT JIKA AKUN MASIH PENDING ATAU REJECTED
            if ($user->account_status === 'pending') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Akun Anda belum aktif. Mohon tunggu proses verifikasi/approval dari Super Admin.']);
            }
            if ($user->account_status === 'rejected') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Pengajuan registrasi akun Anda ditolak oleh Super Admin.']);
            }
        }

        // Jalankan alur login bawaan seperti biasa jika status 'approved'
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Pengalihan dashboard dinamis sesuai role
            if (Auth::user()->role === 'super_admin' || Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard.utama'); // Atau rute dashboard admin-mu
            }
            return redirect()->intended('/peserta/dashboard');
        }

        return redirect()->back()->withInput()->withErrors(['email' => 'Kombinasi email dan password tidak cocok.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
