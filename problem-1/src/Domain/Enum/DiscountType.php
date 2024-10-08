<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Configurator\Discount\BuyXGetYItemsFreeOptionConfigurator;
use App\Domain\Configurator\Discount\CheapestProductPercentageOptionConfigurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Configurator\Discount\HighTotalRevenueOptionConfigurator;
use App\Domain\Model\Discount\BuyXGetYItemsFreeDiscount;
use App\Domain\Model\Discount\CheapestProductPercentageDiscount;
use App\Domain\Model\Discount\HighTotalRevenueDiscount;

enum DiscountType
{
    case BuyXGetYItemsFree;
    case CheapestProductPercentageDiscount;
    case HighTotalRevenueDiscount;

    public function getClass(): string
    {
        return [
            self::BuyXGetYItemsFree->name => BuyXGetYItemsFreeDiscount::class,
            self::CheapestProductPercentageDiscount->name => CheapestProductPercentageDiscount::class,
            self::HighTotalRevenueDiscount->name => HighTotalRevenueDiscount::class,
        ][$this->name];
    }

    public function getConfigurator(): DiscountOptionConfigurator
    {
        return [
            self::BuyXGetYItemsFree->name => new BuyXGetYItemsFreeOptionConfigurator(),
            self::CheapestProductPercentageDiscount->name => new CheapestProductPercentageOptionConfigurator(),
            self::HighTotalRevenueDiscount->name => new HighTotalRevenueOptionConfigurator(),
        ][$this->name];
    }
}
