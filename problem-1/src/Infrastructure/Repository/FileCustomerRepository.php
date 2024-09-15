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

final class FileCustomerRepository implements CustomerRepository
{
    /**
     * @throws CustomerNotFoundException
     */
    public function getById(string $customerId): Customer
    {
        $customerData = file_get_contents(__DIR__ . '/../../../data/customers.json');
        assert($customerData !== false);

        $customerData = json_decode($customerData, true);
        assert(is_array($customerData));

        foreach ($customerData as $customer) {
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
            Id::create($customerData['id']),                // @phpstan-ignore-line
            Name::create((string) $customerData['name']),
            $since,
            Price::create($customerData['revenue']),
        );
    }
}
