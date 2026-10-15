<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\InvalidBlAwbIdentifierException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;
use Throwable;

/**
 * Class BlAwbIdentifier
 *
 * Immutable composite Value Object uniquely identifying a Bill of Lading (B/L)
 * or Air Waybill (AWB) in customs and logistics (e.g., CEISA 4.0 manifest documents).
 *
 * Business Rules & Invariants:
 * - nomorBlAwb must not be empty or whitespace only.
 * - nomorBlAwb is automatically trimmed.
 * - nomorBlAwb maximum length is 64 characters (matching customs/TPS schema standards).
 * - nomorBlAwb allows alphanumeric characters and standard shipping separators (._-/ and space).
 * - tanggalBlAwb must be a valid, parseable calendar date (DateTimeImmutable, DateTimeInterface, or date string).
 * - Provides domain helpers for common B/L and AWB rules (IATA AWB pattern and Mod-7 check digit verification).
 */
final readonly class BlAwbIdentifier implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int MAX_NOMOR_BL_AWB_LENGTH = 64;

    public const int MAX_NUMBER_LENGTH = self::MAX_NOMOR_BL_AWB_LENGTH;

    private const string NOMOR_BL_AWB_PATTERN = '/\A[A-Za-z0-9._\-\/ ]+\z/';

    private const string AWB_PATTERN = '/\A(\d{3})[-\s]?(\d{7,8})\z/';

    private const string STANDARD_IATA_AWB_PATTERN = '/\A(\d{3})[-\s]?(\d{7})(\d)\z/';

    public string $nomorBlAwb;

    public DateTimeImmutable $tanggalBlAwb;

    public function __construct(
        string $nomorBlAwb,
        DateTimeImmutable|DateTimeInterface|string $tanggalBlAwb,
    ) {
        $this->nomorBlAwb = self::validateNomorBlAwb($nomorBlAwb);
        $this->tanggalBlAwb = self::resolveTanggalBlAwb($tanggalBlAwb);
    }

    public static function create(
        string $nomorBlAwb,
        DateTimeImmutable|DateTimeInterface|string $tanggalBlAwb,
    ): self {
        return new self($nomorBlAwb, $tanggalBlAwb);
    }

    public static function fromPrimitives(
        string $nomorBlAwb,
        string $tanggalBlAwb,
        ?string $format = null,
    ): self {
        if ($format !== null) {
            $parsed = DateTimeImmutable::createFromFormat('!'.$format, trim($tanggalBlAwb));
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
                throw InvalidBlAwbIdentifierException::invalidTanggalBlAwb($tanggalBlAwb);
            }

            return new self($nomorBlAwb, $parsed);
        }

        return new self($nomorBlAwb, $tanggalBlAwb);
    }

    public static function fromString(string $nomorBlAwb, string $tanggalBlAwb): self
    {
        return new self($nomorBlAwb, $tanggalBlAwb);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $nomorBlAwb = $payload['nomorBlAwb']
            ?? $payload['nomor_bl_awb']
            ?? $payload['no_bl_awb']
            ?? $payload['no_bl']
            ?? $payload['number']
            ?? null;

        $tanggalBlAwb = $payload['tanggalBlAwb']
            ?? $payload['tanggal_bl_awb']
            ?? $payload['tgl_bl_awb']
            ?? $payload['tgl_bl']
            ?? $payload['date']
            ?? null;

        if ($nomorBlAwb === null || (is_string($nomorBlAwb) && trim($nomorBlAwb) === '')) {
            throw InvalidBlAwbIdentifierException::emptyNomorBlAwb();
        }

        if ($tanggalBlAwb === null || (is_string($tanggalBlAwb) && trim($tanggalBlAwb) === '')) {
            throw InvalidBlAwbIdentifierException::emptyTanggalBlAwb();
        }

        return new self((string) $nomorBlAwb, $tanggalBlAwb);
    }

    public function nomorBlAwb(): string
    {
        return $this->nomorBlAwb;
    }

    public function tanggalBlAwb(): DateTimeImmutable
    {
        return $this->tanggalBlAwb;
    }

    public function formattedTanggalBlAwb(string $format = 'Y-m-d'): string
    {
        return $this->tanggalBlAwb->format($format);
    }

    /**
     * Return date formatted as Y-m-d.
     */
    public function toDateString(): string
    {
        return $this->tanggalBlAwb->format('Y-m-d');
    }

    /**
     * Alias for nomorBlAwb()
     */
    public function number(): string
    {
        return $this->nomorBlAwb;
    }

    /**
     * Alias for tanggalBlAwb()
     */
    public function date(): DateTimeImmutable
    {
        return $this->tanggalBlAwb;
    }

    /**
     * Alias for formattedTanggalBlAwb()
     */
    public function formattedDate(string $format = 'Y-m-d'): string
    {
        return $this->formattedTanggalBlAwb($format);
    }

    /**
     * Normalized uppercase representation of the BL/AWB number with excess whitespace collapsed.
     */
    public function normalizedNomorBlAwb(): string
    {
        return strtoupper((string) preg_replace('/\s+/', ' ', $this->nomorBlAwb));
    }

    /**
     * Alias for normalizedNomorBlAwb()
     */
    public function normalizedNumber(): string
    {
        return $this->normalizedNomorBlAwb();
    }

    /**
     * Determines whether the identifier matches standard Air Waybill (AWB) format
     * (3-digit airline prefix + optional delimiter + 7 or 8 digits).
     */
    public function isAwb(): bool
    {
        return preg_match(self::AWB_PATTERN, $this->nomorBlAwb) === 1;
    }

    /**
     * Determines whether the identifier matches the standard IATA 11-digit AWB format
     * (3-digit prefix + 7-digit serial number + 1 check digit).
     */
    public function isStandardAwb(): bool
    {
        return preg_match(self::STANDARD_IATA_AWB_PATTERN, $this->nomorBlAwb) === 1;
    }

    /**
     * Validates the standard IATA Mod-7 check digit for an Air Waybill.
     * Returns false if the number is not a standard IATA AWB.
     */
    public function hasValidAwbCheckDigit(): bool
    {
        if (preg_match(self::STANDARD_IATA_AWB_PATTERN, $this->nomorBlAwb, $matches) !== 1) {
            return false;
        }

        $serial = (int) $matches[2];
        $expectedCheckDigit = $serial % 7;
        $actualCheckDigit = (int) $matches[3];

        return $expectedCheckDigit === $actualCheckDigit;
    }

    /**
     * Extract the 3-digit IATA airline prefix if the document is an AWB.
     */
    public function airlinePrefix(): ?string
    {
        if (preg_match(self::AWB_PATTERN, $this->nomorBlAwb, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Determines whether the document is an ocean Bill of Lading (not an AWB).
     */
    public function isBl(): bool
    {
        return ! $this->isAwb();
    }

    /**
     * Immutable "with" method returning a new instance with updated nomorBlAwb.
     */
    public function withNomorBlAwb(string $nomorBlAwb): self
    {
        return new self($nomorBlAwb, $this->tanggalBlAwb);
    }

    /**
     * Immutable "with" method returning a new instance with updated tanggalBlAwb.
     */
    public function withTanggalBlAwb(DateTimeImmutable|DateTimeInterface|string $tanggalBlAwb): self
    {
        return new self($this->nomorBlAwb, $tanggalBlAwb);
    }

    /**
     * Alias for withNomorBlAwb()
     */
    public function withNumber(string $number): self
    {
        return $this->withNomorBlAwb($number);
    }

    /**
     * Alias for withTanggalBlAwb()
     */
    public function withDate(DateTimeImmutable|DateTimeInterface|string $date): self
    {
        return $this->withTanggalBlAwb($date);
    }

    /**
     * Structural equality check.
     */
    public function equals(self $other): bool
    {
        return $this->nomorBlAwb === $other->nomorBlAwb
            && $this->tanggalBlAwb->format('Y-m-d') === $other->tanggalBlAwb->format('Y-m-d');
    }

    /**
     * @return array{nomorBlAwb: string, tanggalBlAwb: string}
     */
    public function toArray(string $dateFormat = 'Y-m-d'): array
    {
        return [
            'nomorBlAwb' => $this->nomorBlAwb,
            'tanggalBlAwb' => $this->tanggalBlAwb->format($dateFormat),
        ];
    }

    /**
     * @return array{nomorBlAwb: string, tanggalBlAwb: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->nomorBlAwb, $this->tanggalBlAwb->format('Y-m-d'));
    }

    private static function validateNomorBlAwb(string $nomorBlAwb): string
    {
        $trimmed = trim($nomorBlAwb);

        if ($trimmed === '') {
            throw InvalidBlAwbIdentifierException::emptyNomorBlAwb();
        }

        if (mb_strlen($trimmed) > self::MAX_NOMOR_BL_AWB_LENGTH) {
            throw InvalidBlAwbIdentifierException::nomorBlAwbTooLong($trimmed, self::MAX_NOMOR_BL_AWB_LENGTH);
        }

        if (preg_match(self::NOMOR_BL_AWB_PATTERN, $trimmed) !== 1) {
            throw InvalidBlAwbIdentifierException::invalidNomorBlAwbCharacters($trimmed);
        }

        return $trimmed;
    }

    private static function resolveTanggalBlAwb(DateTimeImmutable|DateTimeInterface|string $tanggalBlAwb): DateTimeImmutable
    {
        if ($tanggalBlAwb instanceof DateTimeImmutable) {
            return $tanggalBlAwb;
        }

        if ($tanggalBlAwb instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($tanggalBlAwb);
        }

        $trimmed = trim($tanggalBlAwb);
        if ($trimmed === '') {
            throw InvalidBlAwbIdentifierException::emptyTanggalBlAwb();
        }

        // Support Indonesian / CEISA standard date formats: DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/^(\d{2})[-|\/](\d{2})[-|\/](\d{4})(?:\s+(\d{2}:\d{2}:\d{2}))?$/', $trimmed, $matches)) {
            $format = str_contains($trimmed, '/') ? 'd/m/Y' : 'd-m-Y';
            if (isset($matches[4])) {
                $format .= ' H:i:s';
            }
            $parsed = DateTimeImmutable::createFromFormat('!'.$format, $trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsed;
            }

            throw InvalidBlAwbIdentifierException::invalidTanggalBlAwb($tanggalBlAwb);
        }

        // Support compact 8-digit date: YYYYMMDD
        if (preg_match('/^\d{8}$/', $trimmed) === 1) {
            $parsed = DateTimeImmutable::createFromFormat('!Ymd', $trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsed;
            }
        }

        try {
            $parsed = new DateTimeImmutable($trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                throw InvalidBlAwbIdentifierException::invalidTanggalBlAwb($tanggalBlAwb);
            }

            return $parsed;
        } catch (Throwable $e) {
            throw InvalidBlAwbIdentifierException::invalidTanggalBlAwb($trimmed, $e);
        }
    }
}
