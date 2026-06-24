<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankQuestion extends Model
{
    protected $fillable = [
        'bank_part_id',
        'question_text',
        'image',
        'audio',
        'options',
        'correct_answer',
        'explanation'
    ];

    // PENTING: Mengubah data JSON options di DB menjadi array PHP otomatis
    protected $casts = [
        'options' => 'array',
    ];

    // Relasi balik ke BankPart
    public function bankPart(): BelongsTo
    {
        return $this->belongsTo(BankPart::class);
    }
}