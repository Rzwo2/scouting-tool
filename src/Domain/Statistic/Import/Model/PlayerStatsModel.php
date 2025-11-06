<?php

declare(strict_types=1);

namespace App\Domain\Statistic\Import\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PlayerStatsModel
{
    public function __construct(
        public ?int $setsPlayed = null,
        public ?int $jerseyNumber = null,
        public ?int $attackKills = null,
        public ?int $attackErrors = null,
        public ?int $attackAttempts = null,
        public ?int $serveAces = null,
        public ?int $serveErrors = null,

        #[SerializedName('serve_1s')]
        public ?int $serve1s = null,
        public ?int $serveAttempts = null,
        public ?float $serveRating = null,

        #[SerializedName('receive_3s')]
        public ?int $receive3s = null,

        #[SerializedName('receive_2s')]
        public ?int $receive2s = null,

        #[SerializedName('receive_1s')]
        public ?int $receive1s = null,

        #[SerializedName('receive_0s')]
        public ?int $receive0s = null,
        public ?int $receiveAttempts = null,
        public ?int $setAssists = null,
        public ?int $setAttempts = null,
        public ?int $digSuccesss = null,
        public ?int $digErrors = null,
        public ?int $blockBlockSolos = null,
        public ?int $blockBlockAssists = null,
        public ?int $blockBlockErrors = null,
    ) {}
}
