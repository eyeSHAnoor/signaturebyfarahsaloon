<?php

use App\Models\SalonGalleryItem;
use App\Models\SalonTestimonial;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

function salonAdmin(): User
{
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate('super_admin', 'web'));

    return $admin;
}

test('salon admins can upload images for services, stylists, gallery items, and testimonials', function () {
    Storage::fake('public');
    $admin = salonAdmin();
    $this->actingAs($admin);

    $this->post('/admin/services', [
        'name' => 'Signature Cut',
        'description' => 'A tailored cut and finish.',
        'duration_minutes' => 60,
        'price' => 60,
        'featured' => false,
        'active' => true,
        'image' => UploadedFile::fake()->image('service.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Service created.');

    $this->post('/admin/stylists', [
        'name' => 'Amelia Rose',
        'title' => 'Stylist',
        'active' => true,
        'image' => UploadedFile::fake()->image('stylist.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Stylist added.');

    $this->post('/admin/gallery', [
        'title' => 'Transformation',
        'before_image' => UploadedFile::fake()->image('before.png'),
        'after_image' => UploadedFile::fake()->image('after.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Gallery image added.');

    $this->post('/admin/testimonials', [
        'customer_name' => 'Taylor Guest',
        'quote' => 'Wonderful service.',
        'rating' => 5,
        'image' => UploadedFile::fake()->image('customer.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Testimonial added.');

    expect(Service::query()->firstOrFail()->image_url)->toContain('/storage/salon/images/');
    expect(Stylist::query()->firstOrFail()->image_url)->toContain('/storage/salon/images/');

    $galleryItem = SalonGalleryItem::query()->firstOrFail();
    expect($galleryItem->image_url)->toBe($galleryItem->before_image_url)
        ->and($galleryItem->before_image_url)->toContain('/storage/salon/images/')
        ->and($galleryItem->after_image_url)->toContain('/storage/salon/images/');

    expect(SalonTestimonial::query()->firstOrFail()->image_url)->toContain('/storage/salon/images/');
    expect(Storage::disk('public')->allFiles('salon/images'))->toHaveCount(5);
});

test('salon admins can set a service duration to any positive whole number of minutes', function () {
    $this->actingAs(salonAdmin())->post('/admin/services', [
        'name' => 'Quick Treatment',
        'description' => 'A short custom treatment.',
        'duration_minutes' => 17,
        'price' => 60,
        'featured' => false,
        'active' => true,
    ])->assertRedirect()->assertSessionHas('success', 'Service created.');

    expect(Service::query()->where('name', 'Quick Treatment')->value('duration_minutes'))->toBe(17);
});

test('salon admins can replace a service image with an uploaded file', function () {
    Storage::fake('public');
    $admin = salonAdmin();
    $service = Service::query()->create([
        'name' => 'Signature Cut',
        'slug' => 'signature-cut',
        'description' => 'A tailored cut and finish.',
        'duration_minutes' => 60,
        'price' => 60,
        'image_url' => 'https://example.com/old-image.jpg',
        'featured' => false,
        'active' => true,
    ]);

    $this->actingAs($admin)->post('/admin/services/'.$service->id, [
        '_method' => 'put',
        'name' => 'Signature Cut',
        'description' => 'A tailored cut and finish.',
        'duration_minutes' => 60,
        'price' => 60,
        'featured' => false,
        'active' => true,
        'image' => UploadedFile::fake()->image('replacement.png'),
    ])->assertRedirect();

    expect($service->refresh()->image_url)->toContain('/storage/salon/images/');
    expect(Storage::disk('public')->allFiles('salon/images'))->toHaveCount(1);
});

test('salon admins can replace gallery and testimonial images while editing', function () {
    Storage::fake('public');
    $admin = salonAdmin();
    $galleryItem = SalonGalleryItem::query()->create([
        'title' => 'Old transformation',
        'image_url' => 'https://example.com/old-before.jpg',
        'before_image_url' => 'https://example.com/old-before.jpg',
        'after_image_url' => 'https://example.com/old-after.jpg',
    ]);
    $testimonial = SalonTestimonial::query()->create([
        'customer_name' => 'Taylor Guest',
        'quote' => 'Wonderful service.',
        'rating' => 5,
        'image_url' => 'https://example.com/old-customer.jpg',
    ]);

    $this->actingAs($admin)->post('/admin/gallery/'.$galleryItem->id, [
        '_method' => 'put',
        'title' => 'New transformation',
        'before_image' => UploadedFile::fake()->image('new-before.png'),
        'after_image' => UploadedFile::fake()->image('new-after.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Gallery image updated.');

    $this->post('/admin/testimonials/'.$testimonial->id, [
        '_method' => 'put',
        'customer_name' => 'Taylor Guest',
        'quote' => 'Wonderful service, updated.',
        'rating' => 5,
        'image' => UploadedFile::fake()->image('new-customer.png'),
    ])->assertRedirect()->assertSessionHas('success', 'Testimonial updated.');

    $galleryItem->refresh();
    expect($galleryItem->title)->toBe('New transformation')
        ->and($galleryItem->image_url)->toBe($galleryItem->before_image_url)
        ->and($galleryItem->before_image_url)->toContain('/storage/salon/images/')
        ->and($galleryItem->after_image_url)->toContain('/storage/salon/images/')
        ->and($testimonial->refresh()->image_url)->toContain('/storage/salon/images/')
        ->and(Storage::disk('public')->allFiles('salon/images'))->toHaveCount(3);
});
