<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model\Balltime;

use Symfony\Component\Validator\Constraints as Assert;

readonly class FolderDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
        #[Assert\NotBlank]
        public string $name,
    ) {}
}
