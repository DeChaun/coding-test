<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Application\Command\Product\GetCustomer;
use App\Domain\Exception\Request\InvalidOrderDataReceivedException;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\Customer;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderFactory
{
    use HandleTrait;

    public function __construct(
        private readonly OrderItemFactory $orderItemFactory,
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderData
     * @throws InvalidOrderDataReceivedException
     * @throws InvalidOrderItemDataReceivedException
     */
    public function fromApiData(array $orderData): Order
    {
        $this->validateData($orderData);

        return new Order(
            (int) $orderData['id'],
            $this->getCustomer((string) $orderData['customer-id']),  // @phpstan-ignore-line
            $this->getOrderItems($orderData['items']),               // @phpstan-ignore-line
            (float) $orderData['total'],
        );
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderData
     * @throws InvalidOrderDataReceivedException
     */
    private function validateData(array $orderData): void
    {
        $requiredKeys = [
            'id',
            'customer-id',
            'items',
            'total',
        ];

        foreach ($requiredKeys as $requiredKey) {
            if (false === array_key_exists($requiredKey, $orderData)) {
                throw InvalidOrderDataReceivedException::missingKey($requiredKey, $requiredKeys);
            }
        }

        $this->validateId($orderData['id']);
        $this->validateCustomer($orderData['customer-id']);
        $this->validateTotal($orderData['total']);
    }

    /**
     * @throws InvalidOrderDataReceivedException
     */
    private function validateId(mixed $id): void
    {
        if (false === is_numeric($id) || intval($id) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((int) $id, 'id');
        }
    }

    /**
     * @throws InvalidOrderDataReceivedException
     */
    private function validateCustomer(mixed $customerId): void
    {
        if (false === is_numeric($customerId) || intval($customerId) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((int) $customerId, 'customer-id');
        }
    }

    private function validateTotal(mixed $total): void
    {
        if (false === is_numeric($total)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((float) $total, 'total');
        }
    }

    private function getCustomer(string $customerId): Customer
    {
        /** @var Customer $result */
        $result = $this->handle(new GetCustomer($customerId));

        return $result;
    }

    /**
     * @param array<int|string, array<int|string, int|string|float>> $items
     * @return OrderItem[]
     * @throws InvalidOrderItemDataReceivedException
     */
    private function getOrderItems(array $items): array
    {
        return array_map(
            fn (array $item) => $this->orderItemFactory->fromApiData($item),
            $items,
        );
    }
}
