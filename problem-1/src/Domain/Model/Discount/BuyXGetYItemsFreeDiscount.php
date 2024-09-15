<?php

declare(strict_types=1);

namespace App\Domain\Model\Discount;

use App\Domain\Configurator\Discount\BuyXGetYItemsFreeOptionConfigurator as Configurator;
use App\Domain\Configurator\Discount\DiscountOptionConfigurator;
use App\Domain\Enum\DiscountType;
use App\Domain\Model\Order;
use App\Domain\Model\OrderItem;

final readonly class BuyXGetYItemsFreeDiscount implements Discount
{
    /** @var int[] $consideredCategories */
    private array $consideredCategories;

    private int $threshold;

    private int $numberOfFreeItems;

    public function __construct(
        private Order $order,
        DiscountOptionConfigurator $optionConfigurator,
    ) {
        assert($optionConfigurator instanceof Configurator);

        $options = $optionConfigurator->getOptions();

        assert(array_key_exists(Configurator::CONFIG_KEY_THRESHOLD, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_THRESHOLD]));

        assert(array_key_exists(Configurator::CONFIG_KEY_NUMBER_OF_FREE_ITEMS, $options));
        assert(is_int($options[Configurator::CONFIG_KEY_NUMBER_OF_FREE_ITEMS]));

        assert(array_key_exists(Configurator::CONFIG_KEY_CATEGORY_IDS, $options));
        assert(is_array($options[Configurator::CONFIG_KEY_CATEGORY_IDS]));

        foreach ($options[Configurator::CONFIG_KEY_CATEGORY_IDS] as $categoryId) {
            assert(is_int($categoryId));
        }

        $this->threshold            = $options[Configurator::CONFIG_KEY_THRESHOLD];
        $this->numberOfFreeItems    = $options[Configurator::CONFIG_KEY_NUMBER_OF_FREE_ITEMS];
        $this->consideredCategories = $options[Configurator::CONFIG_KEY_CATEGORY_IDS];
    }

    public function isApplicable(): bool
    {
        $consideredCategoryOrderItems = $this->getOrderItemsForConsideredCategories();
        if (count($consideredCategoryOrderItems) === 0) {
            return false;
        }

        foreach ($consideredCategoryOrderItems as $orderItem) {
            if ($orderItem->getQuantity() >= $this->threshold) {
                // At least one order item will receive the discount
                return true;
            }
        }

        return false;
    }

    public function getDiscountAmount(): ?float
    {
        $totalDiscount = 0;
        $consideredOrderItems = $this->getOrderItemsForConsideredCategories();
        foreach ($consideredOrderItems as $consideredOrderItem) {
            $iterations = floor($consideredOrderItem->getQuantity() / $this->threshold);
            if (floatval(0) === $iterations) {
                continue;
            }

            $numberOfFreeItems = $iterations * $this->numberOfFreeItems;    // 1 free item for every iteration

            $totalDiscount += $numberOfFreeItems * $consideredOrderItem->getUnitPrice();
        }

        return $totalDiscount;
    }

    public function getType(): DiscountType
    {
        return DiscountType::BuyXGetYItemsFree;
    }

    public function getExplanation(): string
    {
        $categoryText = array_map(
            fn (int $categoryId) => 'id ' . $categoryId,
            $this->consideredCategories,
        );

        return sprintf(
            'For every product in the category list (%s), when you buy %s, %s item is for free. ' .
            'This results in a € %s discount.',
            implode(', ', $categoryText),
            $this->threshold,
            $this->numberOfFreeItems,
            $this->getDiscountAmount(),
        );
    }

    /**
     * @return OrderItem[]
     */
    private function getOrderItemsForConsideredCategories(): array
    {
        $mapper = [];
        foreach ($this->consideredCategories as $categoryId) {
            // Create mapper to improve performance
            $mapper[$categoryId] = $categoryId;
        }

        $consideredOrderItems = [];
        foreach ($this->order->getOrderItems() as $orderItem) {
            if (true === array_key_exists($orderItem->getProduct()->getCategoryId(), $mapper)) {
                $consideredOrderItems[] = $orderItem;
            }
        }

        return $consideredOrderItems;
    }
}
