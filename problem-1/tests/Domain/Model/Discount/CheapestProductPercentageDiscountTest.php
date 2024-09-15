<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Configurator\Discount\CheapestProductPercentageOptionConfigurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Category;
use App\Domain\Model\Customer;
use App\Domain\Model\Description;
use App\Domain\Model\Discount\CheapestProductPercentageDiscount;
use App\Domain\Model\Id;
use App\Domain\Model\Name;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use App\Domain\Model\Product;
use App\Domain\Model\Quantity;
use DateTime;
use PHPUnit\Framework\TestCase;

final class CheapestProductPercentageDiscountTest extends TestCase
{
    private readonly DiscountOptionConfigurator $configurator;

    public function __construct()
    {
        $this->configurator = new CheapestProductPercentageOptionConfigurator();

        parent::__construct();
    }

    public function testIfDiscountIsApplicable(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20),
                ),
                Quantity::create(40),
                Price::create(20),
                Price::create(800),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertTrue($discount->isApplicable());
    }

    public function testIfDiscountIsApplicableWithTwoProducts(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20),
                ),
                Quantity::create(2),
                Price::create(10),
                Price::create(20),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertTrue($discount->isApplicable());
    }

    public function testDiscountIsNotApplicableWhenThresholdIsNotMet(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20),
                ),
                Quantity::create(1),
                Price::create(20),
                Price::create(20),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testDiscountIsNotApplicableWithIncorrectCategory(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('B123'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(20),
                ),
                Quantity::create(40),
                Price::create(20),
                Price::create(800),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testDiscountIsApplicableWithMultipleCategories(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20),
                ),
                Quantity::create(2),
                Price::create(10),
                Price::create(20),
            ),
            new OrderItem(
                new Product(
                    Id::create('B123'),
                    Description::create('Test description'),
                    Category::create(2),
                    Price::create(20),
                ),
                Quantity::create(2),
                Price::create(10),
                Price::create(20),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertTrue($discount->isApplicable());
    }

    public function testDiscountAmountIsCorrect(): void
    {
        $orderItems = [
            new OrderItem(
                new Product(
                    Id::create('A123'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(20),
                ),
                Quantity::create(40),
                Price::create(20),
                Price::create(800),
            ),
            new OrderItem(
                new Product(
                    Id::create('A124'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(8)
                ),
                Quantity::create(10),
                Price::create(8),  // Cheapest
                Price::create(80),
            ),
            new OrderItem(
                new Product(
                    Id::create('A125'),
                    Description::create('Test description'),
                    Category::create(1),
                    Price::create(100),
                ),
                Quantity::create(4),
                Price::create(100),
                Price::create(400),
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertEquals(16, $discount->getDiscountAmount()->getValue());
    }

    public function testTypeIsCorrect(): void
    {
        $order = $this->getMockOrder([]);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertEquals(DiscountType::CheapestProductPercentageDiscount, $discount->getType());
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
