<?php

declare(strict_types=1);

namespace App\Domain\Configurator\Discount;

final readonly class CheapestProductPercentageOptionConfigurator implements DiscountOptionConfigurator
{
    public const string CONFIG_KEY_DISCOUNT_PERCENTAGE = 'discountPercentage';
    public const string CONFIG_KEY_CATEGORY_ID         = 'consideredCategoryId';
    public const string CONFIG_KEY_THRESHOLD           = 'threshold';

    /**
     * @return array<string, int[]|int>
     */
    public function getOptions(): array
    {
        return [
            self::CONFIG_KEY_CATEGORY_ID => 1,
            self::CONFIG_KEY_THRESHOLD => 2,
            self::CONFIG_KEY_DISCOUNT_PERCENTAGE => 20,
        ];
    }
}
