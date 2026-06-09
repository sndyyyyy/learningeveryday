<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'answers'];

    protected $casts = [
        'answers' => 'array',
    ];

    // Hubungan relasi: Hasil kuis ini milik kuis yang mana
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}