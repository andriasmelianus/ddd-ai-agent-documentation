<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\InvalidBc11IdentifierException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;
use Throwable;

/**
 * Class Bc11Identifier
 *
 * Immutable composite Value Object identifying a customs BC 1.1 manifest document.
 * Composed of manifest number (Nomor BC 1.1) and manifest date (Tanggal BC 1.1).
 *
 * Business Rules & Invariants:
 * - nomorBc11 must not be empty or whitespace only.
 * - nomorBc11 is automatically trimmed.
 * - nomorBc11 maximum length is 50 characters.
 * - nomorBc11 must contain only alphanumeric characters and allowed punctuation (._-/).
 * - tanggalBc11 must be a valid, parseable calendar date.
 */
final readonly class Bc11Identifier implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int MAX_NOMOR_BC11_LENGTH = 50;

    private const string NOMOR_BC11_PATTERN = '/\A[A-Za-z0-9._\-\/]+\z/';

    public string $nomorBc11;

    public DateTimeImmutable $tanggalBc11;

    public function __construct(
        string $nomorBc11,
        DateTimeImmutable|DateTimeInterface|string $tanggalBc11,
    ) {
        $trimmedNomorBc11 = trim($nomorBc11);

        if ($trimmedNomorBc11 === '') {
            throw InvalidBc11IdentifierException::emptyNomorBc11();
        }

        if (mb_strlen($trimmedNomorBc11) > self::MAX_NOMOR_BC11_LENGTH) {
            throw InvalidBc11IdentifierException::nomorBc11TooLong($trimmedNomorBc11, self::MAX_NOMOR_BC11_LENGTH);
        }

        if (preg_match(self::NOMOR_BC11_PATTERN, $trimmedNomorBc11) !== 1) {
            throw InvalidBc11IdentifierException::invalidNomorBc11Characters($trimmedNomorBc11);
        }

        $this->nomorBc11 = $trimmedNomorBc11;
        $this->tanggalBc11 = self::resolveTanggalBc11($tanggalBc11);
    }

    public static function create(
        string $nomorBc11,
        DateTimeImmutable|DateTimeInterface|string $tanggalBc11,
    ): self {
        return new self($nomorBc11, $tanggalBc11);
    }

    public static function fromPrimitives(
        string $nomorBc11,
        string $tanggalBc11,
        ?string $format = null,
    ): self {
        if ($format !== null) {
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, trim($tanggalBc11));
            if ($parsed === false) {
                throw InvalidBc11IdentifierException::invalidTanggalBc11($tanggalBc11);
            }

            return new self($nomorBc11, $parsed);
        }

        return new self($nomorBc11, $tanggalBc11);
    }

    public static function fromString(string $nomorBc11, string $tanggalBc11): self
    {
        return new self($nomorBc11, $tanggalBc11);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $nomorBc11 = $payload['nomorBc11'] ?? $payload['number'] ?? $payload['no_bc11'] ?? $payload['noBc11'] ?? null;
        $tanggalBc11 = $payload['tanggalBc11'] ?? $payload['date'] ?? $payload['tgl_bc11'] ?? $payload['tglBc11'] ?? null;

        if ($nomorBc11 === null || trim((string) $nomorBc11) === '') {
            throw InvalidBc11IdentifierException::emptyNomorBc11();
        }

        if ($tanggalBc11 === null || (is_string($tanggalBc11) && trim($tanggalBc11) === '')) {
            throw InvalidBc11IdentifierException::emptyTanggalBc11();
        }

        return new self((string) $nomorBc11, $tanggalBc11);
    }

    public function nomorBc11(): string
    {
        return $this->nomorBc11;
    }

    public function tanggalBc11(): DateTimeImmutable
    {
        return $this->tanggalBc11;
    }

    /**
     * Return formatted tanggalBc11 string.
     */
    public function formattedTanggalBc11(string $format = 'Y-m-d'): string
    {
        return $this->tanggalBc11->format($format);
    }

    /**
     * Alias for nomorBc11()
     */
    public function number(): string
    {
        return $this->nomorBc11;
    }

    /**
     * Alias for tanggalBc11()
     */
    public function date(): DateTimeImmutable
    {
        return $this->tanggalBc11;
    }

    /**
     * Alias for formattedTanggalBc11()
     */
    public function formattedDate(string $format = 'Y-m-d'): string
    {
        return $this->formattedTanggalBc11($format);
    }

    /**
     * Return date formatted as Y-m-d.
     */
    public function toDateString(): string
    {
        return $this->tanggalBc11->format('Y-m-d');
    }

    /**
     * Immutable "with" method returning a new instance with updated nomorBc11.
     */
    public function withNomorBc11(string $nomorBc11): self
    {
        return new self($nomorBc11, $this->tanggalBc11);
    }

    /**
     * Immutable "with" method returning a new instance with updated tanggalBc11.
     */
    public function withTanggalBc11(DateTimeImmutable|DateTimeInterface|string $tanggalBc11): self
    {
        return new self($this->nomorBc11, $tanggalBc11);
    }

    /**
     * Alias for withNomorBc11()
     */
    public function withNumber(string $number): self
    {
        return $this->withNomorBc11($number);
    }

    /**
     * Alias for withTanggalBc11()
     */
    public function withDate(DateTimeImmutable|DateTimeInterface|string $date): self
    {
        return $this->withTanggalBc11($date);
    }

    /**
     * Structural equality check.
     */
    public function equals(self $other): bool
    {
        return $this->nomorBc11 === $other->nomorBc11
            && $this->tanggalBc11->format('Y-m-d') === $other->tanggalBc11->format('Y-m-d');
    }

    /**
     * @return array{nomorBc11: string, tanggalBc11: string}
     */
    public function toArray(string $dateFormat = 'Y-m-d'): array
    {
        return [
            'nomorBc11' => $this->nomorBc11,
            'tanggalBc11' => $this->tanggalBc11->format($dateFormat),
        ];
    }

    /**
     * @return array{nomorBc11: string, tanggalBc11: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->nomorBc11, $this->tanggalBc11->format('Y-m-d'));
    }

    private static function resolveTanggalBc11(DateTimeImmutable|DateTimeInterface|string $tanggalBc11): DateTimeImmutable
    {
        if ($tanggalBc11 instanceof DateTimeImmutable) {
            return $tanggalBc11;
        }

        if ($tanggalBc11 instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($tanggalBc11);
        }

        $trimmedDate = trim($tanggalBc11);
        if ($trimmedDate === '') {
            throw InvalidBc11IdentifierException::emptyTanggalBc11();
        }

        if (preg_match('/^(\d{2})[-|\/](\d{2})[-|\/](\d{4})(?:\s+(\d{2}:\d{2}:\d{2}))?$/', $trimmedDate, $matches)) {
            $format = str_contains($trimmedDate, '/') ? 'd/m/Y' : 'd-m-Y';
            if (isset($matches[4])) {
                $format .= ' H:i:s';
            }
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, $trimmedDate);
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsed;
            }

            throw InvalidBc11IdentifierException::invalidTanggalBc11($tanggalBc11);
        }

        try {
            if (preg_match('/^\d{8}$/', $trimmedDate) === 1) {
                $parsed = DateTimeImmutable::createFromFormat('!Ymd', $trimmedDate);
                if ($parsed !== false) {
                    return $parsed;
                }
            }

            $parsed = new DateTimeImmutable($trimmedDate);
            $errors = DateTimeImmutable::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                throw InvalidBc11IdentifierException::invalidTanggalBc11($tanggalBc11);
            }

            return $parsed;
        } catch (Throwable $e) {
            throw InvalidBc11IdentifierException::invalidTanggalBc11($trimmedDate, $e);
        }
    }
}
