<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = ['user_id', 'folder_id', 'file_name', 'file_path', 'file_type', 'file_size'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
}