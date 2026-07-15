<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\QuizResult;
use App\Models\Question;
use App\Models\Quiz;



class AdminPesertaController extends Controller
{
    // Menampilkan daftar peserta

public function dashboardUtama()
{
    $user = auth()->user();
    $stats = [];

    if ($user->role === 'super_admin') {
        // 👑 STATISTIK PUSAT (SUPER ADMIN)
        $stats['total_instansi'] = User::where('role', 'admin')->count();
        $stats['total_peserta_mandiri'] = User::where('role', 'peserta')->whereNull('instansi_id')->count();
        $stats['total_kuis_pusats'] = Quiz::where('created_by', $user->id)->count();
        $stats['pending_approval'] = User::where('account_status', 'pending')->count();
    } else {
        // 🏫 STATISTIK INTERNAL SEKOLAH (ADMIN INSTANSI)
        $stats['total_siswa'] = User::where('instansi_id', $user->id)->count();
        $stats['total_kuis_mandiri'] = Quiz::where('created_by', $user->id)->count();
        
        // Hitung total ujian yang sudah disubmit oleh murid-muridnya
        $studentIds = User::where('instansi_id', $user->id)->pluck('id');
        $stats['total_ujian_diikuti'] = QuizResult::whereIn('user_id', $studentIds)->count();
        
        // Sisa kuota jika paket basic
        $stats['sisa_kuota'] = $user->subscription === 'instansi_basic' ? (50 - $stats['total_siswa']) : 'Tanpa Batas';
    }

    return view('admin.dashboard-utama', compact('stats'));
}

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
            'email' => 'required|string|max:255|unique:users',
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
        'email' => 'required|string|max:255|unique:users,email,' . $user->id,
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

public function importPeserta(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('excel_file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, "r")) !== FALSE) {
            // Lewati baris pertama (Header: Nama, Email, Password)
            fgetcsv($handle, 1000, ",");

            $importedCount = 0;
            $skippedCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Pastikan ada 3 kolom terisi
                if (count($data) >= 3) {
                    $name = trim($data[0]);
                    $email = trim($data[1]); // Bisa diisi Username / Email
                    $password = trim($data[2]);

                    if (!empty($name) && !empty($email) && !empty($password)) {
                        // Cek apakah email/username sudah pernah ada di database
                        $exists = User::where('email', $email)->exists();
                        
                        if (!$exists) {
                            User::create([
                                'name' => $name,
                                'email' => $email,
                                'password' => Hash::make($password),
                                'raw_password' => $password,
                                'role' => 'peserta',
                            ]);
                            $importedCount++;
                        } else {
                            $skippedCount++; // Lewati jika duplikat
                        }
                    }
                }
            }
            fclose($handle);
            
            $msg = "Berhasil mengimpor {$importedCount} akun peserta baru!";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} akun dilewati karena email/username sudah terdaftar).";
            }

            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Gagal membaca file CSV.');
    }
}