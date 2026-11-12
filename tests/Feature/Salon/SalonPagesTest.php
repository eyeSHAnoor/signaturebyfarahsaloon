<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('salon public pages render with the dynamic service catalog', function () {
    $category = ServiceCategory::query()->create([
        'name' => 'Hair',
        'slug' => 'hair',
        'active' => true,
    ]);
    Service::query()->create([
        'service_category_id' => $category->id,
        'name' => 'Signature Cut',
        'slug' => 'signature-cut',
        'description' => 'A tailored cut and finish.',
        'duration_minutes' => 60,
        'price' => 60,
        'active' => true,
        'featured' => true,
    ]);

    foreach (['/', '/about', '/services', '/services/signature-cut', '/team', '/gallery', '/pricing', '/contact', '/book'] as $path) {
        $this->get($path)->assertOk();
    }
});

test('contact page displays the current salon contact details', function () {
    $this->get('/contact')
        ->assertInertia(fn (Assert $page) => $page
            ->component('salon/Contact')
            ->where('settings.phone', '03336601912')
            ->where('settings.email', 'Signaturebyfarah@gmail.com')
            ->where(
                'settings.address',
                'Signature by Farah, 887/B, Peoples Colony No. 1, near Al-Fateh Sports Complex, Chen One Road, Faisalabad, Pakistan',
            ));
});

test('contact details migration preserves existing setting creation timestamps', function () {
    $createdAt = '2020-01-02 03:04:05';

    DB::table('salon_settings')->where('key', 'phone')->update(['created_at' => $createdAt]);

    $migration = require database_path('migrations/2026_11_13_000001_update_salon_contact_details.php');
    $migration->up();

    expect(DB::table('salon_settings')->where('key', 'phone')->value('created_at'))->toBe($createdAt);
});
