<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\QuizResult;
use App\Models\Question;



class AdminPesertaController extends Controller
{
    // Menampilkan daftar peserta
    public function index()
    {
        // Mengambil user yang role-nya 'peserta' saja
        $peserta = User::where('role', 'peserta')->latest()->get();
$allResults = QuizResult::with(['quiz', 'user'])->latest()->get();

    return view('admin.peserta.index', compact('peserta', 'allResults'));    }

    // Menyimpan data peserta baru ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Create user baru dengan role otomatis 'peserta'
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => 'peserta',
        ]);

        return redirect()->back()->with('success', 'Akun peserta berhasil dibuat!');
    }

public function resetPassword($id)
{
    $peserta = User::findOrFail($id);
    
    $defaultPassword = 'password123'; // Kamu bisa ganti sesuai kemauan klien (misal: 12345678)
    
    $peserta->update([
        'password' => Hash::make($defaultPassword),
        'raw_password' => $defaultPassword
    ]);

    return back()->with('success', "Password peserta {$peserta->name} berhasil direset menjadi: {$defaultPassword}");
}

    // Mengubah data peserta
public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    // Jika admin juga menginput password baru
    if ($request->filled('password')) {
        $request->validate(['password' => 'string|min:6']);
        $user->update(['password' => decrypt($request->password)]); // atau Hash::make
    }

    return redirect()->back()->with('success', 'Data peserta berhasil diperbarui!');
}

// Menghapus peserta
public function destroy(User $user)
{
    $user->delete();
    return redirect()->back()->with('success', 'Akun peserta berhasil dihapus!');
}

public function showResultDetail(QuizResult $result)
{
    // Mengambil data kuis dan pertanyaan terkait
    $quiz = $result->quiz;
    $questions = Question::where('quiz_id', $quiz->id)->get();

    return view('admin.peserta.rekap-detail', compact('result', 'quiz', 'questions'));
}
}