<?php

use App\Http\Controllers\Settings\AccountController;
use App\Http\Controllers\Settings\NotificationSettingController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\PreferencesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/account');

    Route::get('settings/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('settings/account', [AccountController::class, 'update'])->name('account.update');
    Route::delete('settings/account', [AccountController::class, 'destroy'])->name('account.destroy');

    Route::get('settings/profile', [AccountController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [AccountController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [AccountController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/preferences', [PreferencesController::class, 'edit'])->name('preferences.edit');
    Route::post('settings/preferences', [PreferencesController::class, 'update'])->name('preferences.update');
    Route::post('settings/switch-language/{lang}', [PreferencesController::class, 'updateLanguage'])
        ->name('preferences.update-language');

    Route::get('settings/notifications', [NotificationSettingController::class, 'edit'])
        ->name('notification-settings.edit');
    Route::post('settings/notifications', [NotificationSettingController::class, 'update'])
        ->name('notification-settings.update');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
