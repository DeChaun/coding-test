<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Product
{
    public function __construct(
        private string $id,
        private string $description,
        private Category $productCategory,
        private float $price,
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

    public function getProductCategory(): Category
    {
        return $this->productCategory;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
