<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\ValueObjects;

use Src\Ceisa\Domain\Exceptions\InvalidAuthCredentialsException;
use Src\Shared\Framework\Domain\ValueObjects\ValueObjectInterface;

/**
 * Class AuthCredentials
 *
 * Immutable composite Value Object encapsulating CEISA 4.0 authentication credentials.
 * Composed of AuthUsername and AuthPassword Value Objects.
 *
 * Business Rules & Invariants:
 * - Username and Password cannot be the same.
 */
final readonly class AuthCredentials implements ValueObjectInterface
{
    public AuthUsername $username;

    public AuthPassword $password;

    public function __construct(AuthUsername|string $username, AuthPassword|string $password)
    {
        $resolvedUsername = is_string($username) ? new AuthUsername($username) : $username;
        $resolvedPassword = is_string($password) ? new AuthPassword($password) : $password;

        if (strcasecmp($resolvedUsername->value(), $resolvedPassword->value()) === 0) {
            throw InvalidAuthCredentialsException::passwordMatchesUsername();
        }

        $this->username = $resolvedUsername;
        $this->password = $resolvedPassword;
    }

    public static function create(AuthUsername|string $username, AuthPassword|string $password): self
    {
        return new self($username, $password);
    }

    public static function fromStrings(string $username, string $password): self
    {
        return new self($username, $password);
    }

    public static function fromPrimitives(string $username, string $password): self
    {
        return new self($username, $password);
    }

    public function username(): AuthUsername
    {
        return $this->username;
    }

    public function password(): AuthPassword
    {
        return $this->password;
    }

    /**
     * Immutable "with" method to return a new instance with updated Username.
     */
    public function withUsername(AuthUsername|string $username): self
    {
        return new self($username, $this->password);
    }

    /**
     * Immutable "with" method to return a new instance with updated Password.
     */
    public function withPassword(AuthPassword|string $password): self
    {
        return new self($this->username, $password);
    }

    public function equals(self $other): bool
    {
        return $this->username->equals($other->username)
            && $this->password->equals($other->password);
    }

    /**
     * @return array{username: string, password: string}
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username->value(),
            'password' => $this->password->value(),
        ];
    }

    /**
     * @return array{username: string, password: string}
     */
    public function toMaskedArray(): array
    {
        return [
            'username' => $this->username->value(),
            'password' => '******',
        ];
    }
}
