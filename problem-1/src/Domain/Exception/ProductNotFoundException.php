<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;

final class ProductNotFoundException extends Exception
{
    public static function invalidProductId(string $productId): self
    {
        return new self(sprintf('Product with ID %s not found', $productId));
    }
}
