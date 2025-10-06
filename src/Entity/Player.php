<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $number;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
