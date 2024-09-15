<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidPriceException;

final readonly class Price
{
    private function __construct(
        private float $amount,
    ) {
    }

    /**
     * @throws InvalidPriceException
     */
    public static function create(float|string $amount): self
    {
        if (true === is_string($amount)) {
            if (false === is_numeric($amount)) {
                throw InvalidPriceException::fromInvalidType($amount);
            }

            return new self(floatval($amount));
        }

        return new self($amount);
    }

    public function __toString(): string
    {
        return sprintf('EUR %s', $this->amount);
    }

    public function getValue(bool $rounded = false): float
    {
        if (true === $rounded) {
            return round($this->amount, 2);
        }

        return $this->amount;
    }
}
