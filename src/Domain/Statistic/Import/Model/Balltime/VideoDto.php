<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model\Balltime;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class VideoDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $id,
        #[Assert\NotBlank]
        public string $title,
        #[Assert\NotBlank]
        #[SerializedName('folder_id')]
        public ?string $folderId,
    ) {}
}
