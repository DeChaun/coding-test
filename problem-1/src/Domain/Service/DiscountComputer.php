<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Enum\DiscountType;
use App\Domain\Model\Discount\Discount;
use App\Domain\Model\Order;

final class DiscountComputer
{
    /**
     * @param DiscountType[] $availableDiscounts
     */
    public function __invoke(Order $order, array $availableDiscounts): Order
    {
        foreach ($availableDiscounts as $discountType) {
            $discountClassName = $discountType->getClass();
            $discount          = new $discountClassName($order, $discountType->getConfigurator());

            assert($discount instanceof Discount);

            $order = $this->computeDiscount($order, $discount);
        }

        return $order;
    }

    private function computeDiscount(Order $order, Discount $discount): Order
    {
        if (false === $discount->isApplicable()) {
            return $order;
        }

        $order->applyDiscount($discount);

        return $order;
    }
}
