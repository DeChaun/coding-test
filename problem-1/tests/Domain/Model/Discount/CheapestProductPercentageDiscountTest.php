<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Configurator\Discount\CheapestProductPercentageOptionConfigurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Customer;
use App\Domain\Model\Discount\CheapestProductPercentageDiscount;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Product;
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
                new Product('A123', 'Test description', 1, 20),
                40,
                20,
                800,
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
                new Product('A123', 'Test description', 1, 20),
                2,
                10,
                20,
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
                new Product('A123', 'Test description', 1, 20),
                1,
                20,
                20,
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
                new Product('B123', 'Test description', 2, 20),
                40,
                20,
                800,
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
                new Product('A123', 'Test description', 1, 20),
                2,
                10,
                20,
            ),
            new OrderItem(
                new Product('B123', 'Test description', 2, 20),
                2,
                10,
                20,
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
                new Product('A123', 'Test description', 1, 20),
                40,
                20,
                800,
            ),
            new OrderItem(
                new Product('A124', 'Test description', 1, 8),
                10,
                8,  // Cheapest
                80,
            ),
            new OrderItem(
                new Product('A125', 'Test description', 1, 100),
                4,
                100,
                400,
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new CheapestProductPercentageDiscount($order, $this->configurator);
        $this->assertEquals(16, $discount->getDiscountAmount());
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
            1,
            new Customer(1, 'Team Leader', new DateTime(), 500),
            $orderItems,
            888,
        );
    }
}
