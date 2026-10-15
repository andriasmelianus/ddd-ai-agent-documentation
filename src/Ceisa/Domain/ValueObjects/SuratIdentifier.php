<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\InvalidSuratIdentifierException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;
use Throwable;

/**
 * Class SuratIdentifier
 *
 * Immutable composite Value Object identifying a letter or formal document (Surat)
 * by its nomor surat and tanggal surat.
 *
 * Business Rules & Invariants:
 * - nomorSurat cannot be empty or whitespace only.
 * - nomorSurat cannot exceed maximum length (255 characters).
 * - tanggalSurat must be a valid DateTimeImmutable, DateTimeInterface, or parseable date string.
 */
final readonly class SuratIdentifier implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int MAX_NOMOR_SURAT_LENGTH = 255;

    public const int MAX_NUMBER_LENGTH = self::MAX_NOMOR_SURAT_LENGTH;

    public string $nomorSurat;

    public DateTimeImmutable $tanggalSurat;

    public function __construct(
        string $nomorSurat,
        DateTimeImmutable|DateTimeInterface|string $tanggalSurat,
    ) {
        $this->nomorSurat = self::validateNomorSurat($nomorSurat);
        $this->tanggalSurat = self::resolveTanggalSurat($tanggalSurat);
    }

    public static function create(
        string $nomorSurat,
        DateTimeImmutable|DateTimeInterface|string $tanggalSurat,
    ): self {
        return new self($nomorSurat, $tanggalSurat);
    }

    public static function fromPrimitives(
        string $nomorSurat,
        string $tanggalSurat,
        ?string $format = null,
    ): self {
        if ($format !== null) {
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, trim($tanggalSurat));
            if ($parsed === false) {
                throw InvalidSuratIdentifierException::invalidTanggalSuratFormat($tanggalSurat);
            }

            return new self($nomorSurat, $parsed);
        }

        return new self($nomorSurat, $tanggalSurat);
    }

    public static function fromString(string $nomorSurat, string $tanggalSurat): self
    {
        return new self($nomorSurat, $tanggalSurat);
    }

    /**
     * @param  array{nomorSurat?: string, nomor_surat?: string, no_surat?: string, number?: string, tanggalSurat?: DateTimeImmutable|DateTimeInterface|string, tanggal_surat?: DateTimeImmutable|DateTimeInterface|string, tgl_surat?: DateTimeImmutable|DateTimeInterface|string, date?: DateTimeImmutable|DateTimeInterface|string}  $data
     */
    public static function fromArray(array $data): self
    {
        $nomorSurat = $data['nomorSurat'] ?? $data['nomor_surat'] ?? $data['no_surat'] ?? $data['number'] ?? null;
        $tanggalSurat = $data['tanggalSurat'] ?? $data['tanggal_surat'] ?? $data['tgl_surat'] ?? $data['date'] ?? null;

        if ($nomorSurat === null || (is_string($nomorSurat) && trim($nomorSurat) === '')) {
            throw InvalidSuratIdentifierException::emptyNomorSurat();
        }

        if ($tanggalSurat === null || (is_string($tanggalSurat) && trim($tanggalSurat) === '')) {
            throw InvalidSuratIdentifierException::emptyTanggalSurat();
        }

        return new self((string) $nomorSurat, $tanggalSurat);
    }

    public function nomorSurat(): string
    {
        return $this->nomorSurat;
    }

    public function tanggalSurat(): DateTimeImmutable
    {
        return $this->tanggalSurat;
    }

    public function formattedTanggalSurat(string $format = 'Y-m-d'): string
    {
        return $this->tanggalSurat->format($format);
    }

    /**
     * Return date formatted as Y-m-d.
     */
    public function toDateString(): string
    {
        return $this->tanggalSurat->format('Y-m-d');
    }

    /**
     * Alias for nomorSurat()
     */
    public function number(): string
    {
        return $this->nomorSurat;
    }

    /**
     * Alias for tanggalSurat()
     */
    public function date(): DateTimeImmutable
    {
        return $this->tanggalSurat;
    }

    /**
     * Alias for formattedTanggalSurat()
     */
    public function formattedDate(string $format = 'Y-m-d'): string
    {
        return $this->formattedTanggalSurat($format);
    }

    /**
     * Immutable "with" method returning a new instance with updated nomorSurat.
     */
    public function withNomorSurat(string $nomorSurat): self
    {
        return new self($nomorSurat, $this->tanggalSurat);
    }

    /**
     * Immutable "with" method returning a new instance with updated tanggalSurat.
     */
    public function withTanggalSurat(DateTimeImmutable|DateTimeInterface|string $tanggalSurat): self
    {
        return new self($this->nomorSurat, $tanggalSurat);
    }

    /**
     * Alias for withNomorSurat()
     */
    public function withNumber(string $number): self
    {
        return $this->withNomorSurat($number);
    }

    /**
     * Alias for withTanggalSurat()
     */
    public function withDate(DateTimeImmutable|DateTimeInterface|string $date): self
    {
        return $this->withTanggalSurat($date);
    }

    public function equals(self $other): bool
    {
        return $this->nomorSurat === $other->nomorSurat
            && $this->tanggalSurat->format('Y-m-d') === $other->tanggalSurat->format('Y-m-d');
    }

    /**
     * @return array{nomorSurat: string, tanggalSurat: string}
     */
    public function toArray(string $dateFormat = 'Y-m-d'): array
    {
        return [
            'nomorSurat' => $this->nomorSurat,
            'tanggalSurat' => $this->tanggalSurat->format($dateFormat),
        ];
    }

    /**
     * @return array{nomorSurat: string, tanggalSurat: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->nomorSurat, $this->tanggalSurat->format('Y-m-d'));
    }

    private static function validateNomorSurat(string $nomorSurat): string
    {
        $trimmed = trim($nomorSurat);

        if ($trimmed === '') {
            throw InvalidSuratIdentifierException::emptyNomorSurat();
        }

        if (mb_strlen($trimmed) > self::MAX_NOMOR_SURAT_LENGTH) {
            throw InvalidSuratIdentifierException::nomorSuratTooLong($trimmed, self::MAX_NOMOR_SURAT_LENGTH);
        }

        return $trimmed;
    }

    private static function resolveTanggalSurat(DateTimeImmutable|DateTimeInterface|string $tanggalSurat): DateTimeImmutable
    {
        if ($tanggalSurat instanceof DateTimeImmutable) {
            return $tanggalSurat;
        }

        if ($tanggalSurat instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($tanggalSurat);
        }

        $trimmed = trim($tanggalSurat);
        if ($trimmed === '') {
            throw InvalidSuratIdentifierException::emptyTanggalSurat();
        }

        // Support Indonesian / CEISA standard date formats: DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/^(\d{2})[-|\/](\d{2})[-|\/](\d{4})(?:\s+(\d{2}:\d{2}:\d{2}))?$/', $trimmed, $matches)) {
            $format = str_contains($trimmed, '/') ? 'd/m/Y' : 'd-m-Y';
            if (isset($matches[4])) {
                $format .= ' H:i:s';
            }
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, $trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($parsed !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsed;
            }

            throw InvalidSuratIdentifierException::invalidTanggalSuratFormat($tanggalSurat);
        }

        try {
            $parsed = new DateTimeImmutable($trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                throw InvalidSuratIdentifierException::invalidTanggalSuratFormat($tanggalSurat);
            }

            return $parsed;
        } catch (Throwable) {
            throw InvalidSuratIdentifierException::invalidTanggalSuratFormat($tanggalSurat);
        }
    }
}
