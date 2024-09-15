<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Application\Command\Product\GetProduct;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Product;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemFactory
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderItemData
     * @throws InvalidOrderItemDataReceivedException
     */
    public function fromApiData(array $orderItemData): OrderItem
    {
        $this->validateData($orderItemData);

        /** @var Product $product */
        $product = $this->handle(new GetProduct((string) $orderItemData['product-id'])); // @phpstan-ignore-line

        return new OrderItem(
            $product,
            (int) $orderItemData['quantity'],
            (float) $orderItemData['unit-price'],
            (float) $orderItemData['total'],
        );
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderItemData
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateData(array $orderItemData): void
    {
        $requiredKeys = [
            'product-id',
            'quantity',
            'unit-price',
            'total',
        ];

        foreach ($requiredKeys as $requiredKey) {
            if (false === array_key_exists($requiredKey, $orderItemData)) {
                throw InvalidOrderItemDataReceivedException::missingKey($requiredKey, $requiredKeys);
            }
        }

        $this->validateProductId($orderItemData['product-id']);
        $this->validateQuantity($orderItemData['quantity']);
        $this->validateUnitPrice($orderItemData['unit-price']);
        $this->validateTotal($orderItemData['total']);
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateProductId(mixed $productId): void
    {
        if (false === is_string($productId)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((string) $productId, 'product-id');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateQuantity(mixed $quantity): void
    {
        if (false === is_numeric($quantity) || intval($quantity) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((int) $quantity, 'quantity');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateUnitPrice(mixed $unitPrice): void
    {
        if (false === is_numeric($unitPrice)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((float) $unitPrice, 'unit-price');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateTotal(mixed $total): void
    {
        if (false === is_numeric($total)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((float) $total, 'total');
        }
    }
}
