<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Modifikasi kolom role lama agar mendukung super_admin
            $table->string('role')->default('peserta')->change();

            // 2. Tambah model jenis subscribe pendaftar
            $table->string('subscription')->nullable()->after('role'); 
            // Isinya nanti: 'siswa_basic', 'siswa_premium', 'instansi_basic', 'instansi_premium'

            // 3. Tambah status gerbang approval akun
            $table->string('account_status')->default('approved')->after('subscription'); 
            // Default 'approved' supaya akun admin/peserta lama kamu tidak terkunci otomatis. 
            // Nanti pendaftar baru lewat Sign Up otomatis berstatus 'pending'.

            // 4. Tambah relasi pengikat antara akun murid dengan instansi induknya
            $table->foreignId('instansi_id')->nullable()->after('account_status')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['instansi_id']);
            $table->dropColumn(['subscription', 'account_status', 'instansi_id']);
        });
    }
};