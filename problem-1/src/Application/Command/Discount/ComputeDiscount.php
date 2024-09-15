<?php

declare(strict_types=1);

namespace App\Application\Command\Discount;

use App\Domain\Model\Order;

final readonly class ComputeDiscount
{
    public function __construct(
        public Order $order,
    ) {
    }
}
