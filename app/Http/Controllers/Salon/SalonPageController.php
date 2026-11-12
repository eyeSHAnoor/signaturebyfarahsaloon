<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\SalonGalleryItem;
use App\Models\SalonSetting;
use App\Models\SalonTestimonial;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Stylist;
use Inertia\Inertia;
use Inertia\Response;

class SalonPageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('salon/Home', [
            'services' => Service::query()->where('active', true)->where('featured', true)->with('category')->take(6)->get(),
            'stylists' => Stylist::query()->where('active', true)->take(3)->get(),
            'gallery' => SalonGalleryItem::query()->where('active', true)->orderBy('sort_order')->take(4)->get(),
            'testimonials' => SalonTestimonial::query()->where('active', true)->latest()->take(3)->get(),
            'settings' => $this->settings(),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('salon/About', ['settings' => $this->settings()]);
    }

    public function services(): Response
    {
        return Inertia::render('salon/Services', [
            'categories' => ServiceCategory::query()->where('active', true)->orderBy('sort_order')->with([
                'services' => fn ($query) => $query->where('active', true)->orderBy('name'),
            ])->get(),
        ]);
    }

    public function service(Service $service): Response
    {
        abort_unless($service->active, 404);

        return Inertia::render('salon/ServiceDetail', [
            'service' => $service->load('category'),
            'services' => Service::query()->where('active', true)->whereKeyNot($service->id)->take(3)->get(),
        ]);
    }

    public function team(): Response
    {
        return Inertia::render('salon/Team', [
            'stylists' => Stylist::query()->where('active', true)->get(),
        ]);
    }

    public function gallery(): Response
    {
        return Inertia::render('salon/Gallery', [
            'gallery' => SalonGalleryItem::query()->where('active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function pricing(): Response
    {
        return Inertia::render('salon/Pricing', [
            'categories' => ServiceCategory::query()->where('active', true)->orderBy('sort_order')->with([
                'services' => fn ($query) => $query->where('active', true)->orderBy('name'),
            ])->get(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('salon/Contact', ['settings' => $this->settings()]);
    }

    private function settings(): array
    {
        return SalonSetting::query()->pluck('value', 'key')->all();
    }
}
