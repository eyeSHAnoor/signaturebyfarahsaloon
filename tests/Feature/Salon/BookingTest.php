<?php

use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Stylist;
use App\Models\User;
use App\Services\Salon\AppointmentScheduler;
use Illuminate\Support\Carbon;
use Carbon\CarbonImmutable;
use Spatie\Permission\Models\Role;

function salonService(int $durationMinutes = 60): Service
{
    $category = ServiceCategory::query()->create([
        'name' => 'Hair',
        'slug' => 'hair',
    ]);

    return Service::query()->create([
        'service_category_id' => $category->id,
        'name' => 'Signature Cut',
        'slug' => 'signature-cut',
        'description' => 'A tailored cut and finish.',
        'duration_minutes' => $durationMinutes,
        'price' => 60,
        'active' => true,
    ]);
}

test('customers can book an available stylist time and see a confirmation', function () {
    $service = salonService();
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $date = CarbonImmutable::now()->next(CarbonImmutable::TUESDAY)->toDateString();

    $response = $this->post('/book', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'customer_name' => 'Taylor Guest',
        'customer_email' => 'taylor@example.com',
        'customer_phone' => '+15550000000',
    ]);

    $appointment = Appointment::query()->firstOrFail();
    $response->assertRedirect(route('bookings.confirmation', $appointment->reference, absolute: false));
    $this->assertDatabaseCount('appointment_slot_reservations', 2);
});

test('a stylist cannot be booked for an overlapping appointment', function () {
    $service = salonService();
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $date = CarbonImmutable::now()->next(CarbonImmutable::TUESDAY)->toDateString();
    $booking = [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'customer_name' => 'Taylor Guest',
        'customer_email' => 'taylor@example.com',
        'customer_phone' => '+15550000000',
    ];

    $this->post('/book', $booking)->assertRedirect();
    $this->post('/book', [...$booking, 'appointment_time' => '10:30'])->assertSessionHasErrors('appointment_time');

    $this->assertDatabaseCount('appointments', 1);
});

test('available times include only times with room for the full service duration', function () {
    $service = salonService(60);
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $date = CarbonImmutable::now()->next(CarbonImmutable::TUESDAY)->toDateString();
    $user = User::factory()->create();

    $this->actingAs($user)->post('/book', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'customer_name' => 'Taylor Guest',
        'customer_email' => 'taylor@example.com',
        'customer_phone' => '+15550000000',
    ])->assertRedirect();

    $response = $this->getJson(route('bookings.availability', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'date' => $date,
    ]));

    $response->assertOk()->assertJsonMissing(['times' => ['10:00', '10:30']]);
    expect($response->json('times'))->not->toContain('10:00')->not->toContain('10:30');
});

test('salon does not offer appointments on Sundays or Mondays', function () {
    $service = salonService();
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $date = CarbonImmutable::now()->next(CarbonImmutable::SUNDAY)->toDateString();

    $response = $this->getJson(route('bookings.availability', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'date' => $date,
    ]));

    $response->assertOk()->assertExactJson(['times' => []]);
});

test('same-day booking only offers future opening slots', function () {
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $time = CarbonImmutable::parse('2026-11-03 10:15:00');
    Carbon::setTestNow($time);
    CarbonImmutable::setTestNow($time);

    try {
        $times = app(AppointmentScheduler::class)->availableStartTimes(
            $stylist->id,
            30,
            CarbonImmutable::parse('2026-11-03'),
        );
    } finally {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
    }

    expect($times)->toContain('10:30')->not->toContain('10:00')->not->toContain('09:00');
});

test('customers can view their account-linked appointments', function () {
    $service = salonService();
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $user = User::factory()->create();
    $date = CarbonImmutable::now()->next(CarbonImmutable::TUESDAY)->toDateString();
    $this->actingAs($user)->post('/book', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'appointment_date' => $date,
        'appointment_time' => '11:00',
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'customer_phone' => '+15550000000',
    ])->assertRedirect();

    $this->get('/dashboard')->assertOk()->assertInertia(fn ($page) => $page
        ->component('salon/CustomerDashboard')
        ->has('appointments', 1));
});

test('salon administration is limited to super admins', function () {
    $this->get('/admin')->assertRedirect('/login');

    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $this->actingAs($admin)->get('/admin')->assertOk()->assertInertia(fn ($page) => $page
        ->component('salon/Admin')
        ->has('stats')
        ->has('customers'));
});

test('administrators can reschedule and cancel appointments while releasing the slots', function () {
    $service = salonService(60);
    $stylist = Stylist::query()->create(['name' => 'Amelia Rose', 'title' => 'Stylist', 'active' => true]);
    $date = CarbonImmutable::tomorrow()->toDateString();
    $this->post('/book', [
        'service_id' => $service->id,
        'stylist_id' => $stylist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'customer_name' => 'Taylor Guest',
        'customer_email' => 'taylor@example.com',
        'customer_phone' => '+15550000000',
    ])->assertRedirect();
    $appointment = Appointment::query()->firstOrFail();

    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'web'));
    $this->actingAs($admin)->patch('/admin/appointments/'.$appointment->id, [
        'status' => 'approved',
        'appointment_date' => CarbonImmutable::now()->next(CarbonImmutable::TUESDAY)->addDay()->toDateString(),
        'start_time' => '11:00',
    ])->assertSessionHasNoErrors();

    expect($appointment->fresh()->status)->toBe('approved');
    $this->assertDatabaseHas('appointment_slot_reservations', [
        'appointment_id' => $appointment->id,
        'slot_start' => '11:00:00',
    ]);

    $this->patch('/admin/appointments/'.$appointment->id, ['status' => 'cancelled'])->assertSessionHasNoErrors();
    $this->assertDatabaseCount('appointment_slot_reservations', 0);
});

test('verification code screens and endpoints are no longer available', function () {
    $this->get('/verify-email')->assertNotFound();
    $this->post('/verify-email')->assertNotFound();
    $this->post('/verify-email/resend')->assertNotFound();
});

test('administrators can update customer account status', function () {
    $customer = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

    $this->actingAs($admin)->patch('/admin/customers/'.$customer->id, ['status' => 'suspended'])
        ->assertSessionHasNoErrors();

    expect($customer->fresh()->status)->toBe('suspended')
        ->and($customer->fresh()->suspended_until)->not->toBeNull();
});
