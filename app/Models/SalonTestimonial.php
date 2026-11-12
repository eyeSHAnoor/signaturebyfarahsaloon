<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalonTestimonial extends Model
{
    protected $fillable = ['customer_name', 'customer_title', 'quote', 'rating', 'image_url', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'rating' => 'integer'];
    }
}
