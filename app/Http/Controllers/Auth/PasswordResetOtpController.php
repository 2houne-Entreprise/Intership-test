<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordWithOtpRequest;
use App\Http\Requests\Auth\SendPasswordResetOtpRequest;
use App\Http\Requests\Auth\VerifyPasswordResetOtpRequest;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetOtpController extends Controller
{
    private const EMAIL_SESSION_KEY = 'password_reset_otp_email';

    private const PROOF_SESSION_KEY = 'password_reset_otp_proof';

    private const CODE_TTL_MINUTES = 10;

    private const MAX_CODE_ATTEMPTS = 5;

    public function requestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function send(SendPasswordResetOtpRequest $request): RedirectResponse
    {
        $email = $request->validated('email');
        $request->session()->forget([self::EMAIL_SESSION_KEY, self::PROOF_SESSION_KEY]);
        $request->session()->put(self::EMAIL_SESSION_KEY, $email);
        $request->session()->regenerate();

        $user = User::query()->where('email', $email)->first();

        if ($user !== null) {
            $this->issueCode($user);
        }

        return redirect()->route('password.otp.verify')->with('status', $this->genericEmailStatus());
    }

    public function verifyForm(): View|RedirectResponse
    {
        $email = session(self::EMAIL_SESSION_KEY);

        if (! is_string($email) || $email === '') {
            return redirect()->route('password.request');
        }

        return view('auth.verify-password-otp', compact('email'));
    }

    public function verify(VerifyPasswordResetOtpRequest $request): RedirectResponse
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        $user = is_string($email) ? User::query()->where('email', $email)->first() : null;
        $challenge = $user === null
            ? null
            : PasswordResetOtp::query()->where('user_id', $user->id)->first();

        if ($challenge === null || $challenge->verified_at !== null || $challenge->code_hash === null) {
            return back()->withErrors(['code' => 'Ce code est invalide ou a déjà été utilisé.']);
        }

        if (! $challenge->expires_at?->isFuture()) {
            $challenge->delete();

            return back()->withErrors(['code' => 'Ce code a expiré. Demandez-en un nouveau.']);
        }

        if ($challenge->attempts >= self::MAX_CODE_ATTEMPTS) {
            $challenge->delete();

            return back()->withErrors(['code' => 'Trop de tentatives. Demandez un nouveau code.']);
        }

        if (! Hash::check($request->validated('code'), $challenge->code_hash)) {
            $challenge->increment('attempts');

            if ($challenge->attempts >= self::MAX_CODE_ATTEMPTS) {
                $challenge->delete();

                return back()->withErrors(['code' => 'Trop de tentatives. Demandez un nouveau code.']);
            }

            return back()->withErrors(['code' => 'Le code saisi est incorrect.']);
        }

        $proof = bin2hex(random_bytes(32));
        $challenge->update([
            'code_hash' => null,
            'verified_at' => now(),
            'proof_hash' => hash('sha256', $proof),
            'proof_expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
        ]);

        $request->session()->put(self::PROOF_SESSION_KEY, $proof);
        $request->session()->regenerate();

        return redirect()->route('password.reset');
    }

    public function resend(): RedirectResponse
    {
        $email = session(self::EMAIL_SESSION_KEY);

        if (! is_string($email) || $email === '') {
            return redirect()->route('password.request');
        }

        session()->forget(self::PROOF_SESSION_KEY);

        $user = User::query()->where('email', $email)->first();

        if ($user !== null) {
            $this->issueCode($user);
        }

        return redirect()->route('password.otp.verify')->with('status', $this->genericEmailStatus());
    }

    public function resetForm(): View|RedirectResponse
    {
        $challenge = $this->validProofChallenge();

        if ($challenge === null) {
            return redirect()->route('password.otp.verify')
                ->withErrors(['code' => 'La vérification a expiré. Demandez un nouveau code.']);
        }

        return view('auth.reset-password-otp');
    }

    public function reset(ResetPasswordWithOtpRequest $request): RedirectResponse
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        $proof = $request->session()->get(self::PROOF_SESSION_KEY);

        if (! is_string($email) || ! is_string($proof)) {
            return redirect()->route('password.request');
        }

        $password = $request->validated('password');
        $reset = DB::transaction(function () use ($email, $password, $proof): bool {
            $user = User::query()->where('email', $email)->lockForUpdate()->first();

            if ($user === null) {
                return false;
            }

            $challenge = PasswordResetOtp::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($challenge === null || ! $this->proofMatches($challenge, $proof)) {
                return false;
            }

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
            $challenge->delete();

            return true;
        });

        if (! $reset) {
            return redirect()->route('password.otp.verify')
                ->withErrors(['code' => 'La vérification a expiré. Demandez un nouveau code.']);
        }

        $request->session()->forget([self::EMAIL_SESSION_KEY, self::PROOF_SESSION_KEY]);
        $request->session()->regenerate();

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
    }

    private function issueCode(User $user): bool
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $challenge = PasswordResetOtp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
                'verified_at' => null,
                'proof_hash' => null,
                'proof_expires_at' => null,
            ],
        );

        try {
            Notification::send($user, new PasswordResetOtpNotification($code));
        } catch (Throwable $exception) {
            $challenge->delete();
            report($exception);

            return false;
        }

        return true;
    }

    private function validProofChallenge(): ?PasswordResetOtp
    {
        $email = session(self::EMAIL_SESSION_KEY);
        $proof = session(self::PROOF_SESSION_KEY);

        if (! is_string($email) || ! is_string($proof)) {
            return null;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return null;
        }

        $challenge = PasswordResetOtp::query()->where('user_id', $user->id)->first();

        return $challenge !== null && $this->proofMatches($challenge, $proof) ? $challenge : null;
    }

    private function proofMatches(PasswordResetOtp $challenge, string $proof): bool
    {
        return $challenge->verified_at !== null
            && $challenge->proof_hash !== null
            && $challenge->proof_expires_at?->isFuture() === true
            && hash_equals($challenge->proof_hash, hash('sha256', $proof));
    }

    private function genericEmailStatus(): string
    {
        return 'Si un compte correspond à cette adresse, un code de vérification lui a été envoyé.';
    }
}
