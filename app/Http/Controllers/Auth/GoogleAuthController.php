<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! class_exists(Socialite::class) && config('seo.demo_mode')) {
            $user = User::firstOrCreate(
                ['email' => 'demo@seo-tool.test'],
                ['name' => 'Demo SEO User', 'password' => bcrypt(str()->random(32))]
            );

            Auth::login($user, true);

            return redirect()->route('dashboard')->with('status', 'Sesion demo iniciada. Instala Socialite para conectar Google real.');
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
        $googleUser = Socialite::driver('google')->stateless()->user();

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
}
