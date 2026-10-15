<?php

declare(strict_types=1);

namespace Features\Shared\Http;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

/**
 * Class ValueObjectRule
 *
 * Menterjemahkan domain invariants dan validasi Value Object (VO) menjadi
 * Laravel validation rules yang dieksekusi di dalam closure validation rule
 * (function (string $attribute, mixed $value, Closure $fail): void).
 */
final class ValueObjectRule implements ValidationRule
{
    /**
     * @var class-string
     */
    private string $voClass;

    /**
     * @var (callable(mixed, string): mixed)|null
     */
    private $factory = null;

    /**
     * Menentukan apakah nilai null atau string kosong diabaikan (dilewati).
     */
    private bool $nullable = false;

    /**
     * Custom message atau callable formatter: fn(Throwable $e, string $attribute, mixed $value): string
     *
     * @var string|(callable(Throwable, string, mixed): string)|null
     */
    private mixed $customMessage = null;

    /**
     * @param  class-string  $voClass  Nama class Value Object yang akan divalidasi.
     */
    public function __construct(string $voClass)
    {
        $this->voClass = $voClass;
    }

    /**
     * Fluent factory untuk membuat instance ValueObjectRule.
     *
     * @param  class-string  $voClass
     */
    public static function make(string $voClass): self
    {
        return new self($voClass);
    }

    /**
     * Alias ekspresif untuk make().
     *
     * @param  class-string  $voClass
     */
    public static function for(string $voClass): self
    {
        return new self($voClass);
    }

    /**
     * Mengembalikan closure validation rule Laravel langsung:
     * function (string $attribute, mixed $value, Closure $fail): void
     *
     * @param  class-string  $voClass
     * @param  (callable(mixed, string): mixed)|null  $factory
     */
    public static function rule(string $voClass, ?callable $factory = null, bool $nullable = false): Closure
    {
        $instance = new self($voClass);

        if ($factory !== null) {
            $instance->using($factory);
        }

        if ($nullable) {
            $instance->nullable();
        }

        return $instance->toClosure();
    }

    /**
     * Menentukan custom factory callable untuk instansiasi Value Object
     * (misal: fn ($val) => Email::fromString($val)).
     *
     * @param  callable(mixed, string): mixed  $factory
     */
    public function using(callable $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Mengizinkan nilai null dan string kosong melewati validasi VO.
     */
    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Menentukan custom error message atau custom message formatter callback.
     *
     * @param  string|(callable(Throwable, string, mixed): string)  $message
     */
    public function withMessage(string|callable $message): self
    {
        $this->customMessage = $message;

        return $this;
    }

    /**
     * Eksekusi validasi sesuai kontrak Illuminate\Contracts\Validation\ValidationRule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->nullable && ($value === null || $value === '')) {
            return;
        }

        try {
            if ($this->factory !== null) {
                ($this->factory)($value, $attribute);
            } else {
                new ($this->voClass)($value);
            }
        } catch (Throwable $e) {
            $message = $this->resolveErrorMessage($e, $attribute, $value);
            $fail($message);
        }
    }

    /**
     * Mendukung pemanggilan langsung sebagai invokable closure rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function __invoke(string $attribute, mixed $value, Closure $fail): void
    {
        $this->validate($attribute, $value, $fail);
    }

    /**
     * Mengubah instance rule ini menjadi Closure validation rule:
     * function (string $attribute, mixed $value, Closure $fail): void
     */
    public function toClosure(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $this->validate($attribute, $value, $fail);
        };
    }

    /**
     * Menyelesaikan format pesan error jika terjadi exception.
     */
    private function resolveErrorMessage(Throwable $e, string $attribute, mixed $value): string
    {
        if (is_callable($this->customMessage)) {
            return (string) ($this->customMessage)($e, $attribute, $value);
        }

        if (is_string($this->customMessage) && $this->customMessage !== '') {
            return str_replace(
                [':attribute', ':value'],
                [$attribute, is_scalar($value) ? (string) $value : ''],
                $this->customMessage
            );
        }

        $message = $e->getMessage();

        return $message !== '' ? $message : sprintf('The %s is invalid.', $attribute);
    }
}

class_alias(ValueObjectRule::class, 'Features\Shared\Http\VoRule');
