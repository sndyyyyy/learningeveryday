<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = ['instansi_id', 'name'];

    public function instansi()
    {
        return $this->belongsTo(User::class, 'instansi_id');
    }
}