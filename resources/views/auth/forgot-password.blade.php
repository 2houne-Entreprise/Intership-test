<x-auth-split-layout>
    <x-auth-session-status class="tf-auth-status" :status="session('status')" />

    <div class="tf-auth-heading">
        <h1>Mot de passe oublié ?</h1>
        <p>Entrez l’adresse e-mail associée à votre compte pour recevoir un code de vérification.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="tf-auth-form">
        @csrf
        <div class="tf-auth-field">
            <label for="email">Adresse e-mail</label>
            <div class="tf-auth-input-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                <input id="email" class="tf-auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="votre@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="tf-auth-error" />
        </div>

        <button type="submit" class="tf-auth-submit"><span>Envoyer le code</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
    </form>

    <p class="tf-auth-register-prompt"><a href="{{ route('login') }}">← Retour à la connexion</a></p>
</x-auth-split-layout>
