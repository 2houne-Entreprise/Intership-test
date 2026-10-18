<x-auth-split-layout>
    <x-auth-session-status class="tf-auth-status" :status="session('status')" />

    <div class="tf-auth-heading">
        <h1>Connexion</h1>
        <p>Connectez-vous à votre espace TaskFlow</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="tf-auth-form">
        @csrf
        <div class="tf-auth-field">
            <label for="email">Adresse e-mail</label>
            <div class="tf-auth-input-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                <input id="email" class="tf-auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="votre@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="tf-auth-error" />
        </div>

        <div class="tf-auth-field" x-data="{ showPassword: false }">
            <label for="password">Mot de passe</label>
            <div class="tf-auth-input-wrap tf-auth-password-wrap">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/></svg>
                <input id="password" class="tf-auth-input" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Votre mot de passe">
                <button type="button" class="tf-auth-password-toggle" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" :title="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'">
                    <svg x-show="!showPassword" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.8 10.8 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3 3.6M6.2 6.8C3.8 8.4 2.5 12 2.5 12s3.4 6 9.5 6c1.4 0 2.6-.3 3.7-.8"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="tf-auth-error" />
        </div>

        <div class="tf-auth-options">
            <label class="tf-auth-remember" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>Se souvenir de moi</span>
            </label>
            <a href="{{ route('password.request') }}" class="tf-auth-link">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="tf-auth-submit">
            <span>Se connecter</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
        </button>
    </form>

    <div class="tf-auth-divider"><span>ou</span></div>
    <p class="tf-auth-register-prompt">Vous n’avez pas de compte ? <a href="{{ route('register') }}">S’inscrire</a></p>
</x-auth-split-layout>
