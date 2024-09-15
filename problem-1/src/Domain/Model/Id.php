<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class Id
{
    private function __construct(
        private string $id,
    ) {
    }

    public static function create(int|string $id): self
    {
        return new self((string) $id);
    }

    public function equals(int|string $value): bool
    {
        return (string) $value === $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
