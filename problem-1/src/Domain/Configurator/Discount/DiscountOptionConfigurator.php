<?php

declare(strict_types=1);

namespace App\Domain\Configurator\Discount;

interface DiscountOptionConfigurator
{
    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;
}
