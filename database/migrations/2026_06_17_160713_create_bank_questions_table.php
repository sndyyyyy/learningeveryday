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
        Schema::create('bank_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_part_id')->constrained('bank_parts')->onDelete('cascade');
            $table->text('question_text');
            $table->string('image')->nullable();
            $table->string('audio')->nullable();
            $table->json('options'); // Menyimpan Pilihan A, B, C, D dalam format JSON
            $table->string('correct_answer', 2); // A, B, C, atau D
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_questions');
    }
};
