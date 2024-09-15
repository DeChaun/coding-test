<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Application\Command\Product\GetCustomer;
use App\Domain\Exception\InvalidPriceException;
use App\Domain\Exception\Request\InvalidOrderDataReceivedException;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\Customer;
use App\Domain\Model\Id;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderFactory
{
    use HandleTrait;

    private const string API_KEY_ID          = 'id';
    private const string API_KEY_CUSTOMER_ID = 'customer-id';
    private const string API_KEY_ITEMS       = 'items';
    private const string API_KEY_TOTAL       = 'total';

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
     * @throws InvalidPriceException
     */
    public function fromApiData(array $orderData): Order
    {
        $this->validateData($orderData);

        return new Order(
            Id::create($orderData[self::API_KEY_ID]),                            // @phpstan-ignore-line
            $this->getCustomer((string) $orderData[self::API_KEY_CUSTOMER_ID]),  // @phpstan-ignore-line
            $this->getOrderItems($orderData[self::API_KEY_ITEMS]),               // @phpstan-ignore-line
            Price::create($orderData[self::API_KEY_TOTAL]),                      // @phpstan-ignore-line
        );
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderData
     * @throws InvalidOrderDataReceivedException
     */
    private function validateData(array $orderData): void
    {
        $requiredKeys = [
            self::API_KEY_ID,
            self::API_KEY_CUSTOMER_ID,
            self::API_KEY_ITEMS,
            self::API_KEY_TOTAL,
        ];

        foreach ($requiredKeys as $requiredKey) {
            if (false === array_key_exists($requiredKey, $orderData)) {
                throw InvalidOrderDataReceivedException::missingKey($requiredKey, $requiredKeys);
            }
        }

        $this->validateId($orderData[self::API_KEY_ID]);
        $this->validateCustomer($orderData[self::API_KEY_CUSTOMER_ID]);
        $this->validateTotal($orderData[self::API_KEY_TOTAL]);
    }

    /**
     * @throws InvalidOrderDataReceivedException
     */
    private function validateId(mixed $id): void
    {
        if (false === is_numeric($id) || intval($id) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((int) $id, self::API_KEY_ID);
        }
    }

    /**
     * @throws InvalidOrderDataReceivedException
     */
    private function validateCustomer(mixed $customerId): void
    {
        if (false === is_numeric($customerId) || intval($customerId) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((int) $customerId, self::API_KEY_CUSTOMER_ID);
        }
    }

    private function validateTotal(mixed $total): void
    {
        if (false === is_numeric($total)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderDataReceivedException::invalidValueForKey((float) $total, self::API_KEY_TOTAL);
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
