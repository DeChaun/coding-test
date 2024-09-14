<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Order
{
    /**
     * @param OrderItem[] $orderItems
     */
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

    /**
     * @return OrderItem[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @return array<string, array<array<string, float|int|string>>|float|int>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer-id' => $this->customer->getId(),
            'items' => array_map(fn (OrderItem $orderItem) => $orderItem->toArray(), $this->orderItems),
            'total' => $this->total,
        ];
    }
}
