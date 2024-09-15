<?php

declare(strict_types=1);

namespace App\Domain\Configurator\Discount;

final readonly class HighTotalRevenueOptionConfigurator implements DiscountOptionConfigurator
{
    public const string CONFIG_KEY_TOTAL_REVENUE_BOUNDARY = 'totalRevenueBoundary';
    public const string CONFIG_KEY_DISCOUNT_PERCENTAGE    = 'discountPercentage';

    /**
     * @return array<string, int>
     */
    public function getOptions(): array
    {
        return [
            self::CONFIG_KEY_TOTAL_REVENUE_BOUNDARY => 1000,
            self::CONFIG_KEY_DISCOUNT_PERCENTAGE => 10,
        ];
    }
}
