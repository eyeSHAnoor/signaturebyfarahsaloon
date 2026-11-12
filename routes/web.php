<?php

use App\Http\Controllers\Salon\BookingController;
use App\Http\Controllers\Salon\CustomerDashboardController;
use App\Http\Controllers\Salon\SalonAdminController;
use App\Http\Controllers\Salon\SalonPageController;
use App\Http\Controllers\Salon\SalonSeoController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SalonSeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SalonSeoController::class, 'sitemap'])->name('sitemap');
Route::get('/', [SalonPageController::class, 'home'])->name('home');
Route::get('/about', [SalonPageController::class, 'about'])->name('salon.about');
Route::get('/services', [SalonPageController::class, 'services'])->name('salon.services');
Route::get('/services/{service:slug}', [SalonPageController::class, 'service'])->name('salon.service');
Route::get('/team', [SalonPageController::class, 'team'])->name('salon.team');
Route::get('/gallery', [SalonPageController::class, 'gallery'])->name('salon.gallery');
Route::get('/pricing', [SalonPageController::class, 'pricing'])->name('salon.pricing');
Route::get('/contact', [SalonPageController::class, 'contact'])->name('salon.contact');

Route::get('/book', [BookingController::class, 'create'])->name('bookings.create');
Route::get('/bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/booking/confirmed/{appointment:reference}', [BookingController::class, 'confirmation'])
    ->name('bookings.confirmation');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/', [SalonAdminController::class, 'index'])->name('dashboard');
    Route::post('/categories', [SalonAdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [SalonAdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [SalonAdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::post('/services', [SalonAdminController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [SalonAdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [SalonAdminController::class, 'destroyService'])->name('services.destroy');
    Route::post('/stylists', [SalonAdminController::class, 'storeStylist'])->name('stylists.store');
    Route::put('/stylists/{stylist}', [SalonAdminController::class, 'updateStylist'])->name('stylists.update');
    Route::delete('/stylists/{stylist}', [SalonAdminController::class, 'destroyStylist'])->name('stylists.destroy');
    Route::patch('/customers/{user}', [SalonAdminController::class, 'updateCustomer'])->name('customers.update');
    Route::patch('/appointments/{appointment}', [SalonAdminController::class, 'updateAppointment'])->name('appointments.update');
    Route::post('/gallery', [SalonAdminController::class, 'storeGalleryItem'])->name('gallery.store');
    Route::put('/gallery/{galleryItem}', [SalonAdminController::class, 'updateGalleryItem'])->name('gallery.update');
    Route::delete('/gallery/{galleryItem}', [SalonAdminController::class, 'destroyGalleryItem'])->name('gallery.destroy');
    Route::post('/testimonials', [SalonAdminController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::put('/testimonials/{testimonial}', [SalonAdminController::class, 'updateTestimonial'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [SalonAdminController::class, 'destroyTestimonial'])->name('testimonials.destroy');
    Route::put('/settings', [SalonAdminController::class, 'updateSettings'])->name('settings.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
