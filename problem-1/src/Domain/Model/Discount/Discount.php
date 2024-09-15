<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;

interface Discount
{
    public function __construct(Order $order);

    public function isApplicable(): bool;

    public function getDiscountAmount(): ?float;

    public function getType(): DiscountType;

    public function getExplanation(): string;
}
