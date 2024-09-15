<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Configurator\Discount\CheapestProductPercentageOptionConfigurator as Configurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;
use App\Domain\Model\Price;

final readonly class CheapestProductPercentageDiscount implements Discount
{
    private const string NUMBER_OF_PRODUCTS  = 'numberOfProducts';
    private const string CHEAPEST_ORDER_ITEM = 'cheapestOrderItem';
    private const string ORDER_ITEMS         = 'orderItems';

    private int $consideredCategory;

    private int $threshold;

    private int $discountPercentage;

    public function __construct(
        private Order $order,
        DiscountOptionConfigurator $optionConfigurator,
    ) {
        assert($optionConfigurator instanceof Configurator);

        $options = $optionConfigurator->getOptions();

        assert(array_key_exists(Configurator::CONFIG_KEY_THRESHOLD, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_THRESHOLD]));

        assert(array_key_exists(Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE]));

        assert(array_key_exists(Configurator::CONFIG_KEY_CATEGORY_ID, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_CATEGORY_ID]));

        $this->threshold          = $options[Configurator::CONFIG_KEY_THRESHOLD];
        $this->discountPercentage = $options[Configurator::CONFIG_KEY_DISCOUNT_PERCENTAGE];
        $this->consideredCategory = $options[Configurator::CONFIG_KEY_CATEGORY_ID];
    }

    public function isApplicable(): bool
    {
        $consideredCategoryOrderItems = $this->getOrderItemsForConsideredCategory();
        if (count($consideredCategoryOrderItems) === 0) {
            return false;
        }

        $mapper = $this->cheapestOrderItemMapper($consideredCategoryOrderItems);
        if ($mapper[self::NUMBER_OF_PRODUCTS] >= $this->threshold) {
            return true;
        }

        return false;
    }

    public function getDiscountAmount(): ?Price
    {
        $consideredCategoryOrderItems = $this->getOrderItemsForConsideredCategory();

        $mapper = $this->cheapestOrderItemMapper($consideredCategoryOrderItems);

        /** @var OrderItem $cheapestOrderItem */
        $cheapestOrderItem = $mapper[self::CHEAPEST_ORDER_ITEM];

        return Price::create($cheapestOrderItem->getTotalPrice()->getValue() * ($this->discountPercentage / 100));
    }

    public function getType(): DiscountType
    {
        return DiscountType::CheapestProductPercentageDiscount;
    }

    public function getExplanation(): string
    {
        return sprintf(
            'If you buy %s or more products of category id %s, you get a %s%% discount ' .
            'on the cheapest product. This results in a € %s discount.',
            $this->threshold,
            $this->consideredCategory,
            $this->discountPercentage,
            $this->getDiscountAmount(),
        );
    }

    /**
     * @return OrderItem[]
     */
    private function getOrderItemsForConsideredCategory(): array
    {
        $consideredOrderItems = [];
        foreach ($this->order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getCategory()->getId()->equals($this->consideredCategory)) {
                $consideredOrderItems[] = $orderItem;
            }
        }

        return $consideredOrderItems;
    }

    /**
     * @param OrderItem[] $consideredCategoryOrderItems
     * @return array<string, int|OrderItem[]|OrderItem>
     */
    private function cheapestOrderItemMapper(array $consideredCategoryOrderItems): array
    {
        $mapper = [
            self::NUMBER_OF_PRODUCTS => 0,
            self::CHEAPEST_ORDER_ITEM => null,
            self::ORDER_ITEMS => [],
        ];

        foreach ($consideredCategoryOrderItems as $orderItem) {
            $cheapestProduct = $mapper[self::CHEAPEST_ORDER_ITEM] ?? $orderItem;
            $cheapestProduct = $orderItem->getUnitPrice() < $cheapestProduct->getUnitPrice()
                ? $orderItem
                : $cheapestProduct
            ;

            $mapper[self::NUMBER_OF_PRODUCTS] = $mapper[self::NUMBER_OF_PRODUCTS]
                + $orderItem->getQuantity()->getValue()
            ;

            $mapper[self::CHEAPEST_ORDER_ITEM] = $cheapestProduct;
            $mapper[self::ORDER_ITEMS][] = $orderItem;
        }

        // Will never by null
        assert(! is_null($mapper[self::CHEAPEST_ORDER_ITEM]));

        return $mapper;
    }
}
