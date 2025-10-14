<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class FilterModel
{
    public function __construct(
        #[SerializedName('firstBallSideOut')]
        public bool $firstBallSideout,
    ) {}
}
