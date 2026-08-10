<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SpecialTest;
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
        // TARIK SELURUH TES KHUSUS DARI MASTER DATA
        $specialTests = SpecialTest::oldest('name')->get();
        return view('auth.register', compact('specialTests'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'subscription' => 'required|string',
        ]);

        $subscription = $request->subscription;
        $specialTestId = null;

        // CEK APAKAH USER MEMILIH PAKET TES KHUSUS (Contoh input: khusus_1, khusus_2)
        if (str_starts_with($subscription, 'khusus_')) {
            $specialTestId = str_replace('khusus_', '', $subscription);
            $subscription = 'siswa_khusus';
        }

        $role = str_contains($subscription, 'instansi') ? 'admin' : 'peserta';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => $role,
            'subscription' => $subscription,
            'special_test_id' => $specialTestId, // 👈 KUNCI KE TERGABUNGNYA TES KHUSUS
            'account_status' => 'pending',
            'instansi_id' => null
        ]);

        $namaPaket = str_replace('_', ' ', strtoupper($subscription));
        if ($specialTestId) {
            $st = SpecialTest::find($specialTestId);
            if ($st) { $namaPaket .= " (" . $st->name . ")"; }
        }

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
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->account_status === 'pending') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Akun Anda belum aktif. Mohon tunggu proses verifikasi/approval dari Super Admin.']);
            }
            if ($user->account_status === 'rejected') {
                return redirect()->back()->withInput()->withErrors(['email' => 'Pengajuan registrasi akun Anda ditolak oleh Super Admin.']);
            }
        }

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