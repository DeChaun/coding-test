<?php

declare(strict_types=1);

namespace App\Application\Command\Discount;

use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;
use App\Domain\Service\DiscountComputer;

final readonly class ComputeDiscountHandler
{
    public function __construct(
        private DiscountComputer $discountComputer,
    ) {
    }

    public function __invoke(ComputeDiscount $command): Order
    {
        return ($this->discountComputer)($command->order, DiscountType::cases());
    }
}
