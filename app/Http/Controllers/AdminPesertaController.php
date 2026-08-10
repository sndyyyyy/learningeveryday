<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\QuizResult;
use App\Models\Question;
use App\Models\Quiz;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PesertaImport;

class AdminPesertaController extends Controller
{
    public function dashboardUtama()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->role === 'super_admin') {
            $stats['total_instansi'] = User::where('role', 'admin')->count();
            $stats['total_peserta_mandiri'] = User::where('role', 'peserta')->whereNull('instansi_id')->count();
            $stats['total_kuis_pusats'] = Quiz::where('created_by', $user->id)->count();
            $stats['pending_approval'] = User::where('account_status', 'pending')->count();
        } else {
            $stats['total_siswa'] = User::where('instansi_id', $user->id)->count();
            $stats['total_kuis_mandiri'] = Quiz::where('created_by', $user->id)->count();
            
            $studentIds = User::where('instansi_id', $user->id)->pluck('id');
            $stats['total_ujian_diikuti'] = QuizResult::whereIn('user_id', $studentIds)->count();
            
            $stats['sisa_kuota'] = $user->subscription === 'instansi_basic' ? (50 - $stats['total_siswa']) : 'Tanpa Batas';
        }

        return view('admin.dashboard-utama', compact('stats'));
    }

    public function index()
    {
        // 1. Peserta Mandiri (Yang dibuat oleh Super Admin / mendaftar mandiri)
        $pesertaMandiri = User::where('role', 'peserta')
                              ->whereNull('instansi_id')
                              ->latest()
                              ->get();

        // 2. Daftar Instansi beserta relasi siswa binaannya
        $instansiList = User::where('role', 'admin')
                            ->with(['students' => function($q) {
                                $q->latest();
                            }])
                            ->latest()
                            ->get();

        $allResults = QuizResult::with(['quiz', 'user'])->latest()->get();

        return view('admin.peserta.index', compact('pesertaMandiri', 'instansiList', 'allResults'));
    }

    // Store Peserta Mandiri Baru
    public function store(Request $request)
    {
        // Validasi bebas username (tanpa wajib @)
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
            'account_status' => 'approved',
            'instansi_id' => null, // Mandiri / Pusat
        ]);

        return redirect()->back()->with('success', 'Akun peserta pusat berhasil dibuat!');
    }

    public function resetPassword($id)
    {
        $peserta = User::findOrFail($id);
        $defaultPassword = 'password123';
        
        $peserta->update([
            'password' => Hash::make($defaultPassword),
            'raw_password' => $defaultPassword
        ]);

        return back()->with('success', "Password peserta {$peserta->name} berhasil direset menjadi: {$defaultPassword}");
    }

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

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $user->update([
                'password' => Hash::make($request->password),
                'raw_password' => $request->password
            ]);
        }

        return redirect()->back()->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Akun peserta berhasil dihapus!');
    }

    public function showResultDetail(QuizResult $result)
    {
        $quiz = $result->quiz;
        $questions = Question::where('quiz_id', $quiz->id)->get();

        return view('admin.peserta.rekap-detail', compact('result', 'quiz', 'questions'));
    }

    // public function importPeserta(Request $request)
    // {
    //     $request->validate([
    //         'excel_file' => 'required|file|mimes:csv,txt|max:2048'
    //     ]);

    //     $file = $request->file('excel_file');
    //     $path = $file->getRealPath();

    //     if (($handle = fopen($path, "r")) !== FALSE) {
    //         fgetcsv($handle, 1000, ",");

    //         $importedCount = 0;
    //         $skippedCount = 0;

    //         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //             if (count($data) >= 3) {
    //                 $name = trim($data[0]);
    //                 $email = trim($data[1]);
    //                 $password = trim($data[2]);

    //                 if (!empty($name) && !empty($email) && !empty($password)) {
    //                     $exists = User::where('email', $email)->exists();
                        
    //                     if (!$exists) {
    //                         User::create([
    //                             'name' => $name,
    //                             'email' => $email,
    //                             'password' => Hash::make($password),
    //                             'raw_password' => $password,
    //                             'role' => 'peserta',
    //                             'account_status' => 'approved',
    //                             'instansi_id' => null,
    //                         ]);
    //                         $importedCount++;
    //                     } else {
    //                         $skippedCount++;
    //                     }
    //                 }
    //             }
    //         }
    //         fclose($handle);
            
    //         $msg = "Berhasil mengimpor {$importedCount} akun peserta pusat baru!";
    //         if ($skippedCount > 0) {
    //             $msg .= " ({$skippedCount} akun dilewati karena username/email sudah terdaftar).";
    //         }

    //         return redirect()->back()->with('success', $msg);
    //     }

    //     return redirect()->back()->with('error', 'Gagal membaca file CSV.');
    // }


    public function importPeserta(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new PesertaImport, $request->file('excel_file'));
            return redirect()->back()->with('success', 'Data peserta berhasil diimpor dari file Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor file Excel. Pastikan format kolom sesuai.');
        }
    }

    public function updateInstansiProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('school_logo')) {
            if ($user->school_logo) {
                \Storage::disk('public')->delete($user->school_logo);
            }
            $data['school_logo'] = $request->file('school_logo')->store('instansi/logos', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil dan Logo Sekolah berhasil diperbarui!');
    }
}