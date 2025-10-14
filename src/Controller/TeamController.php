<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Entity\Team\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/team')]
class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamService $handler,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route(name: 'team_index', methods: ['GET'])]
    public function indexAction(): Response
    {
        return $this->render('team/index.html.twig', [
            'filterDataUrl' => $this->generateUrl('team_filter'),
        ]);
    }

    #[Route(path: '/filter', name: 'team_filter', methods: ['POST'])]
    public function filterDataAction(Request $request): Response
    {
        $result = $this->handler->handleFilterRequest($request);
        $data = $this->serializer->serialize($result, 'json');

        return new Response($data);
    }

    public function editAction() {}
}
