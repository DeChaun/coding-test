<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class Order
{
    public function __construct(
        private int $id,
        private Customer $customer,
        private array $orderItems,
        private float $total,
    ) {
        foreach ($this->orderItems as $orderItem) {
            assert($orderItem instanceof OrderItem);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}
