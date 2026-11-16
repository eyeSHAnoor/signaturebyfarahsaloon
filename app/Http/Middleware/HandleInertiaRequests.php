<?php

namespace App\Http\Middleware;

use App\Models\ServiceCategory;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();
        $profile = $user && Schema::hasTable('user_profiles') ? $user->profile : null;
        $canReadRoles = Schema::hasTable('roles') && Schema::hasTable('model_has_roles');
        $canReadPermissions = $canReadRoles
            && Schema::hasTable('permissions')
            && Schema::hasTable('model_has_permissions')
            && Schema::hasTable('role_has_permissions');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'seo' => [
                'baseUrl' => rtrim(config('app.url') ?: $request->getSchemeAndHttpHost(), '/'),
            ],
            'salonCategories' => fn () => Schema::hasTable('service_categories')
                ? ServiceCategory::query()->where('active', true)->orderBy('sort_order')->get(['id', 'name', 'slug'])
                : [],
            'quote' => [
                'message' => trim($message),
                'author' => trim($author),
            ],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $profile?->username ?? $user->name,
                    'terms_accepted' => $user->terms_accepted,
                    'email' => $user->email,
                    'roles' => $canReadRoles && method_exists($user, 'getRoleNames')
                        ? $user->getRoleNames()
                        : [],
                    'permissions' => $canReadPermissions && method_exists($user, 'getAllPermissions')
                        ? $user->getAllPermissions()->pluck('name')
                        : [],
                    'profile_image' => $profile?->profile_image,
                ] : null,
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state')
                || $request->cookie('sidebar_state') === 'true',

            'settings' => fn () => Schema::hasTable('settings')
                ? DB::table('settings')->pluck('value', 'key')->all()
                : [],
            'flash' => fn () => [
                'success' => session()->get('success'),
                'error' => session()->get('error'),
                'open_order_id' => session()->get('open_order_id'),
                'preview_data' => fn () => $request->session()->get('preview_data'),
                'searchResults' => session()->get('searchResults'),
                'searchedArticle' => session()->get('searchedArticle'),
            ],

            'locale' => fn () => App::getLocale(),
            'translations' => function () {
                $locale = App::getLocale();
                $path = lang_path("$locale.json");

                if (! File::exists($path)) {
                    return [];
                }

                $content = File::get($path);

                // Remove BOM if present
                $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

                // Decode safely
                return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            },
            'selectedCity' => $request->cookie('user_city', 'Pakistan'),
            'selectedRegion' => $request->cookie('user_region'),
            'notifications' => fn () => $user && Schema::hasTable('notifications')
                ? $user->notifications()->latest()->take(10)->get()->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'data' => $notification->data, // No need to json_decode, it's already cast to array
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                        'type' => $notification->type,
                    ];
                })
                : [],

            'unreadCount' => fn () => $user && Schema::hasTable('notifications')
                ? $user->unreadNotifications()->count()
                : 0,
        ];
    }
}
