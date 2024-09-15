<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Enum\DiscountType;
use App\Domain\Model\Customer;
use App\Domain\Model\Discount\HighTotalRevenueDiscount;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Product;
use PHPUnit\Framework\TestCase;

final class HighTotalRevenueDiscountTest extends TestCase
{
    public function testIfDiscountIsApplicableWhenBelow1000Euro(): void
    {
        $order = $this->getMockOrder(500.01);

        $discount = new HighTotalRevenueDiscount($order);
        $this->assertFalse($discount->isApplicable());
    }

    public function testIfDiscountIsApplicableWhenExactly1000Euro(): void
    {
        $order = $this->getMockOrder(1000.00);

        $discount = new HighTotalRevenueDiscount($order);
        $this->assertFalse($discount->isApplicable());
    }

    public function testIfDiscountIsNotApplicableWhenBelow1000Euro(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order);
        $this->assertTrue($discount->isApplicable());
    }

    public function testDiscountAmountIsCorrect(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order);
        $this->assertEquals(88.80, $discount->getDiscountAmount());
    }

    public function testTypeIsCorrect(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order);
        $this->assertEquals(DiscountType::HighTotalRevenueDiscount, $discount->getType());
    }

    private function getMockOrder(float $totalRevenue): Order
    {
        return new Order(
            1,
            new Customer(2, 'Team Leader', new \DateTime(), $totalRevenue),
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
