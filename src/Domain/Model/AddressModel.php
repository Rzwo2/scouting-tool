<?php

declare(strict_types=1);

namespace App\Domain\Model;

class AddressModel
{
    public function __construct(
        public ?string $zip = null,
        public ?string $city = null,
        public ?string $street = null,
        public ?string $suffix = null,
        public ?string $number = null,
    ) {}
}
