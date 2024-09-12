<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;

final class CustomerNotFoundException extends Exception
{
    public static function invalidCustomerId(string $customerId): self
    {
        return new self(sprintf('Customer with ID %s not found', $customerId));
    }
}
