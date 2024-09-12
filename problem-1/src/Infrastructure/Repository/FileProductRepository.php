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

        $productData = json_decode($productData, true);
        foreach ($productData as $product) {
            if (array_key_exists('id', $product) && $product['id'] === $productId) {
                return $this->mapToProduct($product);
            }
        }

        throw ProductNotFoundException::invalidProductId($productId);
    }

    private function mapToProduct(array $customerData): Product
    {
        assert(array_key_exists('id', $customerData));
        assert(array_key_exists('description', $customerData));
        assert(array_key_exists('category', $customerData) && is_numeric($customerData['category']));
        assert(array_key_exists('price', $customerData) && is_numeric($customerData['price']));

        return new Product(
            $customerData['id'],
            $customerData['description'],
            (int) $customerData['category'],
            (float) $customerData['price'],
        );
    }
}
