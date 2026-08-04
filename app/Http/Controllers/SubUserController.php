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

        $classGroups = \App\Models\ClassGroup::where('instansi_id', $instansi->id)->oldest('name')->get();

        $studentIds = $students->pluck('id');
        $histories = \App\Models\QuizResult::whereIn('user_id', $studentIds)
                                          ->with(['user', 'quiz']) 
                                          ->latest()
                                          ->get();

        return view('admin.students.index', compact('students', 'currentStudentCount', 'instansi', 'histories', 'classGroups'));
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

        // BISA USERNAME TANPA @ (Hanya validasi unique string)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'class_group' => 'nullable|string|max:50',
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
            'class_group' => $request->class_group,
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

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('school_logo')) {
            if ($user->school_logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->school_logo);
            }
            $data['school_logo'] = $request->file('school_logo')->store('instansi/logos', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil dan Logo Instansi berhasil diperbarui!');
    }
}