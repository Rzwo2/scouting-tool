<?php

namespace App\Controller\Statistic;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatisticController extends AbstractController
{
    #[Route('/statistic', name: 'app_statistic')]
    public function index(): Response
    {
        return $this->render('statistic/index.html.twig', [
            'controller_name' => 'StatisticController',
        ]);
    }

    #[Route('/statistic/table', name: 'statistic_table')]
    public function getTable(): JsonResponse
    {
        return new JsonResponse();
    }
}
