<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'subscription' => null,
            'account_status' => 'approved', // 👈 FIX: Ubah ke 'approved'
        ]);

        User::create([
            'name' => 'Peserta Standar',
            'email' => 'peserta@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peserta',
            'subscription' => 'siswa_basic', // 👈 Direkomendasikan diisi jenis paket basic/premium
            'account_status' => 'approved', // 👈 FIX: Ubah ke 'approved'
        ]);
    }
}