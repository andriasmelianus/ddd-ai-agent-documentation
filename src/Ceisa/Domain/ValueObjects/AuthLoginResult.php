<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidAuthLoginResultException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;

/**
 * Class AuthLoginResult
 *
 * Immutable composite Value Object encapsulating CEISA 4.0 authentication tokens.
 */
final readonly class AuthLoginResult implements ValueObjectInterface
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn,
        public string $refreshToken,
        public int $refreshExpiresIn,
    ) {
        if (trim($this->accessToken) === '') {
            throw InvalidAuthLoginResultException::emptyAccessToken();
        }

        if (trim($this->tokenType) === '') {
            throw InvalidAuthLoginResultException::emptyTokenType();
        }

        if ($this->expiresIn <= 0) {
            throw InvalidAuthLoginResultException::invalidExpiresIn($this->expiresIn);
        }

        if (trim($this->refreshToken) === '') {
            throw InvalidAuthLoginResultException::emptyRefreshToken();
        }

        if ($this->refreshExpiresIn <= 0) {
            throw InvalidAuthLoginResultException::invalidRefreshExpiresIn($this->refreshExpiresIn);
        }
    }

    public static function create(
        string $accessToken,
        string $tokenType,
        int $expiresIn,
        string $refreshToken,
        int $refreshExpiresIn,
    ): self {
        return new self(
            $accessToken,
            $tokenType,
            $expiresIn,
            $refreshToken,
            $refreshExpiresIn,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromResponse(array $payload): self
    {
        $payloadContent = $payload['item'] ?? $payload['data'] ?? $payload;

        $accessToken = (string) ($payloadContent['access_token'] ?? $payloadContent['token'] ?? '');
        $tokenType = (string) ($payloadContent['token_type'] ?? 'Bearer');
        $expiresIn = (int) ($payloadContent['expires_in'] ?? 0);
        $refreshToken = (string) ($payloadContent['refresh_token'] ?? '');
        $refreshExpiresIn = (int) ($payloadContent['refresh_expires_in'] ?? 0);

        return new self(
            accessToken: $accessToken,
            tokenType: $tokenType,
            expiresIn: $expiresIn,
            refreshToken: $refreshToken,
            refreshExpiresIn: $refreshExpiresIn,
        );
    }

    public function getAuthorizationHeader(): string
    {
        return trim("{$this->tokenType} {$this->accessToken}");
    }

    public function equals(self $other): bool
    {
        return $this->accessToken === $other->accessToken
            && $this->tokenType === $other->tokenType
            && $this->expiresIn === $other->expiresIn
            && $this->refreshToken === $other->refreshToken
            && $this->refreshExpiresIn === $other->refreshExpiresIn;
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
}
