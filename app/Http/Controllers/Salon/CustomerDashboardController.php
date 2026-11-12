<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $appointments = $user->appointments()
            ->with(['service', 'stylist'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->get();

        return Inertia::render('salon/CustomerDashboard', [
            'appointments' => $appointments,
            'upcomingCount' => $appointments->whereIn('status', ['pending', 'approved'])
                ->filter(fn ($appointment) => $appointment->appointment_date->isToday() || $appointment->appointment_date->isFuture())
                ->count(),
        ]);
    }
}
