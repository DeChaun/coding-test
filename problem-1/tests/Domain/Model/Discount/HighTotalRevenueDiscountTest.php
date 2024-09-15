<?php

declare(strict_types=1);

namespace Tests\Domain\Model\Discount;

use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Configurator\Discount\HighTotalRevenueOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Category;
use App\Domain\Model\Customer;
use App\Domain\Model\Description;
use App\Domain\Model\Discount\HighTotalRevenueDiscount;
use App\Domain\Model\Id;
use App\Domain\Model\Name;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;
use App\Domain\Model\Product;
use App\Domain\Model\Quantity;
use PHPUnit\Framework\TestCase;

final class HighTotalRevenueDiscountTest extends TestCase
{
    private readonly DiscountOptionConfigurator $configurator;

    public function __construct()
    {
        $this->configurator = new HighTotalRevenueOptionConfigurator();

        parent::__construct();
    }

    public function testIfDiscountIsApplicableWhenBelow1000Euro(): void
    {
        $order = $this->getMockOrder(500.01);

        $discount = new HighTotalRevenueDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testIfDiscountIsApplicableWhenExactly1000Euro(): void
    {
        $order = $this->getMockOrder(1000.00);

        $discount = new HighTotalRevenueDiscount($order, $this->configurator);
        $this->assertFalse($discount->isApplicable());
    }

    public function testIfDiscountIsNotApplicableWhenBelow1000Euro(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order, $this->configurator);
        $this->assertTrue($discount->isApplicable());
    }

    public function testDiscountAmountIsCorrect(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order, $this->configurator);
        $this->assertEquals(88.80, $discount->getDiscountAmount()->getValue());
    }

    public function testTypeIsCorrect(): void
    {
        $order = $this->getMockOrder(2000.12);

        $discount = new HighTotalRevenueDiscount($order, $this->configurator);
        $this->assertEquals(DiscountType::HighTotalRevenueDiscount, $discount->getType());
    }

    private function getMockOrder(float $totalRevenue): Order
    {
        return new Order(
            Id::create(1),
            new Customer(Id::create(2), Name::create('Team Leader'), new \DateTime(), Price::create($totalRevenue)),
            [
                new OrderItem(
                    new Product(
                        Id::create('A123'),
                        Description::create('Test description'),
                        Category::create(2),
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
                        Category::create(2),
                        Price::create(8),
                    ),
                    Quantity::create(10),
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
                    Quantity::create(4),
                    Price::create(2),
                    Price::create(8),
                ),
            ],
            Price::create(888),
        );
    }
}
