<?php

declare(strict_types=1);

namespace App\Application\Command\Product;

use App\Domain\Model\Customer;
use App\Domain\Repository\CustomerRepository;

final readonly class GetCustomerHandler
{
    public function __construct(
        private CustomerRepository $customerRepository,
    ) {
    }

    public function __invoke(GetCustomer $command): Customer
    {
        return $this->customerRepository->getById($command->customerId);
    }
}
