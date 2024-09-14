<?php

declare(strict_types=1);

namespace App\Domain\Exception\Request;

use App\Domain\Exception\DomainException;

final class InvalidOrderItemDataReceivedException extends DomainException
{
    /**
     * @param string[] $requiredKeys
     */
    public static function missingKey(string $requiredKey, array $requiredKeys): self
    {
        return new self(sprintf(
            'Key %s is missing in data. Required keys are: %s.',
            $requiredKey,
            implode(', ', $requiredKeys)
        ));
    }

    public static function invalidValueForKey(int|string|float $value, string $key): self
    {
        return new self(sprintf(
            'Invalid value "%s" provided for key "%s"',
            $value,
            $key,
        ));
    }
}
