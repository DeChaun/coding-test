<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Factory;

use App\Application\Command\Product\GetCustomer;
use App\Application\Command\Product\GetCustomerHandler;
use App\Application\Command\Product\GetProduct;
use App\Application\Command\Product\GetProductHandler;
use App\Domain\Exception\Request\InvalidOrderDataReceivedException;
use App\Domain\Model\Order;
use App\Infrastructure\Factory\OrderFactory;
use App\Infrastructure\Factory\OrderItemFactory;
use App\Infrastructure\Repository\InMemoryCustomerRepository;
use App\Infrastructure\Repository\InMemoryProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final class OrderFactoryTest extends TestCase
{
    private OrderFactory $orderFactory;

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

        $orderItemFactory   = new OrderItemFactory($queryBus);
        $this->orderFactory = new OrderFactory($orderItemFactory, $queryBus);
    }

    public function testOrdersCanBeCreated(): void
    {
        $order = $this->orderFactory->fromApiData($this->getMockOrder());

        $this->assertInstanceOf(Order::class, $order);

        $this->assertTrue($order->getId() === 1);
        $this->assertTrue($order->getCustomer()->id === 1);
        $this->assertTrue($order->getTotal() === 49.9);
    }

    public function testMissingIdResultsIntoException(): void
    {
        $orderData = $this->getMockOrder();
        unset($orderData['id']);

        $this->expectException(InvalidOrderDataReceivedException::class);
        $this->orderFactory->fromApiData($orderData);
    }

    public function testMissingCustomerIdResultsIntoException(): void
    {
        $orderData = $this->getMockOrder();
        unset($orderData['customer-id']);

        $this->expectException(InvalidOrderDataReceivedException::class);
        $this->orderFactory->fromApiData($orderData);
    }

    public function testMissingItemsResultsIntoException(): void
    {
        $orderData = $this->getMockOrder();
        unset($orderData['items']);

        $this->expectException(InvalidOrderDataReceivedException::class);
        $this->orderFactory->fromApiData($orderData);
    }

    public function testMissingTotalResultsIntoException(): void
    {
        $orderData = $this->getMockOrder();
        unset($orderData['total']);

        $this->expectException(InvalidOrderDataReceivedException::class);
        $this->orderFactory->fromApiData($orderData);
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    private function getMockOrder(): array
    {
        return [
            "id" => "1",
            "customer-id" => "1",
            "items" => [],
            "total" => "49.90"
        ];
    }
}
