<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model\Balltime;

readonly class BalltimeVideosResponseDto
{
    /**
     * @param FolderDto[] $folders
     * @param VideoDto[]  $videos
     */
    public function __construct(
        public array $folders,
        public array $videos,
    ) {}
}
