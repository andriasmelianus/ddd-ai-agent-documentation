<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

/**
 * Class CompanyLogos
 *
 * Value Object representing company logo image assets (default, square, landscape).
 */
readonly class CompanyLogos
{
    public function __construct(
        public ?string $default = null,
        public ?string $square = null,
        public ?string $landscape = null,
    ) {}

    public static function create(?string $default = null, ?string $square = null, ?string $landscape = null): self
    {
        return new self($default, $square, $landscape);
    }

    public static function empty(): self
    {
        return new self;
    }

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    public function hasSquare(): bool
    {
        return $this->square !== null;
    }

    public function hasLandscape(): bool
    {
        return $this->landscape !== null;
    }

    public function equals(self $other): bool
    {
        return $this->default === $other->default
            && $this->square === $other->square
            && $this->landscape === $other->landscape;
    }
}
