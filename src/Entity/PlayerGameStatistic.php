<?php

namespace App\Entity;

use App\Repository\PlayerGameStatisticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerGameStatisticRepository::class)]
#[ORM\UniqueConstraint(fields: ['player', 'game', 'isFirstBallSideOut'])]
#[ORM\Index(fields: ['player', 'game', 'isFirstBallSideOut'])]
class PlayerGameStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'playerGameStatistics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Player $player;

    #[ORM\ManyToOne(inversedBy: 'playerGameStatistics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Game $game;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $setsPlayed;

    #[ORM\Column]
    private bool $isFirstBallSideOut;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackKills = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackErrors = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackAttempts = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveAces = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveErrors = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serve1s = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveAttempts = null;

    #[ORM\Column(nullable: true)]
    private ?float $serveRating = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive3s = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive2s = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive1s = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive0s = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receiveAttempts = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $setAssists = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $setAttempts = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $digSuccesss = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $digErrors = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockBlockSolos = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockBlockAssists = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockBlockErrors = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getSetsPlayed(): int
    {
        return $this->setsPlayed;
    }

    public function setSetsPlayed(int $setsPlayed): static
    {
        $this->setsPlayed = $setsPlayed;

        return $this;
    }

    public function isFirstBallSideOut(): bool
    {
        return $this->isFirstBallSideOut;
    }

    public function setIsFirstBallSideOut(bool $isFirstBallSideOut): static
    {
        $this->isFirstBallSideOut = $isFirstBallSideOut;

        return $this;
    }

    public function getAttackKills(): ?int
    {
        return $this->attackKills;
    }

    public function setAttackKills(?int $attackKills): static
    {
        $this->attackKills = $attackKills;

        return $this;
    }

    public function getAttackErrors(): ?int
    {
        return $this->attackErrors;
    }

    public function setAttackErrors(?int $attackErrors): static
    {
        $this->attackErrors = $attackErrors;

        return $this;
    }

    public function getAttackAttempts(): ?int
    {
        return $this->attackAttempts;
    }

    public function setAttackAttempts(?int $attackAttempts): static
    {
        $this->attackAttempts = $attackAttempts;

        return $this;
    }

    public function getServeAces(): ?int
    {
        return $this->serveAces;
    }

    public function setServeAces(?int $serveAces): static
    {
        $this->serveAces = $serveAces;

        return $this;
    }

    public function getServeErrors(): ?int
    {
        return $this->serveErrors;
    }

    public function setServeErrors(?int $serveErrors): static
    {
        $this->serveErrors = $serveErrors;

        return $this;
    }

    public function getServe1s(): ?int
    {
        return $this->serve1s;
    }

    public function setServe1s(?int $serve1s): static
    {
        $this->serve1s = $serve1s;

        return $this;
    }

    public function getServeAttempts(): ?int
    {
        return $this->serveAttempts;
    }

    public function setServeAttempts(?int $serveAttempts): static
    {
        $this->serveAttempts = $serveAttempts;

        return $this;
    }

    public function getServeRating(): ?float
    {
        return $this->serveRating;
    }

    public function setServeRating(?float $serveRating): static
    {
        $this->serveRating = $serveRating;

        return $this;
    }

    public function getReceive3s(): ?int
    {
        return $this->receive3s;
    }

    public function setReceive3s(?int $receive3s): static
    {
        $this->receive3s = $receive3s;

        return $this;
    }

    public function getReceive2s(): ?int
    {
        return $this->receive2s;
    }

    public function setReceive2s(?int $receive2s): static
    {
        $this->receive2s = $receive2s;

        return $this;
    }

    public function getReceive1s(): ?int
    {
        return $this->receive1s;
    }

    public function setReceive1s(?int $receive1s): static
    {
        $this->receive1s = $receive1s;

        return $this;
    }

    public function getReceive0s(): ?int
    {
        return $this->receive0s;
    }

    public function setReceive0s(?int $receive0s): static
    {
        $this->receive0s = $receive0s;

        return $this;
    }

    public function getReceiveAttempts(): ?int
    {
        return $this->receiveAttempts;
    }

    public function setReceiveAttempts(?int $receiveAttempts): static
    {
        $this->receiveAttempts = $receiveAttempts;

        return $this;
    }

    public function getSetAssists(): ?int
    {
        return $this->setAssists;
    }

    public function setSetAssists(?int $setAssists): static
    {
        $this->setAssists = $setAssists;

        return $this;
    }

    public function getSetAttempts(): ?int
    {
        return $this->setAttempts;
    }

    public function setSetAttempts(?int $setAttempts): static
    {
        $this->setAttempts = $setAttempts;

        return $this;
    }

    public function getDigSuccesss(): ?int
    {
        return $this->digSuccesss;
    }

    public function setDigSuccesss(?int $digSuccesss): static
    {
        $this->digSuccesss = $digSuccesss;

        return $this;
    }

    public function getDigErrors(): ?int
    {
        return $this->digErrors;
    }

    public function setDigErrors(?int $digErrors): static
    {
        $this->digErrors = $digErrors;

        return $this;
    }

    public function getBlockSingle(): ?int
    {
        return $this->blockSingle;
    }

    public function setBlockBlockSolos(?int $blockSolos): static
    {
        $this->blockBlockSolos = $blockSolos;

        return $this;
    }

    public function getBlockBlockAssists(): ?int
    {
        return $this->blockBlockAssists;
    }

    public function setBlockBlockAssists(?int $blockAssists): static
    {
        $this->blockBlockAssists = $blockAssists;

        return $this;
    }

    public function getBlockBlockErrors(): ?int
    {
        return $this->blockBlockErrors;
    }

    public function setBlockBlockErrors(?int $blockErrors): static
    {
        $this->blockBlockErrors = $blockErrors;

        return $this;
    }

    public function getTotalPoints(): int
    {
        return $this->serveAces + $this->attackKills + $this->blockBlockSolos + $this->blockBlockAssists;
    }

    public function getTotalWinMinusLose(): int
    {
        return $this->getTotalPoints() - $this->serveErrors - $this->attackErrors - $this->blockBlockErrors - $this->digErrors;
    }

    public function getServeSuccesss(): int
    {
        return $this->serveAces + $this->serve1s;
    }

    public function getServeSuccesssPercent(): ?float
    {
        return self::calcPercent($this->getServeSuccesss(), $this->serveAttempts);
    }

    public function getServeErrorPercent(): ?float
    {
        return self::calcPercent($this->serveErrors, $this->serveAttempts);
    }

    public function getReceivePerfPercent(): ?float
    {
        return self::calcPercent($this->receive3s, $this->getReceiveAttempts());
    }

    public function getReceiveNegPercent(): ?float
    {
        return self::calcPercent($this->receive1s, $this->getReceiveAttempts());
    }

    public function getReceiveErrorPercent(): ?float
    {
        return self::calcPercent($this->receive0s, $this->getReceiveAttempts());
    }

    public function getAttackKillPercent(): ?float
    {
        return self::calcPercent($this->attackKills, $this->attackAttempts);
    }

    public function getAttackErrorPercent(): ?float
    {
        return self::calcPercent($this->attackErrors, $this->attackAttempts);
    }

    private static function calcPercent(?int $part, ?int $total): ?float
    {
        if (!$total) {
            return null;
        }

        $percent = 0;
        if ($part) {
            $percent = round($part / $total, 5);
        }

        return $percent;
    }
}
