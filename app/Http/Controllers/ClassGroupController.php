<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use Illuminate\Http\Request;

class ClassGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $instansiId = auth()->id();

        // Cegah duplikasi nama kelas di instansi yang sama
        $exists = ClassGroup::where('instansi_id', $instansiId)
                            ->where('name', $request->name)
                            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Nama kelas tersebut sudah ada di daftar kelas Anda!');
        }

        ClassGroup::create([
            'instansi_id' => $instansiId,
            'name' => strtoupper($request->name), // Otomatis kapitalisasi
        ]);

        return redirect()->back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    public function destroy(ClassGroup $classGroup)
    {
        if ($classGroup->instansi_id !== auth()->id()) {
            abort(403, 'Tindakan tidak sah.');
        }

        $classGroup->delete();
        return redirect()->back()->with('success', 'Daftar kelas berhasil dihapus.');
    }
}