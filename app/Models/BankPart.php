<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankPart extends Model
{
    protected $fillable = ['question_bank_id', 'part_name'];

    // Relasi balik ke Bank Soal Utama
    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    // Relasi ke daftar soal di dalam part ini
    public function questions(): HasMany
    {
        return $this->hasMany(BankQuestion::class);
    }
}