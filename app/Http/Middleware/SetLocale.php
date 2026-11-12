<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (auth()->check()) {
            $language = Schema::hasTable('user_preferences')
                ? auth()->user()->preferences?->language
                : null;

            App::setLocale($language ?? config('app.locale'));
        } else {
            App::setLocale(session('locale', config('app.locale')));
        }

        return $next($request);
    }
}
