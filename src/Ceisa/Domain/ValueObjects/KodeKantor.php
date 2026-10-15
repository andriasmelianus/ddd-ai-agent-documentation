<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidKodeKantorException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;
use Stringable;

/**
 * Class KodeKantor
 *
 * Immutable Value Object representing CEISA 4.0 Customs Office Code (Kode Kantor).
 *
 * Constraints:
 * - Minimum 4 characters.
 * - Maximum 16 characters.
 * - Numbers only in string format (digits: 0-9).
 */
final readonly class KodeKantor implements Stringable, ValueObjectInterface
{
    public const int MIN_LENGTH = 4;

    public const int MAX_LENGTH = 16;

    private const string PATTERN = '/\A[0-9]+\z/';

    public string $value;

    public function __construct(string $value)
    {
        if ($value === '' || trim($value) === '') {
            throw InvalidKodeKantorException::empty();
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw InvalidKodeKantorException::tooShort($value, self::MIN_LENGTH);
        }

        if ($length > self::MAX_LENGTH) {
            throw InvalidKodeKantorException::tooLong($value, self::MAX_LENGTH);
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw InvalidKodeKantorException::invalidCharacters($value);
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromStringOrNull(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

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

class_alias(KodeKantor::class, 'Src\Ceisa\Domain\ValueObjects\CustomsOfficeCode');
class_alias(KodeKantor::class, 'Src\Ceisa\Domain\ValueObjects\OfficeCode');

