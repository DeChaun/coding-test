<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InvalidQuantityException extends DomainException
{
    public static function fromInvalidType(string $amount): self
    {
        return new self(sprintf('Invalid quantity "%s" given', $amount));
    }
}
