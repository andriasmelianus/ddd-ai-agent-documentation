<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use DateTimeImmutable;

/**
 * Class ApiAccessConfiguration
 *
 * Composite Value Object representing external client H2H API credentials and access configuration.
 */
readonly class ApiAccessConfiguration
{
    public function __construct(
        public ?string $username = null,
        public ?string $password = null,
        public ?string $apiKey = null,
        public ?AllowedIps $allowedIps = null,
        public ?DateTimeImmutable $lastHitAt = null,
    ) {}

    public static function create(
        ?string $username = null,
        ?string $password = null,
        ?string $apiKey = null,
        ?AllowedIps $allowedIps = null,
        ?DateTimeImmutable $lastHitAt = null,
    ): self {
        return new self(
            $username !== null ? trim($username) : null,
            $password,
            $apiKey !== null ? trim($apiKey) : null,
            $allowedIps,
            $lastHitAt,
        );
    }

    public static function fromPrimitives(
        ?string $username = null,
        ?string $password = null,
        ?string $apiKey = null,
        ?string $allowedIps = null,
        ?string $lastHitAt = null,
    ): self {
        $lastHit = null;
        if ($lastHitAt !== null && trim($lastHitAt) !== '') {
            $lastHit = new DateTimeImmutable($lastHitAt);
        }

        return self::create(
            username: $username,
            password: $password,
            apiKey: $apiKey,
            allowedIps: AllowedIps::fromStringOrNull($allowedIps),
            lastHitAt: $lastHit,
        );
    }

    public static function empty(): self
    {
        return new self;
    }

    public function isClientAllowed(string $clientIp): bool
    {
        if ($this->allowedIps === null) {
            return true;
        }

        return $this->allowedIps->isAllowed($clientIp);
    }

    public function withLastHitAt(DateTimeImmutable $hitAt): self
    {
        return new self(
            $this->username,
            $this->password,
            $this->apiKey,
            $this->allowedIps,
            $hitAt,
        );
    }

    public function equals(self $other): bool
    {
        $ipsEquals = ($this->allowedIps === null && $other->allowedIps === null)
            || ($this->allowedIps !== null && $other->allowedIps !== null && $this->allowedIps->equals($other->allowedIps));

        $hitEquals = ($this->lastHitAt === null && $other->lastHitAt === null)
            || ($this->lastHitAt !== null && $other->lastHitAt !== null && $this->lastHitAt->format('c') === $other->lastHitAt->format('c'));

        return $this->username === $other->username
            && $this->password === $other->password
            && $this->apiKey === $other->apiKey
            && $ipsEquals
            && $hitEquals;
    }
}
