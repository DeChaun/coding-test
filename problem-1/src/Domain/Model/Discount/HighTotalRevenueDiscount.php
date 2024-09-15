<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;

final readonly class HighTotalRevenueDiscount implements Discount
{
    private const int TOTAL_REVENUE_BOUNDARY = 1000;
    private const int DISCOUNT_PERCENTAGE = 10;

    public function __construct(
        private Order $order
    ) {
    }

    public function isApplicable(): bool
    {
        return $this->order->getCustomer()->getRevenue() > self::TOTAL_REVENUE_BOUNDARY;
    }

    public function getDiscountAmount(): ?float
    {
        $total = $this->order->getDiscountedTotal() ?? $this->order->getTotal();

        // Round to 4 decimals as this may introduce floating-point issues (e.g. rounding to 88.80000000000001)
        return round($total * (self::DISCOUNT_PERCENTAGE / 100), 4);
    }

    public function getType(): DiscountType
    {
        return DiscountType::HighTotalRevenueDiscount;
    }

    public function getExplanation(): string
    {
        return sprintf(
            'A customer who has already bought for over € %s, gets a discount of %s%% on the whole order.',
            self::TOTAL_REVENUE_BOUNDARY,
            self::DISCOUNT_PERCENTAGE,
        );
    }
}
