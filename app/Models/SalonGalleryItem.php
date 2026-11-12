<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalonGalleryItem extends Model
{
    protected $fillable = ['title', 'category', 'image_url', 'before_image_url', 'after_image_url', 'alt_text', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
