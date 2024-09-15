<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InvalidPriceException extends DomainException
{
    public static function fromInvalidType(string $amount): self
    {
        return new self(sprintf('Invalid amount "%s" given', $amount));
    }
}
