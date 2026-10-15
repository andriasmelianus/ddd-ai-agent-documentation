<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Company\Domain\Exceptions\InvalidCompanyNameException;
use Stringable;

/**
 * Class CompanyName
 *
 * Immutable Value Object representing the official legal business name of a Company.
 */
readonly class CompanyName implements Stringable
{
    public const int MAX_LENGTH = 255;

    /**
     * Allowed characters: letters, numbers, spaces, dots, commas, dashes,
     * slashes, ampersands, apostrophes, and parentheses.
     */
    private const string PATTERN = '/\A[A-Za-z0-9 .,\-\/&\'()]+\z/u';

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidCompanyNameException::empty();
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw InvalidCompanyNameException::tooLong($trimmed, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $trimmed) !== 1) {
            throw InvalidCompanyNameException::invalidCharacters($trimmed);
        }

        $this->value = $trimmed;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
