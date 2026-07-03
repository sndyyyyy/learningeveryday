<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['quiz_id', 'type', 'bank_part_id', 'question_text', 'image', 'audio', 'options', 'correct_answer', 'explanation'];

    // Beritahu Laravel kalau kolom options adalah array/json
    protected $casts = [
        'options' => 'array',
    ];

    public function bankPart()
    {
        return $this->belongsTo(BankPart::class);
    }
}