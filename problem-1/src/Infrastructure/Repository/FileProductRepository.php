<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\ProductNotFoundException;
use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepository;

final class FileProductRepository implements ProductRepository
{
    /**
     * @throws ProductNotFoundException
     */
    public function getById(string $productId): Product
    {
        $productData = file_get_contents(__DIR__ . '/../../../data/products.json');
        assert($productData !== false);

        $productData = json_decode($productData, true);
        assert(is_array($productData));

        foreach ($productData as $product) {
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
