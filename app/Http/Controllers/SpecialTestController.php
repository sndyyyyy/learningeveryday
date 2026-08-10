<?php

namespace App\Http\Controllers;

use App\Models\SpecialTest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SpecialTestController extends Controller
{
    public function index()
    {
        // Tarik seluruh jenis tes beserta data peserta & kuis di dalamnya
        $specialTests = SpecialTest::with(['users', 'quizzes'])->latest()->get();

        return view('admin.special-tests.index', compact('specialTests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SpecialTest::create([
            'name' => $request->name,
            'code' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Jenis Tes Khusus baru berhasil ditambahkan!');
    }

    public function destroy(SpecialTest $specialTest)
    {
        $specialTest->delete();
        return redirect()->back()->with('success', 'Jenis Tes Khusus berhasil dihapus.');
    }

    // Pendaftaran Akun Peserta Khusus Langsung Ke Tes Spesifik
    public function storeParticipant(Request $request, SpecialTest $specialTest)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => 'peserta',
            'subscription' => 'siswa_khusus',
            'account_status' => 'approved',
            'special_test_id' => $specialTest->id, // Terikat ke jenis tes ini
        ]);

        return redirect()->back()->with('success', "Peserta berhasil didaftarkan untuk {$specialTest->name}!");
    }
}