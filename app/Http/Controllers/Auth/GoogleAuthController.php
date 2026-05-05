<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (config('seo.demo_mode')) {
            abort(403, 'El modo demo no puede usarse en un acceso publico.');
        }

        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/webmasters.readonly',
                'https://www.googleapis.com/auth/analytics.readonly',
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        if (! $this->isAllowedEmail((string) $googleUser->getEmail())) {
            return redirect()
                ->route('home')
                ->with('status', 'Tu cuenta de Google no esta autorizada para acceder.');
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'SEO User',
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
                'password' => bcrypt(str()->random(32)),
            ]
        );

        Auth::login($user, true);

        return redirect()->route('dashboard')->with('status', 'Google conectado correctamente.');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function isAllowedEmail(string $email): bool
    {
        $email = Str::lower(trim($email));
        $allowedEmails = config('auth.access.allowed_emails', []);
        $allowedDomains = config('auth.access.allowed_domains', []);
        $requireAllowlist = (bool) config('auth.access.require_allowlist', true);

        if (! $requireAllowlist && $allowedEmails === [] && $allowedDomains === []) {
            return true;
        }

        if (in_array($email, $allowedEmails, true)) {
            return true;
        }

        $domain = Str::after($email, '@');

        return $domain !== $email && in_array($domain, $allowedDomains, true);
    }
}
