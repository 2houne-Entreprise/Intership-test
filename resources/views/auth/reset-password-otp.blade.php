<x-auth-split-layout>
    <x-auth-session-status class="tf-auth-status" :status="session('status')" />

    <div class="tf-auth-heading">
        <h1>Nouveau mot de passe</h1>
        <p>Choisissez un nouveau mot de passe pour votre compte TaskFlow.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="tf-auth-form">
        @csrf
        <div class="tf-auth-field" x-data="{ showPassword: false }">
            <label for="password">Nouveau mot de passe</label>
            <div class="tf-auth-input-wrap tf-auth-password-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/></svg>
                <input id="password" class="tf-auth-input" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Nouveau mot de passe">
                <button type="button" class="tf-auth-password-toggle" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'">
                    <svg x-show="!showPassword" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3 3.6M6.2 6.8C3.8 8.4 2.5 12 2.5 12s3.4 6 9.5 6c1.4 0 2.6-.3 3.7-.8"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="tf-auth-error" />
        </div>

        <div class="tf-auth-field">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <div class="tf-auth-input-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/></svg>
                <input id="password_confirmation" class="tf-auth-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmez le mot de passe">
            </div>
        </div>

        <button type="submit" class="tf-auth-submit"><span>Enregistrer le nouveau mot de passe</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
    </form>

    <p class="tf-auth-register-prompt"><a href="{{ route('login') }}">← Retour à la connexion</a></p>
</x-auth-split-layout>