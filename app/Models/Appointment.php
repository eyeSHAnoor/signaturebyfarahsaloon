<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'service_id', 'stylist_id', 'customer_name',
        'customer_email', 'customer_phone', 'appointment_date', 'start_time',
        'end_time', 'status', 'price', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function stylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(AppointmentSlotReservation::class);
    }
}
