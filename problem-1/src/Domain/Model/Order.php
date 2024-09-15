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
        private readonly Id $id,
        private readonly Customer $customer,
        private readonly array $orderItems,
        private readonly Price $total,
        private array $discounts = [],
        private ?Price $discountAmount = null,
        private ?Price $discountedTotal = null,
    ) {
        foreach ($this->discounts as $discount) {
            assert($discount instanceof Discount);
        }

        foreach ($this->orderItems as $orderItem) {
            assert($orderItem instanceof OrderItem);
        }
    }

    public function getId(): Id
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

    public function getTotal(): Price
    {
        return $this->total;
    }

    public function applyDiscount(Discount $discount): void
    {
        $this->discounts[]     = $discount;
        $this->discountAmount  = Price::create(
            (($this->discountAmount?->getValue() ?? 0) + $discount->getDiscountAmount()?->getValue())
        );
        $this->discountedTotal = Price::create($this->total->getValue() - $this->discountAmount->getValue());
    }

    /**
     * @return list<Discount>
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function getDiscountAmount(): ?Price
    {
        return $this->discountAmount;
    }

    public function getDiscountedTotal(): ?Price
    {
        return $this->discountedTotal;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => (string) $this->getId(),
            'customer-id' => (string) $this->customer->getId(),
            'items' => array_map(fn (OrderItem $orderItem) => $orderItem->toArray(), $this->orderItems),
            'total' => $this->total->getValue(true),
            'discount' => array_map(function (Discount $discount) {
                return [
                    'type' => $discount->getType()->name,
                    'explanation' => $discount->getExplanation(),
                    'discount-amount' => $discount->getDiscountAmount(),
                ];
            }, $this->discounts),
            'discountedTotal' => $this->discountedTotal?->getValue(true),
        ];
    }
}
