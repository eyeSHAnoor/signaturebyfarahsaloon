<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlotReservation extends Model
{
    public $timestamps = false;

    protected $fillable = ['appointment_id', 'stylist_id', 'appointment_date', 'slot_start'];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
