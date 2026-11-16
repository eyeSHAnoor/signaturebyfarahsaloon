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

test('services can be filtered by category and active categories are shared with the salon navigation', function () {
    $hair = ServiceCategory::query()->create([
        'name' => 'Hair',
        'slug' => 'hair',
        'active' => true,
    ]);
    $nails = ServiceCategory::query()->create([
        'name' => 'Nails',
        'slug' => 'nails',
        'active' => true,
    ]);
    ServiceCategory::query()->create([
        'name' => 'Archived',
        'slug' => 'archived',
        'active' => false,
    ]);

    foreach ([[$hair, 'Signature Cut'], [$nails, 'Manicure']] as [$category, $name]) {
        Service::query()->create([
            'service_category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => 'A thoughtful salon service.',
            'duration_minutes' => 60,
            'price' => 60,
            'active' => true,
        ]);
    }

    $this->get('/services?category=hair')
        ->assertInertia(fn (Assert $page) => $page
            ->component('salon/Services')
            ->has('categories', 1)
            ->where('categories.0.name', 'Hair')
            ->has('categories.0.services', 1)
            ->has('salonCategories', 2)
            ->where('salonCategories.0.slug', 'hair')
            ->where('salonCategories.1.slug', 'nails'));
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
