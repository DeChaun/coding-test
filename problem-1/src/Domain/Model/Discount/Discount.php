<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;
use App\Domain\Model\Price;

interface Discount
{
    public function __construct(
        Order $order,
        DiscountOptionConfigurator $optionConfigurator,
    );

    public function isApplicable(): bool;

    public function getDiscountAmount(): ?Price;

    public function getType(): DiscountType;

    public function getExplanation(): string;
}
