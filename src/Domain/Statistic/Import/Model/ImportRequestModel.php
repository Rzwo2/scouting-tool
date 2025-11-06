<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class ImportRequestModel
{
    /** @param string[] $videoIds */
    public function __construct(
        #[SerializedName('video_ids')] public array $videoIds,
        public FilterModel $filters = new FilterModel(),
    ) {}
}
