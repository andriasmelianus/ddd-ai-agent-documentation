<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

/**
 * Class Ceisa40Credentials
 *
 * Immutable Value Object representing CEISA 4.0 Customs H2H API connection credentials.
 */
readonly class Ceisa40Credentials
{
    public function __construct(
        public ?string $username = null,
        public ?string $password = null,
        public ?string $apiKey = null,
    ) {}

    public static function create(?string $username = null, ?string $password = null, ?string $apiKey = null): self
    {
        return new self(
            $username !== null ? trim($username) : null,
            $password,
            $apiKey !== null ? trim($apiKey) : null,
        );
    }

    public static function empty(): self
    {
        return new self;
    }

    public function isConfigured(): bool
    {
        return $this->username !== null && $this->password !== null;
    }

    public function hasApiKey(): bool
    {
        return $this->apiKey !== null && $this->apiKey !== '';
    }

    public function equals(self $other): bool
    {
        return $this->username === $other->username
            && $this->password === $other->password
            && $this->apiKey === $other->apiKey;
    }
}
