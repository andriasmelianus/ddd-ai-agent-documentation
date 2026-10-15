<?php

declare(strict_types=1);

namespace Src\Ceisa\Infrastructure\Gateway;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;
use Src\Ceisa\Domain\Exceptions\LoginFailedException;
use Src\Ceisa\Domain\Services\AuthServiceInterface;
use Src\Ceisa\Domain\ValueObjects\AuthCredentials;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;
use Throwable;

final readonly class AuthServiceAdapter implements AuthServiceInterface
{
    private const string DEFAULT_AUTH_URL = 'https://sandbox-gw.beacukai.go.id/v1/openapi-auth/user/login';

    private string $authUrl;

    private ?string $apiKey;

    public function __construct(
        ?string $authUrl = null,
        ?string $apiKey = null,
        private ?HttpFactory $http = null,
    ) {
        $this->authUrl = $authUrl ?? $this->resolveConfig('beacukai.ceisa_40.auth_url', self::DEFAULT_AUTH_URL);
        $this->apiKey = $apiKey ?? $this->resolveConfig('beacukai.ceisa_40.api_key', '');
    }

    public function login(AuthCredentials $credentials): AuthLoginResult
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if (! empty($this->apiKey)) {
            $headers['beacukai-api-key'] = $this->apiKey;
        }

        $payload = [
            'username' => $credentials->username->value(),
            'password' => $credentials->password->value(),
        ];

        try {
            $client = $this->resolveHttpClient();
            $response = $client->withHeaders($headers)->post($this->authUrl, $payload);
        } catch (Throwable $e) {
            throw LoginFailedException::fromPrevious($e);
        }

        if (! $response->successful()) {
            $message = $response->json('message') ?? $response->body();

            throw new LoginFailedException(
                sprintf('Login CEISA 4.0 gagal (%d): %s', $response->status(), $message),
                $response->status()
            );
        }

        try {
            /** @var array<string, mixed> $data */
            $data = $response->json() ?? [];

            return AuthLoginResult::fromResponse($data);
        } catch (Throwable $e) {
            throw LoginFailedException::fromPrevious($e);
        }
    }

    private function resolveHttpClient(): HttpFactory
    {
        if ($this->http !== null) {
            return $this->http;
        }

        try {
            $facadeRoot = Http::getFacadeRoot();
            if ($facadeRoot !== null) {
                return $facadeRoot;
            }
        } catch (Throwable) {
            // Container not booted or facade not set
        }

        return new HttpFactory;
    }

    private function resolveConfig(string $key, string $default): string
    {
        try {
            if (function_exists('config')) {
                $value = config($key);
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            }
        } catch (Throwable) {
            // Container not booted
        }

        return $default;
    }
}
