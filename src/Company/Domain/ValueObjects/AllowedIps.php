<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidAllowedIpsException;
use Stringable;

/**
 * Class AllowedIps
 *
 * Immutable Value Object representing IP whitelist (IPv4, IPv6, CIDR).
 */
readonly class AllowedIps implements Stringable
{
    /**
     * @var string[]
     */
    public array $ips;

    /**
     * @param  string[]  $ips
     */
    public function __construct(array $ips)
    {
        $validated = [];

        foreach ($ips as $ip) {
            $trimmed = trim($ip);
            if ($trimmed === '') {
                continue;
            }

            if (! self::isValidIpOrCidr($trimmed)) {
                throw InvalidAllowedIpsException::invalidIpOrCidr($trimmed);
            }

            $validated[] = $trimmed;
        }

        $this->ips = array_values(array_unique($validated));
    }

    public static function fromString(string $ipsString): self
    {
        $parts = preg_split('/[,;\s]+/', trim($ipsString), -1, PREG_SPLIT_NO_EMPTY);

        return new self($parts ?: []);
    }

    public static function fromStringOrNull(?string $ipsString): ?self
    {
        if ($ipsString === null || trim($ipsString) === '') {
            return null;
        }

        return self::fromString($ipsString);
    }

    /**
     * @param  string[]  $ips
     */
    public static function fromArray(array $ips): self
    {
        return new self($ips);
    }

    public static function any(): self
    {
        return new self([]);
    }

    public function allowsAny(): bool
    {
        return empty($this->ips);
    }

    public function isAllowed(string $ip): bool
    {
        if ($this->allowsAny()) {
            return true;
        }

        foreach ($this->ips as $allowed) {
            if (str_contains($allowed, '/')) {
                if (self::cidrMatch($ip, $allowed)) {
                    return true;
                }
            } elseif ($allowed === $ip) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->ips;
    }

    public function value(): string
    {
        return implode(',', $this->ips);
    }

    public function equals(self $other): bool
    {
        return $this->ips === $other->ips;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private static function isValidIpOrCidr(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        if (str_contains($ip, '/')) {
            [$subnet, $bits] = explode('/', $ip, 2);
            if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return ctype_digit($bits) && (int) $bits >= 0 && (int) $bits <= 32;
            }
            if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
                return ctype_digit($bits) && (int) $bits >= 0 && (int) $bits <= 128;
            }
        }

        return false;
    }

    private static function cidrMatch(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr, 2);

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $maskInt = (int) $mask;
        if ($maskInt === 0) {
            return true;
        }

        $netmask = ~((1 << (32 - $maskInt)) - 1);

        return ($ipLong & $netmask) === ($subnetLong & $netmask);
    }
}
