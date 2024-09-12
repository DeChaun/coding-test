<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTime;

final readonly class Customer
{
    public function __construct(
        public int $id,
        public string $name,
        public DateTime $since,
        public float $revenue,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSince(): DateTime
    {
        return $this->since;
    }

    public function getRevenue(): float
    {
        return $this->revenue;
    }
}
