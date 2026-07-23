<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubUserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'super_admin') {
            return redirect()->route('admin.quiz.index')->with('error', 'Super Admin tidak mengelola siswa instansi.');
        }

        $instansi = auth()->user();
        $students = User::where('instansi_id', $instansi->id)->latest()->get();
        $currentStudentCount = $students->count();

        $studentIds = $students->pluck('id');
        $histories = \App\Models\QuizResult::whereIn('user_id', $studentIds)
                                          ->with(['user', 'quiz']) 
                                          ->latest()
                                          ->get();

        return view('admin.students.index', compact('students', 'currentStudentCount', 'instansi', 'histories'));
    }

    public function store(Request $request)
    {
        $instansi = auth()->user();

        if ($instansi->subscription === 'instansi_basic') {
            $currentCount = User::where('instansi_id', $instansi->id)->count();
            if ($currentCount >= 50) {
                return redirect()->back()->withErrors(['limit_reached' => 'Batas maksimal pendaftaran siswa untuk paket Instansi Basic (50 akun) telah tercapai.']);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'class_group' => 'nullable|string|max:50', // 👈 VALIDASI BARU
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'raw_password' => $request->password,
            'role' => 'peserta', 
            'subscription' => null, 
            'account_status' => 'approved', 
            'instansi_id' => $instansi->id, 
            'class_group' => $request->class_group, // 👈 SIMPAN DATA KELAS SISWA
        ]);

        return redirect()->back()->with('success', 'Akun siswa berhasil didaftarkan dan siap digunakan!');
    }

    public function destroy(User $student)
    {
        if ($student->instansi_id !== auth()->id()) {
            abort(403, 'Tindakan tidak sah.');
        }

        $student->delete();
        return redirect()->back()->with('success', 'Akun siswa berhasil dihapus.');
    }
}