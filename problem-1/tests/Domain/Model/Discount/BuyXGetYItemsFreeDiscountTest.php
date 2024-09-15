<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Configurator\Discount\BuyXGetYItemsFreeOptionConfigurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Customer;
use App\Domain\Model\Discount\BuyXGetYItemsFreeDiscount;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Product;
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
                new Product('A123', 'Test description', 2, 20),
                40,
                20,
                800,
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
                new Product('A123', 'Test description', 1, 20),
                5,
                20,
                100,
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
                new Product('A123', 'Test description', 3, 20),
                40,
                20,
                800,
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
                new Product('A123', 'Test description', 2, 20),
                5,
                20,
                100,
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
                new Product('A123', 'Test description', 2, 20),
                40, // 6 free items * EUR 20 = EUR 120
                20,
                800,
            ),
            new OrderItem(
                new Product('A124', 'Test description', 2, 8),
                10, // 1 free item * EUR 8 = EUR 8
                8,
                80,
            ),
            new OrderItem(
                new Product('A125', 'Test description', 2, 2),
                4, // No free items
                2,
                8,
            ),
        ];

        $order = $this->getMockOrder($orderItems);

        $discount = new BuyXGetYItemsFreeDiscount($order, $this->configurator);
        $this->assertEquals(128, $discount->getDiscountAmount());
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
            1,
            new Customer(1, 'Team Leader', new \DateTime(), 500),
            $orderItems,
            888,
        );
    }
}
