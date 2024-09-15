<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Model\Discount\HighTotalRevenueDiscount;
use App\Domain\Model\Order;

enum DiscountType
{
    case HighTotalRevenueDiscount;

    public function mapToClassName(): string
    {
        return [
            self::HighTotalRevenueDiscount->name => HighTotalRevenueDiscount::class,
        ][$this->name];
    }
}
