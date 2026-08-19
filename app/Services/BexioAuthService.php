<?php

namespace App\Services;

use App\Models\BexioToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BexioAuthService
{
    protected string $tokenEndpoint = 'https://auth.bexio.com/realms/bexio/protocol/openid-connect/token';

    public function getValidAccessToken(): string
    {
        $token = BexioToken::first();

        if (!$token) {
            throw new \RuntimeException('Kein Bexio-Token vorhanden. Bitte zuerst den Authorization Code Flow durchführen.');
        }

        if ($token->isExpired()) {
            $token = $this->refreshToken($token);
        }

        return $token->access_token;
    }

    protected function refreshToken(BexioToken $token): BexioToken
    {
        $response = Http::asForm()->post($this->tokenEndpoint, [
            'client_id' => config('services.bexio.client_id'),
            'client_secret' => config('services.bexio.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
        ]);

        if (!$response->successful()) {
            Log::error('Bexio Token Refresh fehlgeschlagen', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Bexio Token konnte nicht erneuert werden. Eventuell muss der Benutzer neu autorisieren.');
        }

        $data = $response->json();

        // WICHTIG: Bexio gibt bei jedem Refresh einen NEUEN refresh_token zurück.
        // Der alte wird ungültig -> immer überschreiben, nie den ursprünglichen wiederverwenden.
        $token->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $token->fresh();
    }

    public function exchangeAuthorizationCode(string $code, string $redirectUri): BexioToken
    {
        $response = Http::asForm()->post($this->tokenEndpoint, [
            'client_id' => config('services.bexio.client_id'),
            'client_secret' => config('services.bexio.client_secret'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);

        $data = $response->json();

        return BexioToken::updateOrCreate(
            ['id' => 1], // Single-Tenant-Annahme: nur ein Token für deine Genossenschaft
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]
        );
    }
}