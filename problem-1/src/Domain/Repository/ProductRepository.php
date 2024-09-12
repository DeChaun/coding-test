<?php

namespace App\Domain\Repository;

use App\Domain\Model\Product;

interface ProductRepository
{
    public function getById(string $productId): Product;
}
