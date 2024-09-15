<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\CustomerNotFoundException;
use App\Domain\Model\Customer;
use App\Domain\Model\Id;
use App\Domain\Model\Name;
use App\Domain\Model\Price;
use App\Domain\Repository\CustomerRepository;
use DateTime;

final class InMemoryCustomerRepository implements CustomerRepository
{
    /**
     * @var list<array<string, int|string|float>>
     */
    private array $customerData = [
        [
            "id" => "1",
            "name" => "Coca Cola",
            "since" => "2014-06-28",
            "revenue" => "492.12"
        ],
        [
            "id" => "2",
            "name" => "Teamleader",
            "since" => "2015-01-15",
            "revenue" => "1505.95"
        ],
        [
            "id" => "3",
            "name" => "Jeroen De Wit",
            "since" => "2016-02-11",
            "revenue" => "0.00"
        ],
    ];

    /**
     * @throws CustomerNotFoundException
     */
    public function getById(string $customerId): Customer
    {
        foreach ($this->customerData as $customer) {
            if (array_key_exists('id', $customer) && $customer['id'] === $customerId) {
                return $this->mapToCustomer($customer);
            }
        }

        throw CustomerNotFoundException::invalidCustomerId($customerId);
    }

    /**
     * @param array<string, int|string|float> $customerData
     */
    private function mapToCustomer(array $customerData): Customer
    {
        assert(array_key_exists('id', $customerData) && is_numeric($customerData['id']));
        assert(array_key_exists('name', $customerData));
        assert(array_key_exists('since', $customerData));
        assert(array_key_exists('revenue', $customerData) && is_numeric($customerData['revenue']));

        $since = DateTime::createFromFormat('Y-m-d', (string) $customerData['since']);

        assert($since instanceof DateTime);

        return new Customer(
            Id::create((string) $customerData['id']),
            Name::create((string) $customerData['name']),
            $since,
            Price::create((float) $customerData['revenue']),
        );
    }
}
