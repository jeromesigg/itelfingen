<?php

namespace App\Services;

use App\Exceptions\BexioException;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Aufrufe der Bexio-API. Jeder Fehler (keine Verbindung, Fehlerantwort, error_code im JSON) wird zur BexioException;
 * im Admin-Bereich zeigt App\Exceptions\Handler sie als Meldung statt als Fehlerseite.
 */
class BexioApiService
{
    public function __construct(protected BexioAuthService $auth) {}

    public function get(string $endpoint, array $query = [])
    {
        $response = $this->send(fn () => $this->request()->get("https://api.bexio.com/2.0/{$endpoint}", $query));

        $this->validateResponse($response);

        return $response->json();
    }

    public function post(string $endpoint, array $data = [])
    {
        $body = empty($data) ? new \stdClass : $data;
        $response = $this->send(fn () => $this->request()->post("https://api.bexio.com/2.0/{$endpoint}", $body));

        $this->validateResponse($response);

        return $response->json();
    }

    public function postAction(string $endpoint): array
    {
        $response = $this->send(fn () => Http::withToken($this->auth->getValidAccessToken())
            ->acceptJson()
            ->send('POST', "https://api.bexio.com/2.0/{$endpoint}"));

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

    /**
     * @param  Closure(): Response  $request
     */
    protected function send(Closure $request): Response
    {
        try {
            return $request();
        } catch (ConnectionException $e) {
            throw new BexioException('Bexio ist nicht erreichbar.', previous: $e);
        }
    }

    protected function validateResponse(Response $response): void
    {
        if ($response->failed()) {
            $message = $response->json('error_message') ?? $response->json('message');
            throw new BexioException(
                'Bexio antwortet mit Fehler '.$response->status().(is_string($message) ? ': '.$message : '.'),
                previous: new RequestException($response),
            );
        }

        $data = $response->json();
        if (is_array($data) && isset($data['error_code'])) {
            throw new BexioException("Bexio API Fehler {$data['error_code']}: {$data['message']}");
        }
    }
}
