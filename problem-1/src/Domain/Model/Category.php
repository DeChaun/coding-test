<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Category
{
    private function __construct(
        private Id $id,
    ) {
    }

    public static function create(int|string $categoryId): self
    {
        return new self(
            Id::create($categoryId)
        );
    }

    public function getId(): Id
    {
        return $this->id;
    }
}
