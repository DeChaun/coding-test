<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Configurator\Discount\HighTotalRevenueOptionConfigurator as Configurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;
use App\Domain\Model\Price;

final readonly class HighTotalRevenueDiscount implements Discount
{
    private int $totalRevenueBoundary;
    private int $discountPercentage;

    public function __construct(
        private Order $order,
        DiscountOptionConfigurator $optionConfigurator,
    ) {
        assert($optionConfigurator instanceof Configurator);

        $options = $optionConfigurator->getOptions();

        assert(array_key_exists(Configurator::CONFIG_KEY_TOTAL_REVENUE_BOUNDARY, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_TOTAL_REVENUE_BOUNDARY]));

        $this->totalRevenueBoundary = $options[Configurator::CONFIG_KEY_TOTAL_REVENUE_BOUNDARY];

        assert(array_key_exists(Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE]));

        $this->discountPercentage = $options[Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE];
    }

    public function isApplicable(): bool
    {
        return $this->order->getCustomer()->getRevenue()->getValue() > $this->totalRevenueBoundary;
    }

    public function getDiscountAmount(): ?Price
    {
        $total = $this->order->getDiscountedTotal()?->getValue() ?? $this->order->getTotal()->getValue();

        // Round to 4 decimals as this may introduce floating-point issues (e.g. rounding to 88.80000000000001)
        return Price::create(round($total * ($this->discountPercentage / 100), 4));
    }

    public function getType(): DiscountType
    {
        return DiscountType::HighTotalRevenueDiscount;
    }

    public function getExplanation(): string
    {
        return sprintf(
            'A customer who has already bought for over € %s, gets a discount of %s%% on the whole order. ' .
            'This results in € %s discount',
            $this->totalRevenueBoundary,
            $this->discountPercentage,
            $this->getDiscountAmount()?->getValue(true),
        );
    }
}
