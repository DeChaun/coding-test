<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class OrderItem
{
    public function __construct(
        private Product $product,
        private int $quantity,
        private float $unitPrice,       // For historical reference, master of the current price is on the Product model
        private float $totalPrice,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }
}
