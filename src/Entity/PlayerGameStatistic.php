<?php

namespace App\Entity;

use App\Repository\PlayerGameStatisticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerGameStatisticRepository::class)]
class PlayerGameStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'playerGameStatistics')]
    #[ORM\JoinColumn(nullable: false)]
    private Player $player;

    #[ORM\ManyToOne(inversedBy: 'playerGameStatistics')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $setsPlayed;

    #[ORM\Column]
    private bool $isFirstBallSideOut;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackKill = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackError = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $attackTotal = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveAce = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveError = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $serveTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $serveRating = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive3 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive2 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive1 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $receive0 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $setAssist = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $setTotal = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $digSuccess = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $digError = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockSingle = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockAssist = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $blockError = null;


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

    public function getAttackKill(): ?int
    {
        return $this->attackKill;
    }

    public function setAttackKill(?int $attackKill): static
    {
        $this->attackKill = $attackKill;

        return $this;
    }

    public function getAttackError(): ?int
    {
        return $this->attackError;
    }

    public function setAttackError(?int $attackError): static
    {
        $this->attackError = $attackError;

        return $this;
    }

    public function getAttackTotal(): ?int
    {
        return $this->attackTotal;
    }

    public function setAttackTotal(?int $attackTotal): static
    {
        $this->attackTotal = $attackTotal;

        return $this;
    }

    public function getServeAce(): ?int
    {
        return $this->serveAce;
    }

    public function setServeAce(?int $serveAce): static
    {
        $this->serveAce = $serveAce;

        return $this;
    }

    public function getServeError(): ?int
    {
        return $this->serveError;
    }

    public function setServeError(?int $serveError): static
    {
        $this->serveError = $serveError;

        return $this;
    }

    public function getServeTotal(): ?int
    {
        return $this->serveTotal;
    }

    public function setServeTotal(?int $serveTotal): static
    {
        $this->serveTotal = $serveTotal;

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

    public function getReceive3(): ?int
    {
        return $this->receive3;
    }

    public function setReceive3(?int $receive3): static
    {
        $this->receive3 = $receive3;

        return $this;
    }

    public function getReceive2(): ?int
    {
        return $this->receive2;
    }

    public function setReceive2(?int $receive2): static
    {
        $this->receive2 = $receive2;

        return $this;
    }

    public function getReceive1(): ?int
    {
        return $this->receive1;
    }

    public function setReceive1(?int $receive1): static
    {
        $this->receive1 = $receive1;

        return $this;
    }

    public function getReceive0(): ?int
    {
        return $this->receive0;
    }

    public function setReceive0(?int $receive0): static
    {
        $this->receive0 = $receive0;

        return $this;
    }

    public function getSetAssist(): ?int
    {
        return $this->setAssist;
    }

    public function setSetAssist(?int $setAssist): static
    {
        $this->setAssist = $setAssist;

        return $this;
    }

    public function getSetTotal(): ?int
    {
        return $this->setTotal;
    }

    public function setSetTotal(?int $setTotal): static
    {
        $this->setTotal = $setTotal;

        return $this;
    }

    public function getDigSuccess(): ?int
    {
        return $this->digSuccess;
    }

    public function setDigSuccess(?int $digSuccess): static
    {
        $this->digSuccess = $digSuccess;

        return $this;
    }

    public function getDigError(): ?int
    {
        return $this->digError;
    }

    public function setDigError(?int $digError): static
    {
        $this->digError = $digError;

        return $this;
    }

    public function getBlockSingle(): ?int
    {
        return $this->blockSingle;
    }

    public function setBlockSingle(?int $blockSingle): static
    {
        $this->blockSingle = $blockSingle;

        return $this;
    }

    public function getBlockAssist(): ?int
    {
        return $this->blockAssist;
    }

    public function setBlockAssist(?int $blockAssist): static
    {
        $this->blockAssist = $blockAssist;

        return $this;
    }

    public function getBlockError(): ?int
    {
        return $this->blockError;
    }

    public function setBlockError(?int $blockError): static
    {
        $this->blockError = $blockError;

        return $this;
    }

}
