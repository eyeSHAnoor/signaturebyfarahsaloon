<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'file_location',
        'file_name',
        'collection',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $appends = [
        'file_url',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_location
            ? Storage::disk('public')->url($this->file_location)
            : null;
    }
}
