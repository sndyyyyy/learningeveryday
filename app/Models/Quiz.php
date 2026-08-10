<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['title', 'description', 'created_by', 'tier_access', 'class_group', 'special_test_id'];

    // Relasi ke Question (1 Quiz punya banyak Question)
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function specialTest()
{
    return $this->belongsTo(SpecialTest::class, 'special_test_id');
}
}
