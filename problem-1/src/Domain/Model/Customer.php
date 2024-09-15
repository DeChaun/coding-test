<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTime;

final readonly class Customer
{
    public function __construct(
        public Id $id,
        public Name $name,
        public DateTime $since,
        public Price $revenue,
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getSince(): DateTime
    {
        return $this->since;
    }

    public function getRevenue(): Price
    {
        return $this->revenue;
    }
}
