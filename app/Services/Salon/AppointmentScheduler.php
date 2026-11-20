<?php

namespace App\Services\Salon;

use App\Models\Appointment;
use App\Models\AppointmentSlotReservation;
use App\Models\Stylist;
use Carbon\CarbonImmutable;

class AppointmentScheduler
{
    public function availableStartTimes(
        int $stylistId,
        int $durationMinutes,
        CarbonImmutable $date,
        ?int $ignoreAppointmentId = null,
    ): array {
        if ($date->toDateString() < now()->toDateString()) {
            return [];
        }

        $reservations = AppointmentSlotReservation::query()
            ->where('stylist_id', $stylistId)
            ->whereDate('appointment_date', $date->toDateString())
            ->when($ignoreAppointmentId, fn ($query) => $query->where('appointment_id', '!=', $ignoreAppointmentId))
            ->pluck('slot_start')
            ->map(fn ($time) => substr((string) $time, 0, 5))
            ->all();
        $occupied = array_fill_keys($reservations, true);
        $times = [];

        for ($minute = 10 * 60 + 30; $minute + $durationMinutes <= 20 * 60; $minute += 30) {
            if ($date->isToday() && $date->setTime(intdiv($minute, 60), $minute % 60)->lessThanOrEqualTo(now())) {
                continue;
            }

            $isAvailable = true;
            for ($slot = $minute; $slot < $minute + $durationMinutes; $slot += 30) {
                $slotLabel = sprintf('%02d:%02d', intdiv($slot, 60), $slot % 60);
                if (isset($occupied[$slotLabel])) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $times[] = sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
            }
        }

        return $times;
    }

    public function reserve(Appointment $appointment): void
    {
        if (! $appointment->stylist_id) {
            return;
        }

        $start = CarbonImmutable::parse($appointment->appointment_date->toDateString().' '.$appointment->start_time);
        $end = CarbonImmutable::parse($appointment->appointment_date->toDateString().' '.$appointment->end_time);
        $slots = [];

        for ($slot = $start; $slot->lessThan($end); $slot = $slot->addMinutes(30)) {
            $slots[] = [
                'appointment_id' => $appointment->id,
                'stylist_id' => $appointment->stylist_id,
                'appointment_date' => $appointment->appointment_date->toDateString(),
                'slot_start' => $slot->format('H:i:s'),
            ];
        }

        AppointmentSlotReservation::query()->insert($slots);
    }

    public function lockStylist(int $stylistId): Stylist
    {
        return Stylist::query()->whereKey($stylistId)->lockForUpdate()->firstOrFail();
    }
}
