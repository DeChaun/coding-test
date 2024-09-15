<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepository;

final class InMemoryProductRepository implements ProductRepository
{
    /**
     * @var list<array<string, int|string|float>>
     */
    private array $productData = [
        [
            "id" => "A101",
            "description" => "Screwdriver",
            "category" => "1",
            "price" => "9.75"
        ],
        [
            "id" => "A102",
            "description" => "Electric screwdriver",
            "category" => "1",
            "price" => "49.50"
        ],
        [
            "id" => "B101",
            "description" => "Basic on-off switch",
            "category" => "2",
            "price" => "4.99"
        ],
        [
            "id" => "B102",
            "description" => "Press button",
            "category" => "2",
            "price" => "4.99"
        ],
        [
            "id" => "B103",
            "description" => "Switch with motion detector",
            "category" => "2",
            "price" => "12.95"
        ]
    ];

    /**
     * @throws ProductNotFoundException
     */
    public function getById(string $productId): Product
    {
        foreach ($this->productData as $product) {
            if (array_key_exists('id', $product) && $product['id'] === $productId) {
                return $this->mapToProduct($product);
            }
        }

        throw ProductNotFoundException::invalidProductId($productId);
    }

    /**
     * @param array<string, string|int|float> $productData
     */
    private function mapToProduct(array $productData): Product
    {
        assert(array_key_exists('id', $productData));
        assert(array_key_exists('description', $productData));
        assert(array_key_exists('category', $productData) && is_numeric($productData['category']));
        assert(array_key_exists('price', $productData) && is_numeric($productData['price']));

        return new Product(
            (string) $productData['id'],
            (string) $productData['description'],
            (int) $productData['category'],
            (float) $productData['price'],
        );
    }
}
