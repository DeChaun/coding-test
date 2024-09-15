<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidQuantityException;

final readonly class Quantity
{
    private function __construct(
        private int $value,
    ) {
    }

    public static function create(int|string $value): self
    {
        if (true === is_string($value)) {
            if (false === is_numeric($value)) {
                throw InvalidQuantityException::fromInvalidType($value);
            }

            return new self(intval($value));
        }

        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
