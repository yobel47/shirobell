<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MusicTrack extends Model
{
    protected $fillable = ['title', 'artist', 'file_path', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
