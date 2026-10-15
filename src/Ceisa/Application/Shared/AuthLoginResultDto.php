<?php

declare(strict_types=1);

namespace Src\Ceisa\Application\Shared;

use JsonSerializable;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;

/**
 * Class AuthLoginResultDto
 *
 * Immutable Read DTO representing the authentication result of CEISA 4.0.
 * Contains all properties from AuthLoginResult.
 */
final readonly class AuthLoginResultDto implements JsonSerializable
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public string $refreshToken,
        public int $refreshExpiresIn,
    ) {}

    public static function fromValueObject(AuthLoginResult $loginResult): self
    {
        return new self(
            accessToken: $loginResult->accessToken,
            tokenType: $loginResult->tokenType,
            expiresIn: $loginResult->expiresIn,
            refreshToken: $loginResult->refreshToken,
            refreshExpiresIn: $loginResult->refreshExpiresIn,
        );
    }

    /**
     * @param array{access_token: string, token_type: string, expires_in: int, refresh_token: string, refresh_expires_in: int} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: (string) ($data['access_token'] ?? ''),
            tokenType: (string) ($data['token_type'] ?? 'Bearer'),
            expiresIn: (int) ($data['expires_in'] ?? 0),
            refreshToken: (string) ($data['refresh_token'] ?? ''),
            refreshExpiresIn: (int) ($data['refresh_expires_in'] ?? 0),
        );
    }

    public function getAuthorizationHeader(): string
    {
        return trim("{$this->tokenType} {$this->accessToken}");
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int, refresh_token: string, refresh_expires_in: int}
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'refresh_token' => $this->refreshToken,
            'refresh_expires_in' => $this->refreshExpiresIn,
        ];
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int, refresh_token: string, refresh_expires_in: int}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
