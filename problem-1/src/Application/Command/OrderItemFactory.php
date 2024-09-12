<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Command\Product\GetProduct;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\OrderItem;
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
     * @throws InvalidOrderItemDataReceivedException
     */
    public function fromApiData(array $orderItemData): OrderItem
    {
        $this->validateData($orderItemData);

        $product = $this->handle(new GetProduct($orderItemData['product-id']));

        return new OrderItem(
            $product,
            (int) $orderItemData['quantity'],
            (float) $orderItemData['unit-price'],
            (float) $orderItemData['total'],
        );
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateData(array $orderData): void
    {
        $requiredKeys = [
            'product-id',
            'quantity',
            'unit-price',
            'total',
        ];

        foreach ($requiredKeys as $requiredKey) {
            if (false === array_key_exists($requiredKey, $orderData)) {
                throw InvalidOrderItemDataReceivedException::missingKey($requiredKey, $requiredKeys);
            }
        }

        $this->validateProductId($orderData['product-id']);
        $this->validateQuantity($orderData['quantity']);
        $this->validateUnitPrice($orderData['unit-price']);
        $this->validateTotal($orderData['total']);
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateProductId(mixed $productId): void
    {
        if (false === is_string($productId)) {
            throw InvalidOrderItemDataReceivedException::invalidValueForKey($productId, 'total');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateQuantity(mixed $quantity): void
    {
        if (false === is_numeric($quantity) || intval($quantity) <= 0) {
            throw InvalidOrderItemDataReceivedException::invalidValueForKey($quantity, 'customer-id');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateUnitPrice(mixed $unitPrice): void
    {
        if (false === is_numeric($unitPrice)) {
            throw InvalidOrderItemDataReceivedException::invalidValueForKey($unitPrice, 'unit-price');
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateTotal(mixed $total): void
    {
        if (false === is_numeric($total)) {
            throw InvalidOrderItemDataReceivedException::invalidValueForKey($total, 'total');
        }
    }
}
