<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PlayerStatsModel
{
    public function __construct(
        public int $setsPlayed,
        public ?int $jerseyNumber = null,
        public ?int $attackAttempts = null,
        public ?int $attackErrors = null,
        public ?int $attackKills = null,
        public ?int $setAssists = null,
        public ?int $setAttempts = null,
        public ?int $setErrors = null,
        public ?int $blockBlockAssists = null,
        public ?int $blockBlockErrors = null,
        public ?int $blockBlockSolos = null,
        public ?int $digSuccesss = null,
        public ?int $digErrors = null,

        #[SerializedName('receive_0s')]
        public ?int $receive0 = null,

        #[SerializedName('receive_1s')]
        public ?int $receive1 = null,

        #[SerializedName('receive_2s')]
        public ?int $receive2 = null,

        #[SerializedName('receive_3s')]
        public ?int $receive3 = null,
        public ?int $receiveAttempts = null,
        public ?int $serveAces = null,
        public ?int $serveErrors = null,
        public ?int $serveAttempts = null,
    )
    {
    }
}

