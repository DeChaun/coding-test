<?php

declare(strict_types=1);

namespace App\Application\Command\Product;

final readonly class GetProduct
{
    public function __construct(
        public string $productId,
    ) {
    }
}
