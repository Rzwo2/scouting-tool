<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Team $teamOne;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Team $teamTwo;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date = null;

    /**
     * @var Collection<int, GameSet>
     */
    #[ORM\OneToMany(targetEntity: GameSet::class, mappedBy: 'game')]
    private Collection $gameSets;

    /**
     * @var Collection<int, PlayerGameStatistic>
     */
    #[ORM\OneToMany(targetEntity: PlayerGameStatistic::class, mappedBy: 'game')]
    private Collection $playerGameStatistics;

    public function __construct()
    {
        $this->gameSets = new ArrayCollection();
        $this->playerGameStatistics = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTeamOne(): Team
    {
        return $this->teamOne;
    }

    public function setTeamOne(Team $teamOne): static
    {
        $this->teamOne = $teamOne;

        return $this;
    }

    public function getTeamTwo(): Team
    {
        return $this->teamTwo;
    }

    public function setTeamTwo(Team $teamTwo): static
    {
        $this->teamTwo = $teamTwo;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, GameSet>
     */
    public function getGameSets(): Collection
    {
        return $this->gameSets;
    }

    public function addGameSet(GameSet $gameSet): static
    {
        if (!$this->gameSets->contains($gameSet)) {
            $this->gameSets->add($gameSet);
            $gameSet->setGame($this);
        }

        return $this;
    }

    public function removeGameSet(GameSet $gameSet): static
    {
        if ($this->gameSets->removeElement($gameSet)) {
            // set the owning side to null (unless already changed)
            if ($gameSet->getGame() === $this) {
                $gameSet->setGame(null);
            }
        }

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
            $playerGameStatistic->setGame($this);
        }

        return $this;
    }

    public function removePlayerGameStatistic(PlayerGameStatistic $playerGameStatistic): static
    {
        if ($this->playerGameStatistics->removeElement($playerGameStatistic)) {
            // set the owning side to null (unless already changed)
            if ($playerGameStatistic->getGame() === $this) {
                $playerGameStatistic->setGame(null);
            }
        }

        return $this;
    }
}
