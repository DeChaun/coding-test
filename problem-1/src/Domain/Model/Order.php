<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Discount\Discount;

final class Order
{
    /**
     * @param OrderItem[] $orderItems
     * @param Discount[] $discounts
     */
    public function __construct(
        private readonly int $id,
        private readonly Customer $customer,
        private readonly array $orderItems,
        private readonly float $total,
        private array $discounts = [],
        private ?float $discountAmount = null,
        private ?float $discountedTotal = null,
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

    public function applyDiscount(Discount $discount): void
    {
        $this->discounts[]     = $discount;
        $this->discountAmount  = ($this->discountAmount ?? 0) + $discount->getDiscountAmount();
        $this->discountedTotal = $this->total - $this->discountAmount;
    }

    /**
     * @return list<Discount>
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function getDiscountAmount(): ?float
    {
        return $this->discountAmount;
    }

    public function getDiscountedTotal(): ?float
    {
        return $this->discountedTotal;
    }

    /**
     * @return array<string, array<array<string, float|int|string|null>>|float|int|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer-id' => $this->customer->getId(),
            'items' => array_map(fn (OrderItem $orderItem) => $orderItem->toArray(), $this->orderItems),
            'total' => $this->total,
            'discount' => array_map(function (Discount $discount) {
                return [
                    'type' => $discount->getType()->name,
                    'explanation' => $discount->getExplanation(),
                    'discount-amount' => $discount->getDiscountAmount(),
                ];
            }, $this->discounts),
            'discountedTotal' => $this->discountedTotal,
        ];
    }
}
