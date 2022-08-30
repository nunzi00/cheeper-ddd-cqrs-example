<?php

declare(strict_types=1);

namespace Cheeper\DomainModel\Cheep;

use Assert\Assertion;
use Cheeper\DomainModel\Common\ValueObject;

/** @psalm-immutable  */
final class CheepMessage extends ValueObject
{
    private const MAX_LENGTH = 260;

    /** @psalm-param non-empty-string $message */
    private function __construct(
        public readonly string $message
    ) {
        $this->assertMessageIsValid($message);
    }

    /**
     * @psalm-param non-empty-string $message
     *
     * @psalm-pure
     */
    public static function write(string $message): self
    {
        return new self($message);
    }

    /** @psalm-suppress ImpureMethodCall */
    private function assertMessageIsValid(string $message): void
    {
        Assertion::notEmpty($message);
        Assertion::maxLength($message, self::MAX_LENGTH);
    }

    public function equalsTo(self $other): bool
    {
        return $this->message === $other->message;
    }
}
