<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Configurator\Discount\BuyXGetYItemsFreeOptionConfigurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Category;
use App\Domain\Model\Customer;
use App\Domain\Model\Description;
use App\Domain\Model\Discount\BuyXGetYItemsFreeDiscount;
use App\Domain\Model\Id;
use App\Domain\Model\Name;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use App\Domain\Model\Product;
use App\Domain\Model\Quantity;
use PHPUnit\Framework\TestCase;

final class BuyXGetYItemsFreeDiscountTest extends TestCase
{
    private readonly DiscountOptionConfigurator $configurator;

    public function __construct()
    {
        $this->configurator = new BuyXGetYItemsFreeOptionConfigurator();

        parent::__construct();
    }

    public function testIfDiscountIsApplicable(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(20)
                ),
                Quantity::create(40),
                Price::create(20),
                Price::create(800),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertTrue($discount->isApplicable());
    }

    public function testNonApplicableCategories(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20)
                ),
                Quantity::create(5),
                Price::create(20),
                Price::create(100),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testNoApplicableCategories(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(3),
                    Price::create(20)
                ),
                Quantity::create(40),
                Price::create(20),
                Price::create(800),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testNotReachingThreshold(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(20)
                ),
                Quantity::create(5),
                Price::create(20),
                Price::create(100),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testDiscountAmountIsCorrect(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(20)
                ),
                Quantity::create(40), // 6 free items * EUR 20 = EUR 120
                Price::create(20),
                Price::create(800),
            ),
            new OrderItem(
                new Product(
                    Id::create('A124'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(8)
                ),
                Quantity::create(10), // 1 free item * EUR 8 = EUR 8
                Price::create(8),
                Price::create(80),
            ),
            new OrderItem(
                new Product(
                    Id::create('A125'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(2)
                ),
                Quantity::create(4), // No free items
                Price::create(2),
                Price::create(8),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertEquals(128, $discount->getDiscountAmount()->getValue());
    }

    public function testTypeIsCorrect(): void
    {
        $order = $this->getMockOrder([]);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertEquals(DiscountType::BuyXGetYItemsFree, $discount->getType());
    }

    /**
     * @param OrderItem[] $orderItems
     */
    private function getMockOrder(array $orderItems): Order
    {
        return new Order(
            Id::create(1),
            new Customer(Id::create(1), Name::create('Team Leader'), new \DateTime(), Price::create(500)),
            $orderItems,
            Price::create(888),
        );
    }
}
