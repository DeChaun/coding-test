<?php

declare(strict_types=1);

namespace Tests\Domain\Service;

use App\Application\Command\Discount\ComputeDiscount;
use App\Application\Command\Discount\ComputeDiscountHandler;
use App\Domain\Model\Customer;
use App\Domain\Model\Discount\HighTotalRevenueDiscount;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Product;
use App\Domain\Service\DiscountComputer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final class DiscountComputerTest extends TestCase
{
    use HandleTrait;

    protected function setUp(): void
    {
        $this->messageBus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                ComputeDiscount::class => [new ComputeDiscountHandler(
                    new DiscountComputer(),
                )],
            ])),
        ]);
    }

    public function testEveryDiscountIsAppliedOnce(): void
    {
        $order = $this->getMockOrder();

        /** @var Order $discountOrder */
        $discountOrder = $this->handle(new ComputeDiscount($order));

        $this->assertIsArray($discountOrder->getDiscounts());

        // Assert every discount is available only once
        $requiredDiscounts = [
            HighTotalRevenueDiscount::class,
        ];

        $requiredDiscounts = array_combine($requiredDiscounts, $requiredDiscounts);

        foreach ($discountOrder->getDiscounts() as $discount) {
            $this->assertTrue(in_array(get_class($discount), $requiredDiscounts));
            unset($requiredDiscounts[get_class($discount)]);
        }

        $this->assertEmpty($requiredDiscounts);
    }

    public function testTotalsAreCorrect(): void
    {
        $order = $this->getMockOrder();

        /** @var Order $discountOrder */
        $discountOrder = $this->handle(new ComputeDiscount($order));

        $this->assertIsArray($discountOrder->getDiscounts());
        $this->assertCount(1, $discountOrder->getDiscounts());

        $this->assertEquals(799.20, $discountOrder->getDiscountedTotal());
        $this->assertEquals(88.8, $discountOrder->getDiscountAmount());
        $this->assertEquals(888, $discountOrder->getTotal());
    }

    private function getMockOrder(): Order
    {
        return new Order(
            1,
            new Customer(2, 'Team Leader', new \DateTime(), 1515.15),
            [
                new OrderItem(
                    new Product('A123', 'Test description', 2, 20),
                    40,
                    20,
                    800,
                ),
                new OrderItem(
                    new Product('A124', 'Test description', 2, 8),
                    10,
                    8,
                    80,
                ),
                new OrderItem(
                    new Product('A125', 'Test description', 2, 2),
                    4,
                    2,
                    8,
                ),
            ],
            888,
        );
    }
}
