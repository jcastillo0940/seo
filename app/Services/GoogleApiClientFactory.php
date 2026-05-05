<?php

namespace App\Services;

use App\Models\User;
use RuntimeException;

class GoogleApiClientFactory
{
    public function make(User $user, array $scopes = []): object
    {
        if (! class_exists(\Google\Client::class)) {
            throw new RuntimeException('google/apiclient no esta instalado. Ejecuta composer require cuando Composer tenga acceso SSL.');
        }

        $client = new \Google\Client();
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes($scopes);

        if ($user->google_token) {
            $token = [
                'access_token' => $user->google_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => max(now()->diffInSeconds($user->google_token_expires_at, false), 0),
                'created' => now()->subSeconds(max(now()->diffInSeconds($user->google_token_expires_at, false), 0))->timestamp,
            ];

            $client->setAccessToken($token);
        }

        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            $freshToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (! isset($freshToken['error'])) {
                $user->forceFill([
                    'google_token' => $freshToken['access_token'] ?? $user->google_token,
                    'google_refresh_token' => $freshToken['refresh_token'] ?? $user->google_refresh_token,
                    'google_token_expires_at' => now()->addSeconds((int) ($freshToken['expires_in'] ?? 3600)),
                ])->save();

                $client->setAccessToken($freshToken);
            }
        }

        return $client;
    }
}
