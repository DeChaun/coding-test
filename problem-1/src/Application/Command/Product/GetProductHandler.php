<?php

declare(strict_types=1);

namespace App\Application\Command\Product;

use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepository;

final readonly class GetProductHandler
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    public function __invoke(GetProduct $command): Product
    {
        return $this->productRepository->getById($command->productId);
    }
}
