<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class BexioApiService
{
    public function __construct(protected BexioAuthService $auth) {}

    public function get(string $endpoint, array $query = [])
    {
        $response = $this->request()->get("https://api.bexio.com/2.0/{$endpoint}", $query);
                
        $this->validateResponse($response);

        return $response->json();
    }

    public function post(string $endpoint, array $data = [])
    {
        $body = empty($data) ? new \stdClass() : $data;
        $response = $this->request()->post("https://api.bexio.com/2.0/{$endpoint}", $body);

        $this->validateResponse($response);

        return $response->json();
    }

    public function postAction(string $endpoint): array
    {
        $response = Http::withToken($this->auth->getValidAccessToken())
            ->acceptJson()
            ->send('POST', "https://api.bexio.com/2.0/{$endpoint}");

        $this->validateResponse($response);

        return $response->json();
    }

    protected function request()
    {
        return Http::withToken($this->auth->getValidAccessToken())
            ->withHeader('Accept', 'application/json')
            ->contentType('application/json')
            ->acceptJson();
    }

    protected function validateResponse($response): void
    {
        if ($response->failed()) {
            throw new RequestException($response);
        }

        $data = $response->json();
        if (is_array($data) && isset($data['error_code'])) {
            throw new \RuntimeException("Bexio API Fehler {$data['error_code']}: {$data['message']}");
        }
    }
}