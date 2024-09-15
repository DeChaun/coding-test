<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Description
{
    private function __construct(
        private string $description,
    ) {
    }

    public static function create(string $description): self
    {
        return new self($description);
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
