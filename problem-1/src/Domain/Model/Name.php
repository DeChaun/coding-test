<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Name
{
    private function __construct(
        private string $name,
    ) {
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
