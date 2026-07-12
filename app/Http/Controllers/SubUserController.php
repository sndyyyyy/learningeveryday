<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubUserController extends Controller
{
    // 1. Menampilkan daftar siswa yang terikat dengan instansi ini
public function index()
{
    // Jika Super Admin menembak rute ini, alihkan langsung
    if (auth()->user()->role === 'super_admin') {
        return redirect()->route('admin.quiz.index')->with('error', 'Super Admin tidak mengelola siswa instansi.');
    }

    $instansi = auth()->user();
    
    // 1. Ambil seluruh akun siswa terdaftar di instansi ini
    $students = User::where('instansi_id', $instansi->id)->latest()->get();
    $currentStudentCount = $students->count();

    // 2. Tarik histori pengerjaan kuis khusus murid instansi ini
    // Mengambil data dari QuizResult milik user yang instansi_id-nya cocok
    $studentIds = $students->pluck('id');
    $histories = \App\Models\QuizResult::whereIn('user_id', $studentIds)
                                      ->with(['user', 'quiz']) // Memuat data siswa dan kuis bersangkutan
                                      ->latest()
                                      ->get();

    return view('admin.students.index', compact('students', 'currentStudentCount', 'instansi', 'histories'));
}

    // 2. Menyimpan akun siswa baru dengan validasi kuota langganan
    public function store(Request $request)
    {
        $instansi = auth()->user();

        // HITUNG KUOTA KHUSUS INSTANSI BASIC
        if ($instansi->subscription === 'instansi_basic') {
            $currentCount = User::where('instansi_id', $instansi->id)->count();
            if ($currentCount >= 50) {
                return redirect()->back()->withErrors(['limit_reached' => 'Batas maksimal pendaftaran siswa untuk paket Instansi Basic (50 akun) telah tercapai. Silakan hubungi Super Admin untuk upgrade ke Paket Premium!']);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => 'peserta', // Otomatis role peserta
            'subscription' => null, // Murid instansi tidak perlu subscription mandiri
            'account_status' => 'approved', // Siswa bentukan instansi otomatis aktif (tidak perlu approval super admin lagi)
            'instansi_id' => $instansi->id, // Ikat ke instansi yang membuatnya
        ]);

        return redirect()->back()->with('success', 'Akun siswa berhasil didaftarkan dan siap digunakan!');
    }

    // 3. Menghapus akun siswa
    public function destroy(User $student)
    {
        // Pastikan hanya instansi pemilik yang bisa menghapus siswa ini
        if ($student->instansi_id !== auth()->id()) {
            abort(403, 'Tindakan tidak sah.');
        }

        $student->delete();
        return redirect()->back()->with('success', 'Akun siswa berhasil dihapus.');
    }
}