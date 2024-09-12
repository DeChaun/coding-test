<?php

declare(strict_types=1);

namespace App\Application\Command\Product;

final readonly class GetCustomer
{
    public function __construct(
        public string $customerId,
    ) {
    }
}
