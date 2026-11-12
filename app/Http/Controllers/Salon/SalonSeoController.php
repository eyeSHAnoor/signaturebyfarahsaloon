<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Response;

class SalonSeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            route('home'),
            route('salon.about'),
            route('salon.services'),
            route('salon.team'),
            route('salon.gallery'),
            route('salon.pricing'),
            route('salon.contact'),
            route('bookings.create'),
        ])->merge(
            Service::query()
                ->where('active', true)
                ->get(['slug', 'updated_at'])
                ->map(fn (Service $service) => [
                    'url' => route('salon.service', $service->slug),
                    'lastmod' => $service->updated_at?->toAtomString(),
                ]),
        )->map(fn ($url) => is_array($url) ? $url : ['url' => $url, 'lastmod' => null]);

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /booking/confirmed/',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /confirm-password',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
