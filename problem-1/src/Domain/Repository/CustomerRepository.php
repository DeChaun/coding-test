<?php

namespace App\Domain\Repository;

use App\Domain\Model\Customer;

interface CustomerRepository
{
    public function getById(string $customerId): Customer;
}
