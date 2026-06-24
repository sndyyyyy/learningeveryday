<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    protected $fillable = ['name'];

    // Relasi ke model BankPart
    public function parts(): HasMany
    {
        return $this->hasMany(BankPart::class);
    }
}