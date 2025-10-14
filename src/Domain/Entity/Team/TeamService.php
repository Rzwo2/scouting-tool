<?php

declare(strict_types=1);

namespace App\Domain\Entity\Team;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TeamService
{
    public function __construct(
        private TeamRepository $teamRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function handleFilterRequest(Request $request): array
    {
        $teams = $this->teamRepository->findAll();

        return array_map(
            fn (Team $team) => [
                'name' => $team->getName(),
            ],
            $teams
        );
    }
}
