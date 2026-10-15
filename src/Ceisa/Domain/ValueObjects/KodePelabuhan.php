<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use JsonSerializable;
use Src\Ceisa\Domain\Enums\Negara;
use Src\Ceisa\Domain\Exceptions\InvalidKodePelabuhanException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class KodePelabuhan
 *
 * Immutable Value Object representing Port Code (Kode Pelabuhan) according to
 * the UN/LOCODE standard (United Nations Code for Trade and Transport Locations / UNECE Rec 16)
 * used in CEISA 4.0 Bea Cukai, manifest BC 1.1, and international trade/shipping logistics.
 *
 * Standard & Specifications:
 * - Length: Exactly 5 characters.
 * - Country Code (ISO 3166-1 alpha-2): 2 uppercase letters (A-Z) (e.g., 'ID' for Indonesia, 'SG', 'MY').
 * - Location Code: 3 alphanumeric characters (A-Z, 0-9) (e.g., 'TPP' for Tanjung Priok, 'SUB' for Tanjung Perak).
 * - Format: Normalized to uppercase, trimmed of leading/trailing spaces and internal spacing/hyphens.
 * - Domestic Port: Country code equals 'ID' (e.g., 'IDTPP', 'IDSUB', 'IDBLW').
 * - International Port: Country code differs from 'ID' (e.g., 'SGSIN', 'MYPKG', 'NLRTM').
 */
final readonly class KodePelabuhan implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int REQUIRED_LENGTH = 5;

    public const int COUNTRY_CODE_LENGTH = 2;

    public const int LOCATION_CODE_LENGTH = 3;

    public const string DOMESTIC_COUNTRY_CODE = 'ID';

    public const string PATTERN = '/\A(?<country>[A-Z]{2})(?<location>[A-Z0-9]{3})\z/';

    public string $value;

    public string $countryCode;

    public string $locationCode;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidKodePelabuhanException::empty();
        }

        // Normalize delimiters (like hyphens or spaces) and convert to uppercase
        $normalized = strtoupper((string) preg_replace('/[-\s]+/', '', $trimmed));
        $length = mb_strlen($normalized);

        if ($length !== self::REQUIRED_LENGTH) {
            throw InvalidKodePelabuhanException::invalidLength($value, $length);
        }

        if (preg_match(self::PATTERN, $normalized, $matches) !== 1) {
            $countryPart = substr($normalized, 0, self::COUNTRY_CODE_LENGTH);
            if (! preg_match('/\A[A-Z]{2}\z/', $countryPart)) {
                throw InvalidKodePelabuhanException::invalidCountryCode($value, $countryPart);
            }

            $locationPart = substr($normalized, self::COUNTRY_CODE_LENGTH);
            if (! preg_match('/\A[A-Z0-9]{3}\z/', $locationPart)) {
                throw InvalidKodePelabuhanException::invalidLocationCode($value, $locationPart);
            }

            throw InvalidKodePelabuhanException::invalidFormat($value);
        }

        $this->value = $normalized;
        $this->countryCode = $matches['country'];
        $this->locationCode = $matches['location'];
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Create an instance from separate country code and location code parts.
     */
    public static function fromParts(string $countryCode, string $locationCode): self
    {
        return new self($countryCode.$locationCode);
    }

    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new self($value);
    }

    /**
     * Safely attempt to create a KodePelabuhan instance without throwing exceptions.
     */
    public static function tryFrom(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return new self($value);
        } catch (InvalidKodePelabuhanException) {
            return null;
        }
    }

    /**
     * Determine if a given string matches standard port code format.
     */
    public static function isValid(string $value): bool
    {
        try {
            new self($value);

            return true;
        } catch (InvalidKodePelabuhanException) {
            return false;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * Alias for value()
     */
    public function code(): string
    {
        return $this->value;
    }

    /**
     * 2-letter ISO 3166-1 alpha-2 country code.
     */
    public function countryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * 3-character location/port code.
     */
    public function locationCode(): string
    {
        return $this->locationCode;
    }

    /**
     * Check if port is in Indonesia (UN/LOCODE country code 'ID').
     */
    public function isDomestic(): bool
    {
        return $this->countryCode === self::DOMESTIC_COUNTRY_CODE;
    }

    /**
     * Alias for isDomestic().
     */
    public function isIndonesia(): bool
    {
        return $this->isDomestic();
    }

    /**
     * Check if port is international (outside Indonesia).
     */
    public function isInternational(): bool
    {
        return $this->countryCode !== self::DOMESTIC_COUNTRY_CODE;
    }

    /**
     * Resolve corresponding Negara enum, if defined.
     */
    public function country(): ?Negara
    {
        return Negara::tryFrom($this->countryCode);
    }

    /**
     * Format port code with an optional separator (e.g. "ID TPP" or "ID-TPP").
     */
    public function formatted(string $delimiter = ' '): string
    {
        return $this->countryCode.$delimiter.$this->locationCode;
    }

    /**
     * Structural equality check.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

class_alias(KodePelabuhan::class, 'Src\Ceisa\Domain\ValueObjects\PortCode');
