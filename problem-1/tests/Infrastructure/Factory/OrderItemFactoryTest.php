<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Factory;

use App\Application\Command\Product\GetCustomer;
use App\Application\Command\Product\GetCustomerHandler;
use App\Application\Command\Product\GetProduct;
use App\Application\Command\Product\GetProductHandler;
use App\Domain\Exception\Request\InvalidOrderItemDataReceivedException;
use App\Domain\Model\OrderItem;
use App\Infrastructure\Factory\OrderItemFactory;
use App\Infrastructure\Repository\InMemoryCustomerRepository;
use App\Infrastructure\Repository\InMemoryProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final class OrderItemFactoryTest extends TestCase
{
    private OrderItemFactory $orderItemFactory;

    protected function setUp(): void
    {
        $queryBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                GetCustomer::class => [new GetCustomerHandler(
                    new InMemoryCustomerRepository(),
                )],
                getProduct::class => [new GetProductHandler(
                    new InMemoryProductRepository(),
                )],
            ])),
        ]);

        $this->orderItemFactory = new OrderItemFactory($queryBus);
    }

    public function testOrderItemsCanBeCreated(): void
    {
        $orderItem = $this->orderItemFactory->fromApiData($this->getMockOrderItem());

        $this->assertInstanceOf(OrderItem::class, $orderItem);

        $this->assertTrue($orderItem->getProduct()->getId()->equals('B102'));
        $this->assertTrue($orderItem->getQuantity()->getValue() === 10);
        $this->assertTrue($orderItem->getUnitPrice()->getValue() === 4.99);
        $this->assertTrue($orderItem->getTotalPrice()->getValue() === 49.9);
    }

    public function testMissingProductIdResultsIntoException(): void
    {
        $orderItemData = $this->getMockOrderItem();
        unset($orderItemData['product-id']);

        $this->expectException(InvalidOrderItemDataReceivedException::class);
        $this->orderItemFactory->fromApiData($orderItemData);
    }

    public function testMissingQuantityResultsIntoException(): void
    {
        $orderItemData = $this->getMockOrderItem();
        unset($orderItemData['quantity']);

        $this->expectException(InvalidOrderItemDataReceivedException::class);
        $this->orderItemFactory->fromApiData($orderItemData);
    }

    public function testMissingUnitPriceResultsIntoException(): void
    {
        $orderItemData = $this->getMockOrderItem();
        unset($orderItemData['unit-price']);

        $this->expectException(InvalidOrderItemDataReceivedException::class);
        $this->orderItemFactory->fromApiData($orderItemData);
    }

    public function testMissingTotalResultsIntoException(): void
    {
        $orderItemData = $this->getMockOrderItem();
        unset($orderItemData['total']);

        $this->expectException(InvalidOrderItemDataReceivedException::class);
        $this->orderItemFactory->fromApiData($orderItemData);
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    private function getMockOrderItem(): array
    {
        return [
            "product-id" => "B102",
            "quantity" => "10",
            "unit-price" => "4.99",
            "total" => "49.90"
        ];
    }
}
