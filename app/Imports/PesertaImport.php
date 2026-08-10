<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Ambil Nama Lengkap
        $name = trim(
            $row['nama_lengkap'] ?? 
            $row['nama'] ?? 
            reset($row) ?? ''
        );

        // 2. Ambil Email / Username (Dukungan variasi slug Maatwebsite)
        $email = trim(
            $row['emailusername'] ?? 
            $row['email_username'] ?? 
            $row['email'] ?? 
            $row['username'] ?? ''
        );

        // 3. Ambil Password
        $password = trim(
            $row['password'] ?? 
            $row['pass'] ?? ''
        );

        // Jika salah satu kolom utama kosong, lewati
        if (empty($name) || empty($email) || empty($password)) {
            return null;
        }

        // Jika email/username sudah terdaftar di DB, lewati agar tidak error
        if (User::where('email', $email)->exists()) {
            return null;
        }

        // Simpan ke database
        return new User([
            'name'           => $name,
            'email'          => $email,
            'password'       => Hash::make($password),
            'raw_password'   => $password,
            'role'           => 'peserta',
            'account_status' => 'approved',
            'instansi_id'     => null, // Set null untuk peserta pusat
        ]);
    }
}