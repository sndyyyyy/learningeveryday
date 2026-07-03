<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Tambahkan kolom type dengan default multiple_choice
            $table->string('type')->default('multiple_choice')->after('quiz_id');
            
            // Ubah tipe kolom correct_answer menjadi text (nullable) agar muat array JSON untuk essay
            $table->text('correct_answer')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};