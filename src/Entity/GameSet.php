<?php

namespace App\Entity;

use App\Repository\GameSetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSetRepository::class)]
class GameSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'gameSets')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $setNumber;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $pointsTeamOne;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $pointsTeamTwo;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $durationMinutes = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getSetNumber(): int
    {
        return $this->setNumber;
    }

    public function setSetNumber(int $setNumber): static
    {
        $this->setNumber = $setNumber;

        return $this;
    }

    public function getPointsTeamOne(): int
    {
        return $this->pointsTeamOne;
    }

    public function setPointsTeamOne(int $pointsTeamOne): static
    {
        $this->pointsTeamOne = $pointsTeamOne;

        return $this;
    }

    public function getPointsTeamTwo(): int
    {
        return $this->pointsTeamTwo;
    }

    public function setPointsTeamTwo(int $pointsTeamTwo): static
    {
        $this->pointsTeamTwo = $pointsTeamTwo;

        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }
}
