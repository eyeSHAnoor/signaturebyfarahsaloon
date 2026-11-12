<?php

use App\Models\Service;
use App\Models\ServiceCategory;

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

