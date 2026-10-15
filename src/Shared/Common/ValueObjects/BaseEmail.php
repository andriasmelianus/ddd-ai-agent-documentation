<?php

declare(strict_types=1);

namespace Src\Shared\Common\ValueObjects;

use Src\Shared\Common\Exceptions\InvalidEmailException;
use Stringable;

/**
 * Immutable Value Object representing a generic business email address.
 */
readonly class BaseEmail implements Stringable
{
    /**
     * Maximum email length.
     */
    private const int MAX_LENGTH = 255;

    /**
     * Validated, immutable email value (trimmed, domain lowercased).
     */
    public string $value;

    /**
     * @throws InvalidEmailException When the value is empty, too long, or invalid.
     */
    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw InvalidEmailException::empty();
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw InvalidEmailException::tooLong(self::MAX_LENGTH);
        }

        if (! self::isValidEmail($trimmed)) {
            throw InvalidEmailException::invalidFormat($trimmed);
        }

        $this->value = self::normalize($trimmed);
    }

    /**
     * @throws InvalidEmailException When validation fails.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function localPart(): string
    {
        $atPosition = strrpos($this->value, '@');

        return substr($this->value, 0, (int) $atPosition);
    }

    public function domain(): string
    {
        $atPosition = strrpos($this->value, '@');

        return substr($this->value, (int) $atPosition + 1);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate addresses that PHP's email filter rejects at its 254-character
     * implementation limit while retaining its validation for all other values.
     */
    private static function isValidEmail(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
            return true;
        }

        if (mb_strlen($email) !== self::MAX_LENGTH || substr_count($email, '@') !== 1) {
            return false;
        }

        [$localPart, $domain] = explode('@', $email, 2);

        if (
            mb_strlen($localPart) > 64
            || preg_match("/^(?!\\.)(?!.*\\.\\.)[A-Za-z0-9!#$%&'*+\\/=?^_`{|}~-]+(?:\\.[A-Za-z0-9!#$%&'*+\\/=?^_`{|}~-]+)*$/D", $localPart) !== 1
        ) {
            return false;
        }

        $labels = explode('.', $domain);
        if (count($labels) < 2) {
            return false;
        }

        foreach ($labels as $label) {
            if (preg_match('/^(?=.{1,63}$)[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?$/D', $label) !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Normalise an already-validated email by lowercasing its domain while
     * preserving the local part verbatim.
     */
    private static function normalize(string $email): string
    {
        $atPosition = strrpos($email, '@');

        if ($atPosition === false) {
            return $email;
        }

        $localPart = substr($email, 0, $atPosition);
        $domain = substr($email, $atPosition + 1);

        return $localPart.'@'.mb_strtolower($domain);
    }
}
