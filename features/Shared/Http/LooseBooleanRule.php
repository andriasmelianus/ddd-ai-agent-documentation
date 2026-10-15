<?php

declare(strict_types=1);

namespace Features\Shared\Http;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class LooseBooleanRule
 *
 * Rule validasi boolean yang fleksibel. Memperbolehkan tipe boolean asli
 * serta representasi string/integer ekuivalen:
 * - True equivalents: true, 1, '1', 'true', 'on', 'yes'
 * - False equivalents: false, 0, '0', 'false', 'off', 'no'
 */
final class LooseBooleanRule implements ValidationRule
{
    public bool $implicit = true;

    private bool $nullable;

    public function __construct(bool $nullable = true)
    {
        $this->nullable = $nullable;
    }

    public static function make(bool $nullable = true): self
    {
        return new self($nullable);
    }

    public static function rule(bool $nullable = true): self
    {
        return new self($nullable);
    }

    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            if ($this->nullable) {
                return;
            }

            $fail("The {$attribute} field must be true or false.");

            return;
        }

        if (is_bool($value)) {
            return;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($filtered === null) {
            $fail("The {$attribute} field must be true or false.");
        }
    }
}
