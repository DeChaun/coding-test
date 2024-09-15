<?php

declare(strict_types=1);

namespace App\Domain\Model;

use LogicException;

final readonly class Product
{
    public function __construct(
        private Id $id,
        private Description $description,
        private Category $category,
        private Price $price,   // @phpstan-ignore-line
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getPrice(): void
    {
        throw new LogicException('Do not use this getter, the correct price is on the OrderItem');
    }
}
