<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MATIKAN PENGECEKAN FOREIGN KEY SEMENTARA (Mencegah Error 1451)
        Schema::disableForeignKeyConstraints();

        // 2. HAPUS TABEL SPESIAL DAHULU JIKA ADA TERSISA
        Schema::dropIfExists('special_tests');

        // 3. BUAT TABEL SPECIAL TESTS
        Schema::create('special_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. TAMBAHKAN KOLOM KE TABEL QUIZZES (Cek dulu apakah kolom sudah ada)
        if (!Schema::hasColumn('quizzes', 'special_test_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreignId('special_test_id')->nullable()->after('tier_access')->constrained('special_tests')->nullOnDelete();
            });
        }

        // 5. TAMBAHKAN KOLOM KE TABEL USERS (Cek dulu apakah kolom sudah ada)
        if (!Schema::hasColumn('users', 'special_test_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('special_test_id')->nullable()->after('subscription')->constrained('special_tests')->nullOnDelete();
            });
        }

        // 6. NYALAKAN KEMBALI PENGECEKAN FOREIGN KEY
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['special_test_id']);
            $table->dropColumn('special_test_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['special_test_id']);
            $table->dropColumn('special_test_id');
        });

        Schema::dropIfExists('special_tests');

        Schema::enableForeignKeyConstraints();
    }
};