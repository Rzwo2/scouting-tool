<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 20, unique: true)]
    private string $playerId;

    #[ORM\Column(name: 'first_name', length: 255)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', length: 255)]
    private string $lastName;

    #[ORM\Column]
    private int $number;

    #[ORM\Column]
    private ?int $height;

    #[ORM\Column(name: 'birth_date', type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $birthDate;

    #[ORM\Column]
    private string $position;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Team $team;

    /**
     * @var Collection<int, PlayerGameStatistic>
     */
    #[ORM\OneToMany(targetEntity: PlayerGameStatistic::class, mappedBy: 'player')]
    private Collection $playerGameStatistics;

    public function __construct()
    {
        $this->playerGameStatistics = new ArrayCollection();
    }

    public function __toString(): string
    {
        return "$this->firstName $this->lastName";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): static
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, PlayerGameStatistic>
     */
    public function getPlayerGameStatistics(): Collection
    {
        return $this->playerGameStatistics;
    }

    public function addPlayerGameStatistic(PlayerGameStatistic $playerGameStatistic): static
    {
        if (!$this->playerGameStatistics->contains($playerGameStatistic)) {
            $this->playerGameStatistics->add($playerGameStatistic);
            $playerGameStatistic->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerGameStatistic(PlayerGameStatistic $playerGameStatistic): static
    {
        if ($this->playerGameStatistics->removeElement($playerGameStatistic)) {
            // set the owning side to null (unless already changed)
            if ($playerGameStatistic->getPlayer() === $this) {
                $playerGameStatistic->setPlayer(null);
            }
        }

        return $this;
    }
}
