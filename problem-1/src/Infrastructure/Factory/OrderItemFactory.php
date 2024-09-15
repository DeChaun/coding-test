<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Application\Command\Product\GetProduct;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use App\Domain\Model\Product;
use App\Domain\Model\Quantity;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemFactory
{
    use HandleTrait;

    private const string API_KEY_PRODUCT_ID = 'product-id';
    private const string API_KEY_QUANTITY   = 'quantity';
    private const string API_KEY_UNIT_PRICE = 'unit-price';
    private const string API_KEY_TOTAL      = 'total';

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
        $product = $this->handle(new GetProduct((string) $orderItemData[self::API_KEY_PRODUCT_ID])); // @phpstan-ignore-line

        return new OrderItem(
            $product,
            Quantity::create($orderItemData[self::API_KEY_QUANTITY]),       // @phpstan-ignore-line
            Price::create($orderItemData[self::API_KEY_UNIT_PRICE]),        // @phpstan-ignore-line
            Price::create($orderItemData[self::API_KEY_TOTAL]),             // @phpstan-ignore-line
        );
    }

    /**
     * @param array<string|int, int|string|float|array<int|string, mixed>> $orderItemData
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateData(array $orderItemData): void
    {
        $requiredKeys = [
            self::API_KEY_PRODUCT_ID,
            self::API_KEY_QUANTITY,
            self::API_KEY_UNIT_PRICE,
            self::API_KEY_TOTAL,
        ];

        foreach ($requiredKeys as $requiredKey) {
            if (false === array_key_exists($requiredKey, $orderItemData)) {
                throw InvalidOrderItemDataReceivedException::missingKey($requiredKey, $requiredKeys);
            }
        }

        $this->validateProductId($orderItemData[self::API_KEY_PRODUCT_ID]);
        $this->validateQuantity($orderItemData[self::API_KEY_QUANTITY]);
        $this->validateUnitPrice($orderItemData[self::API_KEY_UNIT_PRICE]);
        $this->validateTotal($orderItemData[self::API_KEY_TOTAL]);
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateProductId(mixed $productId): void
    {
        if (false === is_string($productId)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((string) $productId, self::API_KEY_PRODUCT_ID);
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateQuantity(mixed $quantity): void
    {
        if (false === is_numeric($quantity) || intval($quantity) <= 0) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((int) $quantity, self::API_KEY_QUANTITY);
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateUnitPrice(mixed $unitPrice): void
    {
        if (false === is_numeric($unitPrice)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((float) $unitPrice, self::API_KEY_UNIT_PRICE);
        }
    }

    /**
     * @throws InvalidOrderItemDataReceivedException
     */
    private function validateTotal(mixed $total): void
    {
        if (false === is_numeric($total)) {
            // @phpstan-ignore-next-line - ignore because check is done, though not registered by phpstan
            throw InvalidOrderItemDataReceivedException::invalidValueForKey((float) $total, self::API_KEY_TOTAL);
        }
    }
}
