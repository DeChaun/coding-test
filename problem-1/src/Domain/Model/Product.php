<?php

declare(strict_types=1);

namespace App\Domain\Model;

use LogicException;

final readonly class Product
{
    public function __construct(
        private string $id,
        private string $description,
        private int $categoryId,
        private float $price,   // @phpstan-ignore-line
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getPrice(): float
    {
        throw new LogicException('Do not use this getter, the correct price is on the OrderItem');
    }
}
