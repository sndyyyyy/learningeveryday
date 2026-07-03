<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Lakukan hal yang sama untuk tabel 'questions' jika kamu memisahkan tabel kuis berjalan
        Schema::table('bank_questions', function (Blueprint $table) {
            // 1. Tambahkan penanda tipe soal (default multiple_choice agar soal lama tidak rusak)
            $table->string('type')->default('multiple_choice')->after('bank_part_id'); 
            
            // 2. Ubah kolom correct_answer dari yang dulunya string pendek/char 1 huruf, 
            // menjadi TEXT atau JSON agar bisa menampung banyak kunci jawaban isian sekaligus
            $table->text('correct_answer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_questions', function (Blueprint $table) {
            //
        });
    }
};
