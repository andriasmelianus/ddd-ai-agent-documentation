<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\InvalidNomorKontainerException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class NomorKontainer
 *
 * Immutable Value Object representing a Standard Shipping Container Number (ISO 6346).
 *
 * Business Rules & Invariants (ISO 6346 Standard):
 * - Length: Exactly 11 alphanumeric characters.
 * - Owner Code: 3 uppercase letters (A-Z) identifying the owner/operator.
 * - Equipment Category Identifier: 1 uppercase letter:
 *     - 'U' for all freight containers (standard shipping).
 *     - 'J' for detachable freight container-related equipment.
 *     - 'Z' for trailers and chassis.
 * - Serial Number: 6 numeric digits (0-9).
 * - Check Digit: 1 numeric digit (0-9), calculated using the ISO 6346 modulo 11 algorithm.
 *
 * Input is automatically trimmed, stripped of standard delimiters (spaces, hyphens), and capitalized.
 */
final readonly class NomorKontainer implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int REQUIRED_LENGTH = 11;

    public const array STANDARD_CATEGORIES = ['U', 'J', 'Z'];

    private const string PATTERN_BASE = '/\A[A-Z]{4}\d{7}\z/';

    private const array LETTER_VALUES = [
        'A' => 10,
        'B' => 12,
        'C' => 13,
        'D' => 14,
        'E' => 15,
        'F' => 16,
        'G' => 17,
        'H' => 18,
        'I' => 19,
        'J' => 20,
        'K' => 21,
        'L' => 23,
        'M' => 24,
        'N' => 25,
        'O' => 26,
        'P' => 27,
        'Q' => 28,
        'R' => 29,
        'S' => 30,
        'T' => 31,
        'U' => 32,
        'V' => 34,
        'W' => 35,
        'X' => 36,
        'Y' => 37,
        'Z' => 38,
    ];

    public string $value;

    public string $ownerCode;

    public string $categoryIdentifier;

    public string $serialNumber;

    public int $checkDigit;

    public function __construct(
        string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ) {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidNomorKontainerException::empty();
        }

        // Normalize by removing spaces, hyphens and converting to uppercase
        $normalized = strtoupper((string) preg_replace('/[-\s]+/', '', $trimmed));
        $length = strlen($normalized);

        if ($length !== self::REQUIRED_LENGTH) {
            throw InvalidNomorKontainerException::invalidLength($value, $length);
        }

        if (preg_match(self::PATTERN_BASE, $normalized) !== 1) {
            throw InvalidNomorKontainerException::invalidFormat($value);
        }

        $category = $normalized[3];
        if ($strictCategory && ! in_array($category, self::STANDARD_CATEGORIES, true)) {
            throw InvalidNomorKontainerException::invalidCategoryIdentifier($value, $category);
        }

        $expectedCheckDigit = self::calculateCheckDigit(substr($normalized, 0, 10));
        $actualCheckDigit = (int) $normalized[10];

        if ($verifyCheckDigit && $actualCheckDigit !== $expectedCheckDigit) {
            throw InvalidNomorKontainerException::invalidCheckDigit(
                $value,
                $expectedCheckDigit,
                $actualCheckDigit,
            );
        }

        $this->value = $normalized;
        $this->ownerCode = substr($normalized, 0, 3);
        $this->categoryIdentifier = $category;
        $this->serialNumber = substr($normalized, 4, 6);
        $this->checkDigit = $actualCheckDigit;
    }

    public static function create(
        string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ): self {
        return new self($value, $verifyCheckDigit, $strictCategory);
    }

    public static function fromString(
        string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ): self {
        return new self($value, $verifyCheckDigit, $strictCategory);
    }

    /**
     * Create an instance bypassing the check digit calculation validation.
     * Useful when handling legacy systems, dummy test data, or unverified external manifests.
     */
    public static function fromUnverified(string $value, bool $strictCategory = true): self
    {
        return new self($value, verifyCheckDigit: false, strictCategory: $strictCategory);
    }

    public static function fromStringOrNull(
        ?string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ): ?self {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return new self($value, $verifyCheckDigit, $strictCategory);
    }

    /**
     * Safely attempt to create a NomorKontainer instance.
     * Returns null instead of throwing an exception on failure.
     */
    public static function tryFrom(
        ?string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ): ?self {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return new self($value, $verifyCheckDigit, $strictCategory);
        } catch (InvalidNomorKontainerException) {
            return null;
        }
    }

    /**
     * Determine if a given string matches standard container number format and optional check digit.
     */
    public static function isValid(
        string $value,
        bool $verifyCheckDigit = true,
        bool $strictCategory = true,
    ): bool {
        try {
            new self($value, $verifyCheckDigit, $strictCategory);

            return true;
        } catch (InvalidNomorKontainerException) {
            return false;
        }
    }

    /**
     * Calculate ISO 6346 check digit (Modulo 11) for a 10-character prefix and serial number.
     */
    public static function calculateCheckDigit(string $prefixAndSerial): int
    {
        $normalized = strtoupper((string) preg_replace('/[-\s]+/', '', trim($prefixAndSerial)));
        $clean10 = substr($normalized, 0, 10);

        if (strlen($clean10) < 10) {
            throw InvalidNomorKontainerException::invalidFormat($prefixAndSerial);
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $char = $clean10[$i];
            if (ctype_alpha($char)) {
                $val = self::LETTER_VALUES[$char] ?? 0;
            } elseif (ctype_digit($char)) {
                $val = (int) $char;
            } else {
                throw InvalidNomorKontainerException::invalidFormat($prefixAndSerial);
            }

            $sum += $val * (1 << $i);
        }

        $remainder = $sum % 11;

        // In ISO 6346, if the remainder is 10, the check digit is 0
        return $remainder % 10;
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * Alias for value()
     */
    public function number(): string
    {
        return $this->value;
    }

    /**
     * 3-letter BIC Owner Code.
     */
    public function ownerCode(): string
    {
        return $this->ownerCode;
    }

    /**
     * 1-letter Equipment Category Identifier ('U', 'J', 'Z').
     */
    public function categoryIdentifier(): string
    {
        return $this->categoryIdentifier;
    }

    /**
     * Alias for categoryIdentifier()
     */
    public function equipmentCategory(): string
    {
        return $this->categoryIdentifier;
    }

    /**
     * Full 4-letter prefix (Owner Code + Category Identifier).
     */
    public function prefix(): string
    {
        return $this->ownerCode . $this->categoryIdentifier;
    }

    /**
     * 6-digit Serial Number.
     */
    public function serialNumber(): string
    {
        return $this->serialNumber;
    }

    /**
     * Actual check digit from the container number.
     */
    public function checkDigit(): int
    {
        return $this->checkDigit;
    }

    /**
     * Calculated check digit according to ISO 6346 Modulo 11 algorithm.
     */
    public function expectedCheckDigit(): int
    {
        return self::calculateCheckDigit(substr($this->value, 0, 10));
    }

    /**
     * Verifies if the container number's check digit matches the ISO 6346 expected check digit.
     */
    public function hasValidCheckDigit(): bool
    {
        return $this->checkDigit === $this->expectedCheckDigit();
    }

    /**
     * Check if equipment category is 'U' (Standard Freight Container).
     */
    public function isFreightContainer(): bool
    {
        return $this->categoryIdentifier === 'U';
    }

    /**
     * Check if equipment category is 'J' (Detachable container equipment, e.g. genset).
     */
    public function isDetachableEquipment(): bool
    {
        return $this->categoryIdentifier === 'J';
    }

    /**
     * Check if equipment category is 'Z' (Trailer / Chassis).
     */
    public function isChassisOrTrailer(): bool
    {
        return $this->categoryIdentifier === 'Z';
    }

    /**
     * Format container number with standard visual spacing, e.g. "CSQU 305438-3" or "CSQU 305438 3".
     */
    public function formatted(string $delimiter = ' ', bool $dashBeforeCheckDigit = true): string
    {
        $checkSeparator = $dashBeforeCheckDigit ? '-' : $delimiter;

        return sprintf(
            '%s%s%s%s%d',
            $this->prefix(),
            $delimiter,
            $this->serialNumber,
            $checkSeparator,
            $this->checkDigit,
        );
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

class_alias(NomorKontainer::class, 'Src\Ceisa\Domain\ValueObjects\ContainerNumber');
