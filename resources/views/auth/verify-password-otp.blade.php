<x-auth-split-layout>
    <x-auth-session-status class="tf-auth-status" :status="session('status')" />

    <div class="tf-auth-heading">
        <h1>Vérifiez votre adresse e-mail</h1>
        <p>Nous avons envoyé un code de vérification à votre adresse e-mail.</p>
        <span class="tf-auth-recipient">{{ $email }}</span>
    </div>

    <form method="POST" action="{{ route('password.otp.verify.store') }}" class="tf-auth-form">
        @csrf
        <div class="tf-auth-field">
            <label for="code">Code de vérification</label>
            <input id="code" class="tf-auth-code-input" type="text" name="code" value="{{ old('code') }}" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" minlength="6" required autofocus placeholder="••••••">
            <x-input-error :messages="$errors->get('code')" class="tf-auth-error" />
        </div>

        <button type="submit" class="tf-auth-submit"><span>Vérifier le code</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
    </form>

    <form method="POST" action="{{ route('password.otp.resend') }}" class="tf-auth-register-prompt">
        @csrf
        <span>Vous n’avez pas reçu le code ? </span>
        <button type="submit" class="tf-auth-link-button">Renvoyer le code</button>
    </form>
    <p class="tf-auth-back-link"><a href="{{ route('password.request') }}">Utiliser une autre adresse e-mail</a></p>
</x-auth-split-layout>