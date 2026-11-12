<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'service_category_id', 'name', 'slug', 'description', 'duration_minutes',
        'price', 'image_url', 'featured', 'active',
    ];

    protected function casts(): array
    {
        return ['featured' => 'boolean', 'active' => 'boolean', 'price' => 'decimal:2'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
