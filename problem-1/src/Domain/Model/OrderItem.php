<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class OrderItem
{
    public function __construct(
        private Product $product,
        private Quantity $quantity,
        private Price $unitPrice,       // For historical reference, master of the current price is on the Product model
        private Price $totalPrice,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    /**
     * @return array<string, int|float|string>
     */
    public function toArray(): array
    {
        return [
            'product' => (string) $this->product->getId(),
            'quantity' => $this->quantity->getValue(),
            'unitPrice' => $this->unitPrice->getValue(),
            'totalPrice' => $this->totalPrice->getValue(),
        ];
    }
}
