<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Stylist;
use App\Services\Salon\AppointmentScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('salon/Booking', [
            'services' => Service::query()->where('active', true)->orderBy('name')->get(),
            'stylists' => Stylist::query()->where('active', true)->orderBy('name')->get(),
            'customer' => $request->user() ? [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
            ] : null,
            'selectedService' => $request->query('service'),
        ]);
    }

    public function availability(Request $request, AppointmentScheduler $scheduler): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $service = Service::query()->where('active', true)->findOrFail($validated['service_id']);
        $stylist = Stylist::query()->where('active', true)->findOrFail($validated['stylist_id']);
        $date = CarbonImmutable::parse($validated['date']);

        return response()->json([
            'times' => $scheduler->availableStartTimes($stylist->id, $service->duration_minutes, $date),
        ]);
    }

    public function store(Request $request, AppointmentScheduler $scheduler): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'stylist_id' => ['required', 'integer', 'exists:stylists,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = Service::query()->where('active', true)->find($validated['service_id']);
        $stylist = Stylist::query()->where('active', true)->find($validated['stylist_id']);
        if (! $service || ! $stylist) {
            throw ValidationException::withMessages(['service_id' => 'Please choose an available service and stylist.']);
        }

        $date = CarbonImmutable::parse($validated['appointment_date']);
        $start = CarbonImmutable::createFromFormat('Y-m-d H:i', $date->toDateString().' '.$validated['appointment_time']);
        if ($start->minute % 30 !== 0 || ! in_array($validated['appointment_time'], $scheduler->availableStartTimes($stylist->id, $service->duration_minutes, $date), true)) {
            throw ValidationException::withMessages(['appointment_time' => 'That time is no longer available. Please choose another.']);
        }

        try {
            $appointment = DB::transaction(function () use ($request, $scheduler, $service, $stylist, $date, $start, $validated) {
                $scheduler->lockStylist($stylist->id);

                if (! in_array($start->format('H:i'), $scheduler->availableStartTimes($stylist->id, $service->duration_minutes, $date), true)) {
                    throw ValidationException::withMessages(['appointment_time' => 'That time is no longer available. Please choose another.']);
                }

                $appointment = Appointment::query()->create([
                    'reference' => (string) Str::uuid(),
                    'user_id' => $request->user()?->id,
                    'service_id' => $service->id,
                    'stylist_id' => $stylist->id,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'appointment_date' => $date->toDateString(),
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $start->addMinutes($service->duration_minutes)->format('H:i:s'),
                    'status' => 'pending',
                    'price' => $service->price,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $scheduler->reserve($appointment);

                return $appointment;
            });
        } catch (QueryException $exception) {
            $sqlState = $exception->errorInfo[0] ?? (string) $exception->getCode();
            $driverCode = (int) ($exception->errorInfo[1] ?? 0);
            $message = $exception->getMessage();
            $slotConstraint = str_contains($message, 'stylist_slot_unique')
                || str_contains($message, 'appointment_slot_reservations.stylist_id');
            $isSlotCollision = ($sqlState === '23000' && $driverCode === 1062 && $slotConstraint)
                || ($sqlState === '23505' && $slotConstraint)
                || ($driverCode === 19 && $slotConstraint);

            if (! $isSlotCollision) {
                throw $exception;
            }

            throw ValidationException::withMessages(['appointment_time' => 'That time was just booked. Please choose another.']);
        }

        return redirect()->route('bookings.confirmation', $appointment->reference);
    }

    public function confirmation(Appointment $appointment): Response
    {
        return Inertia::render('salon/Confirmation', [
            'appointment' => $appointment->load(['service', 'stylist']),
        ]);
    }
}
