<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\CustomerNotFoundException;
use App\Domain\Model\Customer;
use App\Domain\Repository\CustomerRepository;
use DateTime;

final class FileCustomerRepository implements CustomerRepository
{
    /**
     * @throws CustomerNotFoundException
     */
    public function getById(string $customerId): Customer
    {
        $customerData = file_get_contents(__DIR__ . '/../../../data/customers.json');

        $customerData = json_decode($customerData, true);
        foreach ($customerData as $customer) {
            if (array_key_exists('id', $customer) && $customer['id'] === $customerId) {
                return $this->mapToCustomer($customer);
            }
        }

        throw CustomerNotFoundException::invalidCustomerId($customerId);
    }

    private function mapToCustomer(array $customerData): Customer
    {
        assert(array_key_exists('id', $customerData) && is_numeric($customerData['id']));
        assert(array_key_exists('name', $customerData));
        assert(array_key_exists('since', $customerData));
        assert(array_key_exists('revenue', $customerData) && is_numeric($customerData['revenue']));

        return new Customer(
            (int) $customerData['id'],
            $customerData['name'],
            DateTime::createFromFormat('Y-m-d', $customerData['since']),
            (float) $customerData['revenue'],
        );
    }
}
