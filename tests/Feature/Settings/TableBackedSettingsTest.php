<?php

use App\Models\User;

test('users can view preference and notification settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/preferences')
        ->assertOk();

    $this->actingAs($user)
        ->get('/settings/notifications')
        ->assertOk();
});

test('users can save preference and notification settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/settings/preferences', [
            'language' => 'en',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'currency' => 'USD',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->post('/settings/notifications', [
            'settings' => [
                [
                    'type' => 'order',
                    'methods' => ['email'],
                    'timing' => 'anytime',
                    'frequency' => 'instant',
                ],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('user_preferences', [
        'user_id' => $user->id,
        'language' => 'en',
    ]);
    $this->assertDatabaseHas('user_notification_settings', [
        'user_id' => $user->id,
        'type' => 'order',
    ]);
});
