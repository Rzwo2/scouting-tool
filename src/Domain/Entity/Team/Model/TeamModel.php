<?php

declare(strict_types=1);

namespace App\Domain\Entity\Team\Model;

class TeamModel
{
    /** @param LinkModel[] $options */
    public function __construct(
        public string $name,
        /* public AddressModel $address, */
        public array $options,
    ) {}
}
