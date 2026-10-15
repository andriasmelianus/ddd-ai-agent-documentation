<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use DateTimeImmutable;

/**
 * Class TpsDecree
 *
 * Value Object representing Customs Decree (Surat Keputusan Penetapan TPS).
 */
readonly class TpsDecree
{
    public function __construct(
        public string $number,
        public ?DateTimeImmutable $date = null,
    ) {}

    public static function create(string $number, ?DateTimeImmutable $date = null): self
    {
        return new self(trim($number), $date);
    }

    public static function fromPrimitives(string $number, ?string $date = null): self
    {
        $dateTime = null;
        if ($date !== null && trim($date) !== '') {
            $dateTime = new DateTimeImmutable($date);
        }

        return self::create($number, $dateTime);
    }

    public function equals(self $other): bool
    {
        $dateEquals = ($this->date === null && $other->date === null)
            || ($this->date !== null && $other->date !== null && $this->date->format('Y-m-d') === $other->date->format('Y-m-d'));

        return $this->number === $other->number && $dateEquals;
    }
}
