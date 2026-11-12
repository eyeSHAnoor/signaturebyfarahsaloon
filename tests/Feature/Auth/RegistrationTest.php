<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->firstOrFail();
    expect($user->email_verified_at)->toBeNull();
});

test('verification code fields are removed from the users table', function () {
    expect(\Illuminate\Support\Facades\Schema::hasColumn('users', 'verification_code'))->toBeFalse()
        ->and(\Illuminate\Support\Facades\Schema::hasColumn('users', 'verification_code_expires_at'))->toBeFalse();
});
