<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects\Tps;

use DateTimeImmutable;
use JsonSerializable;
use Src\Ceisa\Domain\Exceptions\Tps\InvalidPersetujuanPlpIdentifierException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;
use Throwable;

/**
 * Class PersetujuanPlpIdentifier
 *
 * Immutable composite Value Object uniquely identifying a Persetujuan PLP
 * by its nomor PLP and tanggal PLP.
 */
final readonly class PersetujuanPlpIdentifier implements JsonSerializable, Stringable, ValueObjectInterface
{
    public const int MAX_NOMOR_PLP_LENGTH = 255;

    public const int MAX_NUMBER_LENGTH = self::MAX_NOMOR_PLP_LENGTH;

    public string $nomorPlp;

    public DateTimeImmutable $tanggalPlp;

    public function __construct(string $nomorPlp, DateTimeImmutable|string $tanggalPlp)
    {
        $this->nomorPlp = self::validateNomorPlp($nomorPlp);
        $this->tanggalPlp = self::resolveTanggalPlp($tanggalPlp);
    }

    public static function create(string $nomorPlp, DateTimeImmutable|string $tanggalPlp): self
    {
        return new self($nomorPlp, $tanggalPlp);
    }

    public static function fromPrimitives(string $nomorPlp, string $tanggalPlp): self
    {
        return new self($nomorPlp, $tanggalPlp);
    }

    /**
     * @param  array{nomorPlp?: string, nomor_plp?: string, no_plp?: string, number?: string, tanggalPlp?: DateTimeImmutable|string, tanggal_plp?: DateTimeImmutable|string, tgl_plp?: DateTimeImmutable|string, date?: DateTimeImmutable|string}  $data
     */
    public static function fromArray(array $data): self
    {
        $nomorPlp = $data['nomorPlp'] ?? $data['nomor_plp'] ?? $data['no_plp'] ?? $data['number'] ?? null;
        $tanggalPlp = $data['tanggalPlp'] ?? $data['tanggal_plp'] ?? $data['tgl_plp'] ?? $data['date'] ?? null;

        if ($nomorPlp === null) {
            throw InvalidPersetujuanPlpIdentifierException::emptyNomorPlp();
        }

        if ($tanggalPlp === null) {
            throw InvalidPersetujuanPlpIdentifierException::emptyTanggalPlp();
        }

        return new self($nomorPlp, $tanggalPlp);
    }

    public function nomorPlp(): string
    {
        return $this->nomorPlp;
    }

    public function tanggalPlp(): DateTimeImmutable
    {
        return $this->tanggalPlp;
    }

    public function formattedTanggalPlp(string $format = 'Y-m-d'): string
    {
        return $this->tanggalPlp->format($format);
    }

    public function number(): string
    {
        return $this->nomorPlp;
    }

    public function date(): DateTimeImmutable
    {
        return $this->tanggalPlp;
    }

    public function formattedDate(string $format = 'Y-m-d'): string
    {
        return $this->formattedTanggalPlp($format);
    }

    public function withNomorPlp(string $nomorPlp): self
    {
        return new self($nomorPlp, $this->tanggalPlp);
    }

    public function withTanggalPlp(DateTimeImmutable|string $tanggalPlp): self
    {
        return new self($this->nomorPlp, $tanggalPlp);
    }

    public function withNumber(string $number): self
    {
        return $this->withNomorPlp($number);
    }

    public function withDate(DateTimeImmutable|string $date): self
    {
        return $this->withTanggalPlp($date);
    }

    public function equals(self $other): bool
    {
        return $this->nomorPlp === $other->nomorPlp
            && $this->tanggalPlp->format('Y-m-d') === $other->tanggalPlp->format('Y-m-d');
    }

    /**
     * @return array{nomorPlp: string, tanggalPlp: string}
     */
    public function toArray(): array
    {
        return [
            'nomorPlp' => $this->nomorPlp,
            'tanggalPlp' => $this->tanggalPlp->format('Y-m-d'),
        ];
    }

    /**
     * @return array{nomorPlp: string, tanggalPlp: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->nomorPlp, $this->tanggalPlp->format('Y-m-d'));
    }

    private static function validateNomorPlp(string $nomorPlp): string
    {
        $trimmed = trim($nomorPlp);

        if ($trimmed === '') {
            throw InvalidPersetujuanPlpIdentifierException::emptyNomorPlp();
        }

        if (mb_strlen($trimmed) > self::MAX_NOMOR_PLP_LENGTH) {
            throw InvalidPersetujuanPlpIdentifierException::nomorPlpTooLong($trimmed, self::MAX_NOMOR_PLP_LENGTH);
        }

        return $trimmed;
    }

    private static function resolveTanggalPlp(DateTimeImmutable|string $tanggalPlp): DateTimeImmutable
    {
        if ($tanggalPlp instanceof DateTimeImmutable) {
            return $tanggalPlp;
        }

        $trimmed = trim($tanggalPlp);
        if ($trimmed === '') {
            throw InvalidPersetujuanPlpIdentifierException::emptyTanggalPlp();
        }

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

            throw InvalidPersetujuanPlpIdentifierException::invalidTanggalPlpFormat($tanggalPlp);
        }

        try {
            $parsed = new DateTimeImmutable($trimmed);
            $errors = DateTimeImmutable::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                throw InvalidPersetujuanPlpIdentifierException::invalidTanggalPlpFormat($tanggalPlp);
            }

            return $parsed;
        } catch (Throwable) {
            throw InvalidPersetujuanPlpIdentifierException::invalidTanggalPlpFormat($tanggalPlp);
        }
    }
}
