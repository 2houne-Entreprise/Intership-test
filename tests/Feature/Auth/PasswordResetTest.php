<?php

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('password reset code request screen can be rendered', function () {
    $this->get(route('password.request'))
        ->assertSuccessful()
        ->assertSee('Adresse e-mail');
});

test('it sends a six digit code and stores only its hash', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertRedirect(route('password.otp.verify'))
        ->assertSessionHas('status');

    $notification = Notification::sent($user, PasswordResetOtpNotification::class)->first();
    $challenge = PasswordResetOtp::query()->where('user_id', $user->id)->firstOrFail();

    expect($notification)->toBeInstanceOf(PasswordResetOtpNotification::class)
        ->and($notification->code)->toMatch('/^\d{6}$/')
        ->and($challenge->code_hash)->not->toBe($notification->code)
        ->and(Hash::check($notification->code, $challenge->code_hash))->toBeTrue()
        ->and($challenge->expires_at->isFuture())->toBeTrue();
});

test('the request response does not reveal whether an email exists', function () {
    Notification::fake();
    $user = User::factory()->create();

    $knownEmailResponse = $this->post(route('password.email'), ['email' => $user->email]);
    $unknownEmailResponse = $this->post(route('password.email'), ['email' => 'unknown@example.test']);

    $knownEmailResponse->assertRedirect(route('password.otp.verify'))->assertSessionHas('status');
    $unknownEmailResponse->assertRedirect(route('password.otp.verify'))->assertSessionHas('status');
    Notification::assertSentTo($user, PasswordResetOtpNotification::class);
    expect(PasswordResetOtp::query()->count())->toBe(1);
});

test('a resent code invalidates the previous code', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email'), ['email' => $user->email]);
    $firstCode = Notification::sent($user, PasswordResetOtpNotification::class)->first()->code;

    $this->post(route('password.otp.resend'))
        ->assertRedirect(route('password.otp.verify'))
        ->assertSessionHas('status');
    $latestCode = Notification::sent($user, PasswordResetOtpNotification::class)->last()->code;

    expect($latestCode)->not->toBe($firstCode);

    $this->post(route('password.otp.verify.store'), ['code' => $firstCode])
        ->assertSessionHasErrors('code');

    $this->post(route('password.otp.verify.store'), ['code' => $latestCode])
        ->assertRedirect(route('password.reset'))
        ->assertSessionHas('password_reset_otp_proof');

    expect(PasswordResetOtp::query()->where('user_id', $user->id)->value('code_hash'))->toBeNull();
});

test('expired codes are rejected', function () {
    Notification::fake();
    $user = User::factory()->create();
    Carbon::setTestNow(Carbon::parse('2026-10-17 10:00:00'));

    try {
        $this->post(route('password.email'), ['email' => $user->email]);
        $code = Notification::sent($user, PasswordResetOtpNotification::class)->first()->code;
        Carbon::setTestNow(now()->addMinutes(11));

        $this->post(route('password.otp.verify.store'), ['code' => $code])
            ->assertSessionHasErrors('code');

        expect(PasswordResetOtp::query()->where('user_id', $user->id)->exists())->toBeFalse();
    } finally {
        Carbon::setTestNow();
    }
});

test('five incorrect codes exhaust a challenge', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email'), ['email' => $user->email]);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('password.otp.verify.store'), ['code' => '000000'])
            ->assertSessionHasErrors('code');
    }

    expect(PasswordResetOtp::query()->where('user_id', $user->id)->exists())->toBeFalse();
});

test('code requests are rate limited per email and client', function () {
    Notification::fake();
    $email = User::factory()->create()->email;

    foreach (range(1, 3) as $request) {
        $this->post(route('password.email'), ['email' => $email])
            ->assertRedirect(route('password.otp.verify'));
    }

    $this->post(route('password.email'), ['email' => $email])->assertStatus(429);
});

test('verified code is required to reset and the proof is single use', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->get(route('password.reset'))->assertRedirect(route('password.otp.verify'));

    $this->post(route('password.email'), ['email' => $user->email]);
    $code = Notification::sent($user, PasswordResetOtpNotification::class)->first()->code;
    $this->post(route('password.otp.verify.store'), ['code' => $code])
        ->assertRedirect(route('password.reset'))
        ->assertSessionHas('password_reset_otp_proof');

    $this->get(route('password.reset'))
        ->assertSuccessful()
        ->assertSee('Nouveau mot de passe')
        ->assertSee('Confirmer le mot de passe');

    $this->post(route('password.store'), [
        'password' => 'a-secure-new-password',
        'password_confirmation' => 'a-secure-new-password',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status')
        ->assertSessionMissing('password_reset_otp_proof');

    expect(Hash::check('a-secure-new-password', $user->fresh()->password))->toBeTrue()
        ->and(PasswordResetOtp::query()->where('user_id', $user->id)->exists())->toBeFalse();

    $this->get(route('password.reset'))->assertRedirect(route('password.otp.verify'));
});

test('verification attempts are rate limited', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email'), ['email' => $user->email]);

    foreach (range(1, 6) as $attempt) {
        $this->post(route('password.otp.verify.store'), ['code' => '000000']);
    }

    $this->post(route('password.otp.verify.store'), ['code' => '000000'])->assertStatus(429);
});
