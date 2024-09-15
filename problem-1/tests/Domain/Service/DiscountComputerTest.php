<?php

declare(strict_types=1);

namespace Tests\Domain\Service;

use App\Application\Command\Discount\ComputeDiscount;
use App\Application\Command\Discount\ComputeDiscountHandler;
use App\Domain\Model\Category;
use App\Domain\Model\Customer;
use App\Domain\Model\Description;
use App\Domain\Model\Discount\BuyXGetYItemsFreeDiscount;
use App\Domain\Model\Discount\HighTotalRevenueDiscount;
use App\Domain\Model\Id;
use App\Domain\Model\Name;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use App\Domain\Model\Product;
use App\Domain\Model\Quantity;
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
            BuyXGetYItemsFreeDiscount::class,
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
        $this->assertCount(2, $discountOrder->getDiscounts());

        $this->assertEquals(684, $discountOrder->getDiscountedTotal()->getValue());
        $this->assertEquals(204, $discountOrder->getDiscountAmount()->getValue());
        $this->assertEquals(888, $discountOrder->getTotal()->getValue());
    }

    private function getMockOrder(): Order
    {
        return new Order(
            Id::create(1),
            new Customer(Id::create(2), Name::create('Team Leader'), new \DateTime(), Price::create(1515.15)),
            [
                new OrderItem(
                    new Product(
                        Id::create('A123'),
                        Description::create('Test description'),
                        Category::create(2),
                        Price::create(20),
                    ),
                    Quantity::create(40), // 6 * 20 = 120
                    Price::create(20),
                    Price::create(800),
                ),
                new OrderItem(
                    new Product(
                        Id::create('A124'),
                        Description::create('Test description'),
                        Category::create(2),
                        Price::create(8),
                    ),
                    Quantity::create(10), // 1 * 8 = 8
                    Price::create(8),
                    Price::create(80),
                ),
                new OrderItem(
                    new Product(
                        Id::create('A125'),
                        Description::create('Test description'),
                        Category::create(2),
                        Price::create(2),
                    ),
                    Quantity::create(4), // 0
                    Price::create(2),
                    Price::create(8),
                ),
            ],
            Price::create(888),
        );
    }
}
