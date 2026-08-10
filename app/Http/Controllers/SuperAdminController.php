<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    // 1. Menampilkan Halaman Daftar Pengajuan Akun Belum Di-approve
    public function approvalIndex()
    {
        // Ambil pengguna yang status akunnya masih pending
        $pendingUsers = User::where('account_status', 'pending')->latest()->get();
        
        return view('admin.approval.index', compact('pendingUsers'));
    }

    // 2. Aksi untuk Menyetujui Pendaftar (Approve)
    public function approve(User $user)
    {
        $user->update([
            'account_status' => 'approved'
        ]);

        return redirect()->back()->with('success', "Akun dari {$user->name} berhasil disetujui! Sekarang user tersebut sudah bisa login.");
    }

    // 3. Aksi untuk Menolak Pendaftar (Reject)
    public function reject(User $user)
    {
        $name = $user->name;
        
        // Hapus baris user dari database agar email/username bisa didaftarkan ulang
        $user->delete();

        return redirect()->back()->with('success', "Pengajuan akun dari {$name} telah ditolak dan datanya berhasil dibersihkan.");
    }
}