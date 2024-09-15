<?php

declare(strict_types=1);

namespace App\Domain\Configurator\Discount;

final readonly class BuyXGetYItemsFreeOptionConfigurator implements DiscountOptionConfigurator
{
    public const string CONFIG_KEY_CATEGORY_IDS         = 'consideredCategoryIds';
    public const string CONFIG_KEY_THRESHOLD            = 'threshold';
    public const string CONFIG_KEY_NUMBER_OF_FREE_ITEMS = 'numberOfFreeItems';

    /**
     * @return array<string, int[]|int>
     */
    public function getOptions(): array
    {
        return [
            self::CONFIG_KEY_CATEGORY_IDS => [2],
            self::CONFIG_KEY_THRESHOLD => 6,
            self::CONFIG_KEY_NUMBER_OF_FREE_ITEMS => 1,
        ];
    }
}
